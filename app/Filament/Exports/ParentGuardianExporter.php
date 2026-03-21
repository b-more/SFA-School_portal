<?php

namespace App\Filament\Exports;

use App\Models\ParentGuardian;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ParentGuardianExporter extends Exporter
{
    protected static ?string $model = ParentGuardian::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Full Name'),
            ExportColumn::make('email')
                ->label('Email Address'),
            ExportColumn::make('phone')
                ->label('Phone Number'),
            ExportColumn::make('alternate_phone')
                ->label('Alternate Phone'),
            ExportColumn::make('relationship')
                ->label('Relationship to Student'),
            ExportColumn::make('nrc')
                ->label('NRC Number'),
            ExportColumn::make('nationality')
                ->label('Nationality'),
            ExportColumn::make('occupation')
                ->label('Occupation'),
            ExportColumn::make('address')
                ->label('Physical Address'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your parent guardian export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
