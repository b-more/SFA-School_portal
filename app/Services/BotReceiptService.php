<?php

namespace App\Services;

use App\Http\Controllers\StudentFeeController;
use App\Models\PaymentTransaction;
use App\Models\StudentFee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

/**
 * Produces the same per-transaction receipt PDF that StudentFeeController
 * streams via the web (view: `transaction-receipt`), but returns raw bytes so
 * the WhatsApp bot can upload it as a document. Uses DomPDF exactly like the
 * controller — output is byte-identical to what parents get from the portal.
 */
class BotReceiptService
{
    /**
     * @return array{bytes: string, filename: string, mime: string}
     */
    public function generatePdf(int $paymentTransactionId): array
    {
        $transaction = PaymentTransaction::findOrFail($paymentTransactionId);

        $studentFee = StudentFee::with([
            'student.parentGuardian',
            'feeStructure.grade',
            'feeStructure.term',
            'feeStructure.academicYear',
        ])->findOrFail($transaction->student_fee_id);

        // Match the running-balance calculation in StudentFeeController::generateTransactionReceipt.
        $previousTransactions = $studentFee->paymentTransactions()
            ->where('transaction_date', '<', $transaction->transaction_date)
            ->orWhere(function ($q) use ($transaction) {
                $q->where('transaction_date', '=', $transaction->transaction_date)
                    ->where('id', '<', $transaction->id);
            })
            ->sum('amount');

        $totalFee = (float) ($studentFee->feeStructure->total_fee ?? $studentFee->feeStructure->basic_fee ?? 0);
        $runningBalance = $totalFee - $previousTransactions - (float) $transaction->amount;

        $pdf = Pdf::loadView('transaction-receipt', [
            'studentFee' => $studentFee,
            'transaction' => $transaction,
            'totalFee' => $totalFee,
            'previouslyPaid' => $previousTransactions,
            'runningBalance' => max(0, $runningBalance),
            'logoSrc' => $this->resolveLogoSrc(),
        ]);

        $pdf->setPaper('a5', 'portrait');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('dpi', 150);
        $pdf->setOption('isRemoteEnabled', true);

        $ref = $transaction->reference_number ?: $transaction->external_reference ?: ('tx-' . $transaction->id);

        return [
            'bytes' => $pdf->output(),
            'filename' => "receipt-{$ref}.pdf",
            'mime' => 'application/pdf',
        ];
    }

    /**
     * Same resolution the controller uses — prefer the SchoolSettings logo,
     * fall back to public/images/logo.png. DomPDF is configured with
     * remote fetch disabled, so this MUST return a filesystem path.
     */
    private function resolveLogoSrc(): ?string
    {
        $settings = \App\Models\SchoolSettings::getInstance();
        $rel = $settings->school_logo ?? null;
        if ($rel && File::exists(storage_path('app/public/' . ltrim($rel, '/')))) {
            return storage_path('app/public/' . ltrim($rel, '/'));
        }
        $fallback = public_path('images/logo.png');
        return File::exists($fallback) ? $fallback : null;
    }
}
