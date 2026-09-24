<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\FeeCatalogueItem;
use App\Models\FeeCategory;
use App\Models\Student;
use App\Models\StudentBusAssignment;
use App\Models\StudentFee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Creates categorized StudentFee rows for the school year, the bus month, or
 * a catalogue assignment. Idempotent: every action skips rows the school
 * already has, so the admin can re-run safely.
 */
class FeeProvisioningService
{
    /**
     * Create one StudentFee per (active student × annual category) for $year.
     * Annual categories are: PTA, Computer, Maintenance — driven by
     * FeeCategory::ANNUAL_AUTO_CODES.
     */
    public function generateAnnualFees(AcademicYear $year, bool $dryRun = true): array
    {
        $categories = FeeCategory::whereIn('code', FeeCategory::ANNUAL_AUTO_CODES)->where('is_active', true)->get();
        $students   = Student::where('enrollment_status', 'active')->get(['id', 'name']);
        $stats      = ['planned' => 0, 'created' => 0, 'skipped' => 0, 'failed' => 0];

        foreach ($categories as $cat) {
            foreach ($students as $stu) {
                $exists = StudentFee::where('student_id', $stu->id)
                    ->where('fee_category_id', $cat->id)
                    ->where('academic_year_id', $year->id)
                    ->whereNull('term_id')
                    ->exists();
                if ($exists) { $stats['skipped']++; continue; }

                $stats['planned']++;
                if ($dryRun) continue;

                try {
                    DB::transaction(function () use ($stu, $cat, $year) {
                        StudentFee::create([
                            'student_id'       => $stu->id,
                            'fee_category_id'  => $cat->id,
                            'period_label'     => (string) $year->name,
                            'academic_year_id' => $year->id,
                            'amount_paid'      => 0,
                            'balance'          => (float) $cat->default_amount,
                            'payment_status'   => 'unpaid',
                        ]);
                    });
                    $stats['created']++;
                } catch (\Throwable $e) {
                    $stats['failed']++;
                    Log::warning('Annual fee creation failed', ['student' => $stu->id, 'cat' => $cat->code, 'err' => $e->getMessage()]);
                }
            }
        }
        return $stats;
    }

    /**
     * Create one Bus StudentFee per currently-active assignment for the given month.
     * Period label: e.g. "May 2026". Idempotent per (student × bus × period_label).
     */
    public function generateBusChargesForMonth(Carbon $month, bool $dryRun = true): array
    {
        $bus = FeeCategory::where('code', FeeCategory::BUS)->first();
        if (! $bus) return ['planned' => 0, 'created' => 0, 'skipped' => 0, 'failed' => 0, 'note' => 'bus category missing'];

        $period = $month->copy()->startOfMonth()->format('F Y');
        $assignments = StudentBusAssignment::currentlyActive()->with('student:id,name,enrollment_status')->get();
        $stats = ['planned' => 0, 'created' => 0, 'skipped' => 0, 'failed' => 0];

        foreach ($assignments as $a) {
            if (! $a->student || $a->student->enrollment_status !== 'active') continue;

            $exists = StudentFee::where('student_id', $a->student_id)
                ->where('fee_category_id', $bus->id)
                ->where('period_label', $period)
                ->exists();
            if ($exists) { $stats['skipped']++; continue; }

            $stats['planned']++;
            if ($dryRun) continue;

            try {
                $amount = (float) ($a->monthly_amount ?: $bus->default_amount);
                DB::transaction(function () use ($a, $bus, $period, $amount) {
                    StudentFee::create([
                        'student_id'      => $a->student_id,
                        'fee_category_id' => $bus->id,
                        'period_label'    => $period,
                        'amount_paid'     => 0,
                        'balance'         => $amount,
                        'payment_status'  => 'unpaid',
                    ]);
                });
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['failed']++;
                Log::warning('Bus charge creation failed', ['student' => $a->student_id, 'period' => $period, 'err' => $e->getMessage()]);
            }
        }
        return $stats;
    }

    /**
     * Assign a one-off fee defined inline (item name + amount + category) — used
     * for uniforms whose prices live in fee_structures.additional_charges rather
     * than the catalogue. Idempotent per (student × category × period_label).
     */
    public function assignAdhocFee(string $categoryCode, string $itemName, float $amount, array $studentIds, bool $dryRun = true): array
    {
        $cat = FeeCategory::where('code', $categoryCode)->first();
        if (! $cat) {
            return ['planned' => 0, 'created' => 0, 'skipped' => 0, 'failed' => 0, 'note' => "category {$categoryCode} missing"];
        }

        $stats = ['planned' => 0, 'created' => 0, 'skipped' => 0, 'failed' => 0];
        $existing = StudentFee::whereIn('student_id', $studentIds)
            ->where('fee_category_id', $cat->id)
            ->where('period_label', $itemName)
            ->pluck('student_id')->all();
        $existing = array_flip($existing);

        foreach ($studentIds as $sid) {
            if (isset($existing[$sid])) { $stats['skipped']++; continue; }
            $stats['planned']++;
            if ($dryRun) continue;

            try {
                DB::transaction(function () use ($sid, $cat, $itemName, $amount) {
                    StudentFee::create([
                        'student_id'      => $sid,
                        'fee_category_id' => $cat->id,
                        'period_label'    => $itemName,
                        'amount_paid'     => 0,
                        'balance'         => $amount,
                        'payment_status'  => 'unpaid',
                    ]);
                });
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['failed']++;
                Log::warning('Adhoc fee creation failed', ['student' => $sid, 'item' => $itemName, 'err' => $e->getMessage()]);
            }
        }
        return $stats;
    }

    /**
     * Assign a catalogue item (uniform / educational tour) to a list of students.
     * Period label defaults to the catalogue item's name. Idempotent per
     * (student × catalogue_item_id) so re-running doesn't duplicate.
     */
    public function assignCatalogueItem(FeeCatalogueItem $item, array $studentIds, ?string $periodOverride = null, bool $dryRun = true): array
    {
        $item->loadMissing('category');
        $period = $periodOverride ?: $item->name;
        $stats  = ['planned' => 0, 'created' => 0, 'skipped' => 0, 'failed' => 0];

        $existing = StudentFee::whereIn('student_id', $studentIds)
            ->where('catalogue_item_id', $item->id)
            ->pluck('student_id')->all();
        $existing = array_flip($existing);

        foreach ($studentIds as $sid) {
            if (isset($existing[$sid])) { $stats['skipped']++; continue; }
            $stats['planned']++;
            if ($dryRun) continue;

            try {
                DB::transaction(function () use ($sid, $item, $period) {
                    StudentFee::create([
                        'student_id'        => $sid,
                        'fee_category_id'   => $item->fee_category_id,
                        'catalogue_item_id' => $item->id,
                        'period_label'      => $period,
                        'amount_paid'       => 0,
                        'balance'           => (float) $item->amount,
                        'payment_status'    => 'unpaid',
                    ]);
                });
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['failed']++;
                Log::warning('Catalogue assignment failed', ['student' => $sid, 'item' => $item->id, 'err' => $e->getMessage()]);
            }
        }
        return $stats;
    }
}
