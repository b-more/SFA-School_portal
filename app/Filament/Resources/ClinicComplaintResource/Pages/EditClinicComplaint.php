<?php

namespace App\Filament\Resources\ClinicComplaintResource\Pages;

use App\Filament\Resources\ClinicComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClinicComplaint extends EditRecord
{
    protected static string $resource = ClinicComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
