<?php

namespace App\Filament\Resources\BusPaymentResource\Pages;

use App\Filament\Resources\BusPaymentResource;
use App\Filament\Resources\BusPaymentResource\Widgets\BusPaymentStatsWidget;
use App\Filament\Widgets\ValidBusPassesWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusPayments extends ListRecords
{
    protected static string $resource = BusPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BusPaymentStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ValidBusPassesWidget::class,
        ];
    }
}
