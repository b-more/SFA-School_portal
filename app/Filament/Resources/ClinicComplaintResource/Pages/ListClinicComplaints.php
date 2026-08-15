<?php

namespace App\Filament\Resources\ClinicComplaintResource\Pages;

use App\Filament\Resources\ClinicComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClinicComplaints extends ListRecords
{
    protected static string $resource = ClinicComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
