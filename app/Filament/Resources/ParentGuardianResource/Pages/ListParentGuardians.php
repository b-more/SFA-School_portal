<?php

namespace App\Filament\Resources\ParentGuardianResource\Pages;

use App\Filament\Resources\ParentGuardianResource;
use App\Filament\Resources\ParentGuardianResource\Widgets\ParentGuardianStatsWidget;
use App\Models\ParentGuardian;
use App\Models\SchoolSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParentGuardians extends ListRecords
{
    protected static string $resource = ParentGuardianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $parents = ParentGuardian::with('students')
                        ->orderBy('name')
                        ->get();

                    $settings = SchoolSettings::first();

                    $pdf = Pdf::loadView('pdf.parents-list', [
                        'parents' => $parents,
                        'schoolName' => $settings->school_name ?? 'St. Francis of Assisi Private School',
                        'reportDate' => now()->format('F d, Y'),
                    ])->setPaper('A4', 'landscape');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'parents-guardians-list-' . now()->format('Y-m-d') . '.pdf'
                    );
                }),

            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ParentGuardianStatsWidget::class,
        ];
    }
}
