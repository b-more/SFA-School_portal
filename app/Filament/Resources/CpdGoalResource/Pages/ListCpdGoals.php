<?php

namespace App\Filament\Resources\CpdGoalResource\Pages;

use App\Filament\Resources\CpdGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCpdGoals extends ListRecords
{
    protected static string $resource = CpdGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
