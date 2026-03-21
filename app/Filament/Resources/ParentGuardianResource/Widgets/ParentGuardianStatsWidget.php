<?php

namespace App\Filament\Resources\ParentGuardianResource\Widgets;

use App\Models\ParentGuardian;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ParentGuardianStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = ParentGuardian::count();
        $withChildren = ParentGuardian::has('students')->count();
        $withoutChildren = $total - $withChildren;
        $withPortalAccess = ParentGuardian::whereNotNull('user_id')->count();

        $fathers = ParentGuardian::where('relationship', 'father')->count();
        $mothers = ParentGuardian::where('relationship', 'mother')->count();
        $guardians = ParentGuardian::where('relationship', 'guardian')->count();

        return [
            Stat::make('Total Parents/Guardians', number_format($total))
                ->description("{$withChildren} with children, {$withoutChildren} without")
                ->icon('heroicon-o-heart')
                ->color('primary'),

            Stat::make('Fathers', number_format($fathers))
                ->description(($total > 0 ? round($fathers / $total * 100) : 0) . '% of total')
                ->icon('heroicon-o-user')
                ->color('info'),

            Stat::make('Mothers', number_format($mothers))
                ->description(($total > 0 ? round($mothers / $total * 100) : 0) . '% of total')
                ->icon('heroicon-o-user')
                ->color('success'),

            Stat::make('Guardians', number_format($guardians))
                ->description(($total > 0 ? round($guardians / $total * 100) : 0) . '% of total')
                ->icon('heroicon-o-users')
                ->color('warning'),

            Stat::make('Portal Access', number_format($withPortalAccess))
                ->description(($total > 0 ? round($withPortalAccess / $total * 100) : 0) . '% have login accounts')
                ->icon('heroicon-o-lock-open')
                ->color('gray'),
        ];
    }
}
