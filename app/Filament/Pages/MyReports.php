<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSection;
use App\Models\Homework;
use App\Models\Result;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Term;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MyReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.my-reports';
    protected static ?string $navigationLabel = 'My Reports';
    protected static ?string $navigationGroup = 'Teaching';
    protected static ?int $navigationSort = 3;

    public function getTeacher(): ?Teacher
    {
        $user = Auth::user();
        return Teacher::where('user_id', $user->id)
            ->with(['classSection.grade', 'grade'])
            ->first();
    }

    protected function getCurrentAcademicYear(): ?AcademicYear
    {
        return AcademicYear::current();
    }

    protected function getCurrentTerm(): ?Term
    {
        return Term::current();
    }

    /**
     * Get class section IDs for current academic year only
     */
    public function getClassSectionIds(): array
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return [];
        }

        $currentYear = $this->getCurrentAcademicYear();
        $query = $teacher->subjectTeachings();

        if ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }

        $ids = $query->pluck('class_section_id')->unique()->toArray();

        // Include class teacher's section
        if ($teacher->is_class_teacher && $teacher->class_section_id && !in_array($teacher->class_section_id, $ids)) {
            $ids[] = $teacher->class_section_id;
        }

        return $ids;
    }

    /**
     * Student stats with enrollment breakdown
     */
    public function getStudentStats(): array
    {
        $classSectionIds = $this->getClassSectionIds();

        if (empty($classSectionIds)) {
            return ['total' => 0, 'boys' => 0, 'girls' => 0, 'by_class' => collect()];
        }

        $students = Student::whereIn('class_section_id', $classSectionIds)
            ->where('enrollment_status', 'active')
            ->get();

        $byClass = Student::whereIn('students.class_section_id', $classSectionIds)
            ->where('students.enrollment_status', 'active')
            ->join('class_sections', 'students.class_section_id', '=', 'class_sections.id')
            ->join('grades', 'class_sections.grade_id', '=', 'grades.id')
            ->select(
                'class_sections.id',
                'grades.name as grade_name',
                'class_sections.name as section_name',
                DB::raw("SUM(CASE WHEN students.gender = 'male' THEN 1 ELSE 0 END) as boys"),
                DB::raw("SUM(CASE WHEN students.gender = 'female' THEN 1 ELSE 0 END) as girls"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('class_sections.id', 'grades.name', 'class_sections.name')
            ->orderBy('grades.name')
            ->get();

        return [
            'total' => $students->count(),
            'boys' => $students->where('gender', 'male')->count(),
            'girls' => $students->where('gender', 'female')->count(),
            'by_class' => $byClass,
        ];
    }

    /**
     * Attendance stats with rates
     */
    public function getAttendanceStats(): array
    {
        $classSectionIds = $this->getClassSectionIds();
        $currentYear = $this->getCurrentAcademicYear();
        $currentTerm = $this->getCurrentTerm();

        if (empty($classSectionIds)) {
            return [
                'today' => ['present' => 0, 'absent' => 0, 'late' => 0, 'sick' => 0, 'total_students' => 0, 'marked' => false],
                'term' => ['present' => 0, 'absent' => 0, 'late' => 0, 'sick' => 0, 'total_records' => 0, 'days_marked' => 0, 'rate' => 0],
                'by_class' => collect(),
            ];
        }

        $totalStudents = Student::whereIn('class_section_id', $classSectionIds)
            ->where('enrollment_status', 'active')
            ->count();

        // Today's attendance
        $todayRecords = Attendance::whereIn('class_section_id', $classSectionIds)
            ->whereDate('attendance_date', today())
            ->get();

        $todayStats = [
            'present' => $todayRecords->where('status', 'present')->count(),
            'absent' => $todayRecords->where('status', 'absent')->count(),
            'late' => $todayRecords->where('status', 'late')->count(),
            'sick' => $todayRecords->where('status', 'sick')->count(),
            'total_students' => $totalStudents,
            'marked' => $todayRecords->isNotEmpty(),
        ];

        // Term attendance
        $termQuery = Attendance::whereIn('class_section_id', $classSectionIds);
        if ($currentYear) {
            $termQuery->where('academic_year_id', $currentYear->id);
        }
        if ($currentTerm) {
            $termQuery->where('term_id', $currentTerm->id);
        }
        $termRecords = $termQuery->get();

        $daysMarked = $termRecords->pluck('attendance_date')->unique()->count();
        $presentCount = $termRecords->whereIn('status', ['present', 'late'])->count();
        $totalRecords = $termRecords->count();
        $attendanceRate = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0;

        $termStats = [
            'present' => $termRecords->where('status', 'present')->count(),
            'absent' => $termRecords->where('status', 'absent')->count(),
            'late' => $termRecords->where('status', 'late')->count(),
            'sick' => $termRecords->where('status', 'sick')->count(),
            'total_records' => $totalRecords,
            'days_marked' => $daysMarked,
            'rate' => $attendanceRate,
        ];

        // Per-class attendance rates this term
        $byClass = DB::table('attendances')
            ->join('class_sections', 'attendances.class_section_id', '=', 'class_sections.id')
            ->join('grades', 'class_sections.grade_id', '=', 'grades.id')
            ->whereIn('attendances.class_section_id', $classSectionIds)
            ->when($currentYear, fn($q) => $q->where('attendances.academic_year_id', $currentYear->id))
            ->when($currentTerm, fn($q) => $q->where('attendances.term_id', $currentTerm->id))
            ->select(
                'class_sections.id',
                'grades.name as grade_name',
                'class_sections.name as section_name',
                DB::raw('COUNT(*) as total_records'),
                DB::raw("SUM(CASE WHEN attendances.status IN ('present', 'late') THEN 1 ELSE 0 END) as present_count"),
                DB::raw("SUM(CASE WHEN attendances.status = 'absent' THEN 1 ELSE 0 END) as absent_count"),
                DB::raw("SUM(CASE WHEN attendances.status = 'late' THEN 1 ELSE 0 END) as late_count"),
                DB::raw("SUM(CASE WHEN attendances.status = 'sick' THEN 1 ELSE 0 END) as sick_count"),
                DB::raw('COUNT(DISTINCT attendances.attendance_date) as days_marked')
            )
            ->groupBy('class_sections.id', 'grades.name', 'class_sections.name')
            ->get()
            ->map(function ($row) {
                $row->rate = $row->total_records > 0 ? round(($row->present_count / $row->total_records) * 100, 1) : 0;
                return $row;
            });

        return [
            'today' => $todayStats,
            'term' => $termStats,
            'by_class' => $byClass,
        ];
    }

    /**
     * Homework stats
     */
    public function getHomeworkStats(): array
    {
        $teacher = $this->getTeacher();
        $currentYear = $this->getCurrentAcademicYear();

        if (!$teacher) {
            return ['total' => 0, 'active' => 0, 'past_due' => 0, 'with_submissions' => 0, 'by_subject' => collect()];
        }

        $query = Homework::where('assigned_by', $teacher->id);
        if ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }
        $homework = $query->with('subject')->get();

        $now = now();

        $bySubject = $homework->groupBy(fn($hw) => $hw->subject?->name ?? 'Unknown')
            ->map(fn($group) => [
                'total' => $group->count(),
                'active' => $group->where('due_date', '>=', $now)->count(),
            ])
            ->sortByDesc('total');

        return [
            'total' => $homework->count(),
            'active' => $homework->where('due_date', '>=', $now->toDateString())->count(),
            'past_due' => $homework->where('due_date', '<', $now->toDateString())->count(),
            'by_subject' => $bySubject,
        ];
    }

    /**
     * Academic performance by subject
     */
    public function getPerformanceBySubject()
    {
        $teacher = $this->getTeacher();
        $currentYear = $this->getCurrentAcademicYear();

        if (!$teacher || !$currentYear) {
            return collect();
        }

        $classSectionIds = $this->getClassSectionIds();
        $subjectIds = $teacher->subjectTeachings()
            ->where('academic_year_id', $currentYear->id)
            ->pluck('subject_id')
            ->unique()
            ->toArray();

        if (empty($classSectionIds) || empty($subjectIds)) {
            return collect();
        }

        return DB::table('results')
            ->join('students', 'results.student_id', '=', 'students.id')
            ->join('subjects', 'results.subject_id', '=', 'subjects.id')
            ->whereIn('students.class_section_id', $classSectionIds)
            ->whereIn('results.subject_id', $subjectIds)
            ->where('results.academic_year_id', $currentYear->id)
            ->select(
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                'results.exam_type',
                DB::raw('AVG(results.marks) as avg_marks'),
                DB::raw('MAX(results.marks) as max_marks'),
                DB::raw('MIN(results.marks) as min_marks'),
                DB::raw('COUNT(DISTINCT results.student_id) as student_count'),
                DB::raw("SUM(CASE WHEN results.marks >= 50 THEN 1 ELSE 0 END) as pass_count"),
                DB::raw('COUNT(*) as total_count')
            )
            ->groupBy('subjects.id', 'subjects.name', 'results.exam_type')
            ->orderBy('subjects.name')
            ->get()
            ->map(function ($row) {
                $row->pass_rate = $row->total_count > 0 ? round(($row->pass_count / $row->total_count) * 100, 1) : 0;
                return $row;
            });
    }

    /**
     * Overall class performance
     */
    public function getPerformanceByClass()
    {
        $teacher = $this->getTeacher();
        $currentYear = $this->getCurrentAcademicYear();

        if (!$teacher || !$currentYear) {
            return collect();
        }

        $classSectionIds = $this->getClassSectionIds();

        if (empty($classSectionIds)) {
            return collect();
        }

        return DB::table('results')
            ->join('students', 'results.student_id', '=', 'students.id')
            ->join('class_sections', 'students.class_section_id', '=', 'class_sections.id')
            ->join('grades', 'class_sections.grade_id', '=', 'grades.id')
            ->whereIn('students.class_section_id', $classSectionIds)
            ->where('results.academic_year_id', $currentYear->id)
            ->select(
                'class_sections.id',
                'grades.name as grade_name',
                'class_sections.name as section_name',
                DB::raw('AVG(results.marks) as avg_marks'),
                DB::raw('MAX(results.marks) as max_marks'),
                DB::raw('MIN(results.marks) as min_marks'),
                DB::raw('COUNT(DISTINCT students.id) as student_count'),
                DB::raw("SUM(CASE WHEN results.marks >= 50 THEN 1 ELSE 0 END) as pass_count"),
                DB::raw('COUNT(*) as total_count')
            )
            ->groupBy('class_sections.id', 'grades.name', 'class_sections.name')
            ->get()
            ->map(function ($row) {
                $row->pass_rate = $row->total_count > 0 ? round(($row->pass_count / $row->total_count) * 100, 1) : 0;
                return $row;
            });
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id, RoleConstants::teaching()) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()?->role_id, RoleConstants::teaching()) ?? false;
    }
}
