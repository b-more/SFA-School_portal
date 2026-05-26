<?php

namespace App\Filament\Resources\CpdGoalResource\Pages;

use App\Filament\Resources\CpdGoalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCpdGoal extends CreateRecord
{
    protected static string $resource = CpdGoalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
