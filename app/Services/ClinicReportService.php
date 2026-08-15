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
}
