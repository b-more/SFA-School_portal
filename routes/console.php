<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Credit any mobile-money payments confirmed by CGrate after the app was closed,
// and expire stale unconfirmed ones. The host cron runs `schedule:run` every minute.
Schedule::command('payments:reconcile')->everyTwoMinutes()->withoutOverlapping();

// Watchdog — if payments:reconcile stops stamping its heartbeat, alert the
// admin by SMS. Runs on the SAME scheduler it is watching, so it only catches
// "reconcile itself broke." A dead scheduler is caught externally by polling
// /health/scheduler from an uptime monitor (returns 503 when stale).
Schedule::command('scheduler:selfcheck')->everyFifteenMinutes()->withoutOverlapping();
