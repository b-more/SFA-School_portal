<?php

namespace App\Http\Controllers;

use App\Constants\RoleConstants;
use App\Models\Attendance;
use App\Models\AcademicYear;
use App\Models\ParentGuardian;
use App\Models\SchoolSettings;
use App\Models\Student;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAttendanceController extends Controller
{
    public function download(Request $request, Student $student)
    {
        $user = Auth::user();

        // Verify access: parent must own this child, student must be themselves, admin/teacher allowed
        if ($user->role_id === RoleConstants::PARENT) {
            $parent = ParentGuardian::where('user_id', $user->id)->first();
            $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];
            if (!in_array($student->id, $childIds)) {
                abort(403, 'Access denied.');
            }
        } elseif ($user->role_id === RoleConstants::STUDENT) {
            $ownStudent = Student::where('user_id', $user->id)->first();
            if (!$ownStudent || $ownStudent->id !== $student->id) {
                abort(403, 'Access denied.');
            }
        } elseif (!in_array($user->role_id, [RoleConstants::ADMIN, ...RoleConstants::teaching(), RoleConstants::SCHOOL_SECRETARY])) {
            abort(403, 'Access denied.');
        }

        $student->load(['grade', 'classSection', 'parentGuardian']);

        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();

        // Get attendance records for this term
        $query = Attendance::where('student_id', $student->id)
            ->orderBy('attendance_date', 'asc');

        if ($activeYear) $query->where('academic_year_id', $activeYear->id);
        if ($activeTerm) $query->where('term_id', $activeTerm->id);

        $records = $query->get();

        // Calculate stats
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $sick = $records->where('status', 'sick')->count();
        $excused = $records->where('status', 'excused')->count();
        $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        // Group by month
        $monthlyRecords = $records->groupBy(fn ($r) => $r->attendance_date->format('F Y'));

        $schoolSettings = SchoolSettings::getInstance();

        $data = [
            'student' => $student,
            'schoolSettings' => $schoolSettings,
            'academicYear' => $activeYear,
            'term' => $activeTerm,
            'records' => $records,
            'monthlyRecords' => $monthlyRecords,
            'stats' => compact('total', 'present', 'absent', 'late', 'sick', 'excused', 'rate'),
            'generatedAt' => now(),
        ];

        $pdf = Pdf::loadView('pdf.student-attendance', $data);
        $pdf->setPaper('A4', 'portrait');

        $name = preg_replace('/[^a-zA-Z0-9-]/', '', str_replace(' ', '-', $student->name));
        $termName = $activeTerm ? preg_replace('/[^a-zA-Z0-9-]/', '', str_replace(' ', '-', $activeTerm->name)) : 'Current';
        $filename = "Attendance-{$name}-{$termName}-{$activeYear?->name}.pdf";

        return $pdf->download($filename);
    }
}
