<?php

namespace App\Filament\Resources\CpdActivityResource\Pages;

use App\Filament\Resources\CpdActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCpdActivities extends ListRecords
{
    protected static string $resource = CpdActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
