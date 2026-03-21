<?php

namespace App\Filament\Imports;

use App\Models\ParentGuardian;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ParentGuardianImporter extends Importer
{
    protected static ?string $model = ParentGuardian::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Full Name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('John Doe'),

            ImportColumn::make('email')
                ->label('Email Address')
                ->rules(['nullable', 'email', 'max:255'])
                ->example('john.doe@example.com'),

            ImportColumn::make('phone')
                ->label('Phone Number')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('260972266217'),

            ImportColumn::make('alternate_phone')
                ->label('Alternate Phone')
                ->rules(['nullable', 'max:255'])
                ->example('260972266218'),

            ImportColumn::make('relationship')
                ->label('Relationship to Student')
                ->requiredMapping()
                ->rules(['required', 'in:father,mother,guardian,other'])
                ->example('father'),

            ImportColumn::make('nrc')
                ->label('NRC Number')
                ->rules(['nullable', 'max:255'])
                ->example('123456/78/9'),

            ImportColumn::make('nationality')
                ->label('Nationality')
                ->rules(['nullable', 'max:255'])
                ->example('Zambian'),

            ImportColumn::make('occupation')
                ->label('Occupation')
                ->rules(['nullable', 'max:255'])
                ->example('Teacher'),

            ImportColumn::make('address')
                ->label('Physical Address')
                ->requiredMapping()
                ->rules(['required'])
                ->example('123 Main Street, Lusaka'),
        ];
    }

    public function resolveRecord(): ?ParentGuardian
    {
        // Try to find existing record by phone number (primary identifier)
        return ParentGuardian::firstOrNew([
            'phone' => $this->data['phone'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your parent guardian import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
