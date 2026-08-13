<?php

namespace App\Console\Commands;

use App\Services\PaymentReconciliationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReconcilePayments extends Command
{
    protected $signature = 'payments:reconcile';

    protected $description = 'Re-query CGrate for unconfirmed mobile-money payments and credit/expire them';

    public function handle(PaymentReconciliationService $service): int
    {
        try {
            $summary = $service->reconcileProcessing();
            $this->info('Payment reconcile: ' . json_encode($summary));

            // Heartbeat — read by /health/scheduler and by scheduler:selfcheck.
            // If the scheduler dies, this stops updating and both alerts fire.
            Cache::put('scheduler:reconcile:last_run',     now()->toIso8601String(), now()->addDay());
            Cache::put('scheduler:reconcile:last_summary', $summary,                 now()->addDay());
            Cache::forget('scheduler:reconcile:last_error');

            Log::channel('scheduler')->info('reconcile ok', $summary);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Cache::put('scheduler:reconcile:last_error', [
                'at'      => now()->toIso8601String(),
                'message' => $e->getMessage(),
            ], now()->addDay());
            Log::channel('scheduler')->error('reconcile failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            report($e);

            return self::FAILURE;
        }
    }
}
