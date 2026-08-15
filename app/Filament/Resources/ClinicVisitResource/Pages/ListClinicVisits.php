<?php

namespace App\Filament\Resources\ClinicVisitResource\Pages;

use App\Filament\Resources\ClinicVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClinicVisits extends ListRecords
{
    protected static string $resource = ClinicVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Record a visit')->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\ClinicVisitResource\Widgets\LowStockWidget::class,
        ];
    }
}
