<?php

namespace App\Filament\Resources\CpdResourceResource\Pages;

use App\Filament\Resources\CpdResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCpdResource extends EditRecord
{
    protected static string $resource = CpdResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
