<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSection;
use App\Models\GradingScale;
use App\Models\ReportCardComment;
use App\Models\SchoolSettings;
use App\Models\Student;
use App\Models\Term;
use App\Services\ResultsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportCardController extends Controller
{
    protected ResultsService $resultsService;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    /**
     * Generate a single student's report card PDF.
     */
    public function generate(Request $request, Student $student, Term $term)
    {
        if ($this->blockForArrears($student)) {
            return $this->arrearsResponse($student);
        }

        $year = $request->get('year', now()->year);

        $reportData = $this->prepareReportCardData($student, $term, $year);

        $pdf = Pdf::loadView('pdf.report-card', $reportData);
        $pdf->setPaper('A4', 'portrait');

        // Mark as generated
        $comment = ReportCardComment::findOrCreateFor($student->id, $term->id, $reportData['academicYear']->id);
        $comment->markAsGenerated();

        $sanitizedId = str_replace('/', '-', $student->student_id_number);
        $filename = "report-card-{$sanitizedId}-term{$term->id}-{$year}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Preview a student's report card (HTML).
     */
    public function preview(Request $request, Student $student, Term $term)
    {
        if ($this->blockForArrears($student)) {
            return $this->arrearsResponse($student);
        }

        $year = $request->get('year', now()->year);

        $reportData = $this->prepareReportCardData($student, $term, $year);
        $reportData['isPreview'] = true;

        return view('pdf.report-card', $reportData);
    }

    /**
     * Should this user be stopped from accessing the report card because the student
     * has outstanding fees? Admins/accountants can bypass for office reprints (rare).
     */
    private function blockForArrears(Student $student): bool
    {
        $user = auth()->user();
        $bypassRoles = [
            \App\Constants\RoleConstants::ADMIN,
            \App\Constants\RoleConstants::ACCOUNTANT,
            \App\Constants\RoleConstants::DIRECTOR,
        ];

        if ($user && in_array($user->role_id, $bypassRoles, true)) {
            return false;
        }

        return $student->hasArrears();
    }

    private function arrearsResponse(Student $student)
    {
        $amount = number_format($student->arrearsAmount(), 2);
        $message = "Report card unavailable. {$student->name} has an outstanding fees balance of ZMW {$amount}. Please settle the balance at the school office before the report card can be issued.";

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'blocked' => true,
                'reason' => 'arrears',
                'student_id' => $student->id,
                'student_name' => $student->name,
                'arrears_amount' => $student->arrearsAmount(),
                'message' => $message,
            ], 402);
        }

        return response()->view('errors.arrears-block', [
            'student' => $student,
            'amount' => $student->arrearsAmount(),
            'message' => $message,
        ], 402);
    }

    /**
     * Generate report cards for all students in a class section as one merged PDF.
     *
     * We render a single Blade with page-breaks between students instead of N
     * individual DomPDF runs — dramatically less wall-clock time — and drop the
     * result in storage/app/public/exports/ so the browser downloads it over
     * nginx directly rather than tying up a PHP-FPM worker for the full render.
     */
    public function bulkGenerate(Request $request, ClassSection $classSection, Term $term)
    {
        // Long DomPDF renders (40+ students × letterhead per page) can breach
        // php-fpm's default max_execution_time; disable it for this request.
        @set_time_limit(0);
        ignore_user_abort(true);

        $year = (int) $request->get('year', now()->year);

        $allStudents = $classSection->students()
            ->where('enrollment_status', 'active')
            ->orderBy('name')
            ->get();

        if ($allStudents->isEmpty()) {
            return back()->with('error', 'No active students found in this class.');
        }

        // Filter out arrears students unless the requester is admin/accountant/director.
        $user = auth()->user();
        $bypassRoles = [
            \App\Constants\RoleConstants::ADMIN,
            \App\Constants\RoleConstants::ACCOUNTANT,
            \App\Constants\RoleConstants::DIRECTOR,
        ];
        $canBypass = $user && in_array($user->role_id, $bypassRoles, true);

        $blocked = $canBypass ? collect() : $allStudents->filter(fn ($s) => $s->hasArrears());
        $students = $canBypass ? $allStudents : $allStudents->reject(fn ($s) => $s->hasArrears());

        if ($students->isEmpty()) {
            return back()->with('error',
                'Every student in this class has outstanding fees. Report cards cannot be generated until the balances are settled.'
            );
        }

        $academicYear = AcademicYear::where('is_active', true)->first();

        // Build the report-data payload for each student. The bulk Blade
        // iterates $reports and @includes pdf.report-card per student.
        $reports = $students->map(function ($student) use ($term, $year, $academicYear) {
            $data = $this->prepareReportCardData($student, $term, $year);

            if ($academicYear) {
                ReportCardComment::findOrCreateFor($student->id, $term->id, $academicYear->id)
                    ->markAsGenerated();
            }

            return $data;
        })->all();

        $pdf = Pdf::loadView('pdf.report-cards-bulk', ['reports' => $reports]);
        $pdf->setPaper('A4', 'portrait');

        $gradeName = $classSection->grade ? $classSection->grade->name : 'Unknown';
        $safeGrade = preg_replace('/[^A-Za-z0-9]+/', '-', trim($gradeName));
        $safeSection = preg_replace('/[^A-Za-z0-9]+/', '-', trim((string) $classSection->name));
        $stamp = now()->format('Ymd-His');
        $filename = "report-cards-{$safeGrade}-{$safeSection}-term{$term->id}-{$year}-{$stamp}.pdf";

        // Save to the public exports/ dir. www-data owns the process,
        // so file permissions are correct out of the box.
        Storage::disk('public')->put("exports/{$filename}", $pdf->output());

        // Flash a notice listing which students were skipped due to arrears (if any).
        if ($blocked->count() > 0) {
            $names = $blocked->take(6)->pluck('name')->join(', ');
            $more = $blocked->count() > 6 ? ' and ' . ($blocked->count() - 6) . ' more' : '';
            session()->flash('arrears_notice',
                $blocked->count() . ' student(s) skipped due to outstanding fees: ' . $names . $more
            );
        }

        // Audit line — never let a logfile permission hiccup 500 the whole
        // bulk-generate response. The PDF is already on disk at this point.
        try { Log::channel('single')->info('Bulk report cards generated', [
            'class_section' => $classSection->id,
            'term'          => $term->id,
            'year'          => $year,
            'student_count' => count($reports),
            'skipped'       => $blocked->count(),
            'filename'      => $filename,
        ]); } catch (\Throwable $e) { /* best-effort audit log */ }

        // Redirect to the file's public URL — nginx serves the PDF directly.
        return redirect()->to(Storage::disk('public')->url("exports/{$filename}"));
    }

    /**
     * Prepare all data needed for a report card.
     */
    protected function prepareReportCardData(Student $student, Term $term, int $year): array
    {
        // Load relationships
        $student->load(['classSection.grade', 'classSection.classTeacher', 'parentGuardian']);

        // Get school settings
        $schoolSettings = SchoolSettings::getInstance();

        // Get academic year
        $academicYear = AcademicYear::where('is_active', true)->first();

        // Get results data
        $resultsData = $this->resultsService->getStudentTermResults($student->id, $term->id, $year);

        // Get grading scale
        $gradingScale = null;
        if ($student->classSection && $student->classSection->grade) {
            $gradeLevel = GradingScale::determineGradeLevelFromGrade($student->classSection->grade);
            $gradingScale = GradingScale::getDefaultForGradeLevel($gradeLevel);
        }

        // Get report card comments
        $comments = null;
        if ($academicYear) {
            $comments = ReportCardComment::where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->where('academic_year_id', $academicYear->id)
                ->first();
        }

        // Get class teacher name
        $classTeacherName = null;
        if ($student->classSection && $student->classSection->classTeacher) {
            $classTeacherName = $student->classSection->classTeacher->name;
        }

        // Attendance summary for the term. Late counts as present (they
        // came to school); sick + excused are broken out. Rate is
        // present / (present + absent + sick + excused).
        $counts = Attendance::where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();
        $present = (int) (($counts['present'] ?? 0) + ($counts['late'] ?? 0));
        $absent  = (int) ($counts['absent']  ?? 0);
        $sick    = (int) ($counts['sick']    ?? 0);
        $excused = (int) ($counts['excused'] ?? 0);
        $marked  = $present + $absent + $sick + $excused;
        $attendance = [
            'present' => $present,
            'absent'  => $absent,
            'sick'    => $sick,
            'excused' => $excused,
            'total'   => $marked,
            'rate'    => $marked > 0 ? round(($present / $marked) * 100, 1) : null,
        ];

        // Document reference number — feels institutional and helps the
        // office file / retrieve a physical copy.
        $reportRef = sprintf(
            'SFA/RC/%d/T%d/%s',
            $year,
            $term->id,
            $student->student_id_number ?? $student->id
        );

        return [
            'student' => $student,
            'term' => $term,
            'year' => $year,
            'academicYear' => $academicYear,
            'schoolSettings' => $schoolSettings,
            'resultsData' => $resultsData,
            'gradingScale' => $gradingScale,
            'comments' => $comments,
            'classTeacherName' => $classTeacherName,
            'attendance' => $attendance,
            'reportRef' => $reportRef,
            'generatedAt' => now(),
        ];
    }

    /**
     * Sanitize filename for safe file creation.
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Remove special characters and replace spaces
        $filename = preg_replace('/[^a-zA-Z0-9\s-]/', '', $filename);
        $filename = preg_replace('/\s+/', '-', trim($filename));
        return strtolower($filename);
    }

    /**
     * API endpoint to get report card data (for AJAX preview).
     */
    public function getReportCardData(Request $request, Student $student, Term $term)
    {
        $year = $request->get('year', now()->year);

        $reportData = $this->prepareReportCardData($student, $term, $year);

        // Convert to JSON-friendly format
        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'student_id_number' => $student->student_id_number,
                'gender' => $student->gender,
                'date_of_birth' => $student->date_of_birth?->format('d/m/Y'),
                'class_section' => $student->classSection ? "{$student->classSection->grade->name} - {$student->classSection->name}" : null,
            ],
            'term' => $term->name,
            'year' => $year,
            'results' => $reportData['resultsData'],
            'comments' => $reportData['comments'] ? [
                'class_teacher_comment' => $reportData['comments']->class_teacher_comment,
                'head_teacher_comment' => $reportData['comments']->head_teacher_comment,
            ] : null,
        ]);
    }
}
