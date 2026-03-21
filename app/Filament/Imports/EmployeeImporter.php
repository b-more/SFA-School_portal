<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('John Employee'),

            ImportColumn::make('employee_number')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('EMP001'),

            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255'])
                ->example('employee@example.com'),

            ImportColumn::make('phone')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('260972266217'),

            ImportColumn::make('department')
                ->rules(['nullable', 'max:255'])
                ->example('Administration'),

            ImportColumn::make('position')
                ->rules(['nullable', 'max:255'])
                ->example('Accountant'),

            ImportColumn::make('joining_date')
                ->rules(['nullable', 'date'])
                ->example('2020-01-15'),

            ImportColumn::make('status')
                ->rules(['nullable', 'in:active,inactive'])
                ->example('active'),

            ImportColumn::make('basic_salary')
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('5000'),
        ];
    }

    public function resolveRecord(): ?Employee
    {
        return Employee::firstOrNew([
            'employee_number' => $this->data['employee_number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your employee import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
