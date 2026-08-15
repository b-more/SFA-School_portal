<?php

namespace App\Filament\Resources\MedicalStockItemResource\Pages;

use App\Filament\Resources\MedicalStockItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedicalStockItem extends EditRecord
{
    protected static string $resource = MedicalStockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
