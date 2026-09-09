<x-filament-panels::page>
    @php
        $u = $this->record;
        $sum = $this->summary();
        $now = $this->currentSession();
        $logins = $this->loginHistory();
        $fails  = $this->failedAttempts();
        $resets = $this->passwordResets();

        $roleColor = match ($u->role_id) {
            1  => '#0e2746',    // Admin
            2  => '#3b82f6',    // Teacher
            4  => '#059669',    // Parent
            5  => '#b08a3e',    // Accountant
            10, 20 => '#8b1a1a',// Clinician / Nurse
            default => '#6b7280',
        };
        $statusColor = match ($u->status) {
            'active'    => '#059669',
            'inactive'  => '#6b7280',
            'suspended' => '#b91c1c',
            default     => '#6b7280',
        };
    @endphp

    <div class="space-y-6">
        {{-- ================================================================
             IDENTITY CARD
             ================================================================ --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid {{ $roleColor }};">
            <div style="padding:24px; display:grid; grid-template-columns:auto 1fr auto; gap:24px; align-items:center;">
                <div style="width:88px; height:88px; border-radius:50%; background:{{ $roleColor }}; color:#fff; display:flex; align-items:center; justify-content:center; font-family:'EB Garamond', Georgia, serif; font-size:34px; font-weight:600;">
                    {{ strtoupper(mb_substr($u->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">User #{{ $u->id }}</div>
                    <h1 style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:#0e2746; font-weight:600; margin:2px 0 0; letter-spacing:-.01em;">{{ $u->name }}</h1>
                    <div style="margin-top:8px; display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
                        <span style="display:inline-block; padding:3px 12px; background:{{ $roleColor }}; color:#fff; font-size:11px; font-weight:600; letter-spacing:.1em; border-radius:2px; text-transform:uppercase;">{{ $u->role?->name ?? 'Role '.$u->role_id }}</span>
                        <span style="display:inline-block; padding:3px 12px; background:{{ $statusColor }}; color:#fff; font-size:11px; font-weight:600; letter-spacing:.1em; border-radius:2px; text-transform:uppercase;">{{ $u->status }}</span>
                        @if($u->must_change_password)
                            <span style="display:inline-block; padding:3px 12px; background:#d97706; color:#fff; font-size:11px; font-weight:600; letter-spacing:.1em; border-radius:2px; text-transform:uppercase;">Must change password</span>
                        @endif
                        @if($now)
                            <span style="display:inline-block; padding:3px 12px; background:#059669; color:#fff; font-size:11px; font-weight:600; letter-spacing:.1em; border-radius:2px; text-transform:uppercase;">● Online now</span>
                        @endif
                    </div>
                </div>
                <div style="text-align:right; font-family:'EB Garamond', Georgia, serif; color:#6b7280; font-style:italic;">
                    Joined<br>
                    <span style="color:#0e2746; font-style:normal; font-size:16px;">{{ $u->created_at?->format('d M Y') ?? '—' }}</span>
                </div>
            </div>

            <div style="border-top:1px solid #e5e7eb; padding:16px 24px; display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:20px;">
                <div>
                    <div style="font-size:10px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:600;">Email</div>
                    <div style="font-family:'EB Garamond', Georgia, serif; color:#0e2746; margin-top:2px; word-break:break-all;">{{ $u->email ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:600;">Username</div>
                    <div style="font-family:ui-monospace, monospace; color:#0e2746; margin-top:2px;">{{ $u->username ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:600;">Phone</div>
                    <div style="font-family:ui-monospace, monospace; color:#0e2746; margin-top:2px;">{{ $u->phone ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:600;">Last login</div>
                    <div style="font-family:'EB Garamond', Georgia, serif; color:#0e2746; margin-top:2px;">{{ $sum['last_login_at']?->format('d M Y · H:i') ?? 'Never' }}</div>
                    @if($sum['last_login_ip'])
                        <div style="font-size:11px; color:#6b7280; margin-top:2px; font-family:ui-monospace, monospace;">from {{ $sum['last_login_ip'] }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================================================================
             LIFETIME COUNTERS
             ================================================================ --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px;">
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #0e2746; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Total logins</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:28px; color:#0e2746; font-weight:600; margin-top:4px;">{{ $sum['total_logins'] }}</div>
                <div style="font-size:11px; color:#6b7280; font-style:italic; margin-top:2px;">Since audit began</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #b91c1c; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Failed attempts</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:28px; color:#b91c1c; font-weight:600; margin-top:4px;">{{ $sum['total_failures'] }}</div>
                <div style="font-size:11px; color:#6b7280; font-style:italic; margin-top:2px;">
                    {{ $sum['last_failure_at']?->diffForHumans() ?? 'None recorded' }}
                </div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #d97706; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Password resets</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:28px; color:#d97706; font-weight:600; margin-top:4px;">{{ $sum['total_resets'] }}</div>
                <div style="font-size:11px; color:#6b7280; font-style:italic; margin-top:2px;">Reset link followed</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid {{ $now ? '#059669' : '#9ca3af' }}; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Right now</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:22px; color:{{ $now ? '#059669' : '#6b7280' }}; font-weight:600; margin-top:6px;">
                    {{ $now ? 'ONLINE' : 'Offline' }}
                </div>
                @if($now)
                    <div style="font-size:11px; color:#059669; font-style:italic; margin-top:2px;">from {{ $now->ip_address ?? '—' }} · {{ \Carbon\Carbon::createFromTimestamp($now->last_activity)->diffForHumans() }}</div>
                @else
                    <div style="font-size:11px; color:#6b7280; font-style:italic; margin-top:2px;">No active session</div>
                @endif
            </div>
        </div>

        {{-- ================================================================
             LOGIN / LOGOUT HISTORY
             ================================================================ --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #0e2746;">
            <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
                <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Session history</div>
                <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:20px; color:#0e2746; font-weight:600; margin:2px 0 0;">Recent logins and logouts</h2>
            </div>
            @if($logins->isEmpty())
                <div style="padding:30px; text-align:center; color:#9ca3af; font-style:italic; font-family:'EB Garamond', Georgia, serif;">— No login events recorded yet. The audit trail started on 2026-09-06.</div>
            @else
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead style="background:#f9fafb;">
                        <tr>
                            <th style="text-align:left; padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">Event</th>
                            <th style="text-align:left; padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">When</th>
                            <th style="text-align:left; padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">IP</th>
                            <th style="text-align:left; padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">Browser</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logins as $r)
                            <tr style="border-top:1px solid #f3f4f6;">
                                <td style="padding:10px 16px;">
                                    <span style="display:inline-block; padding:2px 10px; background:{{ $r->event === 'login' ? '#059669' : '#6b7280' }}; color:#fff; font-size:10px; font-weight:600; letter-spacing:.08em; border-radius:2px; text-transform:uppercase;">{{ $r->event }}</span>
                                </td>
                                <td style="padding:10px 16px; color:#0e2746;">
                                    {{ $r->when->format('D d M Y · H:i:s') }}
                                    <div style="font-size:11px; color:#6b7280; font-style:italic;">{{ $r->when->diffForHumans() }}</div>
                                </td>
                                <td style="padding:10px 16px; font-family:ui-monospace, monospace; font-size:12px; color:#4b5563;">{{ $r->ip_address ?? '—' }}</td>
                                <td style="padding:10px 16px; color:#4b5563;">{{ $this->ua($r->user_agent) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ================================================================
             FAILED ATTEMPTS + PASSWORD RESETS
             ================================================================ --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #b91c1c;">
                <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
                    <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Failed attempts</div>
                    <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:20px; color:#0e2746; font-weight:600; margin:2px 0 0;">Wrong password / unknown identifier</h2>
                </div>
                @forelse($fails as $r)
                    <div style="padding:10px 20px; border-top:1px solid #f3f4f6; display:grid; grid-template-columns:1fr auto; gap:10px; align-items:baseline;">
                        <div>
                            <div style="font-family:'EB Garamond', Georgia, serif; font-size:14px; color:#0e2746;">{{ $r->when->format('d M Y · H:i:s') }}</div>
                            <div style="font-size:11px; color:#6b7280; margin-top:1px;">
                                @if($r->attempted)
                                    tried <span style="font-family:ui-monospace, monospace; background:#fef2f2; padding:1px 4px;">{{ $r->attempted }}</span> ·
                                @endif
                                <span style="font-family:ui-monospace, monospace;">{{ $r->ip_address ?? '—' }}</span> · {{ $this->ua($r->user_agent) }}
                            </div>
                        </div>
                        <div style="font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#b91c1c; font-size:12px;">{{ $r->when->diffForHumans() }}</div>
                    </div>
                @empty
                    <div style="padding:30px; text-align:center; color:#9ca3af; font-style:italic; font-family:'EB Garamond', Georgia, serif;">— No failed attempts recorded.</div>
                @endforelse
            </div>

            <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #d97706;">
                <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
                    <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Password resets</div>
                    <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:20px; color:#0e2746; font-weight:600; margin:2px 0 0;">Reset link followed</h2>
                </div>
                @forelse($resets as $r)
                    <div style="padding:10px 20px; border-top:1px solid #f3f4f6; display:grid; grid-template-columns:1fr auto; gap:10px; align-items:baseline;">
                        <div>
                            <div style="font-family:'EB Garamond', Georgia, serif; font-size:14px; color:#0e2746;">{{ $r->when->format('d M Y · H:i:s') }}</div>
                            <div style="font-size:11px; color:#6b7280; margin-top:1px; font-family:ui-monospace, monospace;">from {{ $r->ip_address ?? '—' }}</div>
                        </div>
                        <div style="font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#d97706; font-size:12px;">{{ $r->when->diffForHumans() }}</div>
                    </div>
                @empty
                    <div style="padding:30px; text-align:center; color:#9ca3af; font-style:italic; font-family:'EB Garamond', Georgia, serif;">— No password resets on this account.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
