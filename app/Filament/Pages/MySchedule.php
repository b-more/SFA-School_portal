<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\Homework;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\TimetableEntry;
use App\Models\TimetablePeriod;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MySchedule extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static string $view = 'filament.pages.my-schedule';
    protected static ?string $navigationLabel = 'My Schedule';
    protected static ?string $navigationGroup = 'Teaching';
    protected static ?int $navigationSort = 2;

    public function getTeacher(): ?Teacher
    {
        $user = Auth::user();
        return Teacher::where('user_id', $user->id)
            ->with([
                'subjectTeachings.subject',
                'subjectTeachings.classSection.grade',
                'subjectTeachings.classSection.students',
                'grade',
                'classSection',
            ])
            ->first();
    }

    public function getAcademicYear(): ?AcademicYear
    {
        return AcademicYear::current();
    }

    public function getCurrentTerm(): ?Term
    {
        return Term::current();
    }

    public function getMyClasses(): Collection
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return collect();
        }

        $currentYear = $this->getAcademicYear();

        return $teacher->subjectTeachings()
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->with(['subject', 'classSection.grade', 'classSection.students'])
            ->get()
            ->groupBy('class_section_id');
    }

    public function getMySubjects(): Collection
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return collect();
        }

        return $teacher->subjects ?? collect();
    }

    /**
     * Get total students across all teaching assignments
     */
    public function getTotalStudents(): int
    {
        $classes = $this->getMyClasses();

        return $classes->sum(function ($teachings) {
            $classSection = $teachings->first()->classSection;
            return $classSection ? $classSection->students->where('enrollment_status', 'active')->count() : 0;
        });
    }

    /**
     * Get homework statistics for this teacher
     */
    public function getHomeworkStats(): array
    {
        $teacher = $this->getTeacher();
        $currentYear = $this->getAcademicYear();

        if (!$teacher || !$currentYear) {
            return [
                'total' => 0,
                'active' => 0,
                'pending_review' => 0,
                'recent' => collect(),
            ];
        }

        $homework = Homework::where('assigned_by', $teacher->id)
            ->where('academic_year_id', $currentYear->id)
            ->get();

        $now = now();

        return [
            'total' => $homework->count(),
            'active' => $homework->where('due_date', '>=', $now->toDateString())->count(),
            'past_due' => $homework->where('due_date', '<', $now->toDateString())->count(),
            'recent' => Homework::where('assigned_by', $teacher->id)
                ->where('academic_year_id', $currentYear->id)
                ->with('subject')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Get the timetable for the teacher's class (if class teacher) or their teaching timetable
     */
    public function getTimetableData(): array
    {
        $teacher = $this->getTeacher();
        $currentYear = $this->getAcademicYear();

        if (!$teacher || !$currentYear) {
            return [];
        }

        if ($teacher->is_class_teacher && $teacher->class_section_id) {
            return TimetableEntry::getClassTimetable($teacher->class_section_id, $currentYear->id);
        }

        return TimetableEntry::getTeacherTimetable($teacher->id, $currentYear->id);
    }

    /**
     * Get the days of the week for timetable display
     */
    public function getTimetableDays(): array
    {
        return TimetableEntry::DAYS;
    }

    /**
     * Get today's day name
     */
    public function getTodayDay(): string
    {
        return now()->format('l');
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
