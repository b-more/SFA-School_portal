<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Live view of who is on the portal right now, plus a recent trail of who
 * signed in and who tried to. Sessions come out of the database session
 * driver; login / failed-login / password-reset events come out of the
 * audit trail wired up in LogAuthEvent.
 */
class LoginActivity extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Login Activity';
    protected static ?string $navigationGroup = 'System';
    protected static ?int    $navigationSort  = 91;
    protected static ?string $title           = 'Login Activity';
    protected static string  $view            = 'filament.pages.login-activity';

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    /**
     * People with an active session right now (any activity within the
     * configured session lifetime — 120 min by default).
     */
    public function currentlySignedIn(): Collection
    {
        $lifetimeSeconds = (int) config('session.lifetime') * 60;
        $threshold = time() - $lifetimeSeconds;

        return collect(DB::select("
            SELECT u.id AS user_id, u.name, u.email, u.username, u.role_id,
                   s.ip_address, s.user_agent, s.last_activity,
                   r.name AS role_name
              FROM sessions s
              JOIN users u ON u.id = s.user_id
         LEFT JOIN roles r ON r.id = u.role_id
             WHERE s.user_id IS NOT NULL
               AND s.last_activity >= ?
          ORDER BY s.last_activity DESC
        ", [$threshold]))->map(function ($row) {
            $row->last_seen_at = Carbon::createFromTimestamp($row->last_activity);
            $row->seen_ago    = $row->last_seen_at->diffForHumans();
            $row->ua_short    = $this->shortUa($row->user_agent);
            return $row;
        });
    }

    /**
     * Recent successful logins from the audit trail.
     */
    public function recentLogins(int $limit = 30): Collection
    {
        return collect(DB::select("
            SELECT a.id, a.event, a.user_id, a.ip_address, a.user_agent, a.created_at,
                   u.name, u.email, u.role_id, r.name AS role_name
              FROM audit_logs a
         LEFT JOIN users u ON u.id = a.user_id
         LEFT JOIN roles r ON r.id = u.role_id
             WHERE a.event = 'login'
          ORDER BY a.id DESC
             LIMIT ?
        ", [$limit]))->map(function ($row) {
            $row->when     = Carbon::parse($row->created_at);
            $row->ua_short = $this->shortUa($row->user_agent);
            return $row;
        });
    }

    /**
     * Recent failed login attempts. Includes the identifier the caller
     * tried with (extracted from audit_logs.new_values).
     */
    public function recentFailures(int $limit = 30): Collection
    {
        return collect(DB::select("
            SELECT a.id, a.event, a.user_id, a.ip_address, a.user_agent, a.new_values, a.created_at,
                   u.name, u.email, u.role_id, r.name AS role_name
              FROM audit_logs a
         LEFT JOIN users u ON u.id = a.user_id
         LEFT JOIN roles r ON r.id = u.role_id
             WHERE a.event = 'failed_login'
          ORDER BY a.id DESC
             LIMIT ?
        ", [$limit]))->map(function ($row) {
            $row->when      = Carbon::parse($row->created_at);
            $row->ua_short  = $this->shortUa($row->user_agent);
            $meta           = json_decode($row->new_values ?: '{}', true) ?: [];
            $row->attempted = $meta['attempted_identifier'] ?? null;
            return $row;
        });
    }

    /**
     * At-a-glance counters shown at the top of the page.
     */
    public function summary(): array
    {
        $lifetimeSeconds = (int) config('session.lifetime') * 60;
        $sessionThreshold = time() - $lifetimeSeconds;

        return [
            'signed_in_now'     => DB::selectOne("SELECT COUNT(DISTINCT user_id) c FROM sessions WHERE user_id IS NOT NULL AND last_activity >= ?", [$sessionThreshold])->c,
            'logins_24h'        => DB::selectOne("SELECT COUNT(*) c FROM audit_logs WHERE event = 'login' AND created_at > NOW() - INTERVAL 24 HOUR")->c,
            'unique_logins_24h' => DB::selectOne("SELECT COUNT(DISTINCT user_id) c FROM audit_logs WHERE event = 'login' AND created_at > NOW() - INTERVAL 24 HOUR")->c,
            'failures_24h'      => DB::selectOne("SELECT COUNT(*) c FROM audit_logs WHERE event = 'failed_login' AND created_at > NOW() - INTERVAL 24 HOUR")->c,
            'lockouts_24h'      => DB::selectOne("SELECT COUNT(*) c FROM audit_logs WHERE event = 'lockout' AND created_at > NOW() - INTERVAL 24 HOUR")->c,
        ];
    }

    private function shortUa(?string $ua): string
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
}
