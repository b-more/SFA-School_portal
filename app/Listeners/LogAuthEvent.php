<?php

namespace App\Listeners;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

/**
 * Writes one audit_logs row per authentication event.
 *
 * We reuse the polymorphic audit_logs table: auditable_type = User for a
 * real user, auditable_id may be null for a failed attempt where the
 * identifier didn't resolve. Attempted login credentials (email/phone)
 * are captured in new_values so a failed-login investigation has
 * something to work with — the password itself is scrubbed.
 *
 * This listener MUST NEVER throw. If audit-logging fails we log a
 * warning and swallow it — auth flow trumps auditing.
 */
class LogAuthEvent
{
    public function handleLogin(Login $event): void
    {
        if ($this->alreadySeen($event)) return;
        $this->write('login', $event->user?->getKey(), null);
    }

    public function handleLogout(Logout $event): void
    {
        if ($this->alreadySeen($event)) return;
        $this->write('logout', $event->user?->getKey(), null);
    }

    public function handleFailed(Failed $event): void
    {
        if ($this->alreadySeen($event)) return;
        // Try to resolve the attempted identifier back to a real user id so
        // the trail is useful for investigations, without leaking user
        // existence to the caller of Auth::attempt.
        $creds = $event->credentials ?? [];
        $attempted = [
            'attempted_identifier' => $this->pickIdentifier($creds),
            'guard' => $event->guard ?? null,
        ];
        $userId = $event->user?->getKey() ?? $this->resolveIdentifier($attempted['attempted_identifier']);
        $this->write('failed_login', $userId, $attempted);
    }

    public function handleLockout(Lockout $event): void
    {
        if ($this->alreadySeen($event)) return;
        $this->write('lockout', null, [
            'attempted_identifier' => $this->pickIdentifier($event->request?->all() ?? []),
        ]);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        if ($this->alreadySeen($event)) return;
        $this->write('password_reset', $event->user?->getKey(), null);
    }

    /**
     * Filament's provider chain causes Event::listen(...) to register this
     * listener twice per event class, so each dispatch calls our handler
     * twice with the same event instance. Track seen events by object
     * identity so we write exactly one audit_logs row per real occurrence.
     * SplObjectStorage is per-request (per-worker) and reset when this
     * listener instance is torn down, which is what we want.
     */
    private function alreadySeen(object $event): bool
    {
        static $seen = null;
        if ($seen === null) $seen = new \SplObjectStorage();
        if ($seen->contains($event)) return true;
        $seen->attach($event);
        return false;
    }

    private function write(string $eventName, ?int $userId, ?array $newValues): void
    {
        try {
            AuditLog::create([
                'auditable_type' => $userId ? User::class : null,
                'auditable_id'   => $userId,
                'event'          => $eventName,
                'user_id'        => $userId,
                'ip_address'     => request()?->ip(),
                'user_agent'     => request()?->userAgent(),
                'new_values'     => $newValues,
            ]);
        } catch (\Throwable $e) {
            Log::warning('audit-log write failed', ['event' => $eventName, 'error' => $e->getMessage()]);
        }
    }

    private function pickIdentifier(array $creds): ?string
    {
        foreach (['email', 'username', 'phone', 'login', 'identifier'] as $k) {
            if (! empty($creds[$k])) return (string) $creds[$k];
        }
        return null;
    }

    private function resolveIdentifier(?string $id): ?int
    {
        if (! $id) return null;
        try {
            return User::where('email', $id)
                ->orWhere('username', $id)
                ->orWhere('phone', $id)
                ->value('id');
        } catch (\Throwable) {
            return null;
        }
    }
}
