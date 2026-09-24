<?php

namespace App\Filament\Resources\StudentBusAssignmentResource\Pages;

use App\Filament\Resources\StudentBusAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentBusAssignments extends ManageRecords
{
    protected static string $resource = StudentBusAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
