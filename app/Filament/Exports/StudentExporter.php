<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('student_id_number')->label('Student ID'),
            ExportColumn::make('grade_id')->label('Grade ID'),
            ExportColumn::make('class_section_id')->label('Class Section ID'),
            ExportColumn::make('parent_guardian_id')->label('Parent Guardian ID'),
            ExportColumn::make('date_of_birth'),
            ExportColumn::make('gender'),
            ExportColumn::make('address'),
            ExportColumn::make('admission_date'),
            ExportColumn::make('enrollment_status'),
            ExportColumn::make('place_of_birth'),
            ExportColumn::make('religious_denomination'),
            ExportColumn::make('previous_school'),
            ExportColumn::make('medical_information'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
