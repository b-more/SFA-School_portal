<?php

namespace App\Filament\Resources\CpdActivityResource\Pages;

use App\Filament\Resources\CpdActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCpdActivity extends EditRecord
{
    protected static string $resource = CpdActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
