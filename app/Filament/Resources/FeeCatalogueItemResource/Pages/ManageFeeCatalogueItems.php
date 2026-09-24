<?php

namespace App\Filament\Resources\FeeCatalogueItemResource\Pages;

use App\Filament\Resources\FeeCatalogueItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFeeCatalogueItems extends ManageRecords
{
    protected static string $resource = FeeCatalogueItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
