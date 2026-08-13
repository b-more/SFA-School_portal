<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\PaymentTransaction;
use App\Models\Payroll;
use App\Models\StudentFee;
use App\Models\Term;
use App\Support\SectionResolver;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FeeCollectionTrackerService
{
    /**
     * Build the full tracker payload for a given academic year.
     *
     * Returned shape:
     * [
     *   'academic_year'  => AcademicYear,
     *   'year_label'     => '2026',
     *   'terms'          => Collection<Term> in ascending order,
     *   'sections'       => [ECE, Primary, Secondary],
     *   'by_term'        => [term_id => [section => [pupils, fee_per, fee_min, fee_max, expected, actual, shortfall, pct_collected, pct_loss]]],
     *   'term_totals'    => [term_id => [pupils, expected, actual, shortfall, pct_collected, pct_loss]],
     *   'salary_bill'    => [term_id => amount|null],
     *   'salary_meta'    => [term_id => 'Jan-Apr 2026 (…)'|null],
     *   'annual'         => [expected, actual, shortfall, pct_collected, pct_loss],
     *   'population'     => [section => list of ['class', 'teacher', 'enrolment']],
     *   'section_totals' => [section => enrolment],
     *   'school_total'   => enrolment,
     * ]
     */
    public function build(?int $academicYearId = null): array
    {
        $year = $academicYearId
            ? AcademicYear::findOrFail($academicYearId)
            : AcademicYear::where('is_active', true)->firstOrFail();

        $terms = Term::where('academic_year_id', $year->id)->orderBy('start_date')->get();

        $classSections = ClassSection::with('grade', 'classTeacher')
            ->whereHas('grade')
            ->get()
            ->sortBy(fn ($cs) => $cs->grade->name . '|' . $cs->name)
            ->values();

        // Attach section labels once so we don't recompute per pass.
        $classSections->each(function ($cs) {
            $cs->__section = SectionResolver::sectionFor($cs->grade);
        });

        $population     = $this->buildPopulation($classSections);
        $sectionTotals  = collect($population)->map(fn ($rows) => array_sum(array_column($rows, 'enrolment')))->all();
        $schoolTotal    = array_sum($sectionTotals);

        $byTerm      = [];
        $termTotals  = [];
        $salaryBill  = [];
        $salaryMeta  = [];

        foreach ($terms as $term) {
            $byTerm[$term->id]     = $this->buildTermSectionRows($term, $classSections);
            $termTotals[$term->id] = $this->rollUp($byTerm[$term->id]);
            [$salaryBill[$term->id], $salaryMeta[$term->id]] = $this->salaryBillFor($term);
        }

        $annual = $this->annualRollUp($termTotals);

        return [
            'academic_year'  => $year,
            'year_label'     => (string) ($year->name ?? $year->id),
            'terms'          => $terms,
            'sections'       => SectionResolver::ALL,
            'by_term'        => $byTerm,
            'term_totals'    => $termTotals,
            'salary_bill'    => $salaryBill,
            'salary_meta'    => $salaryMeta,
            'annual'         => $annual,
            'population'     => $population,
            'section_totals' => $sectionTotals,
            'school_total'   => $schoolTotal,
            'generated_at'   => now(),
        ];
    }

    /* --------------------------------------------------------------- */
    /* Per-term section rows                                             */
    /* --------------------------------------------------------------- */

    private function buildTermSectionRows(Term $term, Collection $classSections): array
    {
        $rows = [];
        foreach (SectionResolver::ALL as $section) {
            $sectionClasses = $classSections->filter(fn ($cs) => $cs->__section === $section);
            if ($sectionClasses->isEmpty()) {
                $rows[$section] = $this->emptyRow();
                continue;
            }

            // Pupils = the physical roll for this section (active students in
            // the section's classes) — one person, counted once, even if their
            // fee ledger has multiple rows this term.
            $studentIds = $sectionClasses->flatMap(fn ($cs) =>
                $cs->students()->where('enrollment_status', 'active')->pluck('id')
            )->unique()->values();
            $pupils = $studentIds->count();

            // Expected = sum of all StudentFee.basic_fee for those pupils in
            // this term (tuition + any structured items). basic_fee comes from
            // the joined FeeStructure. Rows with a K0 or missing structure are
            // tracked separately so they can be surfaced as data anomalies.
            $studentFees = StudentFee::query()
                ->with(['feeStructure:id,basic_fee'])
                ->where('term_id', $term->id)
                ->whereIn('student_id', $studentIds)
                ->get(['id', 'student_id', 'fee_structure_id']);

            $feeAmounts   = $studentFees->map(fn ($sf) => (float) ($sf->feeStructure?->basic_fee ?? 0));
            $nonZeroFees  = $feeAmounts->filter(fn ($v) => $v > 0);
            $zeroFeeRows  = $feeAmounts->count() - $nonZeroFees->count();

            $expected  = (float) $feeAmounts->sum();
            $feePer    = $pupils > 0 ? round($expected / $pupils, 2) : 0.0;
            $feeMin    = $nonZeroFees->count() ? (float) $nonZeroFees->min() : 0.0;
            $feeMax    = $nonZeroFees->count() ? (float) $nonZeroFees->max() : 0.0;

            $actual = $studentFees->isEmpty() ? 0.0 : (float) PaymentTransaction::query()
                ->whereIn('student_fee_id', $studentFees->pluck('id'))
                ->where('type', 'payment')
                ->where('status', 'completed')
                ->sum('amount');

            $shortfall     = max(0.0, $expected - $actual);
            $pctCollected  = $expected > 0 ? round(($actual / $expected) * 100, 2) : 0.0;
            $pctLoss       = $expected > 0 ? round(($shortfall / $expected) * 100, 2) : 0.0;

            $rows[$section] = [
                'pupils'         => $pupils,
                'fee_per'        => $feePer,
                'fee_min'        => $feeMin,
                'fee_max'        => $feeMax,
                'expected'       => round($expected, 2),
                'actual'         => round($actual, 2),
                'shortfall'      => round($shortfall, 2),
                'pct_collected'  => $pctCollected,
                'pct_loss'       => $pctLoss,
                'zero_fee_rows'  => $zeroFeeRows,   // surfaced as a warning badge in the UI
            ];
        }

        return $rows;
    }

    private function emptyRow(): array
    {
        return [
            'pupils' => 0, 'fee_per' => 0.0, 'fee_min' => 0.0, 'fee_max' => 0.0,
            'expected' => 0.0, 'actual' => 0.0, 'shortfall' => 0.0,
            'pct_collected' => 0.0, 'pct_loss' => 0.0, 'zero_fee_rows' => 0,
        ];
    }

    private function rollUp(array $sectionRows): array
    {
        $pupils    = array_sum(array_column($sectionRows, 'pupils'));
        $expected  = array_sum(array_column($sectionRows, 'expected'));
        $actual    = array_sum(array_column($sectionRows, 'actual'));
        $shortfall = max(0.0, $expected - $actual);
        return [
            'pupils'        => $pupils,
            'expected'      => round($expected, 2),
            'actual'        => round($actual, 2),
            'shortfall'     => round($shortfall, 2),
            'pct_collected' => $expected > 0 ? round(($actual / $expected) * 100, 2) : 0.0,
            'pct_loss'      => $expected > 0 ? round(($shortfall / $expected) * 100, 2) : 0.0,
        ];
    }

    private function annualRollUp(array $termTotals): array
    {
        $expected  = array_sum(array_column($termTotals, 'expected'));
        $actual    = array_sum(array_column($termTotals, 'actual'));
        $shortfall = max(0.0, $expected - $actual);
        return [
            'expected'      => round($expected, 2),
            'actual'        => round($actual, 2),
            'shortfall'     => round($shortfall, 2),
            'pct_collected' => $expected > 0 ? round(($actual / $expected) * 100, 2) : 0.0,
            'pct_loss'      => $expected > 0 ? round(($shortfall / $expected) * 100, 2) : 0.0,
        ];
    }

    /* --------------------------------------------------------------- */
    /* Salary bill per term                                              */
    /* --------------------------------------------------------------- */

    /**
     * Bucket payroll rows into a term by turning `month`+`year` into a
     * month-start date and testing whether it lies within the term window.
     * Rows whose month can't be parsed are counted separately so a silent
     * typo doesn't quietly drop money.
     */
    private function salaryBillFor(Term $term): array
    {
        if (! $term->start_date || ! $term->end_date) {
            return [null, null];
        }

        $rows = Payroll::query()
            ->select('month', 'year', DB::raw('SUM(net_salary) as total'))
            ->groupBy('month', 'year')
            ->get();

        $start = Carbon::parse($term->start_date);
        $end   = Carbon::parse($term->end_date);

        $matched   = [];
        $sum       = 0.0;
        $unparseable = [];

        foreach ($rows as $r) {
            try {
                $monthStart = Carbon::parse("1 {$r->month} {$r->year}");
            } catch (\Throwable $e) {
                $unparseable[] = "{$r->month} {$r->year}";
                continue;
            }
            if ($monthStart->betweenIncluded($start, $end->copy()->endOfDay())) {
                $sum += (float) $r->total;
                $matched[] = $monthStart->format('M Y');
            }
        }

        if ($sum <= 0 && ! $matched) {
            $meta = $unparseable
                ? 'No payroll matched. Unparseable months: ' . implode(', ', $unparseable)
                : 'No payroll recorded for this term window';
            return [null, $meta];
        }

        $meta = implode(', ', $matched);
        if ($unparseable) $meta .= ' — unparseable months skipped: ' . implode(', ', $unparseable);
        return [round($sum, 2), $meta];
    }

    /* --------------------------------------------------------------- */
    /* Population sheet                                                  */
    /* --------------------------------------------------------------- */

    private function buildPopulation(Collection $classSections): array
    {
        $out = [SectionResolver::ECE => [], SectionResolver::PRIMARY => [], SectionResolver::SECONDARY => []];

        foreach ($classSections as $cs) {
            $enrolment = $cs->students()->where('enrollment_status', 'active')->count();
            $out[$cs->__section][] = [
                'class'     => "{$cs->grade->name} - {$cs->name}",
                'teacher'   => $cs->classTeacher?->name ?? '—',
                'enrolment' => $enrolment,
            ];
        }
        return $out;
    }
}
