<?php

namespace App\Http\Controllers;

use App\Constants\RoleConstants;
use App\Models\SchoolSettings;
use App\Models\Term;
use App\Services\ClinicReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClinicReportController extends Controller
{
    public function termlyPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();

        $termId = (int) $request->get('term_id');
        $data   = $svc->termlyPayload($termId);

        return $this->render($data['from'], $data['to'], "Termly Summary · {$data['term']->name}", 'SFA/CL/T' . $data['term']->id, $svc);
    }

    public function weeklyPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        $anchor = $request->get('anchor', now()->toDateString());
        [$from, $to] = $svc->resolvePeriod(['kind' => 'weekly', 'anchor' => $anchor]);
        $label = 'Weekly Summary · ' . $from->format('d M') . ' – ' . $to->format('d M Y');
        return $this->render($from, $to, $label, 'SFA/CL/W' . $from->format('YW'), $svc);
    }

    public function monthlyPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        $anchor = $request->get('anchor', now()->toDateString());
        [$from, $to] = $svc->resolvePeriod(['kind' => 'monthly', 'anchor' => $anchor]);
        $label = 'Monthly Summary · ' . $from->format('F Y');
        return $this->render($from, $to, $label, 'SFA/CL/M' . $from->format('Ym'), $svc);
    }

    public function customPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        [$from, $to] = $svc->resolvePeriod([
            'kind' => 'custom',
            'from' => $request->get('from', now()->subMonth()->toDateString()),
            'to'   => $request->get('to',   now()->toDateString()),
        ]);
        $label = 'Custom Range · ' . $from->format('d M Y') . ' – ' . $to->format('d M Y');
        return $this->render($from, $to, $label, 'SFA/CL/X' . $from->format('Ymd'), $svc);
    }

    private function render(Carbon $from, Carbon $to, string $subtitle, string $refCode, ClinicReportService $svc): Response
    {
        $data = [
            'from'   => $from,
            'to'     => $to,
            'visits' => $svc->visitStats($from, $to),
            'stock'  => $svc->stockStats($from, $to),
        ];

        $pdf = Pdf::loadView('pdf.clinic-period-summary', [
            'd'              => $data,
            'subtitle'       => $subtitle,
            'refCode'        => $refCode,
            'schoolSettings' => SchoolSettings::getInstance(),
        ]);
        $pdf->setPaper('A4', 'portrait');

        $stamp    = now()->format('Ymd-His');
        $slug     = preg_replace('/[^A-Za-z0-9._-]+/', '-', strtolower($subtitle));
        $filename = "clinic-{$slug}-{$stamp}.pdf";
        return $pdf->download($filename);
    }

    private function authorise(): void
    {
        abort_unless(in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN, RoleConstants::CLINICIAN, RoleConstants::NURSE,
        ], true), 403);
    }

    /* ============================================================
     * Analytical reports — C1 / C2 / G1 / G4 / O1 / O2 / L1
     * ============================================================ */

    public function pupilHistoryPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        $data = $svc->perPupilHistory((int) $request->get('student_id'));

        return $this->pdf('pdf.clinic.pupil-history', [
            'd' => $data,
            'subtitle' => 'Medical History · ' . $data['student']->name,
            'refCode'  => 'SFA/CL/PH/' . $data['student']->id,
        ], "pupil-history-{$data['student']->name}");
    }

    public function complaintTrendPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        $complaintId = $request->integer('complaint_id') ?: null;
        $months = min(24, max(3, $request->integer('months') ?: 12));
        $data = $svc->complaintTrend($complaintId, $months);

        $label = $data['complaint']?->name ?? 'All Complaints';
        return $this->pdf('pdf.clinic.complaint-trend', [
            'd' => $data,
            'subtitle' => "Complaint Trend · {$label} · last {$months} months",
            'refCode'  => 'SFA/CL/CT/' . ($complaintId ?? 'all') . '/' . $months,
        ], "complaint-trend-{$label}");
    }

    public function sickNotesPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        [$from, $to] = $svc->resolvePeriod($this->periodSpec($request));
        $data = $svc->sickNotesRegister($from, $to);

        return $this->pdf('pdf.clinic.sick-notes-register', [
            'd' => $data + ['from' => $from, 'to' => $to],
            'subtitle' => 'Sick Notes Register · ' . $from->format('d M Y') . ' – ' . $to->format('d M Y'),
            'refCode'  => 'SFA/CL/SN/' . $from->format('Ymd'),
        ], "sick-notes-{$from->format('Ymd')}");
    }

    public function attendanceLossPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        [$from, $to] = $svc->resolvePeriod($this->periodSpec($request));
        $data = $svc->attendanceLossImpact($from, $to);

        return $this->pdf('pdf.clinic.attendance-loss', [
            'd' => $data,
            'subtitle' => 'Attendance-Loss Impact · ' . $from->format('d M Y') . ' – ' . $to->format('d M Y'),
            'refCode'  => 'SFA/CL/AL/' . $from->format('Ymd'),
        ], "attendance-loss-{$from->format('Ymd')}");
    }

    public function costMetricsPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        [$from, $to] = $svc->resolvePeriod($this->periodSpec($request));
        $data = $svc->costMetrics($from, $to);

        return $this->pdf('pdf.clinic.cost-metrics', [
            'd' => $data,
            'subtitle' => 'Cost Analysis · ' . $from->format('d M Y') . ' – ' . $to->format('d M Y'),
            'refCode'  => 'SFA/CL/CO/' . $from->format('Ymd'),
        ], "cost-analysis-{$from->format('Ymd')}");
    }

    public function burnRatePdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        $window = min(90, max(7, $request->integer('window') ?: 30));
        $data = $svc->burnRate($window);

        return $this->pdf('pdf.clinic.burn-rate', [
            'd' => $data,
            'subtitle' => "Stock Burn-Rate · last {$window} days",
            'refCode'  => 'SFA/CL/BR/' . $window,
        ], "burn-rate-{$window}d");
    }

    public function classSnapshotPdf(Request $request, ClinicReportService $svc): Response
    {
        $this->authorise();
        [$from, $to] = $svc->resolvePeriod($this->periodSpec($request));
        $data = $svc->classSnapshot((int) $request->get('class_section_id'), $from, $to);

        $classLabel = $data['class']->grade->name . ' - ' . $data['class']->name;
        return $this->pdf('pdf.clinic.class-snapshot', [
            'd' => $data,
            'subtitle' => "Class Health Snapshot · {$classLabel}",
            'refCode'  => 'SFA/CL/CS/' . $data['class']->id,
        ], "class-snapshot-{$classLabel}");
    }

    /* ------------ helpers ------------ */

    private function pdf(string $view, array $data, string $slugBase): Response
    {
        $pdf = Pdf::loadView($view, $data + ['schoolSettings' => SchoolSettings::getInstance()]);
        $pdf->setPaper('A4', 'portrait');
        $slug = preg_replace('/[^A-Za-z0-9._-]+/', '-', strtolower($slugBase));
        return $pdf->download("clinic-{$slug}-" . now()->format('Ymd-His') . '.pdf');
    }

    private function periodSpec(Request $request): array
    {
        return match ($request->get('period', 'monthly')) {
            'weekly'  => ['kind' => 'weekly',  'anchor' => $request->get('anchor', now()->toDateString())],
            'monthly' => ['kind' => 'monthly', 'anchor' => $request->get('anchor', now()->toDateString())],
            'termly'  => ['kind' => 'termly',  'term_id' => (int) $request->get('term_id')],
            'custom'  => ['kind' => 'custom',  'from' => $request->get('from'), 'to' => $request->get('to')],
            default   => ['kind' => 'monthly', 'anchor' => now()->toDateString()],
        };
    }
}
