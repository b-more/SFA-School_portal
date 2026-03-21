<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Teacher;
use App\Models\ClassSection;
use App\Models\Student;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Event;
use App\Models\Result;
use App\Models\Attendance;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Constants\RoleConstants;
use App\Traits\HasPageGuide;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class TeacherDashboard extends Page
{
    use HasPageGuide;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static string $view = 'filament.pages.teacher-dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;

    public function getTeacher()
    {
        $user = Auth::user();
        return Teacher::where('user_id', $user->id)
            ->with(['classSection.grade', 'grade'])
            ->first();
    }

    public function getAssignedClasses()
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return collect();
        }

        $classes = collect();

        // If class teacher, add their assigned class section
        if ($teacher->is_class_teacher && $teacher->class_section_id) {
            $classSection = ClassSection::with(['grade', 'students'])->find($teacher->class_section_id);
            if ($classSection) {
                $classes->push($classSection);
            }
        }

        // Add all class sections where teacher teaches subjects
        $subjectClassSections = $teacher->subjectTeachings()
            ->with(['classSection.grade', 'classSection.students'])
            ->get()
            ->pluck('classSection')
            ->filter()
            ->unique('id');

        return $classes->concat($subjectClassSections)->unique('id');
    }

    /**
     * Get subject teachings grouped by class section for the current teacher.
     * Returns: [ class_section_id => [ 'classSection' => ..., 'subjects' => [...], 'isClassTeacher' => bool ] ]
     */
    public function getTeachingAssignments()
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return collect();
        }

        $currentYear = \App\Models\AcademicYear::where('is_active', true)->first();

        $teachings = $teacher->subjectTeachings()
            ->with(['subject', 'classSection.grade'])
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->get();

        $grouped = $teachings->groupBy('class_section_id')->map(function ($items) use ($teacher) {
            $first = $items->first();
            $cs = $first->classSection;
            $studentCount = Student::where('class_section_id', $cs->id)
                ->where('enrollment_status', 'active')
                ->count();

            return [
                'classSection' => $cs,
                'subjects' => $items->pluck('subject')->sortBy('name')->values(),
                'isClassTeacher' => $teacher->is_class_teacher && $teacher->class_section_id === $cs->id,
                'studentCount' => $studentCount,
            ];
        });

        // Add class teacher section if not already in subject teachings
        if ($teacher->is_class_teacher && $teacher->class_section_id && !$grouped->has($teacher->class_section_id)) {
            $cs = ClassSection::with('grade')->find($teacher->class_section_id);
            if ($cs) {
                $studentCount = Student::where('class_section_id', $cs->id)
                    ->where('enrollment_status', 'active')
                    ->count();
                $grouped[$cs->id] = [
                    'classSection' => $cs,
                    'subjects' => collect(),
                    'isClassTeacher' => true,
                    'studentCount' => $studentCount,
                ];
            }
        }

        // Sort by grade level then section name
        return $grouped->sortBy(fn($item) => ($item['classSection']->grade->level ?? 0) * 100 . $item['classSection']->name);
    }

    public function getStudentCount()
    {
        $classes = $this->getAssignedClasses();

        if ($classes->isEmpty()) {
            return 0;
        }

        $classSectionIds = $classes->pluck('id')->toArray();

        return Student::whereIn('class_section_id', $classSectionIds)
            ->where('enrollment_status', 'active')
            ->count();
    }

    public function getAssignedHomework()
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return collect();
        }

        // Check if homework table has the required columns
        if (!Schema::hasTable('homework') || !Schema::hasColumn('homework', 'assigned_by')) {
            return collect();
        }

        return Homework::where('assigned_by', $teacher->id)
            ->where('status', 'active')
            ->with(['subject', 'grade'])
            ->orderBy('due_date')
            ->get();
    }

    public function getRecentSubmissions()
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return collect();
        }

        // Check if required tables exist
        if (!Schema::hasTable('homework') || !Schema::hasTable('homework_submissions')) {
            return collect();
        }

        $homeworkIds = Homework::where('assigned_by', $teacher->id)->pluck('id');

        if ($homeworkIds->isEmpty()) {
            return collect();
        }

        return HomeworkSubmission::whereIn('homework_id', $homeworkIds)
            ->with(['student', 'homework.subject'])
            ->where('status', 'submitted')
            ->latest()
            ->take(10)
            ->get();
    }

    public function getRecentAttendance()
    {
        $classes = $this->getAssignedClasses();

        if ($classes->isEmpty()) {
            return collect();
        }

        // Check if attendance table exists
        if (!Schema::hasTable('attendances') && !Schema::hasTable('attendance')) {
            return collect();
        }

        $tableName = Schema::hasTable('attendances') ? 'attendances' : 'attendance';
        $classSectionIds = $classes->pluck('id')->toArray();

        return Attendance::whereIn('class_section_id', $classSectionIds)
            ->with(['student', 'classSection.grade'])
            ->where('attendance_date', '>=', now()->subDays(7))
            ->orderByDesc('attendance_date')
            ->take(5)
            ->get();
    }

    public function getUpcomingEvents()
    {
        $teacher = $this->getTeacher();
        $classes = $this->getAssignedClasses();

        // Check if events table exists
        if (!Schema::hasTable('events')) {
            return collect();
        }

        $gradeIds = $classes->pluck('grade_id')->filter()->unique()->toArray();

        // First check if the Event model has the applicable_to column
        $eventColumns = Schema::getColumnListing('events');

        if (in_array('applicable_to', $eventColumns)) {
            // Use the original query with applicable_to
            return Event::where('start_date', '>=', now())
                ->where(function($query) use ($gradeIds) {
                    if (!empty($gradeIds)) {
                        $query->whereIn('applicable_to', $gradeIds)
                            ->orWhere('applicable_to', 'all')
                            ->orWhere('applicable_to', 'teachers')
                            ->orWhereNull('applicable_to');
                    } else {
                        $query->where('applicable_to', 'all')
                            ->orWhere('applicable_to', 'teachers')
                            ->orWhereNull('applicable_to');
                    }
                })
                ->orderBy('start_date')
                ->take(5)
                ->get();
        } else {
            // Fallback to simple query if applicable_to doesn't exist
            return Event::where('start_date', '>=', now())
                ->orderBy('start_date')
                ->take(5)
                ->get();
        }
    }

    public function getGradingSummary()
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return [
                'total_submitted' => 0,
                'ungraded' => 0,
                'graded' => 0,
                'late' => 0,
            ];
        }

        // Check if required tables exist
        if (!Schema::hasTable('homework') || !Schema::hasTable('homework_submissions')) {
            return [
                'total_submitted' => 0,
                'ungraded' => 0,
                'graded' => 0,
                'late' => 0,
            ];
        }

        $homeworkIds = Homework::where('assigned_by', $teacher->id)->pluck('id');

        if ($homeworkIds->isEmpty()) {
            return [
                'total_submitted' => 0,
                'ungraded' => 0,
                'graded' => 0,
                'late' => 0,
            ];
        }

        // Get submissions as a collection to allow multiple operations
        $submissions = HomeworkSubmission::whereIn('homework_id', $homeworkIds)->get();

        return [
            'total_submitted' => $submissions->count(),
            'ungraded' => $submissions->where('status', 'submitted')->count(),
            'graded' => $submissions->whereNotNull('marks')->count(),
            'late' => $submissions->where('is_late', true)->count(),
        ];
    }

    public function isClassTeacher()
    {
        $teacher = $this->getTeacher();
        return $teacher && $teacher->is_class_teacher;
    }

    public function isGradeTeacher()
    {
        $teacher = $this->getTeacher();
        return $teacher && $teacher->is_grade_teacher;
    }

    /**
     * Check if current teacher is a primary teacher (teaches all subjects in one class)
     */
    public function isPrimaryTeacher(): bool
    {
        $teacher = $this->getTeacher();
        return $teacher && $teacher->isPrimaryTeacher();
    }

    /**
     * Check if current teacher is a secondary teacher (teaches specific subjects across classes)
     */
    public function isSecondaryTeacher(): bool
    {
        $teacher = $this->getTeacher();
        return $teacher && $teacher->isSecondaryTeacher();
    }

    /**
     * Get the primary teacher's class with students and all subjects.
     * Primary teachers own one class and teach all subjects.
     */
    public function getMyClassData(): ?array
    {
        $teacher = $this->getTeacher();
        if (!$teacher || !$teacher->isPrimaryTeacher()) {
            return null;
        }

        $classSection = null;

        // Get the class section from class teacher assignment or subject teachings
        if ($teacher->is_class_teacher && $teacher->class_section_id) {
            $classSection = ClassSection::with('grade')->find($teacher->class_section_id);
        }

        // Fallback: get from subject teachings
        if (!$classSection) {
            $currentYear = AcademicYear::where('is_active', true)->first();
            $teaching = $teacher->subjectTeachings()
                ->with('classSection.grade')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->first();
            $classSection = $teaching?->classSection;
        }

        if (!$classSection) {
            return null;
        }

        // Get students in this class
        $students = Student::where('class_section_id', $classSection->id)
            ->where('enrollment_status', 'active')
            ->orderBy('name')
            ->get();

        // Get all subjects for this grade (primary teacher teaches all)
        $currentYear = AcademicYear::where('is_active', true)->first();
        $taughtSubjects = $teacher->subjectTeachings()
            ->where('class_section_id', $classSection->id)
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->with('subject')
            ->get()
            ->pluck('subject')
            ->filter()
            ->sortBy('name')
            ->values();

        // Get gender counts
        $maleCount = $students->where('gender', 'male')->count();
        $femaleCount = $students->where('gender', 'female')->count();

        // Get today's attendance summary
        $attendanceSummary = $this->getMyClassAttendanceSummary($classSection->id);

        return [
            'classSection' => $classSection,
            'students' => $students,
            'subjects' => $taughtSubjects,
            'totalStudents' => $students->count(),
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount,
            'attendance' => $attendanceSummary,
        ];
    }

    /**
     * Get today's attendance summary for a class section
     */
    public function getMyClassAttendanceSummary(int $classSectionId): array
    {
        $today = now()->toDateString();

        if (!Schema::hasTable('attendances') && !Schema::hasTable('attendance')) {
            return ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0, 'recorded' => false];
        }

        $attendance = Attendance::where('class_section_id', $classSectionId)
            ->where('attendance_date', $today)
            ->get();

        if ($attendance->isEmpty()) {
            $totalStudents = Student::where('class_section_id', $classSectionId)
                ->where('enrollment_status', 'active')
                ->count();
            return ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => $totalStudents, 'recorded' => false];
        }

        return [
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'total' => $attendance->count(),
            'recorded' => true,
        ];
    }

    /**
     * Get recent results for the primary teacher's class
     */
    public function getMyClassRecentResults(): array
    {
        $teacher = $this->getTeacher();
        if (!$teacher || !$teacher->class_section_id) {
            return [];
        }

        if (!Schema::hasTable('results')) {
            return [];
        }

        $currentYear = AcademicYear::where('is_active', true)->first();

        // Get student IDs in this class section
        $studentIds = Student::where('class_section_id', $teacher->class_section_id)
            ->where('enrollment_status', 'active')
            ->pluck('id');

        if ($studentIds->isEmpty()) {
            return [];
        }

        $results = Result::whereIn('student_id', $studentIds)
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->selectRaw('subject_id, COUNT(*) as entries, AVG(marks) as avg_marks')
            ->groupBy('subject_id')
            ->with('subject')
            ->get();

        return $results->map(function ($r) {
            return [
                'subject' => $r->subject?->name ?? 'Unknown',
                'entries' => $r->entries,
                'avg_marks' => round($r->avg_marks, 1),
                'total_marks' => 100,
                'percentage' => round($r->avg_marks, 1),
            ];
        })->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getPageGuideAction(),

            Action::make('create_homework')
                ->label('Create Homework')
                ->icon('heroicon-o-document-plus')
                ->url($this->getTeacherHomeworkCreateUrl())
                ->visible($this->routeExists($this->getTeacherHomeworkCreateUrl())),

            Action::make('grade_submissions')
                ->label('Grade Submissions')
                ->icon('heroicon-o-pencil-square')
                ->url($this->getTeacherHomeworkSubmissionsUrl())
                ->visible($this->routeExists($this->getTeacherHomeworkSubmissionsUrl())),

            Action::make('view_my_classes')
                ->label('My Classes')
                ->icon('heroicon-o-user-group')
                ->url($this->getStudentsUrl())
                ->visible($this->routeExists($this->getStudentsUrl())),

            Action::make('record_results')
                ->label('Record Results')
                ->icon('heroicon-o-clipboard-document-list')
                ->url($this->getTeacherResultsCreateUrl())
                ->visible($this->routeExists($this->getTeacherResultsCreateUrl())),
        ];
    }

    protected function getGuideSlug(): string
    {
        return 'teacher-dashboard';
    }

    protected function routeExists(string $url): bool
    {
        try {
            // If URL starts with /admin/, just return true since fallback URLs are used
            if (str_starts_with($url, '/admin/')) {
                return true;
            }
            return Route::has($url);
        } catch (\Exception $e) {
            return true; // Return true to show buttons with fallback URLs
        }
    }

    protected function getTeacherHomeworkCreateUrl(): string
    {
        // Try different possible route names
        $possibleRoutes = [
            'filament.admin.resources.homework.create',
            'filament.admin.resources.homeworks.create',
            'filament.admin.resources.teacher-homework.create',
            'filament.admin.resources.teacherhomework.create',
            'filament.admin.resources.teacher-homeworks.create',
            'filament.admin.resources.teacherhomeworks.create',
            'filament.admin.resources.teacher_homework.create',
        ];

        foreach ($possibleRoutes as $routeName) {
            if (Route::has($routeName)) {
                return route($routeName);
            }
        }

        // Fallback to direct URL if no route is found
        return '/admin/homework/create';
    }

    protected function getTeacherHomeworkSubmissionsUrl(): string
    {
        $possibleRoutes = [
            'filament.admin.resources.homework-submissions.index',
            'filament.admin.resources.homeworksubmissions.index',
            'filament.admin.resources.teacher-homework-submissions.index',
            'filament.admin.resources.teacherhomeworksubmissions.index',
            'filament.admin.resources.teacher-homework-submission.index',
            'filament.admin.resources.teacherhomeworksubmission.index',
        ];

        foreach ($possibleRoutes as $routeName) {
            if (Route::has($routeName)) {
                return route($routeName);
            }
        }

        return '/admin/homework-submissions';
    }

    protected function getStudentsUrl(): string
    {
        $possibleRoutes = [
            'filament.admin.resources.students.index',
            'filament.admin.resources.student.index',
        ];

        foreach ($possibleRoutes as $routeName) {
            if (Route::has($routeName)) {
                return route($routeName);
            }
        }

        return '/admin/students';
    }

    protected function getTeacherResultsCreateUrl(): string
    {
        $possibleRoutes = [
            'filament.admin.resources.results.create',
            'filament.admin.resources.result.create',
            'filament.admin.resources.teacher-results.create',
            'filament.admin.resources.teacherresults.create',
            'filament.admin.resources.teacher-result.create',
            'filament.admin.resources.teacherresult.create',
        ];

        foreach ($possibleRoutes as $routeName) {
            if (Route::has($routeName)) {
                return route($routeName);
            }
        }

        return '/admin/results/create';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        return in_array($user->role_id, RoleConstants::teaching());
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        return in_array($user->role_id, RoleConstants::teaching());
    }
}
