<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\QrPayment;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Fire-and-forget notifier that pings the WhatsApp bot's internal callback
 * when a payment originating from `channel='whatsapp_bot'` is credited.
 *
 * Never throws — a bot outage cannot break reconciliation. Any failure is
 * logged to the `bot_api` channel so we can spot silent breakage on day one.
 */
class BotNotifierService
{
    public function notifyPaymentCompleted(QrPayment $payment, ?PaymentTransaction $transaction = null): void
    {
        $callbackUrl = config('services.whatsapp_bot.callback_url');
        $token = config('services.whatsapp_bot.token');
        $timeout = (int) config('services.whatsapp_bot.timeout', 5);

        if (! $callbackUrl || ! $token) {
            Log::channel('bot_api')->warning('Bot callback not configured — skipping notify', [
                'reference' => $payment->payment_reference,
                'has_url' => (bool) $callbackUrl,
                'has_token' => (bool) $token,
            ]);
            return;
        }

        try {
            $student = Student::with(['parentGuardian', 'classSection.grade'])
                ->find($payment->student_id);

            if (! $student || ! $student->parentGuardian) {
                Log::channel('bot_api')->warning('Bot notify skipped — student or guardian missing', [
                    'reference' => $payment->payment_reference,
                    'student_id' => $payment->student_id,
                ]);
                return;
            }

            $newBalance = (float) StudentFee::where('student_id', $student->id)->sum('balance');
            $className = optional($student->classSection)->name
                ?? optional($student->classSection?->grade)->name
                ?? 'Unknown';

            $payload = [
                'transaction_id' => $transaction?->id,
                'guardian_id' => $student->parentGuardian->id,
                'guardian_phone' => $student->parentGuardian->phone,
                'student_name' => $student->name,
                'student_class' => $className,
                'amount' => (float) $payment->amount,
                'new_balance' => $newBalance,
                'payment_reference' => $payment->payment_reference,
                'receipt_ref' => $transaction?->reference_number ?? $payment->payment_reference,
            ];

            Log::channel('bot_api')->info('Notifying WhatsApp bot of payment completion', [
                'reference' => $payment->payment_reference,
                'guardian_id' => $payload['guardian_id'],
            ]);

            $response = Http::timeout($timeout)
                ->withToken($token)
                ->post($callbackUrl, $payload);

            if (! $response->successful()) {
                Log::channel('bot_api')->warning('Bot callback returned non-2xx', [
                    'reference' => $payment->payment_reference,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('bot_api')->warning('Bot notify failed (fire-and-forget)', [
                'reference' => $payment->payment_reference,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
