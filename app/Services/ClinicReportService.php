<?php

namespace App\Services;

use App\Models\ClinicVisit;
use App\Models\MedicalStockItem;
use App\Models\StockTransaction;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClinicReportService
{
    /**
     * Resolve a period spec into concrete [from, to] Carbon dates.
     *
     * $spec is one of:
     *   ['weekly',   'anchor' => Carbon]   → Mon–Sun of that ISO week
     *   ['monthly',  'anchor' => Carbon]   → 1st … last day of that month
     *   ['termly',   'term_id' => int]     → term.start_date … term.end_date
     *   ['custom',   'from' => ..., 'to' => ...]
     */
    public function resolvePeriod(array $spec): array
    {
        $kind = $spec['kind'] ?? 'weekly';
        return match ($kind) {
            'weekly'  => [
                Carbon::parse($spec['anchor'] ?? now())->startOfWeek(),
                Carbon::parse($spec['anchor'] ?? now())->endOfWeek()->endOfDay(),
            ],
            'monthly' => [
                Carbon::parse($spec['anchor'] ?? now())->startOfMonth(),
                Carbon::parse($spec['anchor'] ?? now())->endOfMonth()->endOfDay(),
            ],
            'termly'  => (function () use ($spec) {
                $term = Term::find($spec['term_id'] ?? 0);
                if (! $term) return [now()->startOfMonth(), now()->endOfMonth()->endOfDay()];
                return [Carbon::parse($term->start_date), Carbon::parse($term->end_date)->endOfDay()];
            })(),
            'custom'  => [
                Carbon::parse($spec['from'] ?? now()->subMonth()),
                Carbon::parse($spec['to']   ?? now())->endOfDay(),
            ],
            default   => [now()->startOfMonth(), now()->endOfMonth()->endOfDay()],
        };
    }

    /* --------------------------------------------------------------- */
    /* Visit metrics                                                     */
    /* --------------------------------------------------------------- */

    public function visitStats(Carbon $from, Carbon $to, ?int $gradeLevel = null): array
    {
        $base = ClinicVisit::whereBetween('visit_date', [$from->toDateString(), $to->toDateString()])
            ->when($gradeLevel, fn ($q) => $q->where('grade_level', $gradeLevel));

        $rows = (clone $base)->with('complaints')->get();

        $byComplaint = collect();
        foreach ($rows as $v) {
            foreach ($v->complaints as $c) {
                $byComplaint[$c->name] = ($byComplaint[$c->name] ?? 0) + 1;
            }
        }

        $byGrade = (clone $base)
            ->selectRaw('COALESCE(grade_level, 0) as gl, COUNT(*) as c')
            ->groupBy('gl')->orderBy('gl')->get()
            ->mapWithKeys(fn ($r) => [(int) $r->gl ?: 'Unknown' => (int) $r->c]);

        $byDay = (clone $base)
            ->selectRaw('DATE(visit_date) as d, COUNT(*) as c')
            ->groupBy('d')->orderBy('d')->get()
            ->mapWithKeys(fn ($r) => [(string) $r->d => (int) $r->c]);

        $byOutcome = (clone $base)
            ->selectRaw('COALESCE(outcome, "unspecified") as o, COUNT(*) as c')
            ->groupBy('o')->get()
            ->mapWithKeys(fn ($r) => [(string) $r->o => (int) $r->c]);

        $repeats = (clone $base)
            ->selectRaw('student_name, COUNT(*) as c')
            ->groupBy('student_name')
            ->having('c', '>=', 3)
            ->orderByDesc('c')
            ->get();

        $sickNotes = (clone $base)->where('sick_note_issued', true)->count();

        return [
            'total_visits'      => $rows->count(),
            'unique_students'   => $rows->pluck('student_name')->unique()->count(),
            'by_complaint'      => $byComplaint->sortDesc(),
            'by_grade'          => $byGrade,
            'by_day'            => $byDay,
            'by_outcome'        => $byOutcome,
            'sick_notes'        => $sickNotes,
            'sick_notes_pct'    => $rows->count() ? round(($sickNotes / $rows->count()) * 100, 1) : 0,
            'repeat_visitors'   => $repeats,
        ];
    }

    /* --------------------------------------------------------------- */
    /* Stock metrics                                                     */
    /* --------------------------------------------------------------- */

    public function stockStats(Carbon $from, Carbon $to): array
    {
        $usageByItem = StockTransaction::query()
            ->where('transaction_type', 'usage')
            ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
            ->join('medical_stock_items', 'stock_transactions.medical_stock_item_id', '=', 'medical_stock_items.id')
            ->selectRaw('medical_stock_items.name, medical_stock_items.unit, SUM(stock_transactions.quantity) as qty')
            ->groupBy('medical_stock_items.id', 'medical_stock_items.name', 'medical_stock_items.unit')
            ->orderByDesc('qty')
            ->get();

        $purchasesByItem = StockTransaction::query()
            ->whereIn('transaction_type', ['purchase', 'opening'])
            ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
            ->join('medical_stock_items', 'stock_transactions.medical_stock_item_id', '=', 'medical_stock_items.id')
            ->selectRaw('medical_stock_items.name, medical_stock_items.unit,
                         SUM(stock_transactions.quantity) as qty,
                         SUM(stock_transactions.quantity * COALESCE(stock_transactions.unit_cost, 0)) as spend')
            ->groupBy('medical_stock_items.id', 'medical_stock_items.name', 'medical_stock_items.unit')
            ->orderByDesc('spend')
            ->get();

        $totalSpend = (float) $purchasesByItem->sum('spend');

        // Opening balance = sum of all IN before $from - sum of all OUT before $from.
        // Closing balance = current_balance (all-time).
        $movement = MedicalStockItem::orderBy('name')->get()->map(function (MedicalStockItem $item) use ($from, $to) {
            $openIn  = StockTransaction::where('medical_stock_item_id', $item->id)
                ->whereIn('transaction_type', ['purchase', 'opening'])
                ->where('transaction_date', '<', $from->toDateString())
                ->sum('quantity');
            $openOut = StockTransaction::where('medical_stock_item_id', $item->id)
                ->whereIn('transaction_type', ['usage', 'expired_damaged'])
                ->where('transaction_date', '<', $from->toDateString())
                ->sum('quantity');
            $openAdj = StockTransaction::where('medical_stock_item_id', $item->id)
                ->where('transaction_type', 'adjustment')
                ->where('transaction_date', '<', $from->toDateString())
                ->sum('quantity');

            $inPeriod  = StockTransaction::where('medical_stock_item_id', $item->id)
                ->whereIn('transaction_type', ['purchase', 'opening'])
                ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
                ->sum('quantity');
            $outPeriod = StockTransaction::where('medical_stock_item_id', $item->id)
                ->whereIn('transaction_type', ['usage', 'expired_damaged'])
                ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
                ->sum('quantity');
            $adjPeriod = StockTransaction::where('medical_stock_item_id', $item->id)
                ->where('transaction_type', 'adjustment')
                ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
                ->sum('quantity');

            $opening = (int) ($openIn - $openOut + $openAdj);
            $closing = (int) ($opening + $inPeriod - $outPeriod + $adjPeriod);

            return [
                'item'    => $item->name,
                'unit'    => $item->unit,
                'opening' => $opening,
                'in'      => (int) $inPeriod,
                'out'     => (int) $outPeriod,
                'adjust'  => (int) $adjPeriod,
                'closing' => $closing,
                'low'     => $closing <= $item->reorder_level,
            ];
        });

        return [
            'usage_by_item'      => $usageByItem,
            'purchases_by_item'  => $purchasesByItem,
            'total_spend'        => $totalSpend,
            'movement'           => $movement,
            'low_stock'          => $movement->filter(fn ($m) => $m['low'])->values(),
        ];
    }

    public function termlyPayload(int $termId): array
    {
        $term = Term::findOrFail($termId);
        [$from, $to] = $this->resolvePeriod(['kind' => 'termly', 'term_id' => $termId]);

        return [
            'term'         => $term,
            'from'         => $from,
            'to'           => $to,
            'visits'       => $this->visitStats($from, $to),
            'stock'        => $this->stockStats($from, $to),
        ];
    }

    /* --------------------------------------------------------------- */
    /* C1 · per-pupil medical history                                    */
    /* --------------------------------------------------------------- */

    public function perPupilHistory(int $studentId): array
    {
        $student = \App\Models\Student::with(['classSection.grade', 'parentGuardian'])->findOrFail($studentId);

        $visits = ClinicVisit::with(['complaints', 'stockTransactions.item'])
            ->where('student_id', $studentId)
            ->orderBy('visit_date')
            ->orderBy('id')
            ->get();

        return [
            'student'    => $student,
            'visits'     => $visits,
            'first_seen' => $visits->min('visit_date'),
            'last_seen'  => $visits->max('visit_date'),
            'total'      => $visits->count(),
            'sick_notes' => $visits->where('sick_note_issued', true)->count(),
            'sent_home'  => $visits->where('outcome', 'sent_home')->count(),
            'referred'   => $visits->where('outcome', 'referred')->count(),
        ];
    }

    /* --------------------------------------------------------------- */
    /* C2 · complaint trend over N months                                 */
    /* --------------------------------------------------------------- */

    public function complaintTrend(?int $complaintId, int $months = 12): array
    {
        $to   = now()->endOfMonth();
        $from = now()->subMonths($months - 1)->startOfMonth();

        $q = ClinicVisit::query()
            ->whereBetween('visit_date', [$from, $to])
            ->join('clinic_visit_clinic_complaint as p', 'p.clinic_visit_id', '=', 'clinic_visits.id');

        if ($complaintId) $q->where('p.clinic_complaint_id', $complaintId);

        $rows = $q->selectRaw("DATE_FORMAT(visit_date, '%Y-%m') as ym, COUNT(DISTINCT clinic_visits.id) as c")
            ->groupBy('ym')->orderBy('ym')->get()
            ->mapWithKeys(fn ($r) => [(string) $r->ym => (int) $r->c]);

        // Fill missing months with 0
        $series = collect();
        foreach (\Carbon\CarbonPeriod::create($from, '1 month', $to) as $d) {
            $key = $d->format('Y-m');
            $series[$key] = $rows[$key] ?? 0;
        }

        $complaint = $complaintId ? \App\Models\ClinicComplaint::find($complaintId) : null;

        return [
            'from'      => $from, 'to' => $to,
            'complaint' => $complaint,
            'series'    => $series,
            'peak'      => max(1, $series->max() ?: 0),
            'total'     => $series->sum(),
        ];
    }

    /* --------------------------------------------------------------- */
    /* G1 · sick-notes register                                           */
    /* --------------------------------------------------------------- */

    public function sickNotesRegister(Carbon $from, Carbon $to): array
    {
        $rows = ClinicVisit::with('complaints')
            ->where('sick_note_issued', true)
            ->whereBetween('visit_date', [$from, $to])
            ->orderBy('visit_date')
            ->orderBy('id')
            ->get();

        return [
            'from' => $from, 'to' => $to,
            'rows' => $rows,
            'count' => $rows->count(),
        ];
    }

    /* --------------------------------------------------------------- */
    /* G4 · attendance-loss impact                                       */
    /* --------------------------------------------------------------- */

    /**
     * For every visit where the outcome was sent_home or referred, count how
     * many attendance rows for that pupil in the following school days
     * (up to a window) show absent/sick/excused.
     */
    public function attendanceLossImpact(Carbon $from, Carbon $to, int $windowDays = 3): array
    {
        $visits = ClinicVisit::whereIn('outcome', ['sent_home', 'referred'])
            ->whereBetween('visit_date', [$from, $to])
            ->whereNotNull('student_id')
            ->orderBy('visit_date')
            ->get();

        $rows = $visits->map(function ($v) use ($windowDays) {
            $windowFrom = \Carbon\Carbon::parse($v->visit_date);
            $windowTo   = $windowFrom->copy()->addDays($windowDays);

            $absent = \DB::table('attendances')
                ->where('student_id', $v->student_id)
                ->whereBetween('attendance_date', [$windowFrom->toDateString(), $windowTo->toDateString()])
                ->whereIn('status', ['absent', 'sick', 'excused'])
                ->count();

            return [
                'visit_id'      => $v->id,
                'date'          => $v->visit_date,
                'student_name'  => $v->student_name,
                'grade'         => $v->grade,
                'outcome'       => $v->outcome,
                'days_missed'   => $absent,
            ];
        });

        return [
            'from' => $from, 'to' => $to,
            'rows'         => $rows,
            'total_visits' => $rows->count(),
            'total_days'   => $rows->sum('days_missed'),
        ];
    }

    /* --------------------------------------------------------------- */
    /* O1 · cost per visit + cost per pupil                              */
    /* --------------------------------------------------------------- */

    public function costMetrics(Carbon $from, Carbon $to): array
    {
        $spend = (float) StockTransaction::query()
            ->whereIn('transaction_type', ['purchase', 'opening'])
            ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('SUM(quantity * COALESCE(unit_cost, 0)) as total')
            ->value('total');

        $visits = ClinicVisit::whereBetween('visit_date', [$from, $to])->count();
        $enrolment = \App\Models\Student::where('enrollment_status', 'active')->count();

        // Per-section breakdown
        $bySection = collect(\App\Support\SectionResolver::ALL)->mapWithKeys(function ($section) use ($from, $to) {
            $sectionClasses = \App\Models\ClassSection::with('grade')->get()
                ->filter(fn ($cs) => $cs->grade && \App\Support\SectionResolver::sectionFor($cs->grade) === $section);
            $pupils = \App\Models\Student::whereIn('class_section_id', $sectionClasses->pluck('id'))
                ->where('enrollment_status', 'active')->count();
            $sectionVisits = ClinicVisit::whereBetween('visit_date', [$from, $to])
                ->whereIn('student_id', \App\Models\Student::whereIn('class_section_id', $sectionClasses->pluck('id'))->pluck('id'))
                ->count();
            return [$section => ['pupils' => $pupils, 'visits' => $sectionVisits]];
        });

        return [
            'from' => $from, 'to' => $to,
            'total_spend'    => $spend,
            'total_visits'   => $visits,
            'enrolment'      => $enrolment,
            'cost_per_visit' => $visits > 0     ? round($spend / $visits, 2)     : 0,
            'cost_per_pupil' => $enrolment > 0  ? round($spend / $enrolment, 2)  : 0,
            'by_section'     => $bySection,
        ];
    }

    /* --------------------------------------------------------------- */
    /* O2 · days-of-supply / burn rate                                    */
    /* --------------------------------------------------------------- */

    /**
     * For each item: usage over the last $windowDays days → daily rate →
     * days_of_supply = current_balance / daily_rate. Below the reorder level
     * flags as urgent.
     */
    public function burnRate(int $windowDays = 30): array
    {
        $from = now()->subDays($windowDays);
        $items = \App\Models\MedicalStockItem::where('is_active', true)->orderBy('name')->get();

        $rows = $items->map(function ($item) use ($from, $windowDays) {
            $used = (int) StockTransaction::where('medical_stock_item_id', $item->id)
                ->where('transaction_type', 'usage')
                ->where('transaction_date', '>=', $from->toDateString())
                ->sum('quantity');

            $daily   = $windowDays > 0 ? round($used / $windowDays, 2) : 0;
            $balance = $item->current_balance;
            $days    = $daily > 0 ? (int) floor($balance / $daily) : null;

            return [
                'item'     => $item->name,
                'unit'     => $item->unit,
                'balance'  => $balance,
                'used'     => $used,
                'daily'    => $daily,
                'days_of_supply' => $days,
                'urgency'  => match (true) {
                    $balance <= 0                                          => 'stockout',
                    $daily > 0 && $days !== null && $days < 7              => 'critical',
                    $balance <= $item->reorder_level                       => 'low',
                    $daily > 0 && $days !== null && $days < 21             => 'watch',
                    default                                                => 'ok',
                },
            ];
        })->sortBy(function ($r) {
            return match ($r['urgency']) {
                'stockout' => 0, 'critical' => 1, 'low' => 2, 'watch' => 3, 'ok' => 4,
            };
        })->values();

        return [
            'window_days' => $windowDays,
            'rows'        => $rows,
        ];
    }

    /* --------------------------------------------------------------- */
    /* L1 · class health snapshot                                         */
    /* --------------------------------------------------------------- */

    public function classSnapshot(int $classSectionId, Carbon $from, Carbon $to): array
    {
        $cs = \App\Models\ClassSection::with('grade', 'classTeacher')->findOrFail($classSectionId);

        $studentIds = \App\Models\Student::where('class_section_id', $classSectionId)
            ->where('enrollment_status', 'active')
            ->pluck('id');

        $visits = ClinicVisit::with('complaints')
            ->whereIn('student_id', $studentIds)
            ->whereBetween('visit_date', [$from, $to])
            ->get();

        $byComplaint = collect();
        foreach ($visits as $v) {
            foreach ($v->complaints as $c) {
                $byComplaint[$c->name] = ($byComplaint[$c->name] ?? 0) + 1;
            }
        }

        $repeats = $visits->groupBy('student_name')
            ->filter(fn ($g) => $g->count() >= 2)
            ->map(fn ($g) => $g->count())
            ->sortDesc();

        return [
            'class'             => $cs,
            'from'              => $from,
            'to'                => $to,
            'enrolment'         => $studentIds->count(),
            'total_visits'      => $visits->count(),
            'unique_visitors'   => $visits->pluck('student_name')->unique()->count(),
            'sick_notes'        => $visits->where('sick_note_issued', true)->count(),
            'sent_home'         => $visits->where('outcome', 'sent_home')->count(),
            'referred'          => $visits->where('outcome', 'referred')->count(),
            'top_complaints'    => $byComplaint->sortDesc()->take(6),
            'repeat_visitors'   => $repeats,
        ];
    }
}
