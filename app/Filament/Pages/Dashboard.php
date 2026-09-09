<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Result;
use App\Models\SmsLog;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeStructure;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Constants\RoleConstants;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?int $navigationSort = 1;

    /**
     * Reactive filter for the fee-collection section. Bound to the term
     * picker in the view via wire:model.live so the section reloads without
     * a full page refresh. Defaults to the current term on first mount.
     */
    public ?int $feeTermId = null;

    // Add access control methods
    public static function canAccess(): bool
    {
        $u = auth()->user();
        if (! $u) return false;
        if ($u->isFinanceLocked()) return false;  // Manager sees ManagerDashboard instead
        return in_array($u->role_id, [RoleConstants::ADMIN, RoleConstants::ACCOUNTANT], true);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $u = auth()->user();
        if (! $u) return false;
        if ($u->isFinanceLocked()) return false;  // Manager sees ManagerDashboard instead
        return in_array($u->role_id, [RoleConstants::ADMIN, RoleConstants::ACCOUNTANT], true);
    }

    public function mount()
    {
        $user = auth()->user();

        if ($user?->role_id === RoleConstants::DRIVER) {
            redirect()->to(DriverDashboard::getUrl());
            return;
        }

        if (!static::canAccess()) {
            abort(403);
        }
    }

    public function getStats(): array
    {
        // Get student counts
        $totalStudents = Student::count();
        $activeStudents = Student::where('enrollment_status', 'active')->count();
        $inactiveStudents = Student::where('enrollment_status', 'inactive')->count();

        // Get staff counts - CORRECTED TO USE ROLE_ID
        $totalStaff = Employee::count();
        $teacherCount = Employee::where('role_id', RoleConstants::TEACHER)->count(); // Use role_id instead of role
        $adminCount = Employee::where('role_id', RoleConstants::ADMIN)->count(); // Use role_id instead of role

        // Get fee collection statistics - COMPLETELY REVISED
        // First, get total amount paid from all student fees
        $totalFeesCollected = StudentFee::sum('amount_paid');

        // Calculate total expected from all fee structures assigned to students
        $totalFeesExpected = StudentFee::join('fee_structures', 'student_fees.fee_structure_id', '=', 'fee_structures.id')
            ->sum('fee_structures.total_fee');

        // If no expected fees yet (no fee structures assigned)
        if ($totalFeesExpected == 0) {
            // Use sum of paid + balance as expected
            $totalFeesExpected = StudentFee::sum(DB::raw('amount_paid + balance'));
        }

        // If still no expected fees but some collections exist
        if ($totalFeesExpected == 0 && $totalFeesCollected > 0) {
            // Estimate expected fees as 3x the collected amount (assuming ~33% collection rate)
            $totalFeesExpected = $totalFeesCollected * 3;
        }

        // If no data at all, use placeholder
        if ($totalFeesExpected == 0) {
            $totalFeesExpected = 1; // Avoid division by zero
        }

        $collectionRate = round(($totalFeesCollected / $totalFeesExpected) * 100);

        // Get homework statistics
        $totalHomework = Homework::count();
        $activeHomework = Homework::where('status', 'active')->count();
        $pendingSubmissions = HomeworkSubmission::where('status', 'submitted')->count();

        // Get SMS statistics
        $monthlySMSCount = SmsLog::whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->count();
        $monthlySMSCost = SmsLog::whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->sum('cost');

        return [
            Stat::make('Total Students', $totalStudents)
                ->description("Active: $activeStudents | Inactive: $inactiveStudents")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->chart([7, 2, 10, 3, 15, 4, $activeStudents])
                ->color('primary'),

            Stat::make('Total Staff', $totalStaff)
                ->description("Teachers: $teacherCount | Admin: $adminCount")
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([2, 3, 5, 4, 5, 6, $teacherCount])
                ->color('success'),

            Stat::make('Fee Collection', "ZMW " . number_format($totalFeesCollected, 2))
                ->description("$collectionRate% Collected")
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([15, 30, 20, 45, 35, 40, $collectionRate])
                ->color($collectionRate >= 70 ? 'success' : ($collectionRate >= 40 ? 'warning' : 'danger')),

            Stat::make('Homework', $totalHomework)
                ->description("Active: $activeHomework | Pending: $pendingSubmissions")
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([5, 10, 15, 20, 15, 10, $activeHomework])
                ->color('info'),

            Stat::make('SMS Notifications', $monthlySMSCount)
                ->description("Cost: ZMW " . number_format($monthlySMSCost, 2))
                ->descriptionIcon('heroicon-m-chat-bubble-left')
                ->chart([10, 20, 30, 40, 20, 10, $monthlySMSCount])
                ->color('warning'),
        ];
    }

    public function getQuickActions(): array
    {
        $actions = [];

        // Only add routes that exist
        if ($this->routeExists('filament.admin.resources.students.create')) {
            $actions[] = [
                'title' => 'Add Student',
                'icon' => 'heroicon-o-user-plus',
                'color' => 'primary',
                'url' => route('filament.admin.resources.students.create'),
            ];
        }

        if ($this->routeExists('filament.admin.resources.student-fees.create')) {
            $actions[] = [
                'title' => 'Record Payment',
                'icon' => 'heroicon-o-banknotes',
                'color' => 'success',
                'url' => route('filament.admin.resources.student-fees.create'),
            ];
        }

        // Add Fee Statements Quick Action
        if ($this->routeExists('fee-statements.index')) {
            $actions[] = [
                'title' => 'Fee Statements',
                'icon' => 'heroicon-o-document-chart-bar',
                'color' => 'orange',
                'url' => route('fee-statements.index'),
            ];
        }

        // Check for different homework route variations
        if ($this->routeExists('filament.admin.resources.homework.create')) {
            $actions[] = [
                'title' => 'New Homework',
                'icon' => 'heroicon-o-document-plus',
                'color' => 'warning',
                'url' => route('filament.admin.resources.homework.create'),
            ];
        } elseif ($this->routeExists('filament.admin.resources.homeworks.create')) {
            $actions[] = [
                'title' => 'New Homework',
                'icon' => 'heroicon-o-document-plus',
                'color' => 'warning',
                'url' => route('filament.admin.resources.homeworks.create'),
            ];
        }

        // Check for different SMS route variations
        if ($this->routeExists('filament.admin.resources.sms-logs.create')) {
            $actions[] = [
                'title' => 'Send SMS',
                'icon' => 'heroicon-o-paper-airplane',
                'color' => 'danger',
                'url' => route('filament.admin.resources.sms-logs.create'),
            ];
        } elseif ($this->routeExists('filament.admin.resources.teacher-assignments.index')) {
            $actions[] = [
                'title' => 'Send SMS',
                'icon' => 'heroicon-o-paper-airplane',
                'color' => 'danger',
                'url' => route('filament.admin.resources.teacher-assignments.index'),
            ];
        }

        if ($this->routeExists('filament.admin.resources.employees.create')) {
            $actions[] = [
                'title' => 'Add Teacher',
                'icon' => 'heroicon-o-academic-cap',
                'color' => 'info',
                'url' => route('filament.admin.resources.employees.create'),
            ];
        }

        // Add Quick Guide action
        if ($this->routeExists('quick-guide.view')) {
            $actions[] = [
                'title' => 'Quick Guide',
                'icon' => 'heroicon-o-book-open',
                'color' => 'violet',
                'url' => route('quick-guide.view'),
            ];
        }

        return $actions;
    }

    protected function routeExists($name)
    {
        return Route::has($name);
    }

    public function getRecentActivity(): array
    {
        // Get recent students with proper formatting
        $recentStudents = Student::with('grade')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'grade' => $student->grade?->name ?? 'Unknown Grade',
                    'type' => 'student',
                    'time' => $student->created_at->diffForHumans(),
                    'description' => "New student enrolled in {$student->grade?->name}"
                ];
            });

        // Get recent fee payments with proper formatting
        $recentPayments = StudentFee::where('payment_status', '!=', 'unpaid')
            ->with(['student', 'feeStructure.grade'])
            ->latest()
            ->take(10)
            ->get()
            ->filter(fn ($payment) => $payment->student !== null)
            ->take(5)
            ->map(function ($payment) {
                $gradeName = $payment->feeStructure?->grade?->name ?? 'Unknown Grade';
                return [
                    'id' => $payment->id,
                    'name' => $payment->student?->name ?? 'Unknown student',
                    'grade' => $gradeName,
                    'type' => 'payment',
                    'amount' => $payment->amount_paid,
                    'time' => $payment->updated_at->diffForHumans(),
                    'description' => "Payment of ZMW {$payment->amount_paid} for {$gradeName}",
                ];
            })
            ->values();

        // Get recent homework submissions with proper formatting
        $recentSubmissions = HomeworkSubmission::with(['student', 'homework.grade'])
            ->latest()
            ->take(10)
            ->get()
            ->filter(fn ($submission) => $submission->student !== null && $submission->homework !== null)
            ->take(5)
            ->map(function ($submission) {
                $title = $submission->homework?->title ?? 'Untitled homework';
                return [
                    'id' => $submission->id,
                    'name' => $submission->student?->name ?? 'Unknown student',
                    'homework' => $title,
                    'grade' => $submission->homework?->grade?->name ?? 'Unknown Grade',
                    'type' => 'submission',
                    'time' => $submission->created_at->diffForHumans(),
                    'description' => "Homework submission: {$title}",
                ];
            })
            ->values();

        // Get recent SMS logs with proper formatting
        $recentSms = SmsLog::latest()
            ->take(5)
            ->get()
            ->map(function ($sms) {
                return [
                    'id' => $sms->id,
                    'recipient' => $sms->recipient,
                    'type' => 'sms',
                    'status' => $sms->status,
                    'time' => $sms->created_at->diffForHumans(),
                    'description' => "SMS sent to {$sms->recipient}"
                ];
            });

        // Combine all activities and sort by time
        $allActivities = collect([])
            ->merge($recentStudents)
            ->merge($recentPayments)
            ->merge($recentSubmissions)
            ->merge($recentSms)
            ->sortByDesc('time')
            ->take(10);

        return [
            'students' => $recentStudents,
            'payments' => $recentPayments,
            'submissions' => $recentSubmissions,
            'sms' => $recentSms,
            'all' => $allActivities
        ];
    }

    public function getUpcomingEvents(): array
    {
        // Convert the collection to an array
        return Event::where('start_date', '>=', now())
                ->orderBy('start_date')
                ->take(5)
                ->get()
                ->toArray();
    }

    public function getChartData(): array
    {
        // Get enrollment by grade - JOIN with grades table to get grade name
        $gradeData = Student::join('grades', 'students.grade_id', '=', 'grades.id')
                    ->where('students.enrollment_status', 'active')
                    ->select('grades.name as grade', DB::raw('count(*) as count'))
                    ->groupBy('grades.id', 'grades.name')
                    ->orderBy('grades.level')
                    ->get()
                    ->toArray();

        // Get fee collection by grade - JOIN with grades table
        $feeData = StudentFee::join('students', 'student_fees.student_id', '=', 'students.id')
                    ->join('grades', 'students.grade_id', '=', 'grades.id')
                    ->select('grades.name as grade',
                             DB::raw('sum(student_fees.amount_paid) as collected'),
                             DB::raw('sum(student_fees.balance) as balance'))
                    ->groupBy('grades.id', 'grades.name')
                    ->orderBy('grades.level')
                    ->get()
                    ->toArray();

        // Get subject performance
        $resultData = Result::join('subjects', 'results.subject_id', '=', 'subjects.id')
                    ->select('subjects.name', DB::raw('avg(results.marks) as average'))
                    ->groupBy('subjects.id', 'subjects.name')
                    ->orderBy('average', 'desc')
                    ->take(5)
                    ->get()
                    ->toArray();

        return [
            'gradeData' => $gradeData,
            'feeData' => $feeData,
            'resultData' => $resultData,
        ];
    }

    protected function getCurrentTerm(): string
    {
        $month = date('n');

        if ($month >= 1 && $month <= 4) {
            return 'Term 1';
        } elseif ($month >= 5 && $month <= 8) {
            return 'Term 2';
        } else {
            return 'Term 3';
        }
    }

    /**
     * Get the active academic year name
     */
    public function getActiveAcademicYear(): string
    {
        $academicYear = AcademicYear::current();
        return $academicYear?->name ?? now()->year;
    }

    /**
     * Get academic year and term display string
     */
    public function getAcademicYearTermDisplay(): string
    {
        $academicYear = $this->getActiveAcademicYear();
        $term = $this->getCurrentTerm();
        return "{$academicYear} - {$term}";
    }

    public function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
            'quickActions' => $this->getQuickActions(),
            'recentActivity' => $this->getRecentActivity(),
            'upcomingEvents' => $this->getUpcomingEvents(),
            'chartData' => $this->getChartData(),
            'compactStats' => $this->getCompactStats(),
            'attendanceStats' => $this->getAttendanceStats(),
            'financialSummary' => $this->getFinancialSummary(),
            'topPerformers' => $this->getTopPerformers(),
            'pendingTasks' => $this->getPendingTasks(),
            'recentPayments' => $this->getRecentPayments(),
            'pendingSubmissions' => $this->getPendingSubmissions(),
            'overdueFeees' => $this->getOverdueFees(),
            'genderStats' => $this->getGenderStats(),
            'gradeCapacity' => $this->getGradeCapacity(),
            'monthlyComparison' => $this->getMonthlyComparison(),
            'attendanceRegister' => $this->getAttendanceRegister(),
            'feeDashboard' => $this->getFeeDashboard(),
        ];
    }

    /**
     * Term-scoped fee collection dashboard: KPIs, per-class heat-map,
     * top defaulters. Feeds the new "Fee Collection · {Term}" section
     * on the admin dashboard. Reactively rebound to $feeTermId so the
     * term picker in the view swaps the whole section live.
     */
    public function getFeeDashboard(): array
    {
        // Default the picker to the current term the first time the page
        // renders. After that, the wired property carries the choice.
        if ($this->feeTermId === null) {
            $current = Term::where('is_current', true)->first();
            $this->feeTermId = $current?->id;
        }

        $term = $this->feeTermId ? Term::find($this->feeTermId) : null;
        if (! $term) {
            return ['empty' => true, 'term' => null, 'termChoices' => $this->feeTermChoices()];
        }

        // Term-scoped fee query base — matches StudentFeeStatsWidget so the
        // dashboard tile and the fees resource widget always agree.
        $base = fn () => StudentFee::query()
            ->where('student_fees.term_id', $term->id)
            ->where('student_fees.payment_status', '!=', 'carried_forward');

        // Money totals: expected = tuition (basic_fee) + arrears carried in
        // minus applied discounts. Collected and outstanding read straight
        // off the fee rows for reconcilability with the receipt trail.
        $exp = $base()
            ->join('fee_structures', 'student_fees.fee_structure_id', '=', 'fee_structures.id')
            ->selectRaw('COALESCE(SUM(fee_structures.basic_fee), 0) AS tuition, COALESCE(SUM(student_fees.previous_balance), 0) AS arrears, COALESCE(SUM(student_fees.discount_amount), 0) AS discounts')
            ->first();

        $expected    = max(0, (float) $exp->tuition + (float) $exp->arrears - (float) $exp->discounts);
        $collected   = (float) $base()->sum('amount_paid');
        $outstanding = (float) $base()->sum('balance');
        $rate        = $expected > 0 ? round(($collected / $expected) * 100, 1) : 0.0;

        // Per-class heat-map: pupils billed / collected / rate for every
        // class that has at least one Term-{term} fee row. Sorted by rate
        // ascending so the worst-performers rise to the top of the table.
        $byClass = DB::select("
            SELECT g.name AS grade, cs.name AS section,
                   COUNT(DISTINCT sf.student_id) AS pupils,
                   COALESCE(SUM(sf.amount_paid + sf.balance), 0) AS billed,
                   COALESCE(SUM(sf.amount_paid), 0)              AS collected,
                   COALESCE(SUM(sf.balance), 0)                  AS outstanding
              FROM student_fees sf
              JOIN students s ON s.id = sf.student_id
         LEFT JOIN class_sections cs ON cs.id = s.class_section_id
         LEFT JOIN grades g          ON g.id = cs.grade_id
             WHERE sf.term_id = ?
               AND sf.payment_status <> 'carried_forward'
          GROUP BY g.id, g.name, cs.id, cs.name
          ORDER BY g.id, cs.name
        ", [$term->id]);

        $byClass = collect($byClass)->map(function ($r) {
            $r->rate = $r->billed > 0 ? round(($r->collected / $r->billed) * 100, 1) : 0.0;
            $r->label = ($r->grade ?? '—') . ' / ' . ($r->section ?? '—');
            return $r;
        });

        // Distinct-pupil counts by payment status — matches StudentFeeStatsWidget.
        $counts = [
            'paid'    => $base()->where('payment_status', 'paid')->distinct('student_id')->count('student_id'),
            'partial' => $base()->where('payment_status', 'partial')->distinct('student_id')->count('student_id'),
            'unpaid'  => $base()->where('payment_status', 'unpaid')->distinct('student_id')->count('student_id'),
        ];

        // Top 10 pupils with the biggest outstanding balance in this term.
        $defaulters = DB::select("
            SELECT sf.student_id, s.name, s.student_id_number,
                   g.name AS grade, cs.name AS section,
                   sf.amount_paid, sf.balance, sf.payment_status
              FROM student_fees sf
              JOIN students s ON s.id = sf.student_id
         LEFT JOIN class_sections cs ON cs.id = s.class_section_id
         LEFT JOIN grades g          ON g.id = cs.grade_id
             WHERE sf.term_id = ?
               AND sf.balance > 0
               AND sf.payment_status <> 'carried_forward'
          ORDER BY sf.balance DESC
             LIMIT 10
        ", [$term->id]);

        return [
            'empty'        => false,
            'term'         => $term,
            'termLabel'    => $term->name,
            'yearLabel'    => optional($term->academicYear)->name ?? '',
            'expected'     => $expected,
            'collected'    => $collected,
            'outstanding'  => $outstanding,
            'rate'         => $rate,
            'counts'       => $counts,
            'byClass'      => $byClass,
            'defaulters'   => $defaulters,
            'termChoices'  => $this->feeTermChoices(),
        ];
    }

    /**
     * Term picker options for the fee dashboard — every term, newest
     * academic year first. Cheap to compute, so no caching.
     */
    protected function feeTermChoices(): Collection
    {
        return Term::with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('id')
            ->get(['id', 'name', 'academic_year_id']);
    }

    /**
     * Get gender distribution stats
     */
    public function getGenderStats(): array
    {
        $male = Student::where('enrollment_status', 'active')->where('gender', 'male')->count();
        $female = Student::where('enrollment_status', 'active')->where('gender', 'female')->count();
        $other = Student::where('enrollment_status', 'active')
            ->whereNotIn('gender', ['male', 'female'])->count();
        $total = $male + $female + $other;

        return [
            'male' => $male,
            'female' => $female,
            'other' => $other,
            'total' => $total,
            'malePercent' => $total > 0 ? round(($male / $total) * 100) : 0,
            'femalePercent' => $total > 0 ? round(($female / $total) * 100) : 0,
        ];
    }

    /**
     * Get grade capacity utilization
     */
    public function getGradeCapacity(): array
    {
        return Grade::withCount(['students' => function ($q) {
                $q->where('enrollment_status', 'active');
            }])
            ->with(['classSections' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('level')
            ->get()
            ->map(function ($grade) {
                $capacity = $grade->classSections->sum('capacity');
                $enrolled = $grade->students_count;
                return [
                    'name' => $grade->name,
                    'enrolled' => $enrolled,
                    'capacity' => $capacity,
                    'sections' => $grade->classSections->count(),
                    'percent' => $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0,
                ];
            })
            ->toArray();
    }

    /**
     * Get month-over-month comparison stats
     */
    public function getMonthlyComparison(): array
    {
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        // New enrollments
        $newThisMonth = Student::where('created_at', '>=', $thisMonth)->count();
        $newLastMonth = Student::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();

        // Fee collection
        $feesThisMonth = StudentFee::where('updated_at', '>=', $thisMonth)
            ->where('amount_paid', '>', 0)->sum('amount_paid');
        $feesLastMonth = StudentFee::whereBetween('updated_at', [$lastMonth, $lastMonthEnd])
            ->where('amount_paid', '>', 0)->sum('amount_paid');

        // Attendance average
        $attThisMonth = Attendance::where('attendance_date', '>=', $thisMonth)
            ->where('status', 'present')->count();
        $attTotalThisMonth = Attendance::where('attendance_date', '>=', $thisMonth)->count();
        $attLastMonth = Attendance::whereBetween('attendance_date', [$lastMonth, $lastMonthEnd])
            ->where('status', 'present')->count();
        $attTotalLastMonth = Attendance::whereBetween('attendance_date', [$lastMonth, $lastMonthEnd])->count();

        return [
            'enrollments' => [
                'current' => $newThisMonth,
                'previous' => $newLastMonth,
                'change' => $newLastMonth > 0 ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100) : ($newThisMonth > 0 ? 100 : 0),
            ],
            'fees' => [
                'current' => $feesThisMonth,
                'previous' => $feesLastMonth,
                'change' => $feesLastMonth > 0 ? round((($feesThisMonth - $feesLastMonth) / $feesLastMonth) * 100) : ($feesThisMonth > 0 ? 100 : 0),
            ],
            'attendance' => [
                'current' => $attTotalThisMonth > 0 ? round(($attThisMonth / $attTotalThisMonth) * 100) : 0,
                'previous' => $attTotalLastMonth > 0 ? round(($attLastMonth / $attTotalLastMonth) * 100) : 0,
            ],
        ];
    }

    /**
     * Get compact statistics for the stats row (7 cards in 3 columns)
     */
    public function getCompactStats(): array
    {
        return Cache::remember('dashboard_compact_stats', 60, function () {
            $activeStudents = Student::where('enrollment_status', 'active')->count();
            $totalTeachers = Teacher::count();

            // Fee calculations
            $totalCollected = StudentFee::sum('amount_paid');
            $totalBalance = StudentFee::sum('balance');
            $totalExpected = $totalCollected + $totalBalance;
            $collectionRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100) : 0;

            // Attendance today
            $today = now()->toDateString();
            $presentToday = Attendance::where('attendance_date', $today)->where('status', 'present')->count();
            $attendanceRate = $activeStudents > 0 ? round(($presentToday / $activeStudents) * 100) : 0;

            // Homework
            $activeHomework = Homework::where('status', 'active')->count();
            $pendingSubmissions = HomeworkSubmission::where('status', 'submitted')->count();

            // Results - average this term
            $currentTerm = Term::whereHas('academicYear', fn($q) => $q->where('is_active', true))
                ->where('is_current', true)->first();
            $avgMarks = $currentTerm
                ? Result::where('term_id', $currentTerm->id)->where('year', now()->year)->avg('marks')
                : 0;

            // Events this week
            $eventsThisWeek = Event::whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
            $upcomingEvents = Event::where('start_date', '>=', now())->count();

            return [
                'students' => [
                    'value' => $activeStudents,
                    'label' => 'Students',
                    'icon' => 'academic-cap',
                    'color' => 'blue',
                    'subtitle' => 'Active enrolled',
                ],
                'teachers' => [
                    'value' => $totalTeachers,
                    'label' => 'Teachers',
                    'icon' => 'users',
                    'color' => 'indigo',
                    'subtitle' => 'Teaching staff',
                ],
                'fees' => [
                    'value' => 'K' . number_format($totalCollected, 0),
                    'label' => 'Fees Collected',
                    'icon' => 'banknotes',
                    'color' => 'emerald',
                    'subtitle' => $collectionRate . '% collection rate',
                ],
                'attendance' => [
                    'value' => $attendanceRate . '%',
                    'label' => 'Attendance',
                    'icon' => 'clipboard-document-check',
                    'color' => 'cyan',
                    'subtitle' => $presentToday . ' present today',
                ],
                'homework' => [
                    'value' => $activeHomework,
                    'label' => 'Active H/W',
                    'icon' => 'document-text',
                    'color' => 'amber',
                    'subtitle' => $pendingSubmissions . ' submissions pending',
                ],
                'events' => [
                    'value' => $upcomingEvents,
                    'label' => 'Events',
                    'icon' => 'calendar-days',
                    'color' => 'rose',
                    'subtitle' => $eventsThisWeek . ' this week',
                ],
            ];
        });
    }

    /**
     * Get today's attendance statistics
     */
    public function getAttendanceStats(): array
    {
        $today = now()->toDateString();
        $totalStudents = Student::where('enrollment_status', 'active')->count();

        $presentToday = Attendance::where('attendance_date', $today)
            ->where('status', 'present')->count();
        $absentToday = Attendance::where('attendance_date', $today)
            ->where('status', 'absent')->count();
        $lateToday = Attendance::where('attendance_date', $today)
            ->where('status', 'late')->count();
        $excusedToday = Attendance::where('attendance_date', $today)
            ->where('status', 'excused')->count();

        return [
            'total' => $totalStudents,
            'present' => $presentToday,
            'absent' => $absentToday,
            'late' => $lateToday,
            'excused' => $excusedToday,
            'rate' => $totalStudents > 0 ? round(($presentToday / $totalStudents) * 100) : 0,
        ];
    }

    /**
     * Get attendance register: grade-by-grade, gender-split, with status breakdown
     */
    public function getAttendanceRegister(): array
    {
        $today = now()->toDateString();

        // Get all grades ordered by level
        $grades = Grade::orderBy('level')->get();

        // Get today's attendance joined with student gender and grade
        $raw = Attendance::where('attendance_date', $today)
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->select(
                'students.grade_id',
                'students.gender',
                'attendances.status',
                DB::raw('count(*) as cnt')
            )
            ->groupBy('students.grade_id', 'students.gender', 'attendances.status')
            ->get();

        // Also get enrolled counts per grade+gender for "expected" column
        $enrolled = Student::where('enrollment_status', 'active')
            ->select('grade_id', 'gender', DB::raw('count(*) as cnt'))
            ->groupBy('grade_id', 'gender')
            ->get()
            ->groupBy('grade_id');

        // Build lookup: grade_id => gender => status => count
        $lookup = [];
        foreach ($raw as $row) {
            $lookup[$row->grade_id][$row->gender][$row->status] = $row->cnt;
        }

        $statuses = ['present', 'late', 'excused', 'absent'];
        $statusLabels = [
            'present' => 'Present',
            'late' => 'Sick',
            'excused' => 'Permission',
            'absent' => 'Absent',
        ];

        $register = [];
        $totals = ['boys' => [], 'girls' => [], 'enrolled_boys' => 0, 'enrolled_girls' => 0];
        foreach ($statuses as $s) {
            $totals['boys'][$s] = 0;
            $totals['girls'][$s] = 0;
        }

        foreach ($grades as $grade) {
            $gid = $grade->id;
            $row = ['name' => $grade->name, 'boys' => [], 'girls' => []];

            // Enrolled counts
            $enrolledGrade = $enrolled->get($gid);
            $enrolledBoys = 0;
            $enrolledGirls = 0;
            if ($enrolledGrade) {
                foreach ($enrolledGrade as $e) {
                    if ($e->gender === 'male') $enrolledBoys = $e->cnt;
                    elseif ($e->gender === 'female') $enrolledGirls = $e->cnt;
                }
            }
            $row['enrolled_boys'] = $enrolledBoys;
            $row['enrolled_girls'] = $enrolledGirls;
            $totals['enrolled_boys'] += $enrolledBoys;
            $totals['enrolled_girls'] += $enrolledGirls;

            $boyTotal = 0;
            $girlTotal = 0;
            foreach ($statuses as $s) {
                $b = $lookup[$gid]['male'][$s] ?? 0;
                $g = $lookup[$gid]['female'][$s] ?? 0;
                $row['boys'][$s] = $b;
                $row['girls'][$s] = $g;
                $boyTotal += $b;
                $girlTotal += $g;
                $totals['boys'][$s] += $b;
                $totals['girls'][$s] += $g;
            }
            $row['boys_total'] = $boyTotal;
            $row['girls_total'] = $girlTotal;
            $row['grand_total'] = $boyTotal + $girlTotal;

            $register[] = $row;
        }

        // Calculate totals row
        $totalBoys = array_sum($totals['boys']);
        $totalGirls = array_sum($totals['girls']);

        return [
            'register' => $register,
            'totals' => $totals,
            'totalBoys' => $totalBoys,
            'totalGirls' => $totalGirls,
            'grandTotal' => $totalBoys + $totalGirls,
            'statusLabels' => $statusLabels,
            'date' => now()->format('l, j F Y'),
        ];
    }

    /**
     * Get financial summary for charts
     */
    public function getFinancialSummary(): array
    {
        // Monthly collection trend (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $collected = StudentFee::whereMonth('updated_at', $month->month)
                ->whereYear('updated_at', $month->year)
                ->where('amount_paid', '>', 0)
                ->sum('amount_paid');
            $monthlyData[] = [
                'month' => $month->format('M'),
                'collected' => $collected,
            ];
        }

        // Collection by grade (for donut chart)
        $byGrade = StudentFee::join('students', 'student_fees.student_id', '=', 'students.id')
            ->join('grades', 'students.grade_id', '=', 'grades.id')
            ->select('grades.name as grade', DB::raw('sum(student_fees.amount_paid) as collected'))
            ->groupBy('grades.id', 'grades.name')
            ->orderBy('grades.level')
            ->get()
            ->toArray();

        // Outstanding balances
        $totalOutstanding = StudentFee::where('balance', '>', 0)->sum('balance');
        $studentsWithBalance = StudentFee::where('balance', '>', 0)->distinct('student_id')->count('student_id');

        return [
            'monthly' => $monthlyData,
            'byGrade' => $byGrade,
            'totalOutstanding' => $totalOutstanding,
            'studentsWithBalance' => $studentsWithBalance,
        ];
    }

    /**
     * Get top performing students
     */
    public function getTopPerformers(int $limit = 5): array
    {
        $currentTerm = Term::whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->where('is_current', true)->first();

        if (!$currentTerm) {
            return [];
        }

        return Result::where('term_id', $currentTerm->id)
            ->where('year', now()->year)
            ->select('student_id', DB::raw('AVG(marks) as average'), DB::raw('COUNT(*) as subjects'))
            ->groupBy('student_id')
            ->having('subjects', '>=', 3)
            ->orderByDesc('average')
            ->limit($limit)
            ->with('student:id,name,student_id_number')
            ->get()
            ->map(function ($result, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $result->student->name ?? 'Unknown',
                    'student_id' => $result->student->student_id_number ?? '-',
                    'average' => round($result->average, 1),
                    'subjects' => $result->subjects,
                ];
            })
            ->toArray();
    }

    /**
     * Get pending tasks requiring attention
     */
    public function getPendingTasks(): array
    {
        // Ungraded submissions
        $ungradedSubmissions = HomeworkSubmission::where('status', 'submitted')
            ->whereNull('marks')
            ->count();

        // Overdue homework
        $overdueHomework = Homework::where('status', 'active')
            ->where('due_date', '<', now())
            ->count();

        // Students without fees assigned
        $studentsWithoutFees = Student::where('enrollment_status', 'active')
            ->whereDoesntHave('fees')
            ->count();

        // Overdue fee balances (more than 30 days)
        $overdueBalances = StudentFee::where('balance', '>', 0)
            ->where('updated_at', '<', now()->subDays(30))
            ->count();

        return [
            'ungraded' => $ungradedSubmissions,
            'overdueHomework' => $overdueHomework,
            'noFees' => $studentsWithoutFees,
            'overdueBalances' => $overdueBalances,
            'total' => $ungradedSubmissions + $overdueHomework + $studentsWithoutFees + $overdueBalances,
        ];
    }

    /**
     * Get recent payments for mini table
     */
    public function getRecentPayments(int $limit = 7): array
    {
        return StudentFee::where('amount_paid', '>', 0)
            ->with(['student:id,name', 'feeStructure.grade:id,name'])
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->map(function ($fee) {
                return [
                    'student' => $fee->student->name ?? 'Unknown',
                    'amount' => $fee->amount_paid,
                    'grade' => $fee->feeStructure?->grade?->name ?? '-',
                    'date' => $fee->updated_at->format('M d'),
                    'status' => $fee->payment_status,
                ];
            })
            ->toArray();
    }

    /**
     * Get pending homework submissions for mini table
     */
    public function getPendingSubmissions(int $limit = 7): array
    {
        return HomeworkSubmission::where('status', 'submitted')
            ->whereNull('marks')
            ->with(['student:id,name', 'homework:id,title,subject_id,due_date', 'homework.subject:id,name'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($submission) {
                return [
                    'student' => $submission->student->name ?? 'Unknown',
                    'homework' => $submission->homework->title ?? '-',
                    'subject' => $submission->homework?->subject?->name ?? '-',
                    'submitted' => $submission->created_at->diffForHumans(),
                    'due' => $submission->homework?->due_date?->format('M d') ?? '-',
                ];
            })
            ->toArray();
    }

    /**
     * Get overdue fee balances for mini table
     */
    public function getOverdueFees(int $limit = 7): array
    {
        return StudentFee::where('balance', '>', 0)
            ->with(['student:id,name', 'feeStructure.grade:id,name'])
            ->orderByDesc('balance')
            ->limit($limit)
            ->get()
            ->map(function ($fee) {
                $daysOverdue = $fee->updated_at->diffInDays(now());
                return [
                    'student' => $fee->student->name ?? 'Unknown',
                    'balance' => $fee->balance,
                    'grade' => $fee->feeStructure?->grade?->name ?? '-',
                    'days' => $daysOverdue,
                    'status' => $daysOverdue > 30 ? 'critical' : ($daysOverdue > 14 ? 'warning' : 'normal'),
                ];
            })
            ->toArray();
    }
}
