<?php

namespace App\Filament\Resources\CpdObservationResource\Pages;

use App\Filament\Resources\CpdObservationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCpdObservation extends CreateRecord
{
    protected static string $resource = CpdObservationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
