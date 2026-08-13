<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Watchdog for the payments:reconcile heartbeat.
 *
 * This runs on the SAME scheduler it is watching — so it only catches
 * "scheduler is running but payments:reconcile itself is failing." The
 * "scheduler is entirely dead" case is what the /health/scheduler HTTP
 * endpoint is for; wire that into an external uptime monitor.
 */
class SchedulerSelfCheck extends Command
{
    protected $signature = 'scheduler:selfcheck';

    protected $description = 'Alert admin if payments:reconcile has stopped updating its heartbeat';

    public function handle(SmsService $sms): int
    {
        $staleAfter = (int) config('services.scheduler_alerts.stale_after_minutes', 10);
        $lastRun    = Cache::get('scheduler:reconcile:last_run');
        $threshold  = now()->subMinutes($staleAfter);

        $isFresh = $lastRun && Carbon::parse($lastRun)->gt($threshold);
        if ($isFresh) {
            return self::SUCCESS;
        }

        // Rate-limit so a persistent outage doesn't spam SMS credit.
        $cooldown   = (int) config('services.scheduler_alerts.cooldown_minutes', 30);
        $lastAlert  = Cache::get('scheduler:alert:last_sent');
        if ($lastAlert && Carbon::parse($lastAlert)->gt(now()->subMinutes($cooldown))) {
            return self::SUCCESS;
        }

        $phone = config('services.scheduler_alerts.phone');
        if (! $phone) {
            Log::channel('scheduler')->warning('reconcile is stale but SCHEDULER_ALERT_PHONE is not configured');
            return self::SUCCESS;
        }

        $lastRunPhrase = $lastRun ? Carbon::parse($lastRun)->diffForHumans() : 'never';
        $lastError     = Cache::get('scheduler:reconcile:last_error');
        $suffix        = $lastError ? ' Last error: ' . mb_substr($lastError['message'] ?? '', 0, 80) : '';

        $message = "St Francis alert: payments:reconcile has not run since {$lastRunPhrase}. "
                 . "Mobile-money payments will stay pending until fixed.{$suffix}";

        $sms->send($message, $phone, 'system', null, null, true);

        Cache::put('scheduler:alert:last_sent', now()->toIso8601String(), now()->addDay());
        Log::channel('scheduler')->error('alert sent — reconcile stale', [
            'last_run'  => $lastRun,
            'threshold' => $threshold->toIso8601String(),
            'phone'     => substr($phone, 0, 5) . '****' . substr($phone, -4),
        ]);

        return self::SUCCESS;
    }
}
