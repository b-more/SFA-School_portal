<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSection;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\LeaveApplication;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\Notice;
use App\Models\ParentGuardian;
use App\Models\Payroll;
use App\Models\ReportCardComment;
use App\Models\Result;
use App\Models\CpdActivity;
use App\Models\CpdGoal;
use App\Models\CpdObservation;
use App\Models\CpdResource;
use App\Models\SchoolSettings;
use App\Models\StaffMessage;
use App\Models\Student;
use App\Models\SubjectTeaching;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\TimetableEntry;
use App\Models\TimetablePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherApiController extends Controller
{
    private function getTeacher(): ?Teacher
    {
        return Teacher::where('user_id', Auth::id())->where('is_active', true)->first();
    }

    public function dashboard()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json(['message' => 'Teacher record not found.'], 404);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $yearId = $activeYear?->id;

        // Teaching assignments
        $teachings = SubjectTeaching::where('teacher_id', $teacher->id)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->with(['subject', 'classSection.grade', 'classSection.students'])
            ->get();

        $totalStudents = $teachings->sum(fn($t) => $t->classSection?->students?->where('enrollment_status', 'active')->count() ?? 0);
        $totalClasses = $teachings->pluck('class_section_id')->unique()->count();
        $totalSubjects = $teachings->pluck('subject_id')->unique()->count();

        // Pending grading
        $hwIds = Homework::where('assigned_by', $teacher->id)->where('status', 'active')->pluck('id');
        $pendingGrading = HomeworkSubmission::whereIn('homework_id', $hwIds)->where('status', 'submitted')->count();

        // Today's timetable
        $today = now()->format('l');
        $todayClasses = [];
        if ($yearId) {
            $entries = TimetableEntry::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $yearId)
                ->where('day_of_week', $today)
                ->where('is_active', true)
                ->with(['timetablePeriod', 'subject', 'classSection.grade'])
                ->get()
                ->sortBy(fn($e) => $e->timetablePeriod?->order ?? 0);

            foreach ($entries as $e) {
                $todayClasses[] = [
                    'subject' => $e->subject?->name,
                    'class_name' => ($e->classSection?->grade?->name ?? '') . ' ' . ($e->classSection?->name ?? ''),
                    'room' => $e->room,
                    'start_time' => $e->timetablePeriod?->start_time?->format('H:i'),
                    'end_time' => $e->timetablePeriod?->end_time?->format('H:i'),
                ];
            }
        }

        // Class teacher info
        $classTeacherInfo = null;
        if ($teacher->is_class_teacher && $teacher->class_section_id) {
            $cs = $teacher->classSection()->with(['grade', 'students'])->first();
            $activeStudents = $cs?->students?->where('enrollment_status', 'active') ?? collect();
            $todayAtt = Attendance::where('class_section_id', $teacher->class_section_id)
                ->where('attendance_date', today())
                ->count();
            $studentCount = $activeStudents->count();

            $classTeacherInfo = [
                'class_name' => ($cs?->grade?->name ?? '') . ' ' . ($cs?->name ?? ''),
                'student_count' => $studentCount,
                'attendance_marked' => $todayAtt,
                'attendance_rate' => $studentCount > 0 ? round(($todayAtt / $studentCount) * 100) : 0,
            ];
        }

        return response()->json([
            'teacher_name' => $teacher->name,
            'total_students' => $totalStudents,
            'total_classes' => $totalClasses,
            'total_subjects' => $totalSubjects,
            'pending_grading' => $pendingGrading,
            'today_classes' => $todayClasses,
            'class_teacher_info' => $classTeacherInfo,
        ]);
    }

    public function myClasses()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json([], 404);

        $yearId = AcademicYear::where('is_active', true)->first()?->id;

        $teachings = SubjectTeaching::where('teacher_id', $teacher->id)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->with(['subject', 'classSection.grade', 'classSection.students'])
            ->get();

        return response()->json($teachings->map(fn($t) => [
            'id' => $t->id,
            'class_section_id' => $t->class_section_id,
            'subject_id' => $t->subject_id,
            'grade_id' => $t->classSection?->grade_id,
            'subject' => $t->subject?->name,
            'grade' => $t->classSection?->grade?->name,
            'class_section' => $t->classSection?->name,
            'student_count' => $t->classSection?->students?->where('enrollment_status', 'active')->count() ?? 0,
        ]));
    }

    public function classStudents($classSectionId)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json(['message' => 'Not found.'], 404);

        $students = Student::where('class_section_id', $classSectionId)
            ->where('enrollment_status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json(['students' => $students->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'gender' => $s->gender,
            'student_id_number' => $s->student_id_number,
            'profile_photo' => $s->profile_photo ? '/storage/' . $s->profile_photo : null,
        ])]);
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|integer',
            'date' => 'required|date',
            'attendance' => 'required|array|min:1',
            'attendance.*.student_id' => 'required|integer',
            'attendance.*.status' => 'required|in:present,absent,late,sick,excused',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();
        $student = Student::where('class_section_id', $request->class_section_id)->first();
        $gradeId = $student?->grade_id;

        $marked = 0;
        foreach ($request->attendance as $att) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $att['student_id'],
                    'attendance_date' => $request->date,
                ],
                [
                    'class_section_id' => $request->class_section_id,
                    'grade_id' => $gradeId,
                    'academic_year_id' => $activeYear?->id,
                    'term_id' => $activeTerm?->id,
                    'status' => $att['status'],
                    'notes' => $att['notes'] ?? null,
                    'marked_by' => Auth::id(),
                ]
            );
            $marked++;
        }

        return response()->json(['message' => "Attendance marked for {$marked} students.", 'count' => $marked]);
    }

    public function getAttendance($classSectionId, Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $students = Student::where('class_section_id', $classSectionId)
            ->where('enrollment_status', 'active')
            ->orderBy('name')
            ->get();

        $records = Attendance::where('class_section_id', $classSectionId)
            ->where('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        return response()->json([
            'date' => $date,
            'students' => $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'student_id_number' => $s->student_id_number,
                'status' => $records->get($s->id)?->status ?? null,
            ]),
        ]);
    }

    public function myHomework()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json(['homework' => []], 404);

        $homework = Homework::where('assigned_by', $teacher->id)
            ->with(['subject', 'grade'])
            ->withCount(['submissions', 'submissions as graded_count' => fn($q) => $q->where('status', 'graded')])
            ->orderBy('due_date', 'desc')
            ->take(30)
            ->get();

        return response()->json([
            'homework' => $homework->map(fn($hw) => [
                'id' => $hw->id,
                'title' => $hw->title,
                'description' => $hw->description,
                'subject' => $hw->subject?->name,
                'grade' => $hw->grade?->name,
                'due_date' => $hw->due_date?->format('d M Y'),
                'max_score' => $hw->max_score,
                'status' => $hw->status,
                'submission_count' => $hw->submissions_count,
                'graded_count' => $hw->graded_count,
            ]),
        ]);
    }

    public function createHomework(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json(['message' => 'Not found.'], 404);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|integer',
            'grade_id' => 'required|integer',
            'due_date' => 'required|date',
            'max_score' => 'nullable|integer|min:1',
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->storeAs('homework', $file->getClientOriginalName(), 'public');
        }

        $hw = Homework::create([
            'title' => $request->title,
            'description' => $request->description ?? '',
            'assigned_by' => $teacher->id,
            'subject_id' => $request->subject_id,
            'grade_id' => $request->grade_id,
            'due_date' => $request->due_date,
            'max_score' => $request->max_score ?? 100,
            'status' => 'active',
            'academic_year_id' => $activeYear?->id,
            'homework_file' => $filePath,
        ]);

        return response()->json(['message' => 'Homework created.', 'id' => $hw->id], 201);
    }

    public function homeworkSubmissions($homeworkId)
    {
        $teacher = $this->getTeacher();
        $hw = Homework::where('id', $homeworkId)->where('assigned_by', $teacher?->id)->first();
        if (!$hw) return response()->json(['message' => 'Homework not found.'], 404);

        $subs = HomeworkSubmission::where('homework_id', $homeworkId)
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->get();

        return response()->json([
            'homework_title' => $hw->title,
            'subject' => $hw->subject?->name,
            'max_score' => $hw->max_score,
            'submissions' => $subs->map(fn($s) => [
                'id' => $s->id,
                'student_name' => $s->student?->name,
                'content' => $s->content,
                'file_url' => $s->file_attachment ? '/storage/' . ltrim(str_replace(['\\', '"'], ['/', ''], $s->file_attachment), '/') : null,
                'submitted_at' => $s->submitted_at?->format('d M Y, g:i A'),
                'is_late' => (bool) $s->is_late,
                'status' => $s->status,
                'marks' => $s->marks,
                'feedback' => $s->feedback,
            ]),
        ]);
    }

    public function gradeSubmission(Request $request, $submissionId)
    {
        $request->validate([
            'marks' => 'required|numeric|min:0',
            'feedback' => 'nullable|string|max:5000',
            'teacher_notes' => 'nullable|string|max:5000',
        ]);

        $teacher = $this->getTeacher();
        $sub = HomeworkSubmission::with('homework')->find($submissionId);
        if (!$sub || $sub->homework?->assigned_by !== $teacher?->id) {
            return response()->json(['message' => 'Submission not found.'], 404);
        }

        $sub->update([
            'marks' => $request->marks,
            'feedback' => $request->feedback ?? '',
            'teacher_notes' => $request->teacher_notes ?? '',
            'graded_by' => Auth::id(),
            'graded_at' => now(),
            'status' => 'graded',
        ]);

        return response()->json(['message' => 'Submission graded.']);
    }

    public function enterResults(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|integer',
            'class_section_id' => 'required|integer',
            'exam_type' => 'required|string',
            'results' => 'required|array|min:1',
            'results.*.student_id' => 'required|integer',
            'results.*.marks' => 'required|numeric|min:0|max:100',
        ]);

        $teacher = $this->getTeacher();
        $activeTerm = Term::where('is_active', true)->first();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $saved = 0;
        foreach ($request->results as $r) {
            $marks = $r['marks'];
            $grade = $marks >= 80 ? 'A' : ($marks >= 70 ? 'B' : ($marks >= 60 ? 'C' : ($marks >= 50 ? 'D' : ($marks >= 40 ? 'E' : 'F'))));

            Result::updateOrCreate(
                [
                    'student_id' => $r['student_id'],
                    'subject_id' => $request->subject_id,
                    'exam_type' => $request->exam_type,
                    'term' => $activeTerm?->name,
                    'term_id' => $activeTerm?->id,
                    'year' => $activeYear?->name ? (int) substr($activeYear->name, 0, 4) : date('Y'),
                ],
                [
                    'marks' => $marks,
                    'academic_year_id' => $activeYear?->id,
                    'grade' => $grade,
                    'recorded_by' => $teacher?->id,
                ]
            );
            $saved++;
        }

        return response()->json(['message' => "Results saved for {$saved} students.", 'count' => $saved]);
    }

    public function getResults($classSectionId, $subjectId)
    {
        $students = Student::where('class_section_id', $classSectionId)
            ->where('enrollment_status', 'active')
            ->orderBy('name')
            ->get();

        $results = Result::where('subject_id', $subjectId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return response()->json([
            'students' => $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'marks' => $results->get($s->id)?->marks,
                'grade' => $results->get($s->id)?->grade,
            ]),
        ]);
    }

    public function myTimetable()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json(['periods' => [], 'days' => []], 404);

        $yearId = AcademicYear::where('is_active', true)->first()?->id;
        if (!$yearId) return response()->json(['periods' => [], 'days' => TimetableEntry::DAYS]);

        $entries = TimetableEntry::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $yearId)
            ->where('is_active', true)
            ->with(['timetablePeriod', 'subject', 'classSection.grade'])
            ->get();

        $periods = TimetablePeriod::where('academic_year_id', $yearId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $grid = [];
        foreach ($periods as $period) {
            $row = [
                'period' => $period->name,
                'short_name' => $period->short_name,
                'start_time' => $period->start_time?->format('H:i'),
                'end_time' => $period->end_time?->format('H:i'),
                'type' => $period->type,
                'days' => [],
            ];
            foreach (TimetableEntry::DAYS as $day) {
                $e = $entries->first(fn($en) => $en->timetable_period_id === $period->id && $en->day_of_week === $day);
                $row['days'][$day] = $e ? [
                    'subject' => $e->subject?->name,
                    'class_name' => ($e->classSection?->grade?->name ?? '') . ' ' . ($e->classSection?->name ?? ''),
                    'room' => $e->room,
                ] : null;
            }
            $grid[] = $row;
        }

        return response()->json(['periods' => $grid, 'days' => TimetableEntry::DAYS]);
    }

    public function notices()
    {
        $notices = Notice::whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($notices->map(fn($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'body' => strip_tags($n->body),
            'priority' => $n->priority,
            'posted_by' => $n->postedBy?->name ?? 'Admin',
            'date' => $n->published_at?->format('d M Y'),
        ]));
    }

    public function profile()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json(['message' => 'Not found.'], 404);

        $employee = Employee::where('user_id', Auth::id())->first()
            ?? Employee::find($teacher->employee_id);

        return response()->json([
            // Teacher info
            'name' => $teacher->name,
            'email' => $teacher->email ?? $employee?->email,
            'phone' => $teacher->phone ?? $employee?->phone,
            'qualification' => $teacher->qualification ?? $employee?->highest_qualification,
            'specialization' => $teacher->specialization,
            'join_date' => $teacher->join_date?->format('d M Y') ?? $employee?->joining_date?->format('d M Y'),
            'is_class_teacher' => $teacher->is_class_teacher,
            'is_grade_teacher' => $teacher->is_grade_teacher,
            'grade' => $teacher->grade?->name,
            'class_section' => $teacher->classSection?->name,
            'profile_photo' => $teacher->profile_photo ? '/storage/' . $teacher->profile_photo : ($employee?->profile_photo ? '/storage/' . $employee->profile_photo : null),
            'biography' => $teacher->biography,

            // Employment details
            'employee_number' => $employee?->employee_number,
            'department' => $employee?->department,
            'position' => $employee?->position,
            'employment_type' => $employee?->employment_type,
            'gender' => $employee?->gender,
            'date_of_birth' => $employee?->date_of_birth?->format('d M Y'),
            'address' => $employee?->address,
            'city' => $employee?->city,
            'province' => $employee?->province,
            'nrc_number' => $employee?->nrc_number,
            'napsa_number' => $employee?->napsa_number,
            'tpin_number' => $employee?->tpin_number,
            'bank_name' => $employee?->bank_name,
            'bank_branch' => $employee?->bank_branch,
            'bank_account_number' => $employee?->bank_account_number ? '****' . substr($employee->bank_account_number, -4) : null,
            'emergency_contact_name' => $employee?->emergency_contact_name,
            'emergency_contact_phone' => $employee?->emergency_contact_phone,
            'emergency_contact_relationship' => $employee?->emergency_contact_relationship,
            'next_of_kin_name' => $employee?->next_of_kin_name,
            'next_of_kin_phone' => $employee?->next_of_kin_phone,
            'years_of_service' => $employee?->years_of_service,
        ]);
    }

    public function payslips()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json([], 404);

        $employee = Employee::where('user_id', Auth::id())->first()
            ?? Employee::find($teacher->employee_id);
        if (!$employee) return response()->json([]);

        $payrolls = Payroll::where('employee_id', $employee->id)
            ->orderBy('year', 'desc')
            ->orderByRaw("FIELD(month,'December','November','October','September','August','July','June','May','April','March','February','January') DESC")
            ->get();

        return response()->json($payrolls->map(fn($p) => [
            'id' => $p->id,
            'month' => $p->month,
            'year' => $p->year,
            'basic_salary' => $p->basic_salary,
            'gross_salary' => $p->gross_salary,
            'net_salary' => $p->net_salary,
            'allowances' => $p->allowances,
            'deductions' => $p->deductions,
            'status' => $p->payment_status,
            'payment_date' => $p->payment_date?->format('d M Y'),
            'download_url' => '/payslips/' . $p->id . '/download',
            'view_url' => '/payslips/' . $p->id,
        ]));
    }

    public function myStudents()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json([], 404);

        $yearId = AcademicYear::where('is_active', true)->first()?->id;

        // Get all class sections taught
        $teachings = SubjectTeaching::where('teacher_id', $teacher->id)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->with(['subject', 'classSection.grade'])
            ->get();

        $classSections = $teachings->pluck('classSection')->filter()->unique('id');

        $result = [];
        foreach ($classSections as $cs) {
            $students = Student::where('class_section_id', $cs->id)
                ->where('enrollment_status', 'active')
                ->orderBy('name')
                ->get();

            $subjects = $teachings->where('class_section_id', $cs->id)->pluck('subject.name')->filter()->values();

            $result[] = [
                'class_section_id' => $cs->id,
                'class_name' => ($cs->grade?->name ?? '') . ' ' . ($cs->name ?? ''),
                'grade' => $cs->grade?->name,
                'section' => $cs->name,
                'subjects' => $subjects,
                'student_count' => $students->count(),
                'students' => $students->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'gender' => $s->gender,
                    'student_id_number' => $s->student_id_number,
                    'profile_photo' => $s->profile_photo ? '/storage/' . $s->profile_photo : null,
                ]),
            ];
        }

        return response()->json($result);
    }

    // ── Head Teacher Dashboard (section-filtered) ──
    public function headDashboard()
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();
        $yearId = $activeYear?->id;

        // Section filtering based on role
        $sectionIds = null;
        $sectionLabel = 'School';
        if ($roleId == \App\Constants\RoleConstants::HEAD_TEACHER_PRIMARY) {
            $sectionIds = [1, 2, 3];
            $sectionLabel = 'Primary Section';
        } elseif ($roleId == \App\Constants\RoleConstants::HEAD_TEACHER_SECONDARY) {
            $sectionIds = [4, 5];
            $sectionLabel = 'Secondary Section';
        }
        // Deputy heads and Admin see everything (sectionIds stays null)

        // Grades for this section
        $gradeQuery = \App\Models\Grade::query();
        if ($sectionIds) $gradeQuery->whereIn('school_section_id', $sectionIds);
        $grades = $gradeQuery->orderBy('id')->get();
        $gradeIds = $grades->pluck('id')->toArray();

        // Students
        $studentQuery = Student::where('enrollment_status', 'active');
        if ($gradeIds) $studentQuery->whereIn('grade_id', $gradeIds);
        $totalStudents = $studentQuery->count();
        $studentIds = (clone $studentQuery)->pluck('id')->toArray();

        // Teachers — find by section_id, grade_id, OR subject_teachings in section classes
        if ($sectionIds) {
            // Get class section IDs in these grades
            $sectionClassIds = ClassSection::whereIn('grade_id', $gradeIds)->pluck('id');
            // Find teacher IDs who teach in these class sections
            $teachingTeacherIds = SubjectTeaching::whereIn('class_section_id', $sectionClassIds)
                ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
                ->pluck('teacher_id')->unique();

            $sectionTeachers = Teacher::where('is_active', true)
                ->where(fn($q) => $q
                    ->whereIn('school_section_id', $sectionIds)
                    ->orWhereIn('grade_id', $gradeIds)
                    ->orWhereIn('id', $teachingTeacherIds)
                )
                ->with(['grade', 'classSection'])
                ->get();
        } else {
            $sectionTeachers = Teacher::where('is_active', true)->with(['grade', 'classSection'])->get();
        }
        $totalTeachers = $sectionTeachers->count();

        // Classes
        $classQuery = ClassSection::where('is_active', true);
        if ($gradeIds) $classQuery->whereIn('grade_id', $gradeIds);
        if ($yearId) $classQuery->where('academic_year_id', $yearId);
        $totalClasses = $classQuery->count();

        // Attendance
        $todayAtt = Attendance::where('attendance_date', today())
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->when($gradeIds, fn($q) => $q->whereIn('grade_id', $gradeIds))->get();
        $attPresent = $todayAtt->whereIn('status', ['present', 'late'])->count();
        $attTotal = $todayAtt->count();
        $attRate = $attTotal > 0 ? round(($attPresent / $attTotal) * 100) : 0;
        $classesMarked = $todayAtt->pluck('class_section_id')->unique()->count();

        // Fees
        $feeQuery = \App\Models\StudentFee::query();
        if ($yearId) $feeQuery->where('academic_year_id', $yearId);
        if ($studentIds) $feeQuery->whereIn('student_id', $studentIds);
        $allFees = $feeQuery->with('feeStructure')->get();
        $totalFees = $allFees->sum(fn($f) => $f->feeStructure?->basic_fee ?? 0);
        $totalCollected = $allFees->sum('amount_paid');

        // Homework
        $hwQuery = Homework::where('status', 'active');
        if ($gradeIds) $hwQuery->whereIn('grade_id', $gradeIds);
        $pendingGrading = HomeworkSubmission::whereIn('homework_id', $hwQuery->pluck('id'))->where('status', 'submitted')->count();

        // CPD
        $cpdYear = $activeYear?->name ?? date('Y');
        $cpdCompliant = 0;
        foreach ($sectionTeachers as $t) {
            if (CpdActivity::where('user_id', $t->user_id)->where('academic_year', $cpdYear)->where('status', 'completed')->sum('hours') >= 40) $cpdCompliant++;
        }

        // Grade attendance
        $gradeAtt = [];
        foreach ($grades as $g) {
            $gIds = Student::where('grade_id', $g->id)->where('enrollment_status', 'active')->pluck('id');
            if ($gIds->isEmpty()) continue;
            $gRecs = $todayAtt->whereIn('student_id', $gIds);
            $gPresent = $gRecs->whereIn('status', ['present', 'late'])->count();
            $gradeAtt[] = ['grade' => $g->name, 'present' => $gPresent, 'total' => $gRecs->count(), 'students' => $gIds->count(), 'rate' => $gRecs->count() > 0 ? round(($gPresent / $gRecs->count()) * 100) : 0];
        }

        // Teachers list
        $teacherList = $sectionTeachers->map(fn($t) => [
            'name' => $t->name, 'grade' => $t->grade?->name, 'class' => $t->classSection?->name,
            'is_class_teacher' => $t->is_class_teacher, 'phone' => $t->phone,
        ])->sortBy('name')->values();

        // Leave
        $empIds = Employee::whereIn('user_id', $sectionTeachers->pluck('user_id'))->pluck('id');
        $pendingLeave = LeaveApplication::whereIn('employee_id', $empIds)->where('status', 'pending')->count();

        return response()->json([
            'school_name' => SchoolSettings::first()?->school_name ?? 'St. Francis of Assisi',
            'section_label' => $sectionLabel,
            'term' => $activeTerm?->name, 'year' => $activeYear?->name,
            'total_students' => $totalStudents, 'total_teachers' => $totalTeachers, 'total_classes' => $totalClasses,
            'att_rate' => $attRate, 'att_present' => $attPresent, 'att_absent' => $todayAtt->where('status', 'absent')->count(),
            'att_total' => $attTotal, 'classes_marked' => $classesMarked, 'unmarked_classes' => $totalClasses - $classesMarked,
            'collection_rate' => $totalFees > 0 ? round(($totalCollected / $totalFees) * 100) : 0,
            'pending_grading' => $pendingGrading,
            'cpd_compliant' => $cpdCompliant, 'cpd_total' => $totalTeachers, 'cpd_rate' => $totalTeachers > 0 ? round(($cpdCompliant / $totalTeachers) * 100) : 0,
            'pending_leave' => $pendingLeave,
            'open_complaints' => \App\Models\Complaint::where('status', 'open')->count(),
            'grade_attendance' => $gradeAtt,
            'teachers' => $teacherList,
            'recent_notices' => Notice::whereNotNull('published_at')->orderBy('published_at', 'desc')->take(5)->get()->map(fn($n) => ['title' => $n->title, 'date' => $n->published_at?->format('d M'), 'priority' => $n->priority]),
        ]);
    }

    // ── Feature 1: Attendance Analytics ──
    public function attendanceAnalytics($classSectionId)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();

        $students = Student::where('class_section_id', $classSectionId)->where('enrollment_status', 'active')->get();
        $studentIds = $students->pluck('id');

        $records = Attendance::whereIn('student_id', $studentIds)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->when($activeTerm, fn($q) => $q->where('term_id', $activeTerm->id))
            ->get();

        $total = $records->count();
        $byStatus = $records->groupBy('status')->map->count();

        // Weekly trend (last 8 weeks)
        $weeklyTrend = [];
        for ($w = 7; $w >= 0; $w--) {
            $weekStart = now()->subWeeks($w)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            $weekRecords = $records->filter(fn($r) => $r->attendance_date->between($weekStart, $weekEnd));
            $weekTotal = $weekRecords->count();
            $weekPresent = $weekRecords->whereIn('status', ['present', 'late'])->count();
            $weeklyTrend[] = [
                'week' => $weekStart->format('d M'),
                'rate' => $weekTotal > 0 ? round(($weekPresent / $weekTotal) * 100) : 0,
                'total' => $weekTotal,
            ];
        }

        // Students with high absenteeism (>20%)
        $atRisk = [];
        foreach ($students as $s) {
            $sRecords = $records->where('student_id', $s->id);
            $sTotal = $sRecords->count();
            $sAbsent = $sRecords->where('status', 'absent')->count();
            if ($sTotal > 0 && ($sAbsent / $sTotal) > 0.2) {
                $atRisk[] = ['name' => $s->name, 'absent' => $sAbsent, 'total' => $sTotal, 'rate' => round(($sAbsent / $sTotal) * 100)];
            }
        }

        $overallRate = $total > 0 ? round(($records->whereIn('status', ['present', 'late'])->count() / $total) * 100, 1) : 0;

        return response()->json([
            'overall_rate' => $overallRate,
            'total_records' => $total,
            'by_status' => $byStatus,
            'weekly_trend' => $weeklyTrend,
            'at_risk_students' => $atRisk,
            'student_count' => $students->count(),
        ]);
    }

    // ── Feature 2: Homework Analytics ──
    public function homeworkAnalytics()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json([], 404);

        $homework = Homework::where('assigned_by', $teacher->id)
            ->with(['subject', 'grade', 'submissions'])
            ->get();

        $totalHw = $homework->count();
        $totalSubs = $homework->sum(fn($h) => $h->submissions->count());
        $totalGraded = $homework->sum(fn($h) => $h->submissions->where('status', 'graded')->count());
        $totalLate = $homework->sum(fn($h) => $h->submissions->where('is_late', true)->count());
        $avgScore = $homework->flatMap(fn($h) => $h->submissions->whereNotNull('marks')->pluck('marks'));
        $avgScoreVal = $avgScore->count() > 0 ? round($avgScore->avg(), 1) : null;

        // Per subject breakdown
        $bySubject = $homework->groupBy(fn($h) => $h->subject?->name ?? 'Unknown')->map(function ($items, $subject) {
            $subs = $items->flatMap->submissions;
            return [
                'subject' => $subject,
                'homework_count' => $items->count(),
                'submissions' => $subs->count(),
                'graded' => $subs->where('status', 'graded')->count(),
                'avg_score' => $subs->whereNotNull('marks')->count() > 0 ? round($subs->whereNotNull('marks')->avg('marks'), 1) : null,
            ];
        })->values();

        return response()->json([
            'total_homework' => $totalHw,
            'total_submissions' => $totalSubs,
            'total_graded' => $totalGraded,
            'pending_grading' => $totalSubs - $totalGraded,
            'late_submissions' => $totalLate,
            'average_score' => $avgScoreVal,
            'by_subject' => $bySubject,
        ]);
    }

    // ── Feature 4: Student Performance Tracker ──
    public function studentPerformance($studentId)
    {
        $student = Student::with(['grade', 'classSection'])->find($studentId);
        if (!$student) return response()->json(['message' => 'Student not found.'], 404);

        $results = Result::where('student_id', $studentId)->with('subject')->orderBy('created_at')->get();

        $bySubject = $results->groupBy(fn($r) => $r->subject?->name ?? 'Unknown')->map(function ($items, $subject) {
            return [
                'subject' => $subject,
                'results' => $items->map(fn($r) => ['marks' => $r->marks, 'grade' => $r->grade, 'exam_type' => $r->exam_type, 'term' => $r->term])->values(),
                'average' => round($items->avg('marks'), 1),
                'highest' => $items->max('marks'),
                'lowest' => $items->min('marks'),
                'trend' => $items->count() >= 2 ? ($items->last()->marks > $items->first()->marks ? 'improving' : ($items->last()->marks < $items->first()->marks ? 'declining' : 'stable')) : 'insufficient',
            ];
        })->values();

        return response()->json([
            'student' => ['name' => $student->name, 'grade' => $student->grade?->name, 'class' => $student->classSection?->name],
            'overall_average' => $results->count() > 0 ? round($results->avg('marks'), 1) : null,
            'total_results' => $results->count(),
            'by_subject' => $bySubject,
        ]);
    }

    // ── Feature 5: Send Notice to Parents ──
    public function sendClassNotice(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher || !$teacher->is_class_teacher) {
            return response()->json(['message' => 'Only class teachers can send notices.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'priority' => 'required|in:normal,important,urgent',
        ]);

        $notice = Notice::create([
            'title' => $request->title,
            'body' => $request->body,
            'priority' => $request->priority,
            'audience' => $teacher->classSection?->grade?->name . ' ' . ($teacher->classSection?->name ?? ''),
            'target_type' => 'class',
            'posted_by' => Auth::id(),
            'published_at' => now(),
            'status' => 'published',
        ]);

        return response()->json(['message' => 'Notice sent to parents.', 'id' => $notice->id], 201);
    }

    // ── Feature 7: Leave Application ──
    public function leaveBalances()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) return response()->json([]);

        $year = date('Y');
        $balances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $year)
            ->with('leaveType')
            ->get();

        return response()->json($balances->map(fn($b) => [
            'id' => $b->id,
            'type' => $b->leaveType?->name,
            'code' => $b->leaveType?->code,
            'allocated' => $b->allocated_days,
            'used' => $b->used_days,
            'remaining' => $b->remaining_days,
            'carried_forward' => $b->carried_forward,
        ]));
    }

    public function leaveApplications()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) return response()->json([]);

        $applications = LeaveApplication::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($applications->map(fn($a) => [
            'id' => $a->id,
            'reference' => $a->reference_number,
            'type' => $a->leaveType?->name,
            'start_date' => $a->start_date?->format('d M Y'),
            'end_date' => $a->end_date?->format('d M Y'),
            'days' => $a->days_requested,
            'reason' => $a->reason,
            'status' => $a->status,
            'status_label' => $a->status_label ?? ucfirst(str_replace('_', ' ', $a->status)),
        ]));
    }

    public function applyLeave(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) return response()->json(['message' => 'Employee record not found.'], 404);

        $request->validate([
            'leave_type_id' => 'required|integer|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'contact_during_leave' => 'nullable|string|max:20',
        ]);

        $days = now()->parse($request->start_date)->diffInDays(now()->parse($request->end_date)) + 1;

        $application = LeaveApplication::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $days,
            'reason' => $request->reason,
            'contact_during_leave' => $request->contact_during_leave,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Leave application submitted.', 'reference' => $application->reference_number], 201);
    }

    // ── Feature 9: Report Card Comments ──
    public function reportCardStudents()
    {
        $teacher = $this->getTeacher();
        if (!$teacher || !$teacher->is_class_teacher) {
            return response()->json(['message' => 'Only class teachers can enter report card comments.'], 403);
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();

        $students = Student::where('class_section_id', $teacher->class_section_id)
            ->where('enrollment_status', 'active')
            ->orderBy('name')
            ->get();

        $comments = ReportCardComment::where('term_id', $activeTerm?->id)
            ->where('academic_year_id', $activeYear?->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return response()->json([
            'term' => $activeTerm?->name,
            'year' => $activeYear?->name,
            'students' => $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'comment' => $comments->get($s->id)?->class_teacher_comment,
                'commented_at' => $comments->get($s->id)?->class_teacher_commented_at?->format('d M Y'),
            ]),
        ]);
    }

    public function saveReportCardComment(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher || !$teacher->is_class_teacher) {
            return response()->json(['message' => 'Only class teachers can enter comments.'], 403);
        }

        $request->validate([
            'student_id' => 'required|integer',
            'comment' => 'required|string|max:500',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();

        $rc = ReportCardComment::updateOrCreate(
            ['student_id' => $request->student_id, 'term_id' => $activeTerm?->id, 'academic_year_id' => $activeYear?->id],
            ['class_teacher_comment' => $request->comment, 'class_teacher_id' => $teacher->id, 'class_teacher_commented_at' => now()]
        );

        return response()->json(['message' => 'Comment saved.']);
    }

    // ── Feature 10: Parent Contact Directory ──
    public function parentContacts()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json([], 404);

        $classSectionId = $teacher->class_section_id;
        if (!$classSectionId) {
            // Get from teachings
            $yearId = AcademicYear::where('is_active', true)->first()?->id;
            $classSectionId = SubjectTeaching::where('teacher_id', $teacher->id)
                ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
                ->value('class_section_id');
        }

        $students = Student::where('class_section_id', $classSectionId)
            ->where('enrollment_status', 'active')
            ->with(['parentGuardian', 'grade'])
            ->orderBy('name')
            ->get();

        return response()->json($students->map(fn($s) => [
            'student_name' => $s->name,
            'grade' => $s->grade?->name,
            'parent_name' => $s->parentGuardian?->name ?? '-',
            'parent_phone' => $s->parentGuardian?->phone ?? '-',
            'parent_email' => $s->parentGuardian?->email ?? '-',
            'relationship' => $s->parentGuardian?->relationship ?? '-',
        ]));
    }

    // ── Feature 12: Staff Directory ──
    public function staffDirectory()
    {
        $teachers = Teacher::where('is_active', true)
            ->with(['grade', 'classSection'])
            ->orderBy('name')
            ->get();

        return response()->json($teachers->map(fn($t) => [
            'user_id' => $t->user_id,
            'name' => $t->name,
            'phone' => $t->phone,
            'email' => $t->email,
            'grade' => $t->grade?->name,
            'class_section' => $t->classSection?->name,
            'is_class_teacher' => $t->is_class_teacher,
            'specialization' => $t->specialization,
            'profile_photo' => $t->profile_photo ? '/storage/' . $t->profile_photo : null,
        ]));
    }

    // ── Feature 13: School Calendar ──
    public function schoolCalendar()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return response()->json(['terms' => [], 'events' => []]);

        $terms = Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get();

        $events = Event::where('start_date', '>=', now()->subMonths(1))
            ->orderBy('start_date')
            ->take(20)
            ->get();

        return response()->json([
            'year' => $activeYear->name,
            'terms' => $terms->map(fn($t) => [
                'name' => $t->name,
                'start_date' => $t->start_date?->format('d M Y'),
                'end_date' => $t->end_date?->format('d M Y'),
                'is_active' => $t->is_active,
            ]),
            'events' => $events->map(fn($e) => [
                'title' => $e->title,
                'date' => $e->start_date?->format('d M Y'),
                'time' => $e->start_date?->format('g:i A'),
                'description' => $e->description,
            ]),
        ]);
    }

    // ── Messaging ──
    public function conversations()
    {
        $userId = Auth::id();

        // Get all unique conversation partners with latest message
        $sent = StaffMessage::where('sender_id', $userId)->select('recipient_id as partner_id');
        $received = StaffMessage::where('recipient_id', $userId)->select('sender_id as partner_id');

        $partnerIds = $sent->union($received)->pluck('partner_id')->unique();

        $conversations = [];
        foreach ($partnerIds as $partnerId) {
            $partner = Teacher::where('user_id', $partnerId)->first();
            $partnerUser = \App\Models\User::find($partnerId);

            $lastMsg = StaffMessage::where(function ($q) use ($userId, $partnerId) {
                $q->where('sender_id', $userId)->where('recipient_id', $partnerId);
            })->orWhere(function ($q) use ($userId, $partnerId) {
                $q->where('sender_id', $partnerId)->where('recipient_id', $userId);
            })->latest()->first();

            $unread = StaffMessage::where('sender_id', $partnerId)
                ->where('recipient_id', $userId)
                ->whereNull('read_at')
                ->count();

            $conversations[] = [
                'user_id' => $partnerId,
                'name' => $partner?->name ?? $partnerUser?->name ?? 'Unknown',
                'profile_photo' => $partner?->profile_photo ? '/storage/' . $partner->profile_photo : null,
                'grade' => $partner?->grade?->name,
                'last_message' => $lastMsg?->message ? (strlen($lastMsg->message) > 50 ? substr($lastMsg->message, 0, 50) . '...' : $lastMsg->message) : ($lastMsg?->file_name ? '📎 ' . $lastMsg->file_name : ''),
                'last_time' => $lastMsg?->created_at?->diffForHumans(),
                'last_timestamp' => $lastMsg?->created_at?->timestamp ?? 0,
                'unread' => $unread,
                'is_mine' => $lastMsg?->sender_id === $userId,
            ];
        }

        // Sort by latest message
        usort($conversations, fn($a, $b) => $b['last_timestamp'] - $a['last_timestamp']);

        $totalUnread = StaffMessage::where('recipient_id', $userId)->whereNull('read_at')->count();

        return response()->json([
            'conversations' => $conversations,
            'total_unread' => $totalUnread,
        ]);
    }

    public function chatMessages($partnerId, Request $request)
    {
        $userId = Auth::id();

        // Mark received messages as read
        StaffMessage::where('sender_id', $partnerId)
            ->where('recipient_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = StaffMessage::where(function ($q) use ($userId, $partnerId) {
            $q->where('sender_id', $userId)->where('recipient_id', $partnerId);
        })->orWhere(function ($q) use ($userId, $partnerId) {
            $q->where('sender_id', $partnerId)->where('recipient_id', $userId);
        })->orderBy('created_at', 'asc')
          ->take(100)
          ->get();

        $partner = Teacher::where('user_id', $partnerId)->first();

        return response()->json([
            'partner' => [
                'user_id' => (int) $partnerId,
                'name' => $partner?->name ?? 'Unknown',
                'profile_photo' => $partner?->profile_photo ? '/storage/' . $partner->profile_photo : null,
                'grade' => $partner?->grade?->name,
            ],
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id,
                'is_mine' => $m->sender_id === $userId,
                'message' => $m->message,
                'file_path' => $m->file_path ? '/storage/' . $m->file_path : null,
                'file_name' => $m->file_name,
                'file_type' => $m->file_type,
                'file_size' => $m->file_size,
                'read_at' => $m->read_at?->format('H:i'),
                'time' => $m->created_at->format('H:i'),
                'date' => $m->created_at->format('d M Y'),
            ]),
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'message' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:10240',
        ]);

        if (!$request->message && !$request->hasFile('file')) {
            return response()->json(['message' => 'Message or file is required.'], 422);
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            $filePath = $file->storeAs('staff-messages/' . Auth::id(), $fileName, 'public');
        }

        $msg = StaffMessage::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'message' => $request->message,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ]);

        return response()->json([
            'id' => $msg->id,
            'message' => 'Sent.',
            'time' => $msg->created_at->format('H:i'),
        ]);
    }

    // ══════════════ CPD MODULE ══════════════

    public function cpdDashboard()
    {
        $userId = Auth::id();
        $year = AcademicYear::where('is_active', true)->first()?->name ?? date('Y');
        $targetHours = 40; // TSC minimum per year

        $activities = CpdActivity::where('user_id', $userId)->where('academic_year', $year)->get();
        $totalHours = $activities->where('status', 'completed')->sum('hours');
        $byType = $activities->where('status', 'completed')->groupBy('type')->map(fn($g) => [
            'count' => $g->count(),
            'hours' => (float) $g->sum('hours'),
        ]);

        $goals = CpdGoal::where('user_id', $userId)->where('academic_year', $year)->get();
        $goalsAchieved = $goals->where('status', 'achieved')->count();

        $observations = CpdObservation::where('teacher_user_id', $userId)
            ->orderBy('observation_date', 'desc')->take(5)->get();

        $certificates = $activities->whereNotNull('certificate_file')->count();

        return response()->json([
            'total_hours' => (float) $totalHours,
            'target_hours' => $targetHours,
            'hours_progress' => $targetHours > 0 ? min(100, round(($totalHours / $targetHours) * 100)) : 0,
            'activities_count' => $activities->count(),
            'completed_count' => $activities->where('status', 'completed')->count(),
            'planned_count' => $activities->whereIn('status', ['planned', 'in_progress'])->count(),
            'certificates_count' => $certificates,
            'by_type' => $byType,
            'goals_total' => $goals->count(),
            'goals_achieved' => $goalsAchieved,
            'goals' => $goals->map(fn($g) => [
                'id' => $g->id,
                'title' => $g->title,
                'status' => $g->status,
                'target_date' => $g->target_date?->format('d M Y'),
                'achieved_date' => $g->achieved_date?->format('d M Y'),
            ]),
            'recent_observations' => $observations->map(fn($o) => [
                'id' => $o->id,
                'date' => $o->observation_date->format('d M Y'),
                'subject' => $o->subject,
                'observer' => Teacher::where('user_id', $o->observer_user_id)->first()?->name ?? 'Unknown',
                'rating' => $o->rating,
                'has_reflection' => !empty($o->teacher_reflection),
            ]),
            'year' => $year,
        ]);
    }

    public function cpdActivities()
    {
        $activities = CpdActivity::where('user_id', Auth::id())
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($activities->map(fn($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'type' => $a->type,
            'type_label' => ucfirst(str_replace('_', ' ', $a->type)),
            'provider' => $a->provider,
            'start_date' => $a->start_date->format('d M Y'),
            'end_date' => $a->end_date?->format('d M Y'),
            'hours' => (float) $a->hours,
            'description' => $a->description,
            'reflection' => $a->reflection,
            'key_learnings' => $a->key_learnings,
            'certificate_url' => $a->certificate_file ? '/storage/' . $a->certificate_file : null,
            'status' => $a->status,
            'academic_year' => $a->academic_year,
            'term' => $a->term,
        ]));
    }

    public function createCpdActivity(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:workshop,course,conference,seminar,peer_observation,self_study,mentoring,online_training,research,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'hours' => 'required|numeric|min:0.5|max:200',
            'provider' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'reflection' => 'nullable|string|max:3000',
            'key_learnings' => 'nullable|string|max:3000',
            'status' => 'required|in:planned,in_progress,completed',
            'certificate' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $year = AcademicYear::where('is_active', true)->first()?->name ?? date('Y');
        $term = Term::where('is_active', true)->first()?->name;

        $certPath = null;
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $certPath = $file->storeAs('cpd-certificates/' . Auth::id(), $file->getClientOriginalName(), 'public');
        }

        $activity = CpdActivity::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'type' => $request->type,
            'provider' => $request->provider,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'hours' => $request->hours,
            'description' => $request->description,
            'reflection' => $request->reflection,
            'key_learnings' => $request->key_learnings,
            'certificate_file' => $certPath,
            'status' => $request->status,
            'academic_year' => $year,
            'term' => $term,
        ]);

        return response()->json(['message' => 'CPD activity recorded.', 'id' => $activity->id], 201);
    }

    public function cpdGoals()
    {
        $year = AcademicYear::where('is_active', true)->first()?->name ?? date('Y');
        $goals = CpdGoal::where('user_id', Auth::id())->where('academic_year', $year)->orderBy('created_at', 'desc')->get();

        return response()->json($goals->map(fn($g) => [
            'id' => $g->id,
            'title' => $g->title,
            'description' => $g->description,
            'status' => $g->status,
            'target_date' => $g->target_date?->format('d M Y'),
            'achieved_date' => $g->achieved_date?->format('d M Y'),
            'evidence' => $g->evidence,
        ]));
    }

    public function createCpdGoal(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_date' => 'nullable|date',
        ]);

        $year = AcademicYear::where('is_active', true)->first()?->name ?? date('Y');
        $term = Term::where('is_active', true)->first()?->name;

        $goal = CpdGoal::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'target_date' => $request->target_date,
            'academic_year' => $year,
            'term' => $term,
            'status' => 'not_started',
        ]);

        return response()->json(['message' => 'Goal created.', 'id' => $goal->id], 201);
    }

    public function updateCpdGoal(Request $request, $goalId)
    {
        $goal = CpdGoal::where('id', $goalId)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'status' => 'required|in:not_started,in_progress,achieved',
            'evidence' => 'nullable|string|max:2000',
        ]);

        $goal->update([
            'status' => $request->status,
            'evidence' => $request->evidence,
            'achieved_date' => $request->status === 'achieved' ? now() : null,
        ]);

        return response()->json(['message' => 'Goal updated.']);
    }

    public function cpdResources()
    {
        $resources = CpdResource::with('user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json($resources->map(fn($r) => [
            'id' => $r->id,
            'title' => $r->title,
            'description' => $r->description,
            'subject' => $r->subject,
            'grade' => $r->grade,
            'type' => $r->type,
            'type_label' => ucfirst(str_replace('_', ' ', $r->type)),
            'file_url' => $r->file_path ? '/storage/' . $r->file_path : null,
            'file_name' => $r->file_name,
            'external_url' => $r->external_url,
            'download_count' => $r->download_count,
            'shared_by' => Teacher::where('user_id', $r->user_id)->first()?->name ?? 'Unknown',
            'date' => $r->created_at->format('d M Y'),
            'is_mine' => $r->user_id === Auth::id(),
        ]));
    }

    public function shareCpdResource(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'subject' => 'nullable|string|max:100',
            'grade' => 'nullable|string|max:100',
            'type' => 'required|in:lesson_plan,worksheet,past_paper,presentation,video_link,article,template,other',
            'file' => 'nullable|file|max:10240',
            'external_url' => 'nullable|url|max:500',
        ]);

        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('cpd-resources/' . Auth::id(), $fileName, 'public');
        }

        $resource = CpdResource::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject,
            'grade' => $request->grade,
            'type' => $request->type,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'external_url' => $request->external_url,
        ]);

        return response()->json(['message' => 'Resource shared.', 'id' => $resource->id], 201);
    }

    public function cpdObservations()
    {
        $userId = Auth::id();
        $observations = CpdObservation::where('teacher_user_id', $userId)
            ->orderBy('observation_date', 'desc')
            ->get();

        return response()->json($observations->map(fn($o) => [
            'id' => $o->id,
            'date' => $o->observation_date->format('d M Y'),
            'subject' => $o->subject,
            'class_observed' => $o->class_observed,
            'topic' => $o->topic,
            'observer' => Teacher::where('user_id', $o->observer_user_id)->first()?->name ?? 'Unknown',
            'strengths' => $o->strengths,
            'areas_for_improvement' => $o->areas_for_improvement,
            'recommendations' => $o->recommendations,
            'teacher_reflection' => $o->teacher_reflection,
            'rating' => $o->rating,
        ]));
    }

    public function saveObservationReflection(Request $request, $observationId)
    {
        $obs = CpdObservation::where('id', $observationId)->where('teacher_user_id', Auth::id())->firstOrFail();

        $request->validate(['reflection' => 'required|string|max:3000']);
        $obs->update(['teacher_reflection' => $request->reflection]);

        return response()->json(['message' => 'Reflection saved.']);
    }

    // ── Activity Edit/Delete ──
    public function updateCpdActivity(Request $request, $activityId)
    {
        $activity = CpdActivity::where('id', $activityId)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:workshop,course,conference,seminar,peer_observation,self_study,mentoring,online_training,research,other',
            'hours' => 'sometimes|numeric|min:0.5|max:200',
            'status' => 'sometimes|in:planned,in_progress,completed',
            'provider' => 'nullable|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string|max:2000',
            'reflection' => 'nullable|string|max:3000',
            'key_learnings' => 'nullable|string|max:3000',
        ]);

        $activity->update($request->only(['title', 'type', 'hours', 'status', 'provider', 'start_date', 'end_date', 'description', 'reflection', 'key_learnings']));

        return response()->json(['message' => 'Activity updated.']);
    }

    public function deleteCpdActivity($activityId)
    {
        CpdActivity::where('id', $activityId)->where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Activity deleted.']);
    }

    // ── Goal Edit/Delete ──
    public function deleteCpdGoal($goalId)
    {
        CpdGoal::where('id', $goalId)->where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Goal deleted.']);
    }

    // ── Create Observation (HODs/Senior Teachers) ──
    public function createObservation(Request $request)
    {
        $request->validate([
            'teacher_user_id' => 'required|integer|exists:users,id',
            'observation_date' => 'required|date',
            'subject' => 'nullable|string|max:100',
            'class_observed' => 'nullable|string|max:100',
            'topic' => 'nullable|string|max:255',
            'strengths' => 'nullable|string|max:3000',
            'areas_for_improvement' => 'nullable|string|max:3000',
            'recommendations' => 'nullable|string|max:3000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $obs = CpdObservation::create([
            'teacher_user_id' => $request->teacher_user_id,
            'observer_user_id' => Auth::id(),
            'observation_date' => $request->observation_date,
            'subject' => $request->subject,
            'class_observed' => $request->class_observed,
            'topic' => $request->topic,
            'strengths' => $request->strengths,
            'areas_for_improvement' => $request->areas_for_improvement,
            'recommendations' => $request->recommendations,
            'rating' => $request->rating,
        ]);

        return response()->json(['message' => 'Observation recorded.', 'id' => $obs->id], 201);
    }

    // ── CPD PDF Export ──
    public function cpdExport()
    {
        $userId = Auth::id();
        $year = AcademicYear::where('is_active', true)->first()?->name ?? date('Y');
        $teacher = $this->getTeacher();

        $activities = CpdActivity::where('user_id', $userId)->where('academic_year', $year)->where('status', 'completed')->orderBy('start_date')->get();
        $goals = CpdGoal::where('user_id', $userId)->where('academic_year', $year)->get();
        $observations = CpdObservation::where('teacher_user_id', $userId)->whereYear('observation_date', substr($year, 0, 4))->get();

        $totalHours = $activities->sum('hours');

        return response()->json([
            'teacher_name' => $teacher?->name ?? 'Unknown',
            'employee_number' => Employee::where('user_id', $userId)->first()?->employee_number,
            'qualification' => $teacher?->qualification,
            'specialization' => $teacher?->specialization,
            'year' => $year,
            'total_hours' => (float) $totalHours,
            'target_hours' => 40,
            'activities' => $activities->map(fn($a) => [
                'title' => $a->title, 'type' => ucfirst(str_replace('_', ' ', $a->type)),
                'provider' => $a->provider, 'date' => $a->start_date->format('d M Y'),
                'hours' => (float) $a->hours, 'reflection' => $a->reflection,
                'has_certificate' => !empty($a->certificate_file),
            ]),
            'goals' => $goals->map(fn($g) => [
                'title' => $g->title, 'status' => $g->status,
                'target_date' => $g->target_date?->format('d M Y'),
                'evidence' => $g->evidence,
            ]),
            'observations' => $observations->map(fn($o) => [
                'date' => $o->observation_date->format('d M Y'),
                'subject' => $o->subject, 'rating' => $o->rating,
                'observer' => Teacher::where('user_id', $o->observer_user_id)->first()?->name,
                'strengths' => $o->strengths,
            ]),
        ]);
    }

    // ── Enhanced CPD: Term Tracking + Compliance + Points ──
    public function cpdTermBreakdown()
    {
        $userId = Auth::id();
        $year = AcademicYear::where('is_active', true)->first()?->name ?? date('Y');
        $activities = CpdActivity::where('user_id', $userId)->where('academic_year', $year)->where('status', 'completed')->get();

        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $breakdown = [];
        foreach ($terms as $t) {
            $termActs = $activities->where('term', $t);
            $breakdown[] = [
                'term' => $t,
                'hours' => (float) $termActs->sum('hours'),
                'points' => (float) $termActs->sum('points'),
                'count' => $termActs->count(),
                'mandatory' => $termActs->where('is_mandatory', true)->count(),
                'voluntary' => $termActs->where('is_mandatory', false)->count(),
            ];
        }

        // Get targets
        $teacher = $this->getTeacher();
        $role = $teacher?->is_class_teacher ? 'class_teacher' : null;
        $settings = \DB::table('cpd_settings')->where('academic_year', $year)
            ->where(fn($q) => $q->where('role', $role)->orWhereNull('role'))
            ->orderByRaw('role IS NULL')->first();

        return response()->json([
            'year' => $year,
            'terms' => $breakdown,
            'total_hours' => (float) $activities->sum('hours'),
            'total_points' => (float) $activities->sum('points'),
            'target_hours' => (float) ($settings?->target_hours ?? 40),
            'term_targets' => [
                (float) ($settings?->term1_target ?? 13),
                (float) ($settings?->term2_target ?? 13),
                (float) ($settings?->term3_target ?? 14),
            ],
        ]);
    }

    public function cpdTemplates()
    {
        $templates = \DB::table('cpd_activity_templates')->where('is_active', true)->get();
        return response()->json($templates->map(fn($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'type' => $t->type,
            'provider' => $t->provider,
            'hours' => (float) $t->default_hours,
            'points' => (float) $t->default_points,
            'is_mandatory' => (bool) $t->is_mandatory,
        ]));
    }

    public function quickLogCpd(Request $request)
    {
        $request->validate(['template_id' => 'required|integer', 'date' => 'required|date']);

        $template = \DB::table('cpd_activity_templates')->find($request->template_id);
        if (!$template) return response()->json(['message' => 'Template not found.'], 404);

        $year = AcademicYear::where('is_active', true)->first()?->name ?? date('Y');
        $term = Term::where('is_active', true)->first()?->name;

        // Points config
        $settings = \DB::table('cpd_settings')->where('academic_year', $year)->whereNull('role')->first();
        $pointsConfig = $settings?->points_config ? json_decode($settings->points_config, true) : [];
        $points = $pointsConfig[$template->type] ?? 1;
        $totalPoints = $points * $template->default_hours;

        $activity = CpdActivity::create([
            'user_id' => Auth::id(),
            'title' => $template->title,
            'type' => $template->type,
            'provider' => $template->provider,
            'start_date' => $request->date,
            'hours' => $template->default_hours,
            'points' => $totalPoints,
            'is_mandatory' => $template->is_mandatory,
            'status' => 'completed',
            'approval_status' => $template->is_mandatory ? 'approved' : 'pending',
            'academic_year' => $year,
            'term' => $term,
        ]);

        return response()->json(['message' => 'Activity logged.', 'id' => $activity->id, 'hours' => (float) $template->default_hours, 'points' => $totalPoints], 201);
    }

    public function approveCpdActivity(Request $request, $activityId)
    {
        $request->validate(['status' => 'required|in:approved,rejected', 'remarks' => 'nullable|string|max:500']);

        $activity = CpdActivity::findOrFail($activityId);
        $activity->update([
            'approval_status' => $request->status,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_remarks' => $request->remarks,
        ]);

        return response()->json(['message' => 'Activity ' . $request->status . '.']);
    }

    public function pendingApprovals()
    {
        $activities = CpdActivity::where('approval_status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json($activities->map(fn($a) => [
            'id' => $a->id,
            'teacher' => Teacher::where('user_id', $a->user_id)->first()?->name ?? 'Unknown',
            'title' => $a->title,
            'type' => ucfirst(str_replace('_', ' ', $a->type)),
            'hours' => (float) $a->hours,
            'points' => (float) $a->points,
            'date' => $a->start_date->format('d M Y'),
            'is_mandatory' => $a->is_mandatory,
        ]));
    }

    public function linkActivityToGoal(Request $request, $activityId)
    {
        $request->validate(['goal_id' => 'required|integer']);
        $activity = CpdActivity::where('id', $activityId)->where('user_id', Auth::id())->firstOrFail();
        $activity->update(['goal_id' => $request->goal_id]);
        return response()->json(['message' => 'Activity linked to goal.']);
    }

    public function linkActivityToObservation(Request $request, $activityId)
    {
        $request->validate(['observation_id' => 'required|integer']);
        $activity = CpdActivity::where('id', $activityId)->where('user_id', Auth::id())->firstOrFail();
        $activity->update(['observation_id' => $request->observation_id]);
        return response()->json(['message' => 'Activity linked to observation.']);
    }

    public function cpdCertificates()
    {
        $certs = CpdActivity::where('user_id', Auth::id())
            ->whereNotNull('certificate_file')
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($certs->map(fn($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'type' => ucfirst(str_replace('_', ' ', $a->type)),
            'date' => $a->start_date->format('d M Y'),
            'provider' => $a->provider,
            'hours' => (float) $a->hours,
            'certificate_url' => '/storage/' . $a->certificate_file,
        ]));
    }

    public function schoolWideCpdReport()
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $activeYear = AcademicYear::where('is_active', true)->first();
        $yearId = $activeYear?->id;
        $year = $activeYear?->name ?? date('Y');

        // Section filter — same logic as headDashboard
        $sectionIds = null;
        if ($roleId == \App\Constants\RoleConstants::HEAD_TEACHER_PRIMARY) $sectionIds = [1, 2, 3];
        elseif ($roleId == \App\Constants\RoleConstants::HEAD_TEACHER_SECONDARY) $sectionIds = [4, 5];

        if ($sectionIds) {
            $gradeIds = \App\Models\Grade::whereIn('school_section_id', $sectionIds)->pluck('id');
            $sectionClassIds = ClassSection::whereIn('grade_id', $gradeIds)->pluck('id');
            $teachingTeacherIds = SubjectTeaching::whereIn('class_section_id', $sectionClassIds)
                ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))->pluck('teacher_id')->unique();

            $teachers = Teacher::where('is_active', true)
                ->where(fn($q) => $q->whereIn('school_section_id', $sectionIds)->orWhereIn('grade_id', $gradeIds)->orWhereIn('id', $teachingTeacherIds))
                ->with('grade')->get();
        } else {
            $teachers = Teacher::where('is_active', true)->with('grade')->get();
        }

        $report = [];
        foreach ($teachers as $t) {
            $activities = CpdActivity::where('user_id', $t->user_id)->where('academic_year', $year)->where('status', 'completed')->get();
            $totalHours = (float) $activities->sum('hours');
            $totalPoints = (float) $activities->sum('points');

            // Get target
            $role = $t->is_class_teacher ? 'class_teacher' : null;
            $settings = \DB::table('cpd_settings')->where('academic_year', $year)
                ->where(fn($q) => $q->where('role', $role)->orWhereNull('role'))
                ->orderByRaw('role IS NULL')->first();
            $target = (float) ($settings?->target_hours ?? 40);

            $status = $totalHours >= $target ? 'compliant' : ($totalHours >= $target * 0.5 ? 'on_track' : 'behind');

            $report[] = [
                'name' => $t->name,
                'grade' => $t->grade?->name,
                'is_class_teacher' => $t->is_class_teacher,
                'hours' => $totalHours,
                'points' => $totalPoints,
                'target' => $target,
                'progress' => $target > 0 ? min(100, round(($totalHours / $target) * 100)) : 0,
                'activities' => $activities->count(),
                'certificates' => $activities->whereNotNull('certificate_file')->count(),
                'status' => $status,
            ];
        }

        usort($report, fn($a, $b) => $a['hours'] - $b['hours']);

        $compliant = count(array_filter($report, fn($r) => $r['status'] === 'compliant'));
        $totalTeachers = count($report);

        return response()->json([
            'year' => $year,
            'total_teachers' => $totalTeachers,
            'compliant' => $compliant,
            'on_track' => count(array_filter($report, fn($r) => $r['status'] === 'on_track')),
            'behind' => count(array_filter($report, fn($r) => $r['status'] === 'behind')),
            'compliance_rate' => $totalTeachers > 0 ? round(($compliant / $totalTeachers) * 100) : 0,
            'school_total_hours' => array_sum(array_column($report, 'hours')),
            'teachers' => $report,
        ]);
    }

    public function schoolCpdCsvExport()
    {
        // Reuse the same data from schoolWideCpdReport
        $response = $this->schoolWideCpdReport();
        $data = json_decode($response->getContent(), true);

        $csv = "Name,Grade,Class Teacher,Hours,Target,Progress %,Activities,Certificates,Points,Status\r\n";
        foreach ($data['teachers'] ?? [] as $t) {
            $csv .= '"' . str_replace('"', '""', $t['name']) . '","' . ($t['grade'] ?? '') . '",' . ($t['is_class_teacher'] ? 'Yes' : 'No') . ',' . $t['hours'] . ',' . $t['target'] . ',' . $t['progress'] . ',' . $t['activities'] . ',' . $t['certificates'] . ',' . $t['points'] . ',"' . $t['status'] . "\"\r\n";
        }

        $year = $data['year'] ?? date('Y');
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=School_CPD_Report_{$year}.csv",
        ]);
    }

    public function personalCpdCsvExport()
    {
        $response = $this->cpdExport();
        $data = json_decode($response->getContent(), true);

        $lines = [];
        $lines[] = "CPD REPORT - " . ($data['year'] ?? '');
        $lines[] = str_repeat('=', 40);
        $lines[] = "Teacher: " . ($data['teacher_name'] ?? '');
        $lines[] = "Employee: " . ($data['employee_number'] ?? '-');
        $lines[] = "Total Hours: " . $data['total_hours'] . "/" . $data['target_hours'];
        $lines[] = "";
        $lines[] = "ACTIVITIES";
        $lines[] = str_repeat('-', 30);
        foreach ($data['activities'] ?? [] as $a) {
            $lines[] = $a['date'] . ' | ' . $a['type'] . ' | ' . $a['title'] . ' | ' . $a['hours'] . 'h | ' . ($a['provider'] ?? '-') . ($a['has_certificate'] ? ' [CERT]' : '');
        }
        $lines[] = "";
        $lines[] = "GOALS";
        $lines[] = str_repeat('-', 30);
        foreach ($data['goals'] ?? [] as $g) {
            $lines[] = $g['title'] . ' | ' . str_replace('_', ' ', $g['status']) . ($g['target_date'] ? ' | Target: ' . $g['target_date'] : '');
        }

        $content = implode("\r\n", $lines);
        $year = $data['year'] ?? date('Y');

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=CPD_Report_{$year}.txt",
        ]);
    }

    // ── Change Password ──
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        if (!\Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update(['password' => bcrypt($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    // ── Profile Photo Upload ──
    public function updateProfilePhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|max:5120']);

        $teacher = $this->getTeacher();
        if (!$teacher) return response()->json(['message' => 'Not found.'], 404);

        $path = $request->file('photo')->store('teacher-photos/' . $teacher->id, 'public');
        $teacher->update(['profile_photo' => $path]);

        return response()->json(['message' => 'Photo updated.', 'photo_url' => '/storage/' . $path]);
    }
}
