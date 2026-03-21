<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\PaymentTransaction;
use App\Models\Event;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Result;
use App\Models\Attendance;
use App\Models\AcademicYear;
use App\Models\ReportCardComment;
use App\Models\Term;
use App\Constants\RoleConstants;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.parent-dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;

    public function getParentGuardian()
    {
        return ParentGuardian::where('user_id', Auth::id())->first();
    }

    public function getStudents()
    {
        $parentGuardian = $this->getParentGuardian();
        if (!$parentGuardian) return collect();

        return $parentGuardian->students()
            ->with(['grade', 'classSection', 'classSection.grade'])
            ->where('enrollment_status', 'active')
            ->get();
    }

    /**
     * Get per-child attendance summary for the current term
     */
    public function getAttendanceSummary()
    {
        $students = $this->getStudents();
        if ($students->isEmpty()) return collect();

        $activeTerm = Term::where('is_active', true)->first();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return $students->map(function ($student) use ($activeTerm, $activeYear) {
            $query = Attendance::where('student_id', $student->id);

            if ($activeYear) $query->where('academic_year_id', $activeYear->id);
            if ($activeTerm) $query->where('term_id', $activeTerm->id);

            $records = $query->get();
            $total = $records->count();
            $present = $records->where('status', 'present')->count();
            $absent = $records->where('status', 'absent')->count();
            $late = $records->where('status', 'late')->count();
            $sick = $records->where('status', 'sick')->count();
            $excused = $records->where('status', 'excused')->count();
            $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

            // Last 7 days attendance
            $recentAttendance = Attendance::where('student_id', $student->id)
                ->where('attendance_date', '>=', now()->subDays(7))
                ->orderBy('attendance_date', 'desc')
                ->get();

            return [
                'student' => $student,
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'sick' => $sick,
                'excused' => $excused,
                'rate' => $rate,
                'recent' => $recentAttendance,
            ];
        });
    }

    /**
     * Get per-child fee balance summary
     */
    public function getFeeSummary()
    {
        $students = $this->getStudents();
        if ($students->isEmpty()) return collect();

        return $students->map(function ($student) {
            $fees = StudentFee::where('student_id', $student->id)
                ->with(['feeStructure', 'term', 'academicYear'])
                ->orderBy('created_at', 'desc')
                ->get();

            $totalOwed = $fees->sum(fn ($f) => $f->feeStructure?->total_fee ?? 0);
            $totalPaid = $fees->sum('amount_paid');
            $totalBalance = $fees->sum('balance');
            $unpaidFees = $fees->where('payment_status', '!=', 'paid');

            return [
                'student' => $student,
                'total_owed' => $totalOwed,
                'total_paid' => $totalPaid,
                'total_balance' => $totalBalance,
                'unpaid_count' => $unpaidFees->count(),
                'fees' => $fees->take(3),
            ];
        });
    }

    /**
     * Get per-child academic performance overview
     */
    public function getAcademicPerformance()
    {
        $students = $this->getStudents();
        if ($students->isEmpty()) return collect();

        $activeTerm = Term::where('is_active', true)->first();

        return $students->map(function ($student) use ($activeTerm) {
            $resultsQuery = Result::where('student_id', $student->id)
                ->with('subject');

            if ($activeTerm) {
                $resultsQuery->where('term', $activeTerm->name ?? 'first');
            }

            $results = $resultsQuery->orderBy('created_at', 'desc')->get();

            $avgMarks = $results->count() > 0 ? round($results->avg('marks'), 1) : null;

            // Get top 3 and bottom 3 subjects
            $sorted = $results->sortByDesc('marks');
            $topSubjects = $sorted->take(3);
            $bottomSubjects = $sorted->reverse()->take(3);

            // Grade distribution
            $gradeDistribution = $results->groupBy('grade')->map->count()->sortKeys();

            return [
                'student' => $student,
                'results' => $results,
                'average' => $avgMarks,
                'total_subjects' => $results->count(),
                'top_subjects' => $topSubjects,
                'bottom_subjects' => $bottomSubjects,
                'grade_distribution' => $gradeDistribution,
                'highest' => $results->max('marks'),
                'lowest' => $results->min('marks'),
            ];
        });
    }

    /**
     * Get recent homework per child with submission status
     */
    public function getHomeworkPerChild()
    {
        $students = $this->getStudents();
        if ($students->isEmpty()) return collect();

        return $students->map(function ($student) {
            $homework = Homework::where('grade_id', $student->grade_id)
                ->where('status', 'active')
                ->with(['subject', 'assignedBy'])
                ->orderBy('due_date', 'desc')
                ->take(5)
                ->get();

            // Check submission status for each
            $homeworkWithStatus = $homework->map(function ($hw) use ($student) {
                $submission = HomeworkSubmission::where('homework_id', $hw->id)
                    ->where('student_id', $student->id)
                    ->first();

                return [
                    'homework' => $hw,
                    'submitted' => $submission !== null,
                    'submission' => $submission,
                    'marks' => $submission?->marks,
                    'status' => $submission?->status ?? ($hw->due_date->isPast() ? 'overdue' : 'pending'),
                ];
            });

            $totalAssigned = Homework::where('grade_id', $student->grade_id)
                ->where('status', 'active')
                ->count();
            $totalSubmitted = HomeworkSubmission::where('student_id', $student->id)->count();

            return [
                'student' => $student,
                'homework' => $homeworkWithStatus,
                'total_assigned' => $totalAssigned,
                'total_submitted' => $totalSubmitted,
            ];
        });
    }

    public function getFeePayments()
    {
        $parentGuardian = $this->getParentGuardian();
        if (!$parentGuardian) return collect();

        $studentIds = $parentGuardian->students()
            ->where('enrollment_status', 'active')
            ->pluck('id')->toArray();

        if (empty($studentIds)) return collect();

        $studentFeeIds = StudentFee::whereIn('student_id', $studentIds)->pluck('id')->toArray();
        if (empty($studentFeeIds)) return collect();

        return PaymentTransaction::whereIn('student_fee_id', $studentFeeIds)
            ->with(['studentFee.student', 'studentFee.student.grade'])
            ->latest('transaction_date')
            ->take(5)
            ->get();
    }

    /**
     * Get available report cards for each child
     */
    public function getReportCards()
    {
        $students = $this->getStudents();
        if ($students->isEmpty()) return collect();

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return collect();

        $terms = Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get();

        return $students->map(function ($student) use ($terms, $activeYear) {
            $available = $terms->map(function ($term) use ($student, $activeYear) {
                $comment = ReportCardComment::where('student_id', $student->id)
                    ->where('term_id', $term->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->first();

                $isGenerated = $comment && $comment->generated_at;

                // Also check if there are results for this term
                $hasResults = Result::where('student_id', $student->id)
                    ->where('year', $activeYear->name)
                    ->exists();

                return [
                    'term' => $term,
                    'is_generated' => $isGenerated,
                    'has_results' => $hasResults,
                    'generated_at' => $comment?->generated_at,
                    'download_url' => $isGenerated
                        ? route('report-cards.generate', ['student' => $student->id, 'term' => $term->id, 'year' => $activeYear->name])
                        : null,
                    'preview_url' => $isGenerated
                        ? route('report-cards.preview', ['student' => $student->id, 'term' => $term->id, 'year' => $activeYear->name])
                        : null,
                ];
            });

            return [
                'student' => $student,
                'terms' => $available,
                'has_any' => $available->where('is_generated', true)->isNotEmpty(),
            ];
        });
    }

    public function getUpcomingEvents()
    {
        $parentGuardian = $this->getParentGuardian();
        if (!$parentGuardian) return collect();

        $gradeIds = $parentGuardian->students()
            ->where('enrollment_status', 'active')
            ->pluck('grade_id')->unique()->toArray();

        return Event::where('start_date', '>=', now())
            ->where(function ($query) use ($gradeIds) {
                $query->whereIn('applicable_to', $gradeIds)
                    ->orWhere('applicable_to', 'all')
                    ->orWhereNull('applicable_to');
            })
            ->orderBy('start_date')
            ->take(5)
            ->get();
    }

    public function getDashboardStats()
    {
        $parentGuardian = $this->getParentGuardian();
        if (!$parentGuardian) {
            return [
                'children_count' => 0,
                'total_balance' => 0,
                'attendance_rate' => 0,
                'pending_homework' => 0,
                'overdue_homework' => 0,
                'recent_results' => 0,
                'upcoming_events' => 0,
            ];
        }

        $studentIds = $parentGuardian->students()
            ->where('enrollment_status', 'active')
            ->pluck('id')->toArray();

        $gradeIds = $parentGuardian->students()
            ->where('enrollment_status', 'active')
            ->pluck('grade_id')->unique()->toArray();

        // Total outstanding balance
        $totalBalance = StudentFee::whereIn('student_id', $studentIds)
            ->where('payment_status', '!=', 'paid')
            ->sum('balance');

        // Attendance rate this term
        $activeTerm = Term::where('is_active', true)->first();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $attendanceQuery = Attendance::whereIn('student_id', $studentIds);
        if ($activeYear) $attendanceQuery->where('academic_year_id', $activeYear->id);
        if ($activeTerm) $attendanceQuery->where('term_id', $activeTerm->id);

        $totalAttendance = $attendanceQuery->count();
        $presentCount = (clone $attendanceQuery)->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0;

        return [
            'children_count' => count($studentIds),
            'total_balance' => $totalBalance,
            'attendance_rate' => $attendanceRate,
            'pending_homework' => Homework::whereIn('grade_id', $gradeIds)
                ->where('status', 'active')
                ->where('due_date', '>=', now())
                ->count(),
            'overdue_homework' => Homework::whereIn('grade_id', $gradeIds)
                ->where('status', 'active')
                ->where('due_date', '<', now())
                ->count(),
            'recent_results' => Result::whereIn('student_id', $studentIds)
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'upcoming_events' => Event::where('start_date', '>=', now())
                ->where('start_date', '<=', now()->addDays(14))
                ->where(function ($query) use ($gradeIds) {
                    $query->whereIn('applicable_to', $gradeIds)
                        ->orWhere('applicable_to', 'all')
                        ->orWhereNull('applicable_to');
                })
                ->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_children')
                ->label('My Children')
                ->icon('heroicon-o-academic-cap')
                ->color('info')
                ->url(route('filament.admin.resources.students.index'))
                ->tooltip('View and manage your children\'s information'),

            Action::make('view_homework')
                ->label('All Homework')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->url(route('filament.admin.resources.homework.index'))
                ->tooltip('View all homework assignments for your children'),

            Action::make('view_results')
                ->label('Academic Results')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success')
                ->url(route('filament.admin.resources.results.index'))
                ->tooltip('View your children\'s academic results'),

            Action::make('view_payments')
                ->label('Fee Payments')
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                ->url(route('filament.admin.resources.payment-transactions.index'))
                ->tooltip('View fee payment history and statements'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'parentGuardian' => $this->getParentGuardian(),
            'students' => $this->getStudents(),
            'dashboardStats' => $this->getDashboardStats(),
            'attendanceSummary' => $this->getAttendanceSummary(),
            'feeSummary' => $this->getFeeSummary(),
            'academicPerformance' => $this->getAcademicPerformance(),
            'homeworkPerChild' => $this->getHomeworkPerChild(),
            'feePayments' => $this->getFeePayments(),
            'reportCards' => $this->getReportCards(),
            'upcomingEvents' => $this->getUpcomingEvents(),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::PARENT ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role_id === RoleConstants::PARENT ?? false;
    }

    public function getTitle(): string
    {
        $parentGuardian = $this->getParentGuardian();
        return $parentGuardian ? "Welcome, {$parentGuardian->name}" : "Parent Dashboard";
    }

    public function getHeading(): string
    {
        $parentGuardian = $this->getParentGuardian();
        $stats = $this->getDashboardStats();

        if ($parentGuardian && $stats['children_count'] > 0) {
            $childrenText = $stats['children_count'] === 1 ? 'child' : 'children';
            return "Dashboard - {$stats['children_count']} {$childrenText} enrolled";
        }

        return "Parent Dashboard";
    }

    public function getSubheading(): ?string
    {
        $stats = $this->getDashboardStats();

        if ($stats['children_count'] === 0) {
            return "No children enrolled at this time";
        }

        $activeTerm = Term::where('is_active', true)->first();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $parts = [];
        if ($activeYear) $parts[] = $activeYear->name;
        if ($activeTerm) $parts[] = $activeTerm->name . ' Term';

        return !empty($parts) ? implode(' - ', $parts) : null;
    }
}
