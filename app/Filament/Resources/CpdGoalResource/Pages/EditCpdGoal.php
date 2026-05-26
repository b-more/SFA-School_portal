<?php

namespace App\Filament\Resources\CpdGoalResource\Pages;

use App\Filament\Resources\CpdGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCpdGoal extends EditRecord
{
    protected static string $resource = CpdGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
