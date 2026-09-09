<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Result;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Finance-free dashboard shown to the Manager account (and anyone else
 * flagged users.settings.finance_locked = true).
 *
 *   · Every fee, payroll and payment number is deliberately absent.
 *   · Focuses on the operational picture the Manager is supposed to
 *     watch: enrolment, staff, today's attendance, recent academic
 *     entries, and who is on the portal.
 */
class ManagerDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title           = 'Manager Dashboard';
    protected static ?string $slug            = 'manager-dashboard';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.manager-dashboard';

    public static function canAccess(): bool
    {
        // Any admin who is finance-locked, plus the small set of
        // deliberately-scoped oversight roles.
        $u = auth()->user();
        if (! $u) return false;
        // Anyone flagged finance_locked sees this dashboard instead of the
        // main one (which is where the fee-collection panel lives).
        return (bool) $u->isFinanceLocked();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public function getViewData(): array
    {
        return [
            'greeting'       => $this->greeting(),
            'summary'        => $this->summary(),
            'enrolment'      => $this->enrolmentByClass(),
            'attendance'     => $this->todayAttendance(),
            'academic'       => $this->academicPulse(),
            'upcomingEvents' => $this->upcomingEvents(),
            'recentLogins'   => $this->recentLogins(),
        ];
    }

    protected function greeting(): array
    {
        $h = now()->hour;
        return [
            'salutation' => $h < 12 ? 'Good morning' : ($h < 17 ? 'Good afternoon' : 'Good evening'),
            'name'       => explode(' ', auth()->user()->name ?? 'Manager')[0],
            'today'      => now()->format('l, j F Y'),
        ];
    }

    protected function summary(): array
    {
        return [
            'active_pupils'      => (int) Student::where('enrollment_status', 'active')->count(),
            'transferred_pupils' => (int) Student::where('enrollment_status', 'transferred')->count(),
            'active_teachers'    => (int) Teacher::where('is_active', true)->count(),
            'class_teachers'     => (int) Teacher::where('is_active', true)->where('is_class_teacher', 1)->count(),
        ];
    }

    protected function enrolmentByClass(): Collection
    {
        return collect(DB::select("
            SELECT g.id AS gid, g.name AS grade, cs.name AS section,
                   COUNT(s.id) AS pupils,
                   t.name AS class_teacher
              FROM class_sections cs
         LEFT JOIN grades g   ON g.id = cs.grade_id
         LEFT JOIN teachers t ON t.id = cs.class_teacher_id
         LEFT JOIN students s ON s.class_section_id = cs.id AND s.enrollment_status = 'active'
          GROUP BY cs.id, g.id, g.name, cs.name, t.name
          ORDER BY g.id, cs.name
        "));
    }

    protected function todayAttendance(): array
    {
        $today   = now()->toDateString();
        $present = (int) Attendance::whereDate('attendance_date', $today)->where('status', 'present')->count();
        $absent  = (int) Attendance::whereDate('attendance_date', $today)->where('status', 'absent')->count();
        $sick    = (int) Attendance::whereDate('attendance_date', $today)->where('status', 'sick')->count();
        $late    = (int) Attendance::whereDate('attendance_date', $today)->where('status', 'late')->count();
        $total   = $present + $absent + $sick + $late;
        $rate    = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return compact('present', 'absent', 'sick', 'late', 'total', 'rate');
    }

    protected function academicPulse(): array
    {
        return [
            'results_this_week'     => (int) Result::where('created_at', '>=', now()->startOfWeek())->count(),
            'results_this_term'     => (int) Result::whereYear('created_at', now()->year)->count(),
            'distinct_pupils_week'  => (int) Result::where('created_at', '>=', now()->startOfWeek())->distinct('student_id')->count('student_id'),
        ];
    }

    protected function upcomingEvents(int $limit = 5): Collection
    {
        return Event::query()
            ->where('start_date', '>=', now()->startOfDay())
            ->orderBy('start_date')
            ->limit($limit)
            ->get(['id', 'title', 'description', 'start_date', 'end_date', 'location']);
    }

    protected function recentLogins(int $limit = 8): Collection
    {
        return collect(DB::select("
            SELECT a.user_id, a.ip_address, a.created_at, u.name, u.email, r.name AS role_name
              FROM audit_logs a
         LEFT JOIN users u ON u.id = a.user_id
         LEFT JOIN roles r ON r.id = u.role_id
             WHERE a.event = 'login'
          ORDER BY a.id DESC
             LIMIT ?
        ", [$limit]))->map(function ($r) {
            $r->when = Carbon::parse($r->created_at);
            return $r;
        });
    }
}
