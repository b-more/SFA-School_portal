<?php

namespace App\Filament\Exports;

use App\Models\Teacher;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TeacherExporter extends Exporter
{
    protected static ?string $model = Teacher::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('employee_id')->label('Employee ID'),
            ExportColumn::make('phone'),
            ExportColumn::make('email'),
            ExportColumn::make('nrc')->label('NRC Number'),
            ExportColumn::make('qualification'),
            ExportColumn::make('specialization'),
            ExportColumn::make('join_date'),
            ExportColumn::make('address'),
            ExportColumn::make('is_active'),
            ExportColumn::make('grade_id')->label('Grade ID'),
            ExportColumn::make('class_section_id')->label('Class Section ID'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your teacher export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
