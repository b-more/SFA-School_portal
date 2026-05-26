<?php

namespace App\Http\Controllers\Api;

use App\Constants\RoleConstants;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Notice;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\ParentGuardian;
use App\Models\PaymentTransaction;
use App\Models\ReportCardComment;
use App\Models\Result;
use App\Models\BookLoan;
use App\Models\BusPayment;
use App\Models\Complaint;
use App\Models\News;
use App\Models\SchoolSettings;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use App\Models\PushSubscription;
use App\Models\TimetableEntry;
use App\Models\TimetablePeriod;
use App\Models\QrPayment;
use App\Services\CGrateService;
use App\Services\BalanceForwardService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ParentApiController extends Controller
{
    private function getParent()
    {
        return ParentGuardian::where('user_id', Auth::id())->first();
    }

    private function getChildIds()
    {
        $parent = $this->getParent();
        return $parent ? $parent->students()->where('enrollment_status', 'active')->pluck('id')->toArray() : [];
    }

    private function validateChild(Student $student)
    {
        $childIds = $this->getChildIds();
        if (!in_array($student->id, $childIds)) {
            abort(403, 'Access denied.');
        }
    }

    public function dashboard()
    {
        $childIds = $this->getChildIds();
        $gradeIds = Student::whereIn('id', $childIds)->pluck('grade_id')->unique()->toArray();

        $activeTerm = Term::where('is_active', true)->first();
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Attendance rate
        $attQuery = Attendance::whereIn('student_id', $childIds);
        if ($activeYear) $attQuery->where('academic_year_id', $activeYear->id);
        if ($activeTerm) $attQuery->where('term_id', $activeTerm->id);
        $totalAtt = $attQuery->count();
        $presentCount = (clone $attQuery)->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalAtt > 0 ? round(($presentCount / $totalAtt) * 100, 1) : 0;

        // Fee balance (tuition only)
        $totalBalance = 0;
        $fees = StudentFee::whereIn('student_id', $childIds)->with('feeStructure')->get();
        foreach ($fees as $fee) {
            $tuition = $fee->feeStructure?->basic_fee ?? 0;
            $totalBalance += max($tuition - ($fee->amount_paid ?? 0), 0);
        }

        return response()->json([
            'children_count' => count($childIds),
            'attendance_rate' => $attendanceRate,
            'total_balance' => $totalBalance,
            'pending_homework' => Homework::whereIn('grade_id', $gradeIds)->where('status', 'active')->where('due_date', '>=', now())->count(),
            'overdue_homework' => Homework::whereIn('grade_id', $gradeIds)->where('status', 'active')->where('due_date', '<', now())->count(),
            'upcoming_events' => Event::where('start_date', '>=', now())->where('start_date', '<=', now()->addDays(14))->where(function ($q) use ($gradeIds) {
                $q->whereIn('applicable_to', $gradeIds)->orWhere('applicable_to', 'all')->orWhereNull('applicable_to');
            })->count(),
            'term' => $activeTerm?->name,
            'year' => $activeYear?->name,
            'unread_notices' => Notice::where('published_at', '>=', now()->subDays(7))->whereNotNull('published_at')->count(),
        ]);
    }

    public function children()
    {
        $parent = $this->getParent();
        if (!$parent) return response()->json([]);

        $students = $parent->students()->where('enrollment_status', 'active')
            ->with(['grade', 'classSection.classTeacher.user'])->get();

        return response()->json($students->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'grade' => $s->grade?->name,
            'class' => $s->classSection?->name,
            'gender' => $s->gender,
            'date_of_birth' => $s->date_of_birth?->format('d M Y'),
            'class_teacher' => $s->classSection?->classTeacher?->user?->name
                ?? $s->classSection?->classTeacher?->name
                ?? null,
            'profile_photo' => $s->profile_photo ? '/storage/' . $s->profile_photo : null,
        ]));
    }

    public function attendance(Student $student)
    {
        $this->validateChild($student);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();

        $query = Attendance::where('student_id', $student->id);
        if ($activeYear) $query->where('academic_year_id', $activeYear->id);
        if ($activeTerm) $query->where('term_id', $activeTerm->id);

        $records = $query->orderBy('attendance_date', 'desc')->get();
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $sick = $records->where('status', 'sick')->count();
        $excused = $records->where('status', 'excused')->count();
        $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        // Build current week Mon-Fri with school week number
        $termStartDate = $activeTerm?->start_date;
        $today = now();
        // Get Monday of current week
        $currentMonday = $today->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $currentFriday = $currentMonday->copy()->addDays(4);

        // School week number: week 0 starts from term opening date
        $schoolWeek = null;
        if ($termStartDate) {
            $termMonday = $termStartDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $schoolWeek = (int) $termMonday->diffInWeeks($currentMonday);
        }

        // Get attendance for the current Mon-Fri, ordered Mon→Fri
        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $weekRecords = $records->filter(function ($r) use ($currentMonday, $currentFriday) {
            return $r->attendance_date->between($currentMonday, $currentFriday);
        })->keyBy(fn ($r) => $r->attendance_date->format('l'));

        $currentWeek = [];
        foreach ($dayOrder as $dayName) {
            $r = $weekRecords->get($dayName);
            $currentWeek[] = [
                'day' => substr($dayName, 0, 3),
                'day_full' => $dayName,
                'status' => $r ? $r->status : null,
                'symbol' => $r ? Attendance::getStatusSymbol($r->status) : '-',
                'date' => $currentMonday->copy()->next($dayName)->subWeek()->format('M j'),
            ];
        }
        // Fix date calc — use Monday as base
        $currentWeek = [];
        foreach ($dayOrder as $i => $dayName) {
            $dayDate = $currentMonday->copy()->addDays($i);
            $r = $weekRecords->get($dayName);
            $currentWeek[] = [
                'day' => substr($dayName, 0, 3),
                'day_full' => $dayName,
                'status' => $r ? $r->status : ($dayDate->isFuture() ? null : 'no_record'),
                'symbol' => $r ? Attendance::getStatusSymbol($r->status) : '-',
                'date' => $dayDate->format('M j'),
            ];
        }

        // Monthly calendar
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $monthRecords = $records->filter(function ($r) use ($monthStart, $monthEnd) {
            return $r->attendance_date->between($monthStart, $monthEnd);
        })->keyBy(fn ($r) => $r->attendance_date->day);

        $monthCalendar = [];
        for ($d = 1; $d <= $monthEnd->day; $d++) {
            $dayDate = $monthStart->copy()->addDays($d - 1);
            $r = $monthRecords->get($d);
            $monthCalendar[] = [
                'day' => $d,
                'dow' => $dayDate->dayOfWeekIso, // 1=Mon, 7=Sun
                'status' => $r ? $r->status : null,
                'is_future' => $dayDate->isFuture(),
                'is_today' => $dayDate->isToday(),
                'is_weekend' => $dayDate->isWeekend(),
            ];
        }

        return response()->json([
            'total' => $total, 'present' => $present, 'absent' => $absent,
            'late' => $late, 'sick' => $sick, 'excused' => $excused, 'rate' => $rate,
            'current_week' => $currentWeek,
            'school_week' => $schoolWeek,
            'term_start' => $termStartDate?->toDateString(),
            'download_url' => '/portal/attendance/student/' . $student->id . '/download',
            'month_calendar' => $monthCalendar,
            'month_name' => $today->format('F Y'),
        ]);
    }

    public function fees(Student $student)
    {
        $this->validateChild($student);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $terms = $activeYear ? Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get() : collect();

        $termData = $terms->map(function ($term) use ($student, $activeYear) {
            $fee = StudentFee::where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->where('academic_year_id', $activeYear->id)
                ->with('feeStructure')->first();

            $tuition = $fee?->feeStructure?->basic_fee ?? 0;
            $paid = $fee?->amount_paid ?? 0;
            $balance = max($tuition - $paid, 0);

            return [
                'term' => $term->name,
                'tuition_fee' => $tuition,
                'amount_paid' => $paid,
                'balance' => $balance,
                'status' => !$fee ? 'no_fee' : ($paid >= $tuition ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid')),
                'progress' => $tuition > 0 ? round(($paid / $tuition) * 100) : 0,
            ];
        });

        return response()->json([
            'terms' => $termData,
            'total_tuition' => $termData->sum('tuition_fee'),
            'total_paid' => $termData->sum('amount_paid'),
            'total_balance' => $termData->sum('balance'),
            'statement_url' => '/portal/payment-statement/student/' . $student->id,
        ]);
    }

    public function results(Student $student)
    {
        $this->validateChild($student);

        // Surgical sanction: parents cannot view this term's results until fees are settled.
        if ($student->hasArrears()) {
            return response()->json([
                'blocked' => true,
                'reason' => 'arrears',
                'arrears_amount' => $student->arrearsAmount(),
                'message' => "Results unavailable. Outstanding fees balance: ZMW " . number_format($student->arrearsAmount(), 2) . ". Please settle at the school accounts office to view {$student->name}'s results.",
            ], 402);
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $results = Result::where('student_id', $student->id)->with('subject')->orderBy('created_at', 'desc')->get();

        $avg = $results->count() > 0 ? round($results->avg('marks'), 1) : null;

        return response()->json([
            'average' => $avg,
            'total_subjects' => $results->count(),
            'highest' => $results->max('marks'),
            'lowest' => $results->min('marks'),
            'grade_distribution' => $results->groupBy('grade')->map->count(),
            'subjects' => $results->map(fn ($r) => [
                'subject' => $r->subject?->name,
                'marks' => $r->marks,
                'grade' => $r->grade,
                'exam_type' => $r->exam_type,
                'term' => $r->term,
            ]),
        ]);
    }

    public function homework(Student $student)
    {
        $this->validateChild($student);

        $activeTerm = Term::where('is_active', true)->first();
        $termStart = $activeTerm?->start_date;
        $termStartMonday = $termStart ? $termStart->copy()->startOfWeek(\Carbon\Carbon::MONDAY) : null;

        // Get ALL homework for the active term
        $homework = Homework::where('grade_id', $student->grade_id)
            ->where('status', 'active')
            ->when($activeTerm, fn($q) => $q->where(function($q2) use ($activeTerm) {
                $q2->where('term_id', $activeTerm->id)->orWhereNull('term_id');
            }))
            ->with(['subject', 'assignedBy'])
            ->orderBy('due_date', 'desc')
            ->get();

        $items = $homework->map(function ($hw) use ($student, $termStartMonday) {
            $submission = HomeworkSubmission::where('homework_id', $hw->id)
                ->where('student_id', $student->id)->first();

            // Calculate school week number from term start
            $weekNum = null;
            if ($termStartMonday && $hw->due_date) {
                $hwMonday = $hw->due_date->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                $weekNum = (int) $termStartMonday->diffInWeeks($hwMonday);
            }

            return [
                'id' => $hw->id,
                'title' => $hw->title,
                'subject' => $hw->subject?->name,
                'subject_id' => $hw->subject_id,
                'teacher' => $hw->assignedBy?->name,
                'due_date' => $hw->due_date->format('M j, g:i A'),
                'due_date_raw' => $hw->due_date->toDateString(),
                'week_number' => $weekNum,
                'is_overdue' => $hw->due_date->isPast() && !$submission,
                'is_due_soon' => !$hw->due_date->isPast() && $hw->due_date->diffInDays(now()) <= 2,
                'submitted' => $submission !== null,
                'submitted_at' => $submission?->submitted_at?->format('d M Y, g:i A'),
                'submission_content' => $submission?->content,
                'submission_file' => $submission?->file_attachment ? '/storage/' . ltrim($submission->file_attachment, '"') : null,
                'submission_file_name' => $submission?->file_attachment ? basename(str_replace(['\\', '"'], ['/', ''], $submission->file_attachment)) : null,
                'is_late_submission' => (bool) $submission?->is_late,
                'marks' => $submission?->marks,
                'feedback' => $submission?->feedback,
                'teacher_notes' => $submission?->teacher_notes,
                'graded_at' => $submission?->graded_at ? $submission->graded_at->format('d M Y') : null,
                'status' => $submission?->status ?? ($hw->due_date->isPast() ? 'overdue' : 'pending'),
                'has_file' => !empty($hw->homework_file),
                'download_url' => $hw->homework_file ? '/portal/homework/' . $hw->id . '/download' : null,
            ];
        });

        // Current school week
        $currentWeek = null;
        if ($termStartMonday) {
            $currentWeek = (int) $termStartMonday->diffInWeeks(now()->startOfWeek(\Carbon\Carbon::MONDAY));
        }

        // Build subject list for filters
        $subjects = $items->pluck('subject')->unique()->filter()->values();

        // Build week list for filters
        $weeks = $items->pluck('week_number')->unique()->filter(fn($w) => $w !== null)->sort()->values();

        // Weekly compliance: 2 homework per subject per week (minimum policy)
        $compliance = [];
        $grouped = $items->groupBy(fn($h) => $h['week_number'] . '|' . $h['subject']);
        foreach ($grouped as $key => $group) {
            [$week, $subject] = explode('|', $key, 2);
            if ($week === '') continue;
            $compliance[] = [
                'week' => (int) $week,
                'subject' => $subject,
                'count' => $group->count(),
                'submitted' => $group->where('submitted', true)->count(),
                'meets_policy' => $group->count() >= 2,
            ];
        }

        $totalAssigned = $homework->count();
        $totalSubmitted = $items->where('submitted', true)->count();

        return response()->json([
            'homework' => $items,
            'total_assigned' => $totalAssigned,
            'total_submitted' => $totalSubmitted,
            'current_week' => $currentWeek,
            'subjects' => $subjects,
            'weeks' => $weeks,
            'compliance' => $compliance,
            'policy_minimum' => 2,
        ]);
    }

    public function reportCards(Student $student)
    {
        $this->validateChild($student);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return response()->json(['terms' => [], 'fee_status' => 'unknown']);

        // Check fee status — sum of all year balances; carried-forward rows are 0 so this
        // equals the current outstanding (current-term tuition + any carried arrears).
        $fees = StudentFee::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->get();
        $totalBalance = $fees->sum('balance');
        $totalPaid = $fees->sum('amount_paid');
        $totalFee = $fees->sum(fn($f) => $f->feeStructure?->basic_fee ?? 0);
        $feeStatus = $totalFee <= 0 ? 'no_fee' : ($totalBalance <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid'));
        $isFullyPaid = $feeStatus === 'paid' || $feeStatus === 'no_fee';
        $arrearsAmount = $student->arrearsAmount();

        $terms = Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get();

        $cards = $terms->map(function ($term) use ($student, $activeYear, $isFullyPaid) {
            $comment = ReportCardComment::where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->where('academic_year_id', $activeYear->id)->first();

            $isGenerated = $comment && $comment->generated_at;

            return [
                'term' => $term->name,
                'is_generated' => $isGenerated,
                'is_locked' => !$isFullyPaid,
                'download_url' => $isGenerated && $isFullyPaid ? '/portal/report-cards/' . $student->id . '/' . $term->id . '?year=' . $activeYear->name : null,
                'preview_url' => $isGenerated && $isFullyPaid ? '/portal/report-cards/' . $student->id . '/' . $term->id . '/preview?year=' . $activeYear->name : null,
            ];
        });

        return response()->json([
            'terms' => $cards,
            'fee_status' => $feeStatus,
            'is_fully_paid' => $isFullyPaid,
            'arrears_amount' => $arrearsAmount,
            'message' => $isFullyPaid
                ? null
                : "Outstanding fees balance: ZMW " . number_format($arrearsAmount, 2) . ". Settle at the accounts office to unlock report cards.",
        ]);
    }

    public function events()
    {
        $childIds = $this->getChildIds();
        $gradeIds = Student::whereIn('id', $childIds)->pluck('grade_id')->unique()->toArray();

        $events = Event::where('start_date', '>=', now())
            ->where(function ($q) use ($gradeIds) {
                $q->whereIn('applicable_to', $gradeIds)->orWhere('applicable_to', 'all')->orWhereNull('applicable_to');
            })
            ->orderBy('start_date')->take(10)->get();

        return response()->json($events->map(fn ($e) => [
            'title' => $e->title,
            'description' => $e->description,
            'date' => $e->start_date->format('d M Y'),
            'time' => $e->start_date->format('g:i A'),
            'day' => $e->start_date->format('d'),
            'month' => $e->start_date->format('M'),
            'relative' => $e->start_date->diffForHumans(),
        ]));
    }

    public function submitHomework(Request $request, Student $student, Homework $homework)
    {
        $this->validateChild($student);

        // Check homework belongs to student's grade
        if ($homework->grade_id !== $student->grade_id) {
            return response()->json(['message' => 'This homework is not assigned to your child.'], 403);
        }

        // Check not already submitted
        $existing = HomeworkSubmission::where('homework_id', $homework->id)
            ->where('student_id', $student->id)->first();
        if ($existing) {
            return response()->json(['message' => 'Homework already submitted.'], 422);
        }

        $request->validate([
            'content' => 'nullable|string|max:65535',
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filePath = $file->storeAs(
                'homework-submissions/' . $student->id,
                $originalName,
                'public'
            );
        }

        $submission = new HomeworkSubmission();
        $submission->homework_id = $homework->id;
        $submission->student_id = $student->id;
        $submission->content = $request->input('content', '');
        $submission->submitted_at = now();
        $submission->status = 'submitted';
        $submission->is_late = $homework->due_date->isPast();
        $submission->file_attachment = $filePath;
        $submission->academic_year_id = AcademicYear::where('is_active', true)->first()?->id ?? 0;
        $submission->save();

        return response()->json([
            'message' => 'Homework submitted successfully.',
            'submission_id' => $submission->id,
        ]);
    }

    public function notices()
    {
        $parent = $this->getParent();
        if (!$parent) return response()->json([]);

        $students = $parent->students()->where('enrollment_status', 'active')
            ->with(['grade', 'classSection'])->get();

        // Collect notices visible to any of the parent's children
        $noticeIds = collect();
        foreach ($students as $student) {
            $ids = Notice::published()->forStudent($student)->pluck('id');
            $noticeIds = $noticeIds->merge($ids);
        }

        $notices = Notice::whereIn('id', $noticeIds->unique())
            ->with('postedBy')
            ->orderByRaw("FIELD(priority, 'urgent', 'important', 'normal')")
            ->orderBy('published_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($notices->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'body' => strip_tags($n->body),
            'body_html' => $n->body,
            'priority' => $n->priority,
            'target' => $n->audience,
            'target_type' => $n->target_type,
            'posted_by' => $n->postedBy?->name ?? 'Admin',
            'date' => $n->published_at?->format('d M Y') ?? $n->created_at->format('d M Y'),
            'relative' => $n->published_at?->diffForHumans() ?? $n->created_at->diffForHumans(),
            'has_attachment' => !empty($n->attachment),
            'attachment_url' => $n->attachment ? '/storage/' . $n->attachment : null,
        ]));
    }

    public function payments()
    {
        $parent = $this->getParent();
        if (!$parent) return response()->json(['children' => [], 'transactions' => []]);

        $students = $parent->students()->where('enrollment_status', 'active')
            ->with(['grade'])->get();
        $childIds = $students->pluck('id')->toArray();

        $activeYear = AcademicYear::where('is_active', true)->first();

        // Per-child fee summary
        $childSummaries = $students->map(function ($s) use ($activeYear) {
            $fees = StudentFee::where('student_id', $s->id)
                ->with('feeStructure')
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->get();

            $totalFee = $fees->sum(fn($f) => $f->feeStructure?->basic_fee ?? 0);
            $totalPaid = $fees->sum('amount_paid');
            $totalBalance = $fees->sum('balance');

            return [
                'id' => $s->id,
                'name' => $s->name,
                'grade' => $s->grade?->name,
                'total_fee' => $totalFee,
                'total_paid' => $totalPaid,
                'balance' => $totalBalance,
                'status' => $totalBalance <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid'),
                'progress' => $totalFee > 0 ? round(($totalPaid / $totalFee) * 100) : 0,
                'statement_url' => '/portal/payment-statement/student/' . $s->id,
            ];
        });

        // Recent transactions with receipt links
        $feeIds = StudentFee::whereIn('student_id', $childIds)->pluck('id')->toArray();
        $transactions = PaymentTransaction::whereIn('student_fee_id', $feeIds)
            ->with(['studentFee.student'])
            ->latest('transaction_date')->take(20)->get();

        return response()->json([
            'children' => $childSummaries,
            'total_fees' => $childSummaries->sum('total_fee'),
            'total_paid' => $childSummaries->sum('total_paid'),
            'total_balance' => $childSummaries->sum('balance'),
            'transactions' => $transactions->map(fn ($p) => [
                'id' => $p->id,
                'student' => $p->studentFee?->student?->name,
                'student_fee_id' => $p->student_fee_id,
                'amount' => $p->amount,
                'type' => $p->type,
                'date' => $p->transaction_date?->format('d M Y'),
                'time' => $p->transaction_date?->format('g:i A'),
                'method' => ucfirst(str_replace('_', ' ', $p->payment_method ?? '')),
                'reference' => $p->reference_number,
                'external_ref' => $p->external_reference,
                'status' => $p->status,
                'receipt_url' => $p->student_fee_id ? '/portal/student-fees/' . $p->student_fee_id . '/receipt/pdf' : null,
                'transaction_receipt_url' => $p->student_fee_id ? '/portal/student-fees/' . $p->student_fee_id . '/transaction/' . $p->id . '/receipt' : null,
            ]),
        ]);
    }

    // P1: Class Timetable
    public function timetable(Student $student)
    {
        $this->validateChild($student);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear || !$student->class_section_id) {
            return response()->json(['periods' => [], 'days' => TimetableEntry::DAYS]);
        }

        $timetable = TimetableEntry::getClassTimetable($student->class_section_id, $activeYear->id);

        $grid = [];
        foreach ($timetable as $periodId => $row) {
            $period = $row['period'];
            $entry = [
                'period' => $period->name,
                'short_name' => $period->short_name,
                'start_time' => $period->start_time?->format('H:i'),
                'end_time' => $period->end_time?->format('H:i'),
                'type' => $period->type,
                'days' => [],
            ];
            foreach (TimetableEntry::DAYS as $day) {
                $e = $row['days'][$day] ?? null;
                $entry['days'][$day] = $e ? [
                    'subject' => $e->subject?->name,
                    'teacher' => $e->teacher?->name,
                    'room' => $e->room,
                ] : null;
            }
            $grid[] = $entry;
        }

        return response()->json([
            'periods' => $grid,
            'days' => TimetableEntry::DAYS,
        ]);
    }

    // P2: Library Book Loans
    public function bookLoans(Student $student)
    {
        $this->validateChild($student);

        $loans = BookLoan::where('student_id', $student->id)
            ->with('book')
            ->orderByRaw("FIELD(status, 'overdue', 'active', 'returned', 'lost')")
            ->orderBy('due_date', 'desc')
            ->get();

        $mappedLoans = $loans->map(function ($loan) {
            $daysLeft = null;
            if ($loan->status === 'active' && $loan->due_date) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($loan->due_date->startOfDay(), false);
            }

            return [
                'id' => $loan->id,
                'book_title' => $loan->book?->title ?? 'Unknown Book',
                'book_author' => $loan->book?->author ?? '',
                'book_category' => $loan->book?->category ?? '',
                'book_isbn' => $loan->book?->isbn ?? '',
                'book_cover' => $loan->book?->cover_image ? '/storage/' . $loan->book->cover_image : null,
                'book_shelf' => $loan->book?->shelf_location ?? '',
                'lent_date' => $loan->lent_date?->format('d M Y'),
                'due_date' => $loan->due_date?->format('d M Y'),
                'due_date_raw' => $loan->due_date?->toDateString(),
                'returned_at' => $loan->returned_at?->format('d M Y'),
                'status' => $loan->status,
                'is_overdue' => $loan->isOverdue(),
                'days_left' => $daysLeft,
                'days_overdue' => $loan->daysOverdue(),
                'fine_amount' => (float) $loan->fine_amount,
                'fine_paid' => (bool) $loan->fine_paid,
                'condition_on_loan' => $loan->condition_on_loan,
                'condition_on_return' => $loan->condition_on_return,
                'notes' => $loan->notes,
            ];
        });

        $activeLoans = $mappedLoans->whereIn('status', ['active', 'overdue']);
        $returnedLoans = $mappedLoans->where('status', 'returned');
        $totalFines = $mappedLoans->where('fine_amount', '>', 0)->sum('fine_amount');
        $unpaidFines = $mappedLoans->where('fine_amount', '>', 0)->where('fine_paid', false)->sum('fine_amount');

        // Reading stats
        $categories = $mappedLoans->pluck('book_category')->filter()->countBy();
        $totalBooksRead = $returnedLoans->count();
        $totalBorrowed = $mappedLoans->count();

        return response()->json([
            'loans' => $mappedLoans->values(),
            'active_count' => $activeLoans->count(),
            'overdue_count' => $mappedLoans->where('status', 'overdue')->count(),
            'returned_count' => $returnedLoans->count(),
            'total_borrowed' => $totalBorrowed,
            'total_fines' => $totalFines,
            'unpaid_fines' => $unpaidFines,
            'categories_read' => $categories,
        ]);
    }

    // P3: Bus/Transport Payments
    public function busPayments(Student $student)
    {
        $this->validateChild($student);

        $activeYear = AcademicYear::where('is_active', true)->first();

        $query = BusPayment::where('student_id', $student->id);
        if ($activeYear) {
            $query->where(function ($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id)
                  ->orWhereNull('academic_year_id');
            });
        }

        $payments = $query->orderBy('year', 'desc')->orderBy('month')->get();

        return response()->json([
            'payments' => $payments->map(fn ($p) => [
                'id' => $p->id,
                'month' => $p->month,
                'year' => $p->year,
                'amount' => $p->amount,
                'amount_paid' => $p->amount_paid,
                'balance' => $p->balance,
                'status' => $p->payment_status,
                'due_date' => $p->due_date?->format('d M Y'),
            ]),
            'total_amount' => $payments->sum('amount'),
            'total_paid' => $payments->sum('amount_paid'),
            'total_balance' => $payments->sum('balance'),
        ]);
    }

    // P4: School News Feed
    public function news()
    {
        $news = News::where('status', 'published')
            ->with('author')
            ->orderBy('date', 'desc')
            ->take(20)->get();

        return response()->json($news->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'content' => strip_tags($n->content),
            'content_html' => $n->content,
            'image' => $n->image ? '/storage/' . $n->image : null,
            'category' => $n->category,
            'date' => $n->date?->format('d M Y'),
            'relative' => $n->date?->diffForHumans(),
            'author' => $n->author?->name ?? 'Admin',
        ]));
    }

    // P5: Complaint/Issue Reporting
    public function complaints()
    {
        $parent = $this->getParent();
        if (!$parent) return response()->json([]);

        $user = Auth::user();

        $complaints = Complaint::where('logged_by', $user->id)
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->take(20)->get();

        return response()->json($complaints->map(fn ($c) => [
            'id' => $c->id,
            'type' => $c->complaint_type,
            'subject' => $c->subject,
            'description' => $c->description,
            'priority' => $c->priority,
            'status' => $c->status,
            'resolution' => $c->resolution,
            'student' => $c->student?->name,
            'date' => $c->created_at->format('d M Y'),
            'resolved_at' => $c->resolved_at?->format('d M Y'),
        ]));
    }

    public function createComplaint(Request $request, Student $student)
    {
        $this->validateChild($student);

        $request->validate([
            'complaint_type' => 'required|in:academic,behavioral,facility,staff,other',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $parent = $this->getParent();
        $user = Auth::user();

        $complaint = Complaint::create([
            'complainant_name' => $user->name,
            'phone' => $parent->phone ?? $user->phone ?? '',
            'email' => $user->email ?? '',
            'complaint_type' => $request->complaint_type,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'open',
            'related_student_id' => $student->id,
            'logged_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Complaint submitted successfully.',
            'complaint_id' => $complaint->id,
        ], 201);
    }

    // P6: Academic Calendar
    public function schoolCalendar()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return response()->json(['terms' => [], 'year' => null]);

        $terms = Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get();

        $settings = SchoolSettings::first();

        // Track cumulative week offset across terms (week 0 starts at first term opening)
        $firstTermStart = $terms->first()?->start_date;

        return response()->json([
            'year' => $activeYear->name,
            'terms' => $terms->map(function ($t) use ($firstTermStart) {
                $totalWeeks = 0;
                $startWeekNum = null;
                $endWeekNum = null;
                $weeks = [];

                if ($t->start_date && $t->end_date && $firstTermStart) {
                    $termStartMonday = $t->start_date->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                    $firstMonday = $firstTermStart->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                    $startWeekNum = (int) $firstMonday->diffInWeeks($termStartMonday);

                    $termEndFriday = $t->end_date->copy()->endOfWeek(\Carbon\Carbon::FRIDAY);
                    $totalWeeks = (int) ceil($termStartMonday->diffInDays($termEndFriday) / 7);

                    for ($w = 0; $w < $totalWeeks; $w++) {
                        $weekMonday = $termStartMonday->copy()->addWeeks($w);
                        $weekFriday = $weekMonday->copy()->addDays(4);
                        $weeks[] = [
                            'week_num' => $startWeekNum + $w,
                            'label' => 'Week ' . ($startWeekNum + $w),
                            'start' => $weekMonday->format('d M'),
                            'end' => $weekFriday->format('d M'),
                            'is_current' => now()->between($weekMonday, $weekFriday->endOfDay()),
                        ];
                    }

                    $endWeekNum = $startWeekNum + $totalWeeks - 1;
                }

                return [
                    'name' => $t->name,
                    'start_date' => $t->start_date?->format('d M Y'),
                    'end_date' => $t->end_date?->format('d M Y'),
                    'is_active' => $t->is_active,
                    'start_raw' => $t->start_date?->toDateString(),
                    'end_raw' => $t->end_date?->toDateString(),
                    'start_week' => $startWeekNum,
                    'end_week' => $endWeekNum,
                    'total_weeks' => $totalWeeks,
                    'weeks' => $weeks,
                ];
            }),
            'school_name' => $settings?->school_name ?? 'St. Francis of Assisi',
        ]);
    }

    // P7: Push Notifications
    public function vapidPublicKey()
    {
        return response()->json([
            'key' => config('services.vapid.public_key', env('VAPID_PUBLIC_KEY')),
        ]);
    }

    public function pushSubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = Auth::user();

        PushSubscription::updateOrCreate(
            ['user_id' => $user->id, 'endpoint' => $request->endpoint],
            [
                'p256dh_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
            ]
        );

        return response()->json(['message' => 'Subscribed to push notifications.']);
    }

    public function pushUnsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['message' => 'Unsubscribed from push notifications.']);
    }

    // Mobile Money Payment
    public function initiatePayment(Request $request, Student $student)
    {
        $this->validateChild($student);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'mobile_number' => 'required|string|min:10|max:13',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();

        // Calculate outstanding balance
        $fees = StudentFee::where('student_id', $student->id)
            ->where('balance', '>', 0)
            ->with('feeStructure')
            ->get();

        $totalBalance = $fees->sum('balance');
        $amount = (float) $request->amount;

        if ($totalBalance <= 0) {
            return response()->json(['message' => 'No outstanding balance for this student.'], 422);
        }

        if ($amount > $totalBalance) {
            return response()->json(['message' => "Amount exceeds outstanding balance of K {$totalBalance}."], 422);
        }

        // Generate unique payment reference
        $paymentReference = 'PAR-' . strtoupper(Str::random(10));

        // Find the student fee to link
        $studentFee = StudentFee::where('student_id', $student->id)
            ->where('balance', '>', 0)
            ->orderBy('created_at', 'asc')
            ->first();

        // Create QR payment record
        $qrPayment = QrPayment::create([
            'qr_code' => QrPayment::generateQrCode($paymentReference, $amount, $request->mobile_number),
            'payment_reference' => $paymentReference,
            'amount' => $amount,
            'customer_mobile' => $request->mobile_number,
            'student_id' => $student->id,
            'student_fee_id' => $studentFee?->id,
            'status' => 'pending',
            'initiated_at' => now(),
            'expires_at' => now()->addHours(24),
        ]);

        // Initiate CGrate payment — single attempt, unique reference per try
        $cgrateService = new CGrateService();
        $result = null;

        try {
            $result = $cgrateService->processCustomerPayment($amount, $request->mobile_number, $paymentReference);
        } catch (\Exception $e) {
            \Log::warning("CGrate payment failed: " . $e->getMessage());
            $result = ['success' => false, 'message' => 'Payment service is temporarily unavailable. Please try again in a few minutes.'];
        }

        if ($result && $result['success']) {
            $qrPayment->update([
                'status' => 'processing',
                'cgrate_payment_id' => $result['paymentID'] ?? $result['paymentId'] ?? null,
                'response_message' => $result['message'] ?? 'Payment initiated',
                'response_code' => $result['responseCode'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated. Please check your phone to approve the transaction.',
                'payment_reference' => $paymentReference,
                'payment_id' => $qrPayment->id,
                'amount' => $amount,
            ]);
        }

        // Determine error type
        $errorMsg = $result['message'] ?? 'Payment initiation failed.';
        $responseCode = $result['responseCode'] ?? '';
        $cgPaymentId = $result['paymentID'] ?? $result['paymentId'] ?? null;
        $isTimeout = str_contains(strtolower($errorMsg), 'timeout') || str_contains(strtolower($errorMsg), 'timed out') || str_contains(strtolower($errorMsg), 'delay') || str_contains(strtolower($errorMsg), 'unavailable');
        $isDuplicate = $responseCode === '104' || str_contains(strtolower($errorMsg), 'reference not unique');

        // Code 104 = CGrate already has this payment (from a timed-out first attempt)
        // This means CGrate DID receive it — treat as processing
        if ($isDuplicate && $cgPaymentId) {
            $qrPayment->update([
                'status' => 'processing',
                'cgrate_payment_id' => $cgPaymentId,
                'response_message' => 'Payment accepted by CGrate',
                'response_code' => $responseCode,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment is being processed. Please check your phone to approve.',
                'payment_reference' => $paymentReference,
                'payment_id' => $qrPayment->id,
                'amount' => $amount,
            ]);
        }

        if ($isTimeout) {
            $qrPayment->update([
                'status' => 'processing',
                'response_message' => 'Timeout - awaiting confirmation',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment request sent but confirmation is delayed. Check your phone — if you receive a payment prompt, approve it.',
                'payment_reference' => $paymentReference,
                'payment_id' => $qrPayment->id,
                'amount' => $amount,
                'is_delayed' => true,
            ]);
        }

        $qrPayment->update([
            'status' => 'failed',
            'response_message' => $errorMsg,
            'response_code' => $responseCode,
        ]);

        return response()->json([
            'success' => false,
            'message' => $errorMsg,
        ], 422);
    }

    public function checkPaymentStatus(Request $request)
    {
        $request->validate(['payment_id' => 'required|integer']);

        $qrPayment = QrPayment::find($request->payment_id);
        if (!$qrPayment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        // Verify ownership
        if ($qrPayment->student_id) {
            $childIds = $this->getChildIds();
            if (!in_array($qrPayment->student_id, $childIds)) {
                abort(403, 'Access denied.');
            }
        }

        if ($qrPayment->status === 'completed') {
            return response()->json([
                'status' => 'completed',
                'message' => 'Payment completed successfully!',
                'payment_reference' => $qrPayment->payment_reference,
                'amount' => $qrPayment->amount,
                'completed_at' => $qrPayment->completed_at?->format('d M Y, g:i A'),
                'receipt_url' => $this->generatePaymentReceiptUrl($qrPayment),
            ]);
        }

        if ($qrPayment->status === 'failed' || ($qrPayment->expires_at && $qrPayment->expires_at->isPast())) {
            if ($qrPayment->status !== 'failed') {
                $qrPayment->update(['status' => 'expired']);
            }
            return response()->json([
                'status' => $qrPayment->status,
                'message' => $qrPayment->response_message ?? 'Payment failed or expired.',
            ]);
        }

        // Query CGrate for live status — try payment reference first, then CGrate paymentID
        $cgrateService = new CGrateService();
        $result = $cgrateService->queryCustomerPayment($qrPayment->payment_reference);

        // If reference not found (code 106) and we have CGrate's paymentID, try that
        if (($result['responseCode'] ?? '') === '106' && $qrPayment->cgrate_payment_id) {
            $result = $cgrateService->queryCustomerPayment($qrPayment->cgrate_payment_id);
        }

        if ($result['payment_complete']) {
            $qrPayment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'response_message' => $result['message'] ?? 'Payment completed',
            ]);

            // Apply payment to student fees
            $this->applyPaymentToFees($qrPayment);

            return response()->json([
                'status' => 'completed',
                'message' => 'Payment completed successfully!',
                'payment_reference' => $qrPayment->payment_reference,
                'amount' => $qrPayment->amount,
                'completed_at' => now()->format('d M Y, g:i A'),
                'receipt_url' => $this->generatePaymentReceiptUrl($qrPayment),
            ]);
        }

        $paymentStatus = strtolower($result['payment_status'] ?? '');
        if (in_array($paymentStatus, ['failed', 'cancelled', 'declined', 'rejected'])) {
            $qrPayment->update([
                'status' => 'failed',
                'response_message' => $result['message'] ?? 'Payment failed',
            ]);
            return response()->json([
                'status' => 'failed',
                'message' => $result['message'] ?? 'Payment was not successful.',
            ]);
        }

        return response()->json([
            'status' => 'processing',
            'message' => 'Waiting for payment confirmation...',
        ]);
    }

    private function applyPaymentToFees(QrPayment $payment)
    {
        if (!$payment->student_id || $payment->status !== 'completed') return;

        // Duplicate protection — check if we already created transactions for this reference
        $existingCount = PaymentTransaction::where('external_reference', $payment->payment_reference)->count();
        if ($existingCount > 0) {
            \Log::info('Payment already applied, skipping duplicate: ' . $payment->payment_reference);
            return;
        }

        $fees = StudentFee::where('student_id', $payment->student_id)
            ->where('balance', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $remaining = $payment->amount;

        foreach ($fees as $fee) {
            if ($remaining <= 0) break;

            $apply = min($remaining, $fee->balance);
            $newPaid = $fee->amount_paid + $apply;
            $newBalance = $fee->balance - $apply;

            $fee->update([
                'amount_paid' => $newPaid,
                'balance' => max($newBalance, 0),
                'payment_status' => $newBalance <= 0 ? 'paid' : 'partial',
                'payment_date' => now(),
                'payment_method' => 'mobile_money',
            ]);

            // Create payment transaction record
            PaymentTransaction::create([
                'student_fee_id' => $fee->id,
                'academic_year_id' => $fee->academic_year_id,
                'amount' => $apply,
                'type' => 'payment',
                'payment_method' => 'mobile_money',
                'external_reference' => $payment->payment_reference,
                'notes' => 'Mobile money payment via parent app',
                'status' => 'completed',
                'processed_by' => Auth::id(),
                'transaction_date' => now(),
            ]);

            $remaining -= $apply;
        }

        // Handle overpayment
        if ($remaining > 0) {
            try {
                $lastFee = $fees->last();
                if ($lastFee) {
                    $bfService = new BalanceForwardService();
                    $bfService->processOverpayment($lastFee, $remaining);
                }
            } catch (\Exception $e) {
                \Log::warning('Overpayment forward failed: ' . $e->getMessage());
            }
        }

        // Send SMS confirmation to parent
        $this->sendPaymentSms($payment);
    }

    private function sendPaymentSms(QrPayment $payment)
    {
        try {
            $student = Student::with('parentGuardian')->find($payment->student_id);
            if (!$student) return;

            $parentPhone = $payment->customer_mobile
                ?? $student->parentGuardian?->phone
                ?? null;
            if (!$parentPhone) return;

            // Calculate new balance
            $newBalance = StudentFee::where('student_id', $student->id)->sum('balance');

            $amount = number_format($payment->amount, 2);
            $message = "St Francis of Assisi: Payment of K{$amount} received for {$student->name}. "
                . "Ref: {$payment->payment_reference}. "
                . "New balance: K" . number_format($newBalance, 2) . ". "
                . "Thank you.";

            $smsService = app(SmsService::class);
            $smsService->send($message, $parentPhone, 'payment', $payment->id);

            \Log::info('Payment SMS sent', [
                'phone' => $parentPhone,
                'reference' => $payment->payment_reference,
                'amount' => $payment->amount,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Payment SMS failed: ' . $e->getMessage());
        }
    }

    private function generatePaymentReceiptUrl(QrPayment $payment): ?string
    {
        if (!$payment->student_fee_id) return null;
        return '/portal/student-fees/' . $payment->student_fee_id . '/receipt/pdf';
    }
}
