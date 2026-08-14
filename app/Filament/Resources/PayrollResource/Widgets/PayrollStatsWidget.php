<?php

namespace App\Filament\Resources\PayrollResource\Widgets;

use App\Models\Payroll;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Month-scoped payroll dashboard: gross bill, NAPSA employee + employer
 * match, other deductions, net pay bill, and paid/pending counts.
 *
 * Scope: the most recent (year, month) with any payroll rows. That gives
 * a useful default the moment the accountant opens /admin/payrolls, without
 * needing them to pass a filter first.
 */
class PayrollStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = true;

    // Wide stats fit better in the two-column layout on the payroll list.
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        // Prefer the month/year the accountant selected on the table filters.
        // The parent ListPayrolls page pushes those in via getHeaderWidgetsData(),
        // and the InteractsWithPageFilters trait makes them #[Reactive].
        $month = $this->filters['month'] ?? null;
        $year  = $this->filters['year']  ?? null;

        // If either isn't set, fall back to the most-recent month with rows.
        if (blank($month) || blank($year)) {
            $latest = Payroll::select('month', 'year')
                ->orderByRaw("year DESC, STR_TO_DATE(CONCAT('1 ', month, ' ', year), '%d %M %Y') DESC")
                ->first();
            if (! $latest) {
                return [Stat::make('No payroll rows yet', 'Click Generate Bulk Payroll to start')->color('warning')];
            }
            $month = $latest->month;
            $year  = (int) $latest->year;
        }

        $rows  = Payroll::where('month', $month)->where('year', $year)->get();
        $label = "{$month} {$year}";

        if ($rows->isEmpty()) {
            return [Stat::make("No rows for {$label}", 'Change the Month/Year filter or generate this month first')->color('gray')];
        }

        $gross = (float) $rows->sum('gross_salary');
        $net   = (float) $rows->sum('net_salary');

        $napsaEmployee = 0.0;
        $paye          = 0.0;
        $otherDed      = 0.0;
        foreach ($rows as $p) {
            foreach ($p->deductions ?? [] as $d) {
                $type = strtolower(trim($d['type'] ?? ''));
                $amt  = (float) ($d['amount'] ?? 0);
                if ($type === 'napsa')      $napsaEmployee += $amt;
                elseif ($type === 'paye')   $paye          += $amt;
                else                        $otherDed      += $amt;
            }
        }
        $napsaTotal = $napsaEmployee * 2; // 5% employee + 5% employer match

        $paid    = $rows->where('payment_status', 'paid')->count();
        $pending = $rows->where('payment_status', 'pending')->count();

        $fmt = fn (float $n) => 'K' . number_format($n, 2);

        return [
            Stat::make("Gross Bill · {$label}", $fmt($gross))
                ->description("Basic + allowances across {$rows->count()} employee(s)")
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('NAPSA — Employee (5%)', $fmt($napsaEmployee))
                ->description('Deducted from staff pay')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('warning'),

            Stat::make('NAPSA — Total to remit (10%)', $fmt($napsaTotal))
                ->description("School's 5% + Employee 5% owed to NAPSA")
                ->descriptionIcon('heroicon-o-building-library')
                ->color('warning'),

            Stat::make('Net Pay Bill', $fmt($net))
                ->description(($paid + $pending) . " rows · Paid {$paid} · Pending {$pending}")
                ->descriptionIcon('heroicon-o-arrow-down-on-square')
                ->color('success'),

            Stat::make('PAYE (income tax)', $fmt($paye))
                ->description($paye > 0 ? 'Owed to ZRA' : 'Not applied to any row this month')
                ->color($paye > 0 ? 'danger' : 'gray'),

            Stat::make('Other Deductions', $fmt($otherDed))
                ->description('Advances, School Fees, Uniform, etc.')
                ->color('gray'),

            Stat::make('Employees on Payroll', (string) $rows->count())
                ->description("Records generated for {$label}")
                ->color('primary'),

            Stat::make('Net vs Gross', $gross > 0 ? number_format(($net / $gross) * 100, 1) . '%' : '—')
                ->description('Take-home ratio')
                ->color('primary'),
        ];
    }
}
