<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Constants\RoleConstants;
use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\Widgets\StudentGradeSummary;
use App\Filament\Resources\StudentResource\Widgets\StudentStatsOverview;
use App\Filament\Imports\StudentImporter;
use App\Traits\HasPageGuide;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListStudents extends ListRecords
{
    use HasPageGuide;

    protected static string $resource = StudentResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StudentStatsOverview::class,
            StudentGradeSummary::class,
        ];
    }

    protected function getGuideSlug(): string
    {
        return 'students';
    }

    protected function getHeaderActions(): array
    {
        $isAdmin = auth()->user()?->role_id === RoleConstants::ADMIN;

        $actions = [
            $this->getPageGuideAction(),
        ];

        if ($isAdmin) {
            $actions[] = Actions\CreateAction::make();
            $actions[] = Actions\ImportAction::make()
                ->importer(StudentImporter::class)
                ->label('Import Students')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success');
            $actions[] = Actions\Action::make('downloadTemplate')
                ->label('Download CSV Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function (): StreamedResponse {
                    return response()->streamDownload(function () {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, [
                            'name', 'student_id_number', 'gender', 'date_of_birth',
                            'place_of_birth', 'address', 'grade_id', 'class_section_id',
                            'enrollment_term_id', 'standard_of_education', 'enrollment_status',
                            'admission_date', 'previous_school', 'religious_denomination',
                            'medical_information', 'notes', 'parent_name', 'parent_phone',
                            'parent_email', 'parent_nrc', 'parent_relationship',
                            'parent_occupation', 'parent_address',
                        ]);
                        fputcsv($handle, [
                            'John Banda', 'STU-2026-001', 'male', '20/05/2015',
                            'Lusaka', '123 Main Street, Lusaka', 'Grade 2', 'Grade 2 A',
                            'Term 1', 'Primary', 'active', '15/01/2026', 'ABC Primary',
                            'Catholic', 'None', '', 'James Banda', '0977123456',
                            'james.banda@email.com', '123456/78/1', 'father', 'Teacher',
                            '123 Main Street, Lusaka',
                        ]);
                        fclose($handle);
                    }, 'student_import_template.csv', [
                        'Content-Type' => 'text/csv',
                    ]);
                });
        }

        return $actions;
    }
}
