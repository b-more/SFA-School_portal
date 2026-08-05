<?php

namespace App\Http\Controllers;

use App\Constants\RoleConstants;
use App\Models\Employee;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{
    /**
     * Ownership gate: employees may only fetch their own payslips.
     * Admins, Accountants and Directors bypass (payroll oversight).
     */
    protected function assertCanAccess(Payroll $payroll): void
    {
        $user = auth()->user();
        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        $bypassRoles = [
            RoleConstants::ADMIN,
            RoleConstants::ACCOUNTANT,
            RoleConstants::DIRECTOR,
        ];
        if (in_array((int) $user->role_id, $bypassRoles, true)) {
            return;
        }

        // Otherwise: the payslip's employee must be linked to this user.
        $employee = Employee::find($payroll->employee_id);
        if (! $employee || (int) $employee->user_id !== (int) $user->id) {
            abort(403, 'You may only view your own payslip.');
        }
    }

    /**
     * View payslip in browser (HTML version)
     */
    public function view(Payroll $payroll)
    {
        $this->assertCanAccess($payroll);
        $payroll->load(['employee.salaryGrade', 'employee.leaveBalances']);

        return view('payslips.view', [
            'payroll' => $payroll,
        ]);
    }

    /**
     * Stream payslip PDF in browser
     */
    public function stream(Payroll $payroll)
    {
        $this->assertCanAccess($payroll);
        $payroll->load(['employee.salaryGrade', 'employee.leaveBalances']);

        $pdf = Pdf::loadView('payslips.pdf', [
            'payroll' => $payroll,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream($this->generateFilename($payroll));
    }

    /**
     * Download payslip as PDF
     */
    public function download(Payroll $payroll)
    {
        $this->assertCanAccess($payroll);
        $payroll->load(['employee.salaryGrade', 'employee.leaveBalances']);

        $pdf = Pdf::loadView('payslips.pdf', [
            'payroll' => $payroll,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->download($this->generateFilename($payroll));
    }

    /**
     * Print payslip (PDF stream optimized for printing)
     */
    public function print(Payroll $payroll)
    {
        // Redirect to stream for printing
        return $this->stream($payroll);
    }

    /**
     * Generate consistent filename for payslip
     */
    protected function generateFilename(Payroll $payroll): string
    {
        $employeeId = $payroll->employee->employee_number
            ?? $payroll->employee->employee_id
            ?? $payroll->employee->id;

        // Content-Disposition rejects "/" and "\"; also strip other special
        // chars so the download works across all browsers.
        $employeeId = preg_replace('/[^A-Za-z0-9._-]/', '-', (string) $employeeId);
        $month = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $payroll->month));
        $year = (int) $payroll->year;

        return "payslip_{$employeeId}_{$month}_{$year}.pdf";
    }
}
