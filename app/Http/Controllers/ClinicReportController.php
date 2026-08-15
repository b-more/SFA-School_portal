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
            RoleConstants::ADMIN, RoleConstants::CLINICIAN,
        ], true), 403);
    }
}
