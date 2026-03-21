<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Result;
use App\Models\Student;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReportCards extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static string $view = 'filament.pages.report-cards';
    protected static ?string $navigationLabel = 'Report Cards';
    protected static ?string $navigationGroup = 'Academic Management';
    protected static ?int $navigationSort = 5;

    public ?array $data = [];
    public $selectedClassSection = null;
    public $selectedTerm = null;
    public $selectedYear = null;

    public function mount(): void
    {
        $currentYear = AcademicYear::where('is_active', true)->first();

        $this->form->fill([
            'class_section_id' => null,
            'term' => 'first',
            'year' => $currentYear ? $currentYear->name : date('Y'),
        ]);
    }

    public function form(Form $form): Form
    {
        $user = Auth::user();
        $isTeacher = $user && in_array($user->role_id, RoleConstants::teaching());
        $teacher = $isTeacher ? Teacher::where('user_id', $user->id)->first() : null;

        return $form
            ->schema([
                Forms\Components\Section::make('Select Class and Term')
                    ->description('Choose a class and term to generate report cards')
                    ->schema([
                        Forms\Components\Select::make('class_section_id')
                            ->label('Class Section')
                            ->options(function () use ($isTeacher, $teacher) {
                                if ($isTeacher && $teacher) {
                                    // Get classes where teacher teaches
                                    $classIds = $teacher->subjectTeachings()
                                        ->pluck('class_section_id')
                                        ->unique();

                                    return ClassSection::whereIn('id', $classIds)
                                        ->with('grade')
                                        ->get()
                                        ->mapWithKeys(function ($section) {
                                            return [$section->id => "{$section->grade->name} - {$section->name}"];
                                        })
                                        ->toArray();
                                }

                                // Admin sees all classes
                                return ClassSection::with('grade')
                                    ->get()
                                    ->mapWithKeys(function ($section) {
                                        return [$section->id => "{$section->grade->name} - {$section->name}"];
                                    })
                                    ->toArray();
                            })
                            ->required()
                            ->reactive()
                            ->searchable(),

                        Forms\Components\Select::make('term')
                            ->options([
                                'first' => 'First Term',
                                'second' => 'Second Term',
                                'third' => 'Third Term',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->required()
                            ->default(date('Y'))
                            ->reactive(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $classSectionId = $this->data['class_section_id'] ?? null;
                $term = $this->data['term'] ?? null;
                $year = $this->data['year'] ?? null;

                if (!$classSectionId) {
                    return Student::query()->whereRaw('1 = 0'); // Return empty
                }

                return Student::where('class_section_id', $classSectionId)
                    ->with(['classSection.grade', 'parentGuardian']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('admission_number')
                    ->label('Admission No.')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Student $record): string =>
                        $record->classSection
                            ? "{$record->classSection->grade->name} - {$record->classSection->name}"
                            : 'No class'
                    ),

                Tables\Columns\TextColumn::make('results_count')
                    ->label('Results Entered')
                    ->getStateUsing(function (Student $record) {
                        $term = $this->data['term'] ?? null;
                        $year = $this->data['year'] ?? null;

                        return Result::where('student_id', $record->id)
                            ->where('term', $term)
                            ->where('year', $year)
                            ->count();
                    })
                    ->badge()
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('parentGuardian.name')
                    ->label('Parent/Guardian')
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('viewReportCard')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Student $record) => route('filament.admin.pages.report-cards.preview', [
                        'student' => $record->id,
                        'term' => $this->data['term'] ?? 'first',
                        'year' => $this->data['year'] ?? date('Y'),
                    ]))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('downloadReportCard')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Student $record) {
                        return $this->downloadReportCard($record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('downloadAll')
                    ->label('Download All Report Cards')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($records) {
                        return $this->downloadBulkReportCards($records);
                    }),
            ])
            ->emptyStateHeading('No students found')
            ->emptyStateDescription('Please select a class section above to view students and generate report cards.');
    }

    public function downloadReportCard(Student $student)
    {
        $term = $this->data['term'] ?? 'first';
        $year = $this->data['year'] ?? date('Y');

        $data = $this->getReportCardData($student, $term, $year);

        $pdf = Pdf::loadView('pdf.report-card', $data)
            ->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "report-card-{$student->admission_number}-{$term}-{$year}.pdf");
    }

    public function downloadBulkReportCards($students)
    {
        $term = $this->data['term'] ?? 'first';
        $year = $this->data['year'] ?? date('Y');

        $allData = [];
        foreach ($students as $student) {
            $allData[] = $this->getReportCardData($student, $term, $year);
        }

        $pdf = Pdf::loadView('pdf.report-cards-bulk', ['reports' => $allData])
            ->setPaper('a4', 'portrait');

        $classSection = ClassSection::find($this->data['class_section_id']);
        $className = $classSection ? str_replace(' ', '-', $classSection->name) : 'class';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "report-cards-{$className}-{$term}-{$year}.pdf");
    }

    protected function getReportCardData(Student $student, string $term, int $year): array
    {
        // Get all results for this student, term, and year
        $results = Result::where('student_id', $student->id)
            ->where('term', $term)
            ->where('year', $year)
            ->with(['subject', 'recordedBy'])
            ->orderBy('subject_id')
            ->get();

        // Group results by subject
        $subjectResults = $results->groupBy('subject_id')->map(function ($subjectGroup) {
            $subject = $subjectGroup->first()->subject;

            // Calculate averages and get individual scores
            $scores = [
                'subject_name' => $subject->name,
                'mid_term' => $subjectGroup->where('exam_type', 'mid-term')->first()?->marks,
                'end_of_term' => $subjectGroup->where('exam_type', 'end-of-term')->first()?->marks,
                'total' => 0,
                'average' => 0,
                'grade' => '',
                'comment' => '',
            ];

            // Calculate total and average
            $midTerm = $scores['mid_term'] ?? 0;
            $endOfTerm = $scores['end_of_term'] ?? 0;

            if ($midTerm > 0 && $endOfTerm > 0) {
                $scores['total'] = $midTerm + $endOfTerm;
                $scores['average'] = round($scores['total'] / 2, 1);
            } elseif ($endOfTerm > 0) {
                $scores['average'] = $endOfTerm;
                $scores['total'] = $endOfTerm;
            } elseif ($midTerm > 0) {
                $scores['average'] = $midTerm;
                $scores['total'] = $midTerm;
            }

            // Determine grade
            $scores['grade'] = $this->calculateGrade($scores['average']);

            // Get teacher comment (from end-of-term result if available)
            $endOfTermResult = $subjectGroup->where('exam_type', 'end-of-term')->first();
            $scores['comment'] = $endOfTermResult?->comment ?? '';

            return $scores;
        })->values();

        // Calculate overall statistics
        $totalMarks = $subjectResults->sum('total');
        $totalPossible = $subjectResults->count() * 200; // 100 for mid-term + 100 for end-of-term
        $overallAverage = $subjectResults->count() > 0 ? round($subjectResults->avg('average'), 1) : 0;
        $overallGrade = $this->calculateGrade($overallAverage);

        return [
            'student' => $student,
            'term' => ucfirst($term) . ' Term',
            'year' => $year,
            'results' => $subjectResults,
            'total_marks' => $totalMarks,
            'total_possible' => $totalPossible,
            'overall_average' => $overallAverage,
            'overall_grade' => $overallGrade,
            'class_position' => $this->calculateClassPosition($student, $term, $year),
            'total_students' => Student::where('class_section_id', $student->class_section_id)->count(),
            'generated_date' => now()->format('F d, Y'),
        ];
    }

    protected function calculateGrade(float $average): string
    {
        if ($average >= 90) return 'A+';
        if ($average >= 80) return 'A';
        if ($average >= 70) return 'B+';
        if ($average >= 60) return 'B';
        if ($average >= 50) return 'C+';
        if ($average >= 40) return 'C';
        if ($average >= 30) return 'D';
        return 'F';
    }

    protected function calculateClassPosition(Student $student, string $term, int $year): int
    {
        // Get all students in the same class
        $classStudents = Student::where('class_section_id', $student->class_section_id)->pluck('id');

        // Calculate average for each student
        $studentAverages = [];
        foreach ($classStudents as $studentId) {
            $results = Result::where('student_id', $studentId)
                ->where('term', $term)
                ->where('year', $year)
                ->get();

            if ($results->count() > 0) {
                $average = $results->avg('marks');
                $studentAverages[$studentId] = $average;
            }
        }

        // Sort by average descending
        arsort($studentAverages);

        // Find position
        $position = 1;
        foreach ($studentAverages as $studentId => $average) {
            if ($studentId == $student->id) {
                return $position;
            }
            $position++;
        }

        return 0; // No results
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return in_array($user->role_id, array_merge([RoleConstants::ADMIN], RoleConstants::teaching()));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
