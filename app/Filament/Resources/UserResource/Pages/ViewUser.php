<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Carbon\Carbon;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static string $view     = 'filament.resources.user-resource.pages.view-user';

    /**
     * Session record for this user right now (if any).
     */
    public function currentSession(): ?object
    {
        $lifetime = (int) config('session.lifetime') * 60;
        return DB::selectOne(
            'SELECT ip_address, user_agent, last_activity FROM sessions
              WHERE user_id = ? AND last_activity >= ?
              ORDER BY last_activity DESC LIMIT 1',
            [$this->record->id, time() - $lifetime]
        );
    }

    /**
     * Last 30 login events for this user.
     */
    public function loginHistory(int $limit = 30): Collection
    {
        return collect(DB::select("
            SELECT event, ip_address, user_agent, created_at
              FROM audit_logs
             WHERE user_id = ? AND event IN ('login', 'logout')
          ORDER BY id DESC
             LIMIT ?
        ", [$this->record->id, $limit]))->map(function ($r) {
            $r->when = Carbon::parse($r->created_at);
            return $r;
        });
    }

    /**
     * Last 30 failed login attempts against this account.
     */
    public function failedAttempts(int $limit = 30): Collection
    {
        return collect(DB::select("
            SELECT ip_address, user_agent, new_values, created_at
              FROM audit_logs
             WHERE user_id = ? AND event = 'failed_login'
          ORDER BY id DESC
             LIMIT ?
        ", [$this->record->id, $limit]))->map(function ($r) {
            $r->when      = Carbon::parse($r->created_at);
            $meta         = json_decode($r->new_values ?: '{}', true) ?: [];
            $r->attempted = $meta['attempted_identifier'] ?? null;
            return $r;
        });
    }

    /**
     * Password-reset events on this account.
     */
    public function passwordResets(int $limit = 10): Collection
    {
        return collect(DB::select("
            SELECT ip_address, created_at
              FROM audit_logs
             WHERE user_id = ? AND event = 'password_reset'
          ORDER BY id DESC
             LIMIT ?
        ", [$this->record->id, $limit]))->map(function ($r) {
            $r->when = Carbon::parse($r->created_at);
            return $r;
        });
    }

    public function summary(): array
    {
        $total     = fn (string $event) => (int) DB::selectOne(
            'SELECT COUNT(*) c FROM audit_logs WHERE user_id = ? AND event = ?',
            [$this->record->id, $event]
        )->c;

        $totalLogins   = $total('login');
        $totalFailures = $total('failed_login');
        $totalResets   = $total('password_reset');

        $lastFailure = DB::selectOne(
            "SELECT created_at FROM audit_logs WHERE user_id = ? AND event = 'failed_login' ORDER BY id DESC LIMIT 1",
            [$this->record->id]
        );

        return [
            'total_logins'      => $totalLogins,
            'total_failures'    => $totalFailures,
            'total_resets'      => $totalResets,
            'last_login_at'     => $this->record->last_login_at,
            'last_login_ip'     => $this->record->last_login_ip,
            'last_failure_at'   => $lastFailure ? Carbon::parse($lastFailure->created_at) : null,
        ];
    }

    protected function shortUa(?string $ua): string
    {
        if (! $ua) return '—';
        if (str_contains($ua, 'Chrome') && ! str_contains($ua, 'Edg')) return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari') && ! str_contains($ua, 'Chrome')) return 'Safari';
        if (str_contains($ua, 'Edg')) return 'Edge';
        if (str_contains($ua, 'okhttp') || str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        return \Illuminate\Support\Str::limit($ua, 30, '…');
    }

    public function ua(?string $ua): string
    {
        return $this->shortUa($ua);
    }
}
