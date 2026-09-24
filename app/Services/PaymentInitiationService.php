<?php

namespace App\Services;

use App\Models\QrPayment;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Single seam that starts a CGrate mobile-money payment for a student.
 *
 * Extracted verbatim from ParentApiController::initiatePayment so the parent
 * app AND the WhatsApp bot both go through identical validation, DB writes,
 * CGrate call, and status-transition logic. Reconciliation is unaffected —
 * it still keys off qr_payments.payment_reference.
 *
 * The service does NOT decide HTTP status codes or throw — it returns a
 * uniform array the caller can shape into a JSON response.
 */
class PaymentInitiationService
{
    /**
     * @param  string  $channel  'parent_app' | 'whatsapp_bot' | 'public_pay'
     * @param  string  $referencePrefix  Two-to-four-letter prefix, e.g. 'PAR' | 'WA' | 'QR'
     * @return array{
     *   success: bool,
     *   message: string,
     *   payment_reference?: string,
     *   payment_id?: int,
     *   amount?: float,
     *   is_delayed?: bool,
     *   qr_payment?: \App\Models\QrPayment,
     *   error_code?: string,
     * }
     */
    public function initiate(
        Student $student,
        float $amount,
        string $payerMsisdn,
        string $channel = 'parent_app',
        string $referencePrefix = 'PAR'
    ): array {
        $fees = StudentFee::where('student_id', $student->id)
            ->where('balance', '>', 0)
            ->with('feeStructure')
            ->get();

        $totalBalance = (float) $fees->sum('balance');

        if ($totalBalance <= 0) {
            return [
                'success' => false,
                'message' => 'No outstanding balance for this student.',
                'error_code' => 'NO_BALANCE',
            ];
        }

        if ($amount > $totalBalance) {
            return [
                'success' => false,
                'message' => "Amount exceeds outstanding balance of K {$totalBalance}.",
                'error_code' => 'AMOUNT_TOO_LARGE',
            ];
        }

        $paymentReference = $referencePrefix . '-' . strtoupper(Str::random(10));

        $studentFee = StudentFee::where('student_id', $student->id)
            ->where('balance', '>', 0)
            ->orderBy('created_at', 'asc')
            ->first();

        $qrPayment = QrPayment::create([
            'qr_code' => QrPayment::generateQrCode($paymentReference, $amount, $payerMsisdn),
            'payment_reference' => $paymentReference,
            'amount' => $amount,
            'customer_mobile' => $payerMsisdn,
            'student_id' => $student->id,
            'student_fee_id' => $studentFee?->id,
            'status' => 'pending',
            'channel' => $channel,
            'initiated_at' => now(),
            'expires_at' => now()->addHours(24),
        ]);

        $result = null;
        try {
            $result = (new CGrateService())
                ->processCustomerPayment($amount, $payerMsisdn, $paymentReference);
        } catch (\Throwable $e) {
            Log::warning('CGrate payment threw', [
                'error' => $e->getMessage(),
                'reference' => $paymentReference,
            ]);
            $result = [
                'success' => false,
                'message' => 'Payment service is temporarily unavailable. Please try again in a few minutes.',
            ];
        }

        if ($result && ! empty($result['success'])) {
            $qrPayment->update([
                'status' => 'processing',
                'cgrate_payment_id' => $result['paymentID'] ?? $result['paymentId'] ?? null,
                'response_message' => $result['message'] ?? 'Payment initiated',
                'response_code' => $result['responseCode'] ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'Payment initiated. Please check your phone to approve the transaction.',
                'payment_reference' => $paymentReference,
                'payment_id' => $qrPayment->id,
                'amount' => $amount,
                'qr_payment' => $qrPayment->fresh(),
            ];
        }

        $errorMsg = $result['message'] ?? 'Payment initiation failed.';
        $responseCode = $result['responseCode'] ?? '';
        $cgPaymentId = $result['paymentID'] ?? $result['paymentId'] ?? null;
        $lc = strtolower($errorMsg);

        $isTimeout = str_contains($lc, 'timeout') || str_contains($lc, 'timed out')
            || str_contains($lc, 'delay') || str_contains($lc, 'unavailable');
        $isDuplicate = $responseCode === '104' || str_contains($lc, 'reference not unique');

        if ($isDuplicate && $cgPaymentId) {
            $qrPayment->update([
                'status' => 'processing',
                'cgrate_payment_id' => $cgPaymentId,
                'response_message' => 'Payment accepted by CGrate',
                'response_code' => $responseCode,
            ]);

            return [
                'success' => true,
                'message' => 'Payment is being processed. Please check your phone to approve.',
                'payment_reference' => $paymentReference,
                'payment_id' => $qrPayment->id,
                'amount' => $amount,
                'qr_payment' => $qrPayment->fresh(),
            ];
        }

        if ($isTimeout) {
            $qrPayment->update([
                'status' => 'processing',
                'response_message' => 'Timeout - awaiting confirmation',
            ]);

            return [
                'success' => true,
                'is_delayed' => true,
                'message' => 'Payment request sent but confirmation is delayed. Check your phone — if you receive a payment prompt, approve it.',
                'payment_reference' => $paymentReference,
                'payment_id' => $qrPayment->id,
                'amount' => $amount,
                'qr_payment' => $qrPayment->fresh(),
            ];
        }

        $qrPayment->update([
            'status' => 'failed',
            'response_message' => $errorMsg,
            'response_code' => $responseCode,
        ]);

        return [
            'success' => false,
            'message' => $errorMsg,
            'error_code' => $result['error_code'] ?? 'PAYMENT_FAILED',
        ];
    }
}
