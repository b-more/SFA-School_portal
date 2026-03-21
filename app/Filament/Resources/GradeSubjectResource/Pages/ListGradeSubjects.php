<?php

namespace App\Filament\Resources\GradeSubjectResource\Pages;

use App\Filament\Resources\GradeSubjectResource;
use App\Models\ClassSection;
use App\Models\Grade;
use App\Models\GradeSubject;
use App\Models\SchoolSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListGradeSubjects extends ListRecords
{
    protected static string $resource = GradeSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $settings = SchoolSettings::first();

                    $gradeSubjects = GradeSubject::with(['grade.schoolSection', 'subject'])
                        ->whereHas('grade', fn ($q) => $q->where('is_active', true))
                        ->get()
                        ->groupBy('grade_id');

                    $grades = [];
                    $allTeacherIds = collect();
                    $multiTeacherFlags = [];

                    foreach ($gradeSubjects as $gradeId => $subjects) {
                        $grade = $subjects->first()->grade;
                        $classSectionIds = ClassSection::where('grade_id', $gradeId)->pluck('id');

                        $subjectData = [];
                        foreach ($subjects->sortBy('subject.name') as $gs) {
                            $teachers = DB::table('subject_teachings')
                                ->join('teachers', 'subject_teachings.teacher_id', '=', 'teachers.id')
                                ->join('class_sections', 'subject_teachings.class_section_id', '=', 'class_sections.id')
                                ->where('subject_teachings.subject_id', $gs->subject_id)
                                ->whereIn('subject_teachings.class_section_id', $classSectionIds)
                                ->select('teachers.id', 'teachers.name', 'class_sections.name as class_section_name')
                                ->distinct()
                                ->get();

                            $allTeacherIds = $allTeacherIds->merge($teachers->pluck('id'));

                            // Count unique teachers (not unique teacher+section combos)
                            $uniqueTeacherCount = $teachers->unique('id')->count();

                            $teacherList = $teachers->map(fn ($t) => [
                                'id' => $t->id,
                                'name' => $t->name,
                                'class_section' => $t->class_section_name,
                            ])->toArray();

                            // Flag if multiple different teachers are assigned
                            $hasMultipleTeachers = $uniqueTeacherCount > 1;

                            if ($hasMultipleTeachers) {
                                $teacherNames = $teachers->unique('id')->pluck('name')->implode(', ');
                                $multiTeacherFlags[] = [
                                    'grade' => $grade->name,
                                    'subject' => $gs->subject->name,
                                    'teacher_count' => $uniqueTeacherCount,
                                    'teachers' => $teacherNames,
                                ];
                            }

                            $subjectData[] = [
                                'name' => $gs->subject->name,
                                'code' => $gs->subject->code,
                                'is_mandatory' => $gs->is_mandatory,
                                'teachers' => $teacherList,
                                'unique_teacher_count' => $uniqueTeacherCount,
                                'has_multiple_teachers' => $hasMultipleTeachers,
                            ];
                        }

                        $grades[] = [
                            'name' => $grade->name,
                            'section' => $grade->schoolSection->name ?? 'N/A',
                            'level' => $grade->level,
                            'subject_count' => count($subjectData),
                            'subjects' => $subjectData,
                        ];
                    }

                    usort($grades, fn ($a, $b) => ($a['level'] ?? 0) <=> ($b['level'] ?? 0));

                    $pdf = Pdf::loadView('pdf.grade-subjects', [
                        'settings' => $settings,
                        'grades' => $grades,
                        'totalGrades' => count($grades),
                        'totalSubjects' => GradeSubject::distinct('subject_id')->count('subject_id'),
                        'totalAssignments' => GradeSubject::count(),
                        'totalTeachers' => $allTeacherIds->unique()->count(),
                        'multiTeacherFlags' => $multiTeacherFlags,
                    ]);

                    $pdf->setPaper('A4', 'landscape');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'grade-subjects-teacher-assignments-' . now()->format('Y-m-d') . '.pdf'
                    );
                }),
        ];
    }
}
