<?php

namespace App\Services;

use App\Models\ParentGuardian;
use App\Models\SchoolSettings;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Compute + apply the two automatic tuition discounts:
 *
 *   • Sibling      — flat percentage on each pupil's tuition once the
 *                    family has N or more active pupils enrolled.
 *   • Early payment — retroactive credit for pupils whose term fee was
 *                    fully paid on or before term.start_date.
 *
 * Both policies are idempotent: re-running only changes rows whose
 * computed discount differs from what's already stamped. Every applied
 * row carries discount_type, discount_reason, and approved_by so the
 * audit trail is always on the fee row itself.
 */
class DiscountPolicyService
{
    public const TYPE_SIBLING       = 'sibling';
    public const TYPE_EARLY_PAYMENT = 'early_payment';

    /* --------------------------------------------------------------- */
    /* Sibling discount                                                  */
    /* --------------------------------------------------------------- */

    /**
     * Returns a preview: [
     *   'enabled' => bool, 'min_pupils' => int, 'percentage' => float,
     *   'families' => Collection<[
     *      'guardian_id','guardian_name','pupils' => Collection<[student_id,name,class,basic_fee,discount,current_discount]>,
     *      'family_pupil_count','total_new_discount',
     *   ]>,
     *   'summary' => ['families' => int, 'pupils' => int, 'discount_total' => float],
     * ]
     */
    public function previewSibling(int $termId): array
    {
        $ss = SchoolSettings::getInstance();
        $min = (int) ($ss->sibling_discount_min_pupils ?? 4);
        $pct = (float) ($ss->sibling_discount_percentage ?? 10);

        $families = collect();
        $summary  = ['families' => 0, 'pupils' => 0, 'discount_total' => 0.0];

        if (! $ss->sibling_discount_enabled || $min < 2 || $pct <= 0) {
            return compact('summary') + [
                'enabled'    => (bool) $ss->sibling_discount_enabled,
                'min_pupils' => $min,
                'percentage' => $pct,
                'families'   => $families,
            ];
        }

        // Guardians with N+ active pupils, and each pupil's term fee row.
        $guardianRows = DB::table('parent_guardians as pg')
            ->join('students as s', 's.parent_guardian_id', '=', 'pg.id')
            ->where('s.enrollment_status', 'active')
            ->select('pg.id', 'pg.name', DB::raw('COUNT(s.id) as pupil_count'))
            ->groupBy('pg.id', 'pg.name')
            ->having('pupil_count', '>=', $min)
            ->get();

        foreach ($guardianRows as $gr) {
            $pupilFees = StudentFee::query()
                ->with(['student.classSection.grade', 'feeStructure:id,basic_fee'])
                ->where('term_id', $termId)
                ->whereHas('student', fn ($q) => $q->where('parent_guardian_id', $gr->id)->where('enrollment_status', 'active'))
                ->get();

            $pupils = $pupilFees->map(function (StudentFee $sf) use ($pct) {
                $basic    = (float) ($sf->feeStructure?->basic_fee ?? 0);
                $discount = round($basic * ($pct / 100), 2);
                return [
                    'fee_id'          => $sf->id,
                    'student_id'      => $sf->student_id,
                    'name'            => $sf->student?->name ?? '?',
                    'class'           => $sf->student && $sf->student->classSection
                        ? ($sf->student->classSection->grade?->name . ' - ' . $sf->student->classSection->name)
                        : '—',
                    'basic_fee'       => $basic,
                    'discount'        => $discount,
                    'current_discount' => (float) $sf->discount_amount,
                    'current_type'    => (string) ($sf->discount_type ?? ''),
                ];
            });

            $families->push([
                'guardian_id'         => $gr->id,
                'guardian_name'       => $gr->name,
                'family_pupil_count'  => (int) $gr->pupil_count,
                'pupils'              => $pupils,
                'total_new_discount'  => $pupils->sum('discount'),
            ]);

            $summary['families']++;
            $summary['pupils']         += $pupils->count();
            $summary['discount_total'] += $pupils->sum('discount');
        }

        return [
            'enabled'    => true,
            'min_pupils' => $min,
            'percentage' => $pct,
            'families'   => $families,
            'summary'    => $summary,
        ];
    }

    public function applySibling(int $termId, ?int $actorId = null): array
    {
        $preview  = $this->previewSibling($termId);
        if (! $preview['enabled']) return ['applied' => 0, 'skipped' => 0];

        $pct      = $preview['percentage'];
        $applied  = 0;
        $skipped  = 0;

        foreach ($preview['families'] as $family) {
            foreach ($family['pupils'] as $p) {
                // Skip if same-type + same-amount already there (idempotent).
                if ($p['current_type'] === self::TYPE_SIBLING
                    && abs($p['current_discount'] - $p['discount']) < 0.01) {
                    $skipped++;
                    continue;
                }
                // Don't overwrite a manual/bursary discount without warning.
                if ($p['current_type'] && $p['current_type'] !== self::TYPE_SIBLING) {
                    $skipped++;
                    Log::channel('scheduler')->info('discount.sibling.skipped-nonempty', [
                        'fee_id'       => $p['fee_id'],
                        'current_type' => $p['current_type'],
                    ]);
                    continue;
                }

                StudentFee::where('id', $p['fee_id'])->update([
                    'discount_type'       => self::TYPE_SIBLING,
                    'discount_amount'     => $p['discount'],
                    'discount_percentage' => $pct,
                    'discount_reason'     => sprintf('Sibling discount — %d pupils in family (%s%%)', $family['family_pupil_count'], rtrim(rtrim(number_format($pct, 2), '0'), '.')),
                    'approved_by'         => $actorId,
                ]);
                // Trigger balance recompute via the saving observer.
                StudentFee::find($p['fee_id'])->save();
                $applied++;
            }
        }
        return ['applied' => $applied, 'skipped' => $skipped];
    }

