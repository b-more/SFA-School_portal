<?php

namespace App\Filament\Resources\CpdResourceResource\Pages;

use App\Filament\Resources\CpdResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCpdResources extends ListRecords
{
    protected static string $resource = CpdResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
