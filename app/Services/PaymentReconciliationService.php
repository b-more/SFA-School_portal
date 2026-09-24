<?php

namespace App\Services;

use App\Models\FeeCategory;
use App\Models\PaymentTransaction;
use App\Models\QrPayment;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Single source of truth for confirming and crediting CGrate mobile-money payments.
 * Used by the live parent-app status poll AND the scheduled reconciliation command,
 * so a payment approved after the app is closed still gets credited.
 */
class PaymentReconciliationService
{
    /**
     * Re-query the gateway for every payment still awaiting confirmation and
     * credit / fail / expire it accordingly. Returns a per-outcome summary.
     */
    public function reconcileProcessing(): array
    {
        $summary = ['checked' => 0, 'completed' => 0, 'failed' => 0, 'expired' => 0, 'pending' => 0];

        $payments = QrPayment::whereIn('status', ['processing', 'pending'])->get();

        foreach ($payments as $payment) {
            $summary['checked']++;
            try {
                $outcome = $this->syncWithGateway($payment);
                $summary[$outcome] = ($summary[$outcome] ?? 0) + 1;
            } catch (\Throwable $e) {
                Log::warning('Payment reconcile failed', [
                    'reference' => $payment->payment_reference,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $summary;
    }

    /**
     * Ask CGrate for the live status of one payment and update it.
     * Returns one of: completed | failed | expired | pending.
     */
    public function syncWithGateway(QrPayment $payment): string
    {
        if ($payment->status === 'completed') {
            return 'completed';
        }

        $cgrate = new CGrateService();
        $result = $cgrate->queryCustomerPayment($payment->payment_reference);

        // If the reference is unknown (106) but CGrate handed us a paymentID, try that.
        if (($result['responseCode'] ?? '') === '106' && $payment->cgrate_payment_id) {
            $result = $cgrate->queryCustomerPayment($payment->cgrate_payment_id);
        }

        if (! empty($result['payment_complete'])) {
            $payment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'response_message' => $result['message'] ?? 'Payment completed',
            ]);
            $this->creditPayment($payment);

            return 'completed';
        }

        $paymentStatus = strtolower($result['payment_status'] ?? '');
        if (in_array($paymentStatus, ['failed', 'cancelled', 'declined', 'rejected'])) {
            $payment->update([
                'status' => 'failed',
                'response_message' => $result['message'] ?? 'Payment failed',
            ]);

            return 'failed';
        }

        // Still unconfirmed — expire it once the 24h window has passed so it stops lingering.
        if ($payment->expires_at && $payment->expires_at->isPast()) {
            $payment->update(['status' => 'expired']);

            return 'expired';
        }

        return 'pending';
    }

    /**
     * Apply a completed payment to the student's outstanding fees, oldest first,
     * record transactions, forward any overpayment, and SMS the parent.
     * Idempotent: a payment_reference already turned into transactions is skipped.
     */
    public function creditPayment(QrPayment $payment): void
    {
        if (! $payment->student_id || $payment->status !== 'completed') {
            return;
        }

        // Duplicate protection — never credit the same reference twice.
        if (PaymentTransaction::where('external_reference', $payment->payment_reference)->exists()) {
            Log::info('Payment already applied, skipping duplicate: ' . $payment->payment_reference);

            return;
        }

        // Pay-as-you-go bus payments don't pre-exist as a debt; the successful
        // payment itself creates a paid bus StudentFee for that month so the
        // parent has a receipt and we never carry "unpaid bus" on the books.
        if ($payment->payment_kind === 'bus_month') {
            $this->creditBusMonthPayment($payment);
            return;
        }

        $fees = StudentFee::with('feeCategory')
            ->where('student_id', $payment->student_id)
            ->where('balance', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $remaining = $payment->amount;
        $applied   = []; // [['category' => 'Bus', 'period' => 'May 2026', 'amount' => 500.00], ...]

        foreach ($fees as $fee) {
            if ($remaining <= 0) {
                break;
            }

            $apply = min($remaining, $fee->balance);
            $newBalance = $fee->balance - $apply;

            $fee->update([
                'amount_paid' => $fee->amount_paid + $apply,
                'balance' => max($newBalance, 0),
                'payment_status' => $newBalance <= 0 ? 'paid' : 'partial',
                'payment_date' => now(),
                'payment_method' => 'mobile_money',
            ]);

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

            $applied[] = [
                'category' => optional($fee->feeCategory)->name ?? 'Fees',
                'period'   => $fee->period_label ?? '',
                'amount'   => $apply,
            ];

            $remaining -= $apply;
        }

        // Overpayment beyond all outstanding balances → carry forward.
        if ($remaining > 0) {
            try {
                $lastFee = $fees->last();
                if ($lastFee) {
                    (new BalanceForwardService())->processOverpayment($lastFee, $remaining);
                }
            } catch (\Throwable $e) {
                Log::warning('Overpayment forward failed: ' . $e->getMessage());
            }
        }

        $this->sendConfirmationSms($payment, $applied);

        // Notify the WhatsApp bot for payments it initiated. Fire-and-forget:
        // a bot outage cannot break reconciliation — the service swallows all
        // exceptions internally.
        if ($payment->channel === 'whatsapp_bot') {
            $lastTx = PaymentTransaction::where('external_reference', $payment->payment_reference)
                ->orderByDesc('id')
                ->first();
            app(\App\Services\BotNotifierService::class)->notifyPaymentCompleted($payment, $lastTx);
        }
    }

    /**
     * Settle a pay-as-you-go bus payment: create the Bus StudentFee fully paid
     * for the chosen month (period_label), record the transaction, ALSO mirror
     * the payment into the legacy bus_payments table so accounts see it there,
     * then SMS the parent. Idempotent per (student × bus × period_label).
     */
    private function creditBusMonthPayment(QrPayment $payment): void
    {
        $busCategory = FeeCategory::where('code', FeeCategory::BUS)->first();
        if (! $busCategory) {
            Log::warning('Bus category missing; cannot credit bus payment', ['ref' => $payment->payment_reference]);
            return;
        }

        $period = $payment->period_label ?: now()->format('F Y');

        $alreadyPaid = StudentFee::where('student_id', $payment->student_id)
            ->where('fee_category_id', $busCategory->id)
            ->where('period_label', $period)
            ->where('payment_status', 'paid')
            ->exists();
        if ($alreadyPaid) {
            Log::info('Bus already paid for ' . $period . '; skipping duplicate', ['ref' => $payment->payment_reference]);
            return;
        }

        // Derive month name + year for the legacy bus_payments row.
        // period_label is "{Month Year}" or "{Month Year} — {Route}" — strip the route part.
        $bareMonth = trim(explode('—', $period)[0]);
        $monthName = explode(' ', $bareMonth)[0] ?? now()->format('F');
        $year      = (int) (explode(' ', $bareMonth)[1] ?? now()->format('Y'));

        \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $busCategory, $period, $monthName, $year) {
            $fee = StudentFee::create([
                'student_id'      => $payment->student_id,
                'fee_category_id' => $busCategory->id,
                'period_label'    => $period,
                'amount_paid'     => $payment->amount,
                'balance'         => 0,
                'payment_status'  => 'paid',
                'payment_date'    => now(),
                'payment_method'  => 'mobile_money',
            ]);

            PaymentTransaction::create([
                'student_fee_id'     => $fee->id,
                'amount'             => $payment->amount,
                'type'               => 'payment',
                'payment_method'     => 'mobile_money',
                'external_reference' => $payment->payment_reference,
                'notes'              => 'Bus fare (pay-as-you-go) via parent app',
                'status'             => 'completed',
                'processed_by'       => Auth::id(),
                'transaction_date'   => now(),
            ]);

            // Mirror into the legacy bus_payments table so the existing admin
            // view continues to show every bus payment, regardless of channel.
            try {
                $activeYear = \App\Models\AcademicYear::where('is_active', true)->first()
                    ?? \App\Models\AcademicYear::where('is_current', true)->first();
                $activeTerm = \App\Models\Term::where('is_current', true)->first()
                    ?? \App\Models\Term::where('is_active', true)->first();

                \App\Models\BusPayment::create([
                    'student_id'            => $payment->student_id,
                    'bus_fare_structure_id' => $payment->bus_fare_structure_id,
                    'academic_year_id'      => $activeYear?->id,
                    'term_id'               => $activeTerm?->id,
                    'month'                 => $monthName,
                    'year'                  => $year,
                    'amount'                => $payment->amount,
                    'amount_paid'           => $payment->amount,
                    'balance'               => 0,
                    'payment_status'        => 'paid',
                    'notes'                 => 'Paid via parent app — ref ' . $payment->payment_reference,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Legacy BusPayment mirror failed', ['ref' => $payment->payment_reference, 'err' => $e->getMessage()]);
            }
        });

        $this->sendConfirmationSms($payment, [
            ['category' => $busCategory->name, 'period' => $period, 'amount' => (float) $payment->amount],
        ]);

        if ($payment->channel === 'whatsapp_bot') {
            $lastTx = PaymentTransaction::where('external_reference', $payment->payment_reference)
                ->orderByDesc('id')
                ->first();
            app(\App\Services\BotNotifierService::class)->notifyPaymentCompleted($payment, $lastTx);
        }
    }

    private function sendConfirmationSms(QrPayment $payment, array $applied = []): void
    {
        try {
            $student = Student::with('parentGuardian')->find($payment->student_id);
            if (! $student) {
                return;
            }

            $parentPhone = $payment->customer_mobile ?? $student->parentGuardian?->phone;
            if (! $parentPhone) {
                return;
            }

            $newBalance = StudentFee::where('student_id', $student->id)->sum('balance');

            $appliedText = '';
            if (! empty($applied)) {
                $parts = array_map(function ($a) {
                    $period = $a['period'] ? " {$a['period']}" : '';
                    return "{$a['category']}{$period} K" . number_format($a['amount'], 2);
                }, $applied);
                $appliedText = ' Applied to: ' . implode(', ', $parts) . '.';
            }

            $message = 'St Francis of Assisi: K' . number_format($payment->amount, 2)
                . " received for {$student->name}." . $appliedText
                . ' New balance: K' . number_format($newBalance, 2)
                . ". Ref: {$payment->payment_reference}. Thank you.";

            app(SmsService::class)->send($message, $parentPhone, 'payment', $payment->id);
        } catch (\Throwable $e) {
            Log::warning('Payment SMS failed: ' . $e->getMessage());
        }
    }
}
