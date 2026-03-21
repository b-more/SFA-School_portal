<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Filament\Resources\GradeResource\Widgets\GradeStatsWidget;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewGrade extends ViewRecord
{
    protected static string $resource = GradeResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Grade Information')
                ->schema([
                    TextEntry::make('schoolSection.name')
                        ->label('School Section')
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('name')
                        ->label('Grade Name'),

                    TextEntry::make('code')
                        ->label('Code')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('level')
                        ->label('Level'),

                    TextEntry::make('capacity')
                        ->label('Capacity'),

                    TextEntry::make('breakeven_number')
                        ->label('Breakeven Number'),
                ])
                ->columns(3)
                ->compact(),
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GradeStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadStudentList')
                ->label('Download Student List')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $grade = $this->record;
                    $students = $grade->students()
                        ->with(['grade', 'classSection.classTeacher', 'parentGuardian'])
                        ->where('enrollment_status', 'active')
                        ->orderByRaw('LOWER(TRIM(name)) ASC')
                        ->get();

                    if ($students->isEmpty()) {
                        Notification::make()
                            ->title('No Students')
                            ->body('There are no active students in this grade.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $pdf = Pdf::loadView('pdf.students-list', [
                        'students' => $students,
                        'schoolName' => 'St. Francis Of Assisi Private School',
                        'reportDate' => now()->format('F d, Y'),
                        'reportType' => $grade->name . ' Students',
                    ]);

                    $filename = 'students-' . $grade->code . '-' . now()->format('Y-m-d') . '.pdf';

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),
            Actions\EditAction::make(),
        ];
    }
}
