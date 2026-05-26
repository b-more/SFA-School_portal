<?php

namespace App\Filament\Resources\CpdObservationResource\Pages;

use App\Filament\Resources\CpdObservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCpdObservations extends ListRecords
{
    protected static string $resource = CpdObservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
