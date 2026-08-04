<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTeaching;
use App\Models\Teacher;
use App\Models\Term;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Coverage dashboard for the class ("grade") teacher.
 *
 * Shows every subject taught in the class teacher's own section, who is
 * assigned to teach it (via SubjectTeaching for the active year), and
 * whether Mid-Term and End-of-Term marks have been entered for the whole
 * class this term. Subjects with no SubjectTeaching row but with recorded
 * results still appear so the class teacher can see all incoming marks.
 */
class ClassResultsStatus extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string $view = 'filament.pages.class-results-status';
    protected static ?string $navigationLabel = 'Results Coverage';
    protected static ?string $title = 'Results Coverage — My Class';
    protected static ?string $navigationGroup = 'Academic';
    protected static ?int $navigationSort = 11;

    public ?array $data = [];
    public ?int $classSectionId = null;
    public ?int $termId = null;
    public array $rows = [];
    public int $studentCount = 0;
    public ?string $classLabel = null;

    public function mount(): void
    {
        $user = Auth::user();
        $activeTerm = Term::where('is_active', true)->first();
        if ($activeTerm) {
            $this->termId = $activeTerm->id;
        }

        // Class teacher: auto-load their own class
        if (in_array($user->role_id, RoleConstants::teaching())) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher && $teacher->is_class_teacher && $teacher->class_section_id) {
                $this->classSectionId = $teacher->class_section_id;
                $this->loadCoverage();
            }
        }

        $this->form->fill([
            'classSectionId' => $this->classSectionId,
            'termId' => $this->termId,
        ]);
    }

    public function form(Form $form): Form
    {
        $user = Auth::user();
        $isAdmin = $user->role_id === RoleConstants::ADMIN;
        $teacher = Teacher::where('user_id', $user->id)->first();
        $isFixedToOwnClass = $teacher && $teacher->is_class_teacher && $teacher->class_section_id && ! $isAdmin;

        $classOptions = [];
        if ($isAdmin) {
            $classOptions = ClassSection::with('grade')
                ->where('is_active', true)
                ->get()
                ->mapWithKeys(fn ($cs) => [$cs->id => ($cs->grade?->name ?? 'Unknown') . ' ' . $cs->name])
                ->toArray();
        } elseif ($teacher && $teacher->class_section_id) {
            $cs = ClassSection::with('grade')->find($teacher->class_section_id);
            if ($cs) {
                $classOptions = [$cs->id => ($cs->grade?->name ?? 'Unknown') . ' ' . $cs->name];
            }
        }

        $termOptions = Term::whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();

        return $form
            ->schema([
                Select::make('classSectionId')
                    ->label('Class')
                    ->options($classOptions)
                    ->default($this->classSectionId)
                    ->required()
                    ->disabled($isFixedToOwnClass)
                    ->dehydrated()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->classSectionId = (int) $state;
                        $this->loadCoverage();
                    }),
                Select::make('termId')
                    ->label('Term')
                    ->options($termOptions)
                    ->default($this->termId)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->termId = (int) $state;
                        $this->loadCoverage();
                    }),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function loadCoverage(): void
    {
        $this->rows = [];
        $this->studentCount = 0;
        $this->classLabel = null;

        if (! $this->classSectionId || ! $this->termId) {
            return;
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            return;
        }

        $classSection = ClassSection::with('grade')->find($this->classSectionId);
        $this->classLabel = $classSection
            ? (($classSection->grade?->name ?? '') . ' ' . $classSection->name)
            : null;

        $studentIds = Student::where('class_section_id', $this->classSectionId)
            ->where('enrollment_status', 'active')
            ->pluck('id')
            ->all();
        $this->studentCount = count($studentIds);

        if ($this->studentCount === 0) {
            return;
        }

        // Officially assigned subjects for this class this year
        $teachings = SubjectTeaching::with(['subject', 'teacher'])
            ->where('class_section_id', $this->classSectionId)
            ->where('academic_year_id', $activeYear->id)
            ->get()
            ->keyBy('subject_id');

        // Union with any subject that ALREADY has results for these pupils this
        // term — catches subjects being marked informally by the class teacher
        // without a SubjectTeaching row.
        $subjectsWithResults = Result::whereIn('student_id', $studentIds)
            ->where('term_id', $this->termId)
            ->pluck('subject_id')
            ->unique();

        $subjectIds = $teachings->keys()->merge($subjectsWithResults)->unique()->values();
        $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();

        // Batch-fetch mid-term + end-of-term counts + recorded_by teacher in one query
        $counts = Result::selectRaw('subject_id, exam_type, COUNT(*) as c, MAX(recorded_by) as recorded_by')
            ->whereIn('student_id', $studentIds)
            ->where('term_id', $this->termId)
            ->whereIn('exam_type', ['mid-term', 'end-of-term'])
            ->groupBy('subject_id', 'exam_type')
            ->get()
            ->groupBy('subject_id');

        // Backfill teacher names from Teacher table for recorded_by lookups
        $recordedTeacherIds = $counts->flatten(1)
            ->pluck('recorded_by')
            ->filter()
            ->unique()
            ->all();
        $recordedTeachers = Teacher::whereIn('id', $recordedTeacherIds)
            ->pluck('name', 'id')
            ->toArray();

        $rows = [];
        foreach ($subjects as $subject) {
            $teaching = $teachings->get($subject->id);
            $subjectCounts = $counts->get($subject->id, collect());
            $midRow = $subjectCounts->firstWhere('exam_type', 'mid-term');
            $eotRow = $subjectCounts->firstWhere('exam_type', 'end-of-term');
            $midCount = $midRow ? (int) $midRow->c : 0;
            $eotCount = $eotRow ? (int) $eotRow->c : 0;

            // Teacher: prefer official SubjectTeaching assignment; fall back
            // to whoever actually recorded results (mid-term first, then EOT).
            $teacherName = null;
            $assignmentSource = null;
            if ($teaching && $teaching->teacher) {
                $teacherName = $teaching->teacher->name;
                $assignmentSource = 'assigned';
            } else {
                $recordedBy = $midRow->recorded_by ?? $eotRow->recorded_by ?? null;
                if ($recordedBy && isset($recordedTeachers[$recordedBy])) {
                    $teacherName = $recordedTeachers[$recordedBy];
                    $assignmentSource = 'recorded_by';
                }
            }

            $rows[] = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'teacher_name' => $teacherName,
                'assignment_source' => $assignmentSource,
                'mid_count' => $midCount,
                'eot_count' => $eotCount,
                'total' => $this->studentCount,
            ];
        }

        $this->rows = $rows;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->role_id === RoleConstants::ADMIN) {
            return true;
        }
        if (in_array($user->role_id, RoleConstants::teaching())) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            return $teacher && $teacher->is_class_teacher;
        }
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
