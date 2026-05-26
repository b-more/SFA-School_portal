<?php

namespace App\Console\Commands;

use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Carry unpaid balances from one term to the next, and generate fresh invoices
 * for any student missing an invoice in the destination term.
 *
 * For each student with `from` term balance > 0:
 *   - Find/create their `to` term invoice (matched by section fee structure)
 *   - Add the `from` balance into the new invoice's previous_balance
 *   - Recompute balance + payment_status on the new invoice
 *   - Close the `from` invoice: payment_status = 'carried_forward', balance = 0,
 *     carried_forward_to_fee_id = new invoice id
 *
 * For students with no balance and no `to` term invoice yet: generate a fresh
 * invoice with previous_balance = 0.
 *
 * Idempotent: re-running after a successful apply is a no-op.
 */
class CarryForwardTermFees extends Command
{
    protected $signature = 'fees:carry-forward
        {from : Source term id (the term that is closing)}
        {to   : Destination term id (the term that is starting)}
        {--dry-run : Compute the diff but do not commit any changes}
        {--apply   : Actually commit changes (required when not --dry-run)}';

    protected $description = 'Carry forward unpaid term balances and generate missing invoices for the destination term.';

    public function handle(): int
    {
        $fromId = (int) $this->argument('from');
        $toId = (int) $this->argument('to');

        $fromTerm = Term::find($fromId);
        $toTerm = Term::find($toId);
        if (! $fromTerm || ! $toTerm) {
            $this->error("Invalid term id(s). Got from={$fromId}, to={$toId}.");
            return self::FAILURE;
        }

        if (! $this->option('dry-run') && ! $this->option('apply')) {
            $this->error('Refusing to run. Pass either --dry-run or --apply.');
            return self::FAILURE;
        }

        $this->line("From: T{$fromTerm->id} {$fromTerm->name} ({$fromTerm->academicYear?->name})");
        $this->line("To:   T{$toTerm->id} {$toTerm->name} ({$toTerm->academicYear?->name})");
        $this->line($this->option('dry-run') ? '** DRY RUN **' : '** APPLYING **');

        // Build a section -> fee_structure lookup for the destination term.
        $sectionFees = FeeStructure::query()
            ->where('term_id', $toId)
            ->whereNotNull('school_section_id')
            ->get()
            ->keyBy('school_section_id');

        if ($sectionFees->isEmpty()) {
            $this->error("No fee structures defined for the destination term {$toTerm->name}. Create them under Finance Management → Fee Structures first.");
            return self::FAILURE;
        }

        $stats = [
            'arrears_carried' => 0,
            'arrears_amount' => 0.0,
            'invoices_generated' => 0,
            'invoices_updated' => 0,
            'students_skipped' => 0,
            'skipped_reasons' => [],
        ];

        $samples = [];

        // Student IDs Pass 1 will create/update — used by Pass 2 to exclude them
        // even during --dry-run when nothing is actually persisted.
        $studentsHandledByPass1 = [];

        $tx = function () use ($fromId, $toId, $sectionFees, &$stats, &$samples, &$studentsHandledByPass1) {
            // 1. Carry-forward pass: every from-term invoice with balance > 0.
            $arrearsRows = StudentFee::query()
                ->where('term_id', $fromId)
                ->where('balance', '>', 0)
                ->with([
                    'student:id,name,grade_id',
                    'student.grade:id,name,school_section_id',
                ])
                ->get();

            foreach ($arrearsRows as $fromFee) {
                $student = $fromFee->student;
                $sectionId = $student?->grade?->school_section_id;
                $sectionFee = $sectionFees->get($sectionId);

                if (! $sectionFee) {
                    $stats['students_skipped']++;
                    $stats['skipped_reasons'][] = "{$student?->name}: no fee structure for section id {$sectionId}";
                    continue;
                }

                $toFee = StudentFee::firstOrNew([
                    'student_id' => $student->id,
                    'term_id' => $toId,
                ]);

                $isNew = ! $toFee->exists;

                if ($isNew) {
                    $toFee->fee_structure_id = $sectionFee->id;
                    $toFee->academic_year_id = $sectionFee->academic_year_id;
                    $toFee->grade_id = $student->grade_id;
                    $toFee->amount_paid = 0;
                    $toFee->discount_amount = 0;
                    $toFee->previous_balance = 0;
                }

                // Add the arrears (idempotent: if we already carried this exact balance, skip).
                $newArrearsForThisRow = (float) $fromFee->balance;
                if ($fromFee->carried_forward_to_fee_id) {
                    // Already carried in a prior run — skip.
                    $stats['students_skipped']++;
                    continue;
                }

                $toFee->previous_balance = (float) $toFee->previous_balance + $newArrearsForThisRow;

                // Use basic_fee (tuition-only) to match how the rest of the system
                // tracks `balance`. The StudentFee::saving hook will recompute on save,
                // but we also set explicitly so the dry-run preview matches the
                // committed values.
                $totalOwed = (float) $sectionFee->basic_fee
                    + (float) $toFee->previous_balance
                    - (float) $toFee->discount_amount;
                $toFee->balance = max(0, $totalOwed - (float) $toFee->amount_paid);
                $toFee->payment_status = $this->statusFor((float) $toFee->amount_paid, $totalOwed);

                if (! $this->option('dry-run')) {
                    $toFee->save();
                    $fromFee->update([
                        'balance' => 0,
                        'payment_status' => 'carried_forward',
                        'carried_forward_to_fee_id' => $toFee->id,
                    ]);
                }

                $stats['arrears_carried']++;
                $stats['arrears_amount'] += $newArrearsForThisRow;
                if ($isNew) {
                    $stats['invoices_generated']++;
                } else {
                    $stats['invoices_updated']++;
                }
                $studentsHandledByPass1[] = $student->id;

                if (count($samples) < 5) {
                    $samples[] = [
                        'student' => $student?->name,
                        'section' => $student?->grade?->schoolSection?->code ?? '?',
                        'section_fee' => (float) $sectionFee->basic_fee,
                        'arrears' => $newArrearsForThisRow,
                        'new_balance' => (float) $toFee->balance,
                    ];
                }
            }

            // 2. Generate-missing pass: any active student with no destination invoice and no
            //    arrears in the source term either — they need a fresh empty invoice.
            $existingToStudentIds = StudentFee::query()
                ->where('term_id', $toId)
                ->pluck('student_id')
                ->merge($studentsHandledByPass1)
                ->unique();

            $missing = Student::query()
                ->where('enrollment_status', 'active')
                ->whereNotIn('id', $existingToStudentIds)
                ->with('grade:id,school_section_id')
                ->get();

            foreach ($missing as $student) {
                $sectionId = $student?->grade?->school_section_id;
                $sectionFee = $sectionFees->get($sectionId);

                if (! $sectionFee) {
                    $stats['students_skipped']++;
                    $stats['skipped_reasons'][] = "{$student->name}: no fee structure for section id {$sectionId}";
                    continue;
                }

                $totalOwed = (float) $sectionFee->basic_fee;

                if (! $this->option('dry-run')) {
                    StudentFee::create([
                        'student_id' => $student->id,
                        'fee_structure_id' => $sectionFee->id,
                        'academic_year_id' => $sectionFee->academic_year_id,
                        'term_id' => $toId,
                        'grade_id' => $student->grade_id,
                        'amount_paid' => 0,
                        'previous_balance' => 0,
                        'discount_amount' => 0,
                        'balance' => $totalOwed,
                        'payment_status' => 'unpaid',
                    ]);
                }

                $stats['invoices_generated']++;
            }
        };

        try {
            if ($this->option('dry-run')) {
                DB::transaction(function () use ($tx) {
                    $tx();
                    throw new DryRunRollback();
                });
            } else {
                DB::transaction($tx);
            }
        } catch (DryRunRollback $e) {
            // expected
        }

        // ---- Output report ----
        $this->newLine();
        $this->info('=== RESULTS ===');
        $this->table(['Metric', 'Value'], [
            ['Invoices: arrears carried forward', number_format($stats['arrears_carried'])],
            ['  Total arrears amount (ZMW)', number_format($stats['arrears_amount'], 2)],
            ['Invoices generated fresh', number_format($stats['invoices_generated'])],
            ['Existing invoices updated with arrears', number_format($stats['invoices_updated'])],
            ['Students skipped', number_format($stats['students_skipped'])],
        ]);

        if (! empty($stats['skipped_reasons'])) {
            $this->warn('Skipped students (first 10):');
            foreach (array_slice($stats['skipped_reasons'], 0, 10) as $r) {
                $this->line('  • ' . $r);
            }
        }

        if (! empty($samples)) {
            $this->newLine();
            $this->info('Sample (5 of the carried-forward students):');
            $this->table(
                ['Student', 'Section', 'Section fee', 'Arrears', 'New T2 balance'],
                array_map(fn ($s) => [
                    $s['student'],
                    $s['section'],
                    number_format($s['section_fee'], 2),
                    number_format($s['arrears'], 2),
                    number_format($s['new_balance'], 2),
                ], $samples)
            );
        }

        $this->newLine();
        if ($this->option('dry-run')) {
            $this->warn('No changes were committed. Re-run with --apply to commit.');
        } else {
            $this->info('Committed.');
        }

        return self::SUCCESS;
    }

    private function statusFor(float $paid, float $total): string
    {
        if ($total <= 0) {
            return $paid > 0 ? 'overpaid' : 'unpaid';
        }
        if ($paid <= 0) return 'unpaid';
        if ($paid >= $total) return $paid > $total ? 'overpaid' : 'paid';
        return 'partial';
    }
}

/** Internal: used to roll back a dry-run transaction. */
class DryRunRollback extends \RuntimeException {}
