<?php

namespace App\Filament\Resources\GradeResource\Widgets;

use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GradeStatsWidget extends BaseWidget
{
    protected static bool $isLazy = true;

    public Model|int|string|null $record = null;

    protected function getStats(): array
    {
        $grade = $this->record;

        $totalStudents = $grade->students()->where('enrollment_status', 'active')->count();
        $maleCount = $grade->students()->where('enrollment_status', 'active')->where('gender', 'male')->count();
        $femaleCount = $grade->students()->where('enrollment_status', 'active')->where('gender', 'female')->count();

        $capacity = $grade->capacity;
        $utilization = $capacity > 0 ? round(($totalStudents / $capacity) * 100, 1) : 0;

        $utilizationColor = 'success';
        if ($utilization >= 90) {
            $utilizationColor = 'danger';
        } elseif ($utilization >= 70) {
            $utilizationColor = 'warning';
        }

        $activeSections = $grade->activeClassSections()->count();

        return [
            Stat::make('Total Students', $totalStudents)
                ->description("{$maleCount} male, {$femaleCount} female")
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Male Students', $maleCount)
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('Female Students', $femaleCount)
                ->descriptionIcon('heroicon-m-user')
                ->color('warning'),

            Stat::make('Capacity Utilization', "{$utilization}%")
                ->description("{$totalStudents} of {$capacity} capacity")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($utilizationColor),

            Stat::make('Active Class Sections', $activeSections)
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('success'),
        ];
    }
}
