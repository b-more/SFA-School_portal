<?php

namespace App\Filament\Resources\CpdObservationResource\Pages;

use App\Filament\Resources\CpdObservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCpdObservation extends EditRecord
{
    protected static string $resource = CpdObservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
