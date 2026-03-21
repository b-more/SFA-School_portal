<?php

namespace App\Filament\Resources\BusPaymentResource\Widgets;

use App\Models\BusPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusPaymentStatsWidget extends BaseWidget
{
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $currentYear = now()->year;
        $currentMonth = now()->format('F');

        $monthQuery = BusPayment::where('year', $currentYear)->where('month', $currentMonth);

        $totalPayments = (clone $monthQuery)->count();

        $paidCount = (clone $monthQuery)->where('payment_status', 'paid')->count();

        $partialCount = (clone $monthQuery)->where('payment_status', 'partial')->count();

        $unpaidCount = (clone $monthQuery)->where('payment_status', 'unpaid')->count();

        $totalCollected = (clone $monthQuery)->sum('amount_paid');

        $totalOutstanding = (clone $monthQuery)->sum('balance');

        return [
            Stat::make('Total Bus Payments', $totalPayments)
                ->description("{$currentMonth} {$currentYear}")
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Fully Paid', $paidCount)
                ->description('Students with full payment')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Partially Paid', $partialCount)
                ->description('Students with partial payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Unpaid', $unpaidCount)
                ->description('Students with no payment')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Total Collected', 'ZMW ' . number_format($totalCollected, 2))
                ->description("{$currentMonth} bus fare collected")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Outstanding', 'ZMW ' . number_format($totalOutstanding, 2))
                ->description("{$currentMonth} balance pending")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalOutstanding > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
}
