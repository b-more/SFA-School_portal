<?php

namespace App\Filament\Resources\StudentFeeResource\Widgets;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StudentFeeStatsWidget extends BaseWidget
{
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $year = AcademicYear::where('is_active', true)->first();

        if (! $year) {
            return [
                Stat::make('No Active Academic Year', 'Please set an active academic year')
                    ->color('danger'),
            ];
        }

        $term = Term::where('academic_year_id', $year->id)
            ->where('is_current', true)
            ->first();

        if (! $term) {
            return [
                Stat::make('No Current Term', 'Mark one term as current in /admin/terms')
                    ->color('danger'),
            ];
        }

        // ---- Term-scoped query base ----
        // Exclude `carried_forward` rows: those are closed (balance=0, amount_paid
        // is historical), they don't represent current-term activity.
        // Columns are qualified because later we JOIN fee_structures which also has
        // term_id / payment_status-adjacent columns.
        $base = StudentFee::query()
            ->where('student_fees.term_id', $term->id)
            ->where('student_fees.payment_status', '!=', 'carried_forward');

        // ---- Money totals (term-scoped) ----
        // Expected = current-term tuition (basic_fee) + arrears carried forward (previous_balance) - discounts
        $expectedRow = (clone $base)
            ->join('fee_structures', 'student_fees.fee_structure_id', '=', 'fee_structures.id')
            ->selectRaw('
                COALESCE(SUM(fee_structures.basic_fee), 0)     AS tuition,
                COALESCE(SUM(student_fees.previous_balance), 0) AS arrears,
                COALESCE(SUM(student_fees.discount_amount), 0) AS discounts
            ')
            ->first();

        $expected = max(0, (float) $expectedRow->tuition + (float) $expectedRow->arrears - (float) $expectedRow->discounts);

        $collected   = (float) (clone $base)->sum('amount_paid');
        $outstanding = (float) (clone $base)->sum('balance'); // canonical from DB

        $collectionRate = $expected > 0 ? round(($collected / $expected) * 100, 1) : 0;

        // ---- Student counts (distinct, term-scoped) ----
        $paidStudents = (int) (clone $base)
            ->where('payment_status', 'paid')
            ->distinct('student_id')
            ->count('student_id');

        $partialStudents = (int) (clone $base)
            ->where('payment_status', 'partial')
            ->distinct('student_id')
            ->count('student_id');

        $unpaidStudents = (int) (clone $base)
            ->where('payment_status', 'unpaid')
            ->distinct('student_id')
            ->count('student_id');

        $studentsWithFees = (int) (clone $base)
            ->distinct('student_id')
            ->count('student_id');

        $activeStudents = (int) Student::where('enrollment_status', 'active')->count();

        return [
            Stat::make('Total Expected Fees', 'ZMW '.number_format($expected, 2))
                ->description("{$year->name} – {$term->name} ({$activeStudents} active students)")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Total Collected', 'ZMW '.number_format($collected, 2))
                ->description("Collection rate: {$collectionRate}% (this term)")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Outstanding Balance', 'ZMW '.number_format($outstanding, 2))
                ->description('Owed across all '.$studentsWithFees.' invoices this term')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($outstanding > 0 ? 'warning' : 'success'),

            Stat::make('Fully Paid Students', $paidStudents)
                ->description("Out of {$studentsWithFees} students with a {$term->name} invoice")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Partially Paid', $partialStudents)
                ->description('Started paying, still owe a balance')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Unpaid Students', $unpaidStudents)
                ->description('Have an invoice but have not paid yet')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
}
