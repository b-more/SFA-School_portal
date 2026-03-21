<?php

namespace App\Filament\Imports;

use App\Models\Teacher;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TeacherImporter extends Importer
{
    protected static ?string $model = Teacher::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Jane Doe'),

            ImportColumn::make('employee_id')
                ->label('Employee ID')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('TEACH001'),

            ImportColumn::make('phone')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('260972266217'),

            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255'])
                ->example('jane.doe@example.com'),

            ImportColumn::make('nrc')
                ->label('NRC Number')
                ->rules(['nullable', 'max:255'])
                ->example('123456/78/9'),

            ImportColumn::make('qualification')
                ->rules(['nullable', 'max:255'])
                ->example('Bachelor of Education'),

            ImportColumn::make('specialization')
                ->rules(['nullable', 'max:255'])
                ->example('Mathematics'),

            ImportColumn::make('join_date')
                ->rules(['nullable', 'date'])
                ->example('2020-01-15'),

            ImportColumn::make('address')
                ->rules(['nullable'])
                ->example('123 Main Street, Lusaka'),

            ImportColumn::make('is_active')
                ->rules(['nullable', 'boolean'])
                ->example('1'),

            ImportColumn::make('grade_id')
                ->label('Grade ID')
                ->rules(['nullable', 'integer', 'exists:grades,id'])
                ->example('1'),

            ImportColumn::make('class_section_id')
                ->label('Class Section ID')
                ->rules(['nullable', 'integer', 'exists:class_sections,id'])
                ->example('1'),
        ];
    }

    public function resolveRecord(): ?Teacher
    {
        return Teacher::firstOrNew([
            'employee_id' => $this->data['employee_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your teacher import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