    /* --------------------------------------------------------------- */
    /* Early payment discount                                            */
    /* --------------------------------------------------------------- */

    /**
     * Returns a preview: [
     *   'enabled','percentage','cutoff_date',
     *   'pupils' => Collection<[fee_id,student_id,name,class,basic_fee,amount_paid,payment_date,discount,current_type]>,
     *   'summary' => ['pupils','discount_total'],
     * ]
     */
    public function previewEarlyPayment(int $termId): array
    {
        $ss  = SchoolSettings::getInstance();
        $pct = (float) ($ss->early_payment_discount_percentage ?? 5);

        $term = Term::find($termId);
        $cutoff = $term?->start_date ? Carbon::parse($term->start_date)->endOfDay() : null;

        $result = [
            'enabled'     => (bool) $ss->early_payment_discount_enabled,
            'percentage'  => $pct,
            'cutoff_date' => $cutoff,
            'pupils'      => collect(),
            'summary'     => ['pupils' => 0, 'discount_total' => 0.0],
        ];
        if (! $result['enabled'] || ! $cutoff || $pct <= 0) return $result;

        $rows = StudentFee::query()
            ->with(['student.classSection.grade', 'feeStructure:id,basic_fee'])
            ->where('term_id', $termId)
            ->whereNotNull('payment_date')
            ->where('payment_date', '<=', $cutoff)
            ->get()
            ->filter(function (StudentFee $sf) {
                $basic = (float) ($sf->feeStructure?->basic_fee ?? 0);
                if ($basic <= 0) return false;
                // Full payment landed on or before term start.
                return (float) $sf->amount_paid >= $basic;
            });

        $result['pupils'] = $rows->map(function (StudentFee $sf) use ($pct) {
            $basic    = (float) $sf->feeStructure->basic_fee;
            $discount = round($basic * ($pct / 100), 2);
            return [
                'fee_id'          => $sf->id,
                'student_id'      => $sf->student_id,
                'name'            => $sf->student?->name ?? '?',
                'class'           => $sf->student && $sf->student->classSection
                    ? ($sf->student->classSection->grade?->name . ' - ' . $sf->student->classSection->name)
                    : '—',
                'basic_fee'       => $basic,
                'amount_paid'     => (float) $sf->amount_paid,
                'payment_date'    => $sf->payment_date,
                'discount'        => $discount,
                'current_discount' => (float) $sf->discount_amount,
                'current_type'    => (string) ($sf->discount_type ?? ''),
            ];
        })->values();

        $result['summary'] = [
            'pupils'         => $result['pupils']->count(),
            'discount_total' => $result['pupils']->sum('discount'),
        ];
        return $result;
    }

    public function applyEarlyPayment(int $termId, ?int $actorId = null): array
    {
        $preview = $this->previewEarlyPayment($termId);
        if (! $preview['enabled']) return ['applied' => 0, 'skipped' => 0];

        $pct     = $preview['percentage'];
        $applied = 0;
        $skipped = 0;
        $cutoff  = $preview['cutoff_date'];

        foreach ($preview['pupils'] as $p) {
            if ($p['current_type'] === self::TYPE_EARLY_PAYMENT
                && abs($p['current_discount'] - $p['discount']) < 0.01) {
                $skipped++;
                continue;
            }
            if ($p['current_type'] && $p['current_type'] !== self::TYPE_EARLY_PAYMENT) {
                $skipped++;
                Log::channel('scheduler')->info('discount.early.skipped-nonempty', [
                    'fee_id'       => $p['fee_id'],
                    'current_type' => $p['current_type'],
                ]);
                continue;
            }
            StudentFee::where('id', $p['fee_id'])->update([
                'discount_type'       => self::TYPE_EARLY_PAYMENT,
                'discount_amount'     => $p['discount'],
                'discount_percentage' => $pct,
                'discount_reason'     => sprintf('Early payment discount — full fee settled by %s (%s%%)', $cutoff->format('d M Y'), rtrim(rtrim(number_format($pct, 2), '0'), '.')),
                'approved_by'         => $actorId,
            ]);
            StudentFee::find($p['fee_id'])->save();
            $applied++;
        }
        return ['applied' => $applied, 'skipped' => $skipped];
    }

    /* --------------------------------------------------------------- */
    /* Reverse                                                           */
    /* --------------------------------------------------------------- */

    public function reverse(int $termId, string $type, ?int $actorId = null): int
    {
        $rows = StudentFee::where('term_id', $termId)
            ->where('discount_type', $type)
            ->get();
        foreach ($rows as $sf) {
            $sf->fill([
                'discount_type'       => null,
                'discount_amount'     => 0,
                'discount_percentage' => 0,
                'discount_reason'     => null,
                'approved_by'         => $actorId,
            ])->save();
        }
        return $rows->count();
    }

    public function currentByTerm(int $termId): array
    {
        $rows = StudentFee::where('term_id', $termId)
            ->whereIn('discount_type', [self::TYPE_SIBLING, self::TYPE_EARLY_PAYMENT])
            ->selectRaw('discount_type, COUNT(*) as pupils, SUM(discount_amount) as total')
            ->groupBy('discount_type')
            ->get()
            ->keyBy('discount_type');
        return [
            'sibling'       => $rows->get(self::TYPE_SIBLING),
            'early_payment' => $rows->get(self::TYPE_EARLY_PAYMENT),
        ];
    }
}
