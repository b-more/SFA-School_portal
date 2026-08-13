<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * Cheap public endpoint an external uptime monitor can poll to detect
     * a dead scheduler. Returns 200 if payments:reconcile has stamped a
     * heartbeat in the last {stale_after_minutes} minutes, 503 otherwise.
     *
     * Deliberately holds no sensitive info — just enough for a monitor
     * to page whoever is on call.
     */
    public function scheduler(): JsonResponse
    {
        $staleAfter = (int) config('services.scheduler_alerts.stale_after_minutes', 10);
        $lastRun    = Cache::get('scheduler:reconcile:last_run');
        $lastError  = Cache::get('scheduler:reconcile:last_error');

        $isFresh = $lastRun && Carbon::parse($lastRun)->gt(now()->subMinutes($staleAfter));

        $payload = [
            'component'         => 'payments:reconcile',
            'healthy'           => (bool) $isFresh,
            'last_run'          => $lastRun,
            'stale_after_mins'  => $staleAfter,
            'last_error_at'     => $lastError['at'] ?? null,
        ];

        return response()->json($payload, $isFresh ? 200 : 503);
    }
}
