<?php

namespace App\Filament\Resources\CpdActivityResource\Pages;

use App\Filament\Resources\CpdActivityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCpdActivity extends CreateRecord
{
    protected static string $resource = CpdActivityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
