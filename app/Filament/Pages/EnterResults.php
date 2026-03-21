<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\GradeSubject;
use App\Models\StudentSubjectEnrollment;
use App\Traits\HasPageGuide;
use App\Models\ClassSection;
use App\Models\GradingScale;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTeaching;
use App\Models\Teacher;
use App\Models\Term;
use App\Services\ResultsService;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class EnterResults extends Page implements HasForms
{
    use InteractsWithForms, HasPageGuide;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static string $view = 'filament.pages.enter-results';

    protected static ?string $navigationLabel = 'Enter Results';

    protected static ?string $title = 'Enter Student Results';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 8;

    public ?array $data = [];

    public $classSectionId = null;

    public $subjectId = null;

    public $termId = null;

    public $examType = 'final';

    public $year = null;

    public $students = [];

    public $resultsData = [];

    public $gradingScale = null;

    public bool $isOptionalSubject = false;

    protected $resultsService;

    public function boot(ResultsService $resultsService): void
    {
        $this->resultsService = $resultsService;
    }

    public function mount(): void
    {
        $this->year = now()->year;

        // Get current term
        $currentTerm = Term::whereHas('academicYear', function ($q) {
            $q->where('is_active', true);
        })->where('is_current', true)->first();

        if ($currentTerm) {
            $this->termId = $currentTerm->id;
        }

        // Auto-select class for class teachers
        $user = Auth::user();
        if (in_array($user->role_id, RoleConstants::teaching())) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher && $teacher->is_class_teacher && $teacher->class_section_id) {
                $this->classSectionId = $teacher->class_section_id;
                $this->loadGradingScale();
            }
        }

        $this->form->fill([
            'classSectionId' => $this->classSectionId,
            'year' => $this->year,
            'termId' => $this->termId,
            'examType' => $this->examType,
        ]);
    }

    public function form(Form $form): Form
    {
        $user = Auth::user();
        $teacher = null;
        $isAdmin = $user->role_id === RoleConstants::ADMIN;
        $isClassTeacher = false;

        if (in_array($user->role_id, RoleConstants::teaching())) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            $isClassTeacher = $teacher && $teacher->is_class_teacher && $teacher->class_section_id;
        }

        // Get class section options based on role
        $classSectionOptions = $this->getClassSectionOptions($teacher, $isAdmin);

        // Get terms
        $termOptions = Term::whereHas('academicYear', function ($q) {
            $q->where('is_active', true);
        })->orderBy('name')->pluck('name', 'id')->toArray();

        return $form
            ->schema([
                Select::make('classSectionId')
                    ->label('Select Class')
                    ->options($classSectionOptions)
                    ->required()
                    ->reactive()
                    ->disabled($isClassTeacher)
                    ->dehydrated()
                    ->default($this->classSectionId)
                    ->afterStateUpdated(function ($state) {
                        $this->classSectionId = $state;
                        $this->subjectId = null;
                        $this->students = [];
                        $this->resultsData = [];
                        $this->loadGradingScale();
                    }),

                Select::make('subjectId')
                    ->label('Select Subject')
                    ->options(function () use ($teacher, $isAdmin) {
                        return $this->getSubjectOptions($teacher, $isAdmin);
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->subjectId = $state;
                        $this->loadStudentsAndResults();
                    }),

                Select::make('termId')
                    ->label('Term')
                    ->options($termOptions)
                    ->required()
                    ->default($this->termId)
                    ->disabled()
                    ->dehydrated()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->termId = $state;
                        $this->loadStudentsAndResults();
                    }),

                Select::make('examType')
                    ->label('Exam Type')
                    ->options([
                        'mid_term' => 'Mid-Term Exam',
                        'final' => 'Final Exam',
                        'quiz' => 'Quiz',
                        'assignment' => 'Assignment',
                        'test' => 'Test',
                    ])
                    ->required()
                    ->default('final')
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->examType = $state;
                        $this->loadStudentsAndResults();
                    }),

                Select::make('year')
                    ->label('Year')
                    ->options(function () {
                        $currentYear = now()->year;
                        return [
                            $currentYear - 1 => $currentYear - 1,
                            $currentYear => $currentYear,
                            $currentYear + 1 => $currentYear + 1,
                        ];
                    })
                    ->default(now()->year)
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->year = $state;
                        $this->loadStudentsAndResults();
                    }),
            ])
            ->statePath('data')
            ->columns(5);
    }

    protected function getClassSectionOptions($teacher, $isAdmin): array
    {
        if ($isAdmin) {
            return ClassSection::with('grade')
                ->where('is_active', true)
                ->get()
                ->mapWithKeys(function ($section) {
                    $gradeName = $section->grade ? $section->grade->name : 'Unknown';
                    return [$section->id => "{$gradeName} - {$section->name}"];
                })
                ->toArray();
        }

        // Section heads see all classes in their section(s)
        $user = Auth::user();
        $accessor = new class { use \App\Traits\HasSectionBasedAccess; };
        if ($accessor->shouldBypassTeacherFilter($user)) {
            $sectionIds = $accessor->getIncludedSectionIds($user);
            if (!empty($sectionIds)) {
                $gradeIds = \App\Models\Grade::whereIn('school_section_id', $sectionIds)->pluck('id');
                return ClassSection::with('grade')
                    ->whereIn('grade_id', $gradeIds)
                    ->where('is_active', true)
                    ->get()
                    ->mapWithKeys(function ($section) {
                        $gradeName = $section->grade ? $section->grade->name : 'Unknown';
                        return [$section->id => "{$gradeName} - {$section->name}"];
                    })
                    ->toArray();
            }
        }

        if ($teacher) {
            // Class teacher: only their assigned class
            if ($teacher->is_class_teacher && $teacher->class_section_id) {
                $section = ClassSection::with('grade')->find($teacher->class_section_id);
                if ($section) {
                    $gradeName = $section->grade ? $section->grade->name : 'Unknown';
                    return [$section->id => "{$gradeName} - {$section->name}"];
                }
            }

            // Non-class teachers: get from subject teachings (any year as fallback)
            $classSectionIds = SubjectTeaching::where('teacher_id', $teacher->id)
                ->pluck('class_section_id')
                ->unique();

            return ClassSection::with('grade')
                ->whereIn('id', $classSectionIds)
                ->where('is_active', true)
                ->get()
                ->mapWithKeys(function ($section) {
                    $gradeName = $section->grade ? $section->grade->name : 'Unknown';
                    return [$section->id => "{$gradeName} - {$section->name}"];
                })
                ->toArray();
        }

        return [];
    }

    protected function getSubjectOptions($teacher, $isAdmin): array
    {
        if (!$this->classSectionId) {
            return [];
        }

        // Get subjects assigned to this grade with mandatory flag
        $classSection = ClassSection::with('grade')->find($this->classSectionId);
        if ($classSection && $classSection->grade) {
            $gradeSubjects = GradeSubject::where('grade_id', $classSection->grade_id)
                ->with('subject')
                ->get();

            return $gradeSubjects->sortBy('subject.name')
                ->mapWithKeys(function ($gs) {
                    $label = $gs->subject->name;
                    if (!$gs->is_mandatory) {
                        $label .= ' (Optional)';
                    }
                    return [$gs->subject_id => $label];
                })
                ->toArray();
        }

        // Fallback to all subjects (admin only)
        if ($isAdmin) {
            return Subject::orderBy('name')->pluck('name', 'id')->toArray();
        }

        return [];
    }

    protected function loadGradingScale(): void
    {
        if (!$this->classSectionId) {
            $this->gradingScale = null;
            return;
        }

        $classSection = ClassSection::with('grade')->find($this->classSectionId);
        if ($classSection && $classSection->grade) {
            $gradeLevel = GradingScale::determineGradeLevelFromGrade($classSection->grade);
            $this->gradingScale = GradingScale::getDefaultForGradeLevel($gradeLevel);
        }
    }

    public function loadStudentsAndResults(): void
    {
        if (!$this->classSectionId || !$this->subjectId || !$this->termId || !$this->examType || !$this->year) {
            $this->students = [];
            $this->resultsData = [];
            $this->isOptionalSubject = false;
            return;
        }

        // Check if this subject is optional for the grade
        $classSection = ClassSection::find($this->classSectionId);
        $gradeSubject = $classSection
            ? GradeSubject::where('grade_id', $classSection->grade_id)
                ->where('subject_id', $this->subjectId)
                ->first()
            : null;

        $this->isOptionalSubject = $gradeSubject && !$gradeSubject->is_mandatory;

        if ($this->isOptionalSubject) {
            // Optional subject: only load enrolled students in this class section
            $academicYear = AcademicYear::where('is_active', true)->first();
            $enrolledStudentIds = $academicYear
                ? StudentSubjectEnrollment::where('subject_id', $this->subjectId)
                    ->where('grade_id', $classSection->grade_id)
                    ->where('academic_year_id', $academicYear->id)
                    ->pluck('student_id')
                : collect();

            $this->students = Student::where('class_section_id', $this->classSectionId)
                ->where('enrollment_status', 'active')
                ->whereIn('id', $enrolledStudentIds)
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            // Mandatory subject: load ALL active students in class section
            $this->students = Student::where('class_section_id', $this->classSectionId)
                ->where('enrollment_status', 'active')
                ->orderBy('name')
                ->get()
                ->toArray();
        }

        // Load existing results
        $existingResults = Result::where('subject_id', $this->subjectId)
            ->where('term', $this->termId)
            ->where('exam_type', $this->examType)
            ->where('year', $this->year)
            ->whereIn('student_id', collect($this->students)->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Initialize results data
        $this->resultsData = [];
        foreach ($this->students as $student) {
            $studentId = $student['id'];
            if (isset($existingResults[$studentId])) {
                $result = $existingResults[$studentId];
                $this->resultsData[$studentId] = [
                    'marks' => $result->marks,
                    'grade' => $result->grade,
                    'comment' => $result->comment,
                ];
            } else {
                $this->resultsData[$studentId] = [
                    'marks' => '',
                    'grade' => '',
                    'comment' => '',
                ];
            }
        }

        $this->loadGradingScale();
    }

    public function updateMarks($studentId, $marks): void
    {
        $this->resultsData[$studentId]['marks'] = $marks;

        // Auto-calculate grade
        if (is_numeric($marks) && $this->gradingScale) {
            $gradeItem = $this->gradingScale->calculateGrade((float) $marks);
            $this->resultsData[$studentId]['grade'] = $gradeItem ? $gradeItem->grade : '';
        } else {
            $this->resultsData[$studentId]['grade'] = '';
        }
    }

    public function updateGrade($studentId, $grade): void
    {
        $this->resultsData[$studentId]['grade'] = $grade;
    }

    public function updateComment($studentId, $comment): void
    {
        $this->resultsData[$studentId]['comment'] = $comment;
    }

    public function submitResults(): void
    {
        // Validation
        if (!$this->classSectionId || !$this->subjectId || !$this->termId || !$this->examType || !$this->year) {
            Notification::make()
                ->title('Please select all required fields')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->students)) {
            Notification::make()
                ->title('No students found')
                ->danger()
                ->send();
            return;
        }

        // Get teacher ID for recording
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $recordedById = $teacher ? $teacher->id : null;

        // Prepare results data
        $resultsToSave = [];
        $skipped = 0;

        foreach ($this->resultsData as $studentId => $data) {
            // Skip if no marks entered
            if ($data['marks'] === '' || $data['marks'] === null) {
                $skipped++;
                continue;
            }

            $marks = (float) $data['marks'];

            // Validate marks range
            if ($marks < 0 || $marks > 100) {
                Notification::make()
                    ->title("Invalid marks for a student (must be 0-100)")
                    ->danger()
                    ->send();
                return;
            }

            $resultsToSave[] = [
                'student_id' => $studentId,
                'subject_id' => $this->subjectId,
                'exam_type' => $this->examType,
                'marks' => $marks,
                'grade' => $data['grade'] ?: null,
                'term' => $this->termId,
                'year' => $this->year,
                'comment' => $data['comment'] ?: null,
            ];
        }

        if (empty($resultsToSave)) {
            Notification::make()
                ->title('No results to save')
                ->body('Please enter marks for at least one student')
                ->warning()
                ->send();
            return;
        }

        // Use ResultsService to save
        $resultsService = app(ResultsService::class);
        $result = $resultsService->saveBulkResults($resultsToSave, $recordedById);

        if (!empty($result['errors'])) {
            Notification::make()
                ->title('Some results could not be saved')
                ->body(implode("\n", $result['errors']))
                ->warning()
                ->send();
        } else {
            Notification::make()
                ->title('Results Saved Successfully!')
                ->body("Saved: {$result['saved']} | Skipped (no marks): {$skipped}")
                ->success()
                ->send();
        }

        // Reload to show updated data
        $this->loadStudentsAndResults();
    }

    public function clearAllMarks(): void
    {
        foreach ($this->resultsData as $studentId => $data) {
            $this->resultsData[$studentId] = [
                'marks' => '',
                'grade' => '',
                'comment' => '',
            ];
        }

        Notification::make()
            ->title('All marks cleared')
            ->info()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getPageGuideAction(),
        ];
    }

    protected function getGuideSlug(): string
    {
        return 'enter-results';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return in_array($user->role_id, [
            RoleConstants::ADMIN,
            RoleConstants::TEACHER,
            RoleConstants::DEAN_OF_PRIMARY,
            RoleConstants::DEAN_OF_SECONDARY,
        ]);
    }
}
