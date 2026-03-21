<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $teachers = Teacher::with(['classSection.grade', 'grade'])
                        ->orderBy('name')
                        ->get();

                    $pdf = Pdf::loadView('pdf.teachers-list', [
                        'teachers' => $teachers,
                        'title' => 'TEACHERS LIST',
                        'date' => now()->format('F d, Y \a\t h:i A'),
                    ])->setPaper('A4', 'landscape');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'teachers-list-' . now()->format('Y-m-d') . '.pdf'
                    );
                }),
            Actions\CreateAction::make(),
        ];
    }
}
