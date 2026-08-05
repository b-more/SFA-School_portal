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

        if (in_array($user->role_id, RoleConstants::teaching())) {
            $teacher = Teacher::where('user_id', $user->id)->first();
        }

        // Get class section options based on role
        $classSectionOptions = $this->getClassSectionOptions($teacher, $isAdmin);

        // Lock the dropdown only if the teacher has exactly one option — no
        // point in a picker with a single choice. A class teacher who also
        // teaches subjects in other classes needs a usable dropdown.
        $lockClassSelect = ! $isAdmin && count($classSectionOptions) === 1;

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
                    ->disabled($lockClassSelect)
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
                        'mid-term' => 'Mid-Term Exam',
                        'end-of-term' => 'End-of-Term Exam',
                        'final' => 'Final Exam',
                        'quiz' => 'Quiz',
                        'assignment' => 'Assignment',
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
            $activeYear = AcademicYear::where('is_active', true)->first();

            // Merge every class-section this teacher is attached to:
            //   (a) legacy Teacher.class_section_id (if is_class_teacher flag set)
            //   (b) ClassSection.class_teacher_id pointing at them (authoritative;
            //       what the report card uses)
            //   (c) SubjectTeaching rows for the active year
            $classSectionIds = collect();
            if ($teacher->is_class_teacher && $teacher->class_section_id) {
                $classSectionIds->push($teacher->class_section_id);
            }
            $classSectionIds = $classSectionIds->merge(
                ClassSection::where('class_teacher_id', $teacher->id)->pluck('id')
            );
            if ($activeYear) {
                $classSectionIds = $classSectionIds->merge(
                    SubjectTeaching::where('teacher_id', $teacher->id)
                        ->where('academic_year_id', $activeYear->id)
                        ->pluck('class_section_id')
                );
            }

            // Precompute which sections have this teacher as class teacher so
            // we can annotate the dropdown label.
            $classTeacherSectionIds = ClassSection::where('class_teacher_id', $teacher->id)
                ->pluck('id')
                ->push($teacher->is_class_teacher ? $teacher->class_section_id : null)
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->all();

            return ClassSection::with('grade')
                ->whereIn('id', $classSectionIds->unique()->all())
                ->where('is_active', true)
                ->get()
                ->sortBy(fn ($cs) => ($cs->grade->name ?? '') . ' ' . $cs->name)
                ->mapWithKeys(function ($section) use ($classTeacherSectionIds) {
                    $gradeName = $section->grade ? $section->grade->name : 'Unknown';
                    $label = "{$gradeName} - {$section->name}";
                    if (in_array((int) $section->id, $classTeacherSectionIds, true)) {
                        $label .= '  (your class)';
                    }
                    return [$section->id => $label];
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

        $classSection = ClassSection::with('grade')->find($this->classSectionId);
        $activeYear = AcademicYear::where('is_active', true)->first();
        // The class teacher for a section can live either on Teacher.is_class_teacher
        // (legacy) or on ClassSection.class_teacher_id (authoritative — this is
        // what the report card uses). Either match counts.
        $isClassTeacherOfThis = $teacher && $classSection && (
            ((int) ($classSection->class_teacher_id ?? 0) === (int) $teacher->id)
            || ($teacher->is_class_teacher && (int) $teacher->class_section_id === (int) $this->classSectionId)
        );

        // Load ALL teachings for this class section this year — used both to
        // filter (subject teachers) and to annotate labels (class teachers).
        $classTeachings = collect();
        if ($activeYear) {
            $classTeachings = SubjectTeaching::with('teacher')
                ->where('class_section_id', $this->classSectionId)
                ->where('academic_year_id', $activeYear->id)
                ->get();
        }
        $teacherBySubject = $classTeachings->keyBy('subject_id');
        $myTeachingSubjectIds = $teacher
            ? $classTeachings->where('teacher_id', $teacher->id)->pluck('subject_id')->all()
            : [];

        // Subjects officially assigned to this grade (mandatory + optional).
        $gradeSubjects = ($classSection && $classSection->grade)
            ? GradeSubject::where('grade_id', $classSection->grade_id)->with('subject')->get()
            : collect();

        // A subject teacher may have a SubjectTeaching row for a subject that
        // isn't in the grade's GradeSubject curriculum (data drift). Fold
        // those in as synthetic pseudo-GradeSubject rows so the teacher still
        // sees what they've been assigned.
        $curriculumIds = $gradeSubjects->pluck('subject_id')->all();
        $extraSubjectIds = array_diff($myTeachingSubjectIds, $curriculumIds);
        if (! empty($extraSubjectIds)) {
            $extraSubjects = Subject::whereIn('id', $extraSubjectIds)->get();
            foreach ($extraSubjects as $s) {
                // Fabricate a minimal GradeSubject-like object; treated as
                // mandatory (no "(Optional)" tag) since curriculum status is
                // unknown here.
                $gradeSubjects->push((object) [
                    'subject_id' => $s->id,
                    'is_mandatory' => true,
                    'subject' => $s,
                ]);
            }
        }

        // For an admin OR the class teacher of this class, show everything so
        // they can pick up subjects nobody else has been assigned. Subject
        // teachers see only their own assigned subjects (secondary workflow).
        $candidateSubjects = ($isAdmin || $isClassTeacherOfThis)
            ? $gradeSubjects
            : $gradeSubjects->filter(fn ($gs) => in_array($gs->subject_id, $myTeachingSubjectIds, true));

        // Label each subject with either "(you)" if you teach it, the assigned
        // teacher's surname if someone else does, or "(unassigned)" if nobody
        // has a SubjectTeaching row. Subject teachers only see their own so
        // the label just marks (you); the extra hint helps the class teacher.
        $formatLabel = function ($subjectName, $subjectId, $isOptional) use ($teacher, $teacherBySubject, $isClassTeacherOfThis, $isAdmin) {
            $label = $subjectName;
            if ($isOptional) {
                $label .= ' (Optional)';
            }
            if (! ($isClassTeacherOfThis || $isAdmin)) {
                return $label;
            }
            $assigned = $teacherBySubject->get($subjectId);
            if (! $assigned || ! $assigned->teacher) {
                $label .= '  — unassigned';
            } elseif ($teacher && (int) $assigned->teacher_id === (int) $teacher->id) {
                $label .= '  (you)';
            } else {
                $surname = trim(preg_replace('/^\S+\s+/', '', $assigned->teacher->name)) ?: $assigned->teacher->name;
                $label .= "  — {$surname}";
            }
            return $label;
        };

        return $candidateSubjects
            ->sortBy(fn ($gs) => $gs->subject->name)
            ->mapWithKeys(fn ($gs) => [
                $gs->subject_id => $formatLabel($gs->subject->name, $gs->subject_id, ! $gs->is_mandatory),
            ])
            ->toArray();
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

        // Load existing results — filter on term_id (canonical). The legacy
        // `term` column is inconsistent across writers (numeric id vs term
        // name) so it can't be relied on for aggregation.
        $existingResults = Result::where('subject_id', $this->subjectId)
            ->where('term_id', $this->termId)
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
                'term_id' => $this->termId,
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

        // Any admin or teaching role (includes deans, head teachers,
        // deputy heads) can enter marks — head teachers and deputies
        // often teach classes themselves and were previously locked out.
        return in_array($user->role_id, RoleConstants::teachingWithAdmin());
    }
}
