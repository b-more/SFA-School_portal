<?php

namespace App\Filament\Resources\ClinicVisitResource\Widgets;

use App\Models\MedicalStockItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Loud low-stock strip at the top of the clinic visit list so the clinician
 * sees dwindling items every time they open the module.
 */
class LowStockWidget extends BaseWidget
{
    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int { return 3; }

    protected function getStats(): array
    {
        $items = MedicalStockItem::where('is_active', true)->orderBy('name')->get();

        // Force accessor eval once each — bulk load is fine at this scale.
        $low = $items->filter(fn ($i) => $i->is_low_stock)->values();

        $totalItems = $items->count();
        $inStock    = $items->count() - $low->count();

        $stats = [
            Stat::make('Stock items on hand', "{$inStock} / {$totalItems}")
                ->description('Above their re-order level')
                ->color('success'),

            Stat::make('Low stock', (string) $low->count())
                ->description($low->count() ? 'Order these soon' : 'All items OK for now')
                ->color($low->count() ? 'danger' : 'success')
                ->descriptionIcon('heroicon-o-exclamation-triangle'),
        ];

        // List up to 6 low items inline so the clinician can act without clicking through.
        if ($low->isNotEmpty()) {
            $summary = $low->take(6)
                ->map(fn ($i) => "{$i->name}: {$i->current_balance} {$i->unit}")
                ->implode(' · ');
            if ($low->count() > 6) $summary .= '  … +' . ($low->count() - 6) . ' more';

            $stats[] = Stat::make('Items to reorder', $low->count() . ' item(s)')
                ->description($summary)
                ->color('warning');
        }

        return $stats;
    }
}
