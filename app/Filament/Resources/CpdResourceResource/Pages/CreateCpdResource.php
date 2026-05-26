<?php

namespace App\Filament\Resources\CpdResourceResource\Pages;

use App\Filament\Resources\CpdResourceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCpdResource extends CreateRecord
{
    protected static string $resource = CpdResourceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
