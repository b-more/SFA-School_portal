<?php

namespace App\Http\Controllers;

use App\Constants\RoleConstants;
use App\Services\Exports\FeeCollectionTrackerExcelBuilder;
use App\Services\FeeCollectionTrackerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeeCollectionTrackerController extends Controller
{
    public function __construct(private FeeCollectionTrackerService $service)
    {
    }

    public function pdf(Request $request): Response
    {
        $this->authoriseAdmin();

        $year = $request->integer('academic_year_id') ?: null;
        $data = $this->service->build($year);

        $pdf = Pdf::loadView('pdf.fee-collection-tracker', ['d' => $data]);
        $pdf->setPaper('A4', 'landscape');

        $filename = sprintf('fee-collection-tracker-%s-%s.pdf', $data['year_label'], now()->format('Ymd-His'));
        return $pdf->download($filename);
    }

    public function xlsx(Request $request): StreamedResponse
    {
        $this->authoriseAdmin();

        $year    = $request->integer('academic_year_id') ?: null;
        $data    = $this->service->build($year);
        $builder = new FeeCollectionTrackerExcelBuilder($data);

        return $builder->stream();
    }

    private function authoriseAdmin(): void
    {
        $roleId = auth()->user()?->role_id;
        abort_unless(in_array($roleId, [
            RoleConstants::ADMIN,
            RoleConstants::ACCOUNTANT,
            RoleConstants::DIRECTOR,
        ], true), 403);
    }
}
