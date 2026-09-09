<x-filament-panels::page>
    @php
        $s = $this->summary();
        $now = $this->currentlySignedIn();
        $logins = $this->recentLogins();
        $failures = $this->recentFailures();

        $roleColor = function ($roleId) {
            return match ($roleId) {
                1  => '#0e2746',   // Admin
                2  => '#3b82f6',   // Teacher
                4  => '#059669',   // Parent
                5  => '#b08a3e',   // Accountant
                10 => '#8b1a1a',   // Clinician
                default => '#6b7280',
            };
        };
    @endphp

    <div class="space-y-6">
        {{-- ============= AT-A-GLANCE COUNTERS ============= --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px;">
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #059669; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Signed in right now</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:#059669; font-weight:600; margin-top:4px; line-height:1;">{{ $s['signed_in_now'] }}</div>
                <div style="font-size:12px; color:#6b7280; margin-top:6px; font-style:italic;">Active in the last {{ config('session.lifetime') }} min</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #0e2746; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Logins · 24 h</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:#0e2746; font-weight:600; margin-top:4px; line-height:1;">{{ $s['logins_24h'] }}</div>
                <div style="font-size:12px; color:#6b7280; margin-top:6px; font-style:italic;">{{ $s['unique_logins_24h'] }} distinct users</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #b91c1c; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Failed attempts · 24 h</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:#b91c1c; font-weight:600; margin-top:4px; line-height:1;">{{ $s['failures_24h'] }}</div>
                <div style="font-size:12px; color:#6b7280; margin-top:6px; font-style:italic;">Wrong password / unknown identifier</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #d97706; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Lockouts · 24 h</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:#d97706; font-weight:600; margin-top:4px; line-height:1;">{{ $s['lockouts_24h'] }}</div>
                <div style="font-size:12px; color:#6b7280; margin-top:6px; font-style:italic;">Rate-limited by throttle</div>
            </div>
        </div>

        {{-- ============= WHO'S SIGNED IN RIGHT NOW ============= --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #059669;">
            <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:baseline;">
                <div>
                    <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">On the portal now</div>
                    <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:22px; color:#0e2746; font-weight:600; margin:2px 0 0;">Currently signed in · {{ $now->count() }}</h2>
                </div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#6b7280; font-size:14px;">— refresh the page for a live count</div>
            </div>
            @if($now->isEmpty())
                <div style="padding:30px; text-align:center; color:#9ca3af; font-style:italic; font-family:'EB Garamond', Georgia, serif;">— No active sessions right now.</div>
            @else
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead style="background:#f9fafb;">
                        <tr>
                            <th style="text-align:left;  padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">User</th>
                            <th style="text-align:left;  padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">Role</th>
                            <th style="text-align:left;  padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">IP</th>
                            <th style="text-align:left;  padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">Browser</th>
                            <th style="text-align:right; padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">Last activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($now as $u)
                            <tr style="border-top:1px solid #f3f4f6;">
                                <td style="padding:10px 16px;">
                                    <div style="font-family:'EB Garamond', Georgia, serif; font-size:15px; color:#0e2746;">{{ $u->name ?? '—' }}</div>
                                    <div style="font-size:11px; color:#6b7280; margin-top:2px;">{{ $u->email ?? $u->username ?? ('#' . $u->user_id) }}</div>
                                </td>
                                <td style="padding:10px 16px;"><span style="display:inline-block; padding:2px 10px; background:{{ $roleColor($u->role_id) }}; color:#fff; font-size:10px; font-weight:600; letter-spacing:.08em; border-radius:2px; text-transform:uppercase;">{{ $u->role_name ?? 'role ' . $u->role_id }}</span></td>
                                <td style="padding:10px 16px; font-family:ui-monospace, monospace; font-size:12px; color:#4b5563;">{{ $u->ip_address ?? '—' }}</td>
                                <td style="padding:10px 16px; color:#4b5563;">{{ $u->ua_short }}</td>
                                <td style="padding:10px 16px; text-align:right; color:#059669; font-style:italic; font-family:'EB Garamond', Georgia, serif;">{{ $u->seen_ago }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ============= TWO-COLUMN: RECENT LOGINS + RECENT FAILURES ============= --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            {{-- Recent logins --}}
            <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #0e2746;">
                <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
                    <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Recent successful logins</div>
                    <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:20px; color:#0e2746; font-weight:600; margin:2px 0 0;">Last {{ $logins->count() }}</h2>
                </div>
                @forelse($logins as $r)
                    <div style="padding:10px 20px; border-top:1px solid #f3f4f6; display:grid; grid-template-columns:1fr auto; gap:10px; align-items:baseline;">
                        <div>
                            <div style="font-family:'EB Garamond', Georgia, serif; font-size:14px; color:#0e2746;">
                                {{ $r->name ?? '—' }}
                                <span style="display:inline-block; padding:1px 6px; background:{{ $roleColor($r->role_id) }}; color:#fff; font-size:9px; font-weight:600; letter-spacing:.06em; border-radius:2px; text-transform:uppercase; margin-left:4px;">{{ $r->role_name ?? '—' }}</span>
                            </div>
                            <div style="font-size:11px; color:#6b7280; margin-top:1px;">
                                <span style="font-family:ui-monospace, monospace;">{{ $r->ip_address ?? '—' }}</span> · {{ $r->ua_short }}
                            </div>
                        </div>
                        <div style="font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#6b7280; font-size:12px;">{{ $r->when->diffForHumans() }}</div>
                    </div>
                @empty
                    <div style="padding:30px; text-align:center; color:#9ca3af; font-style:italic; font-family:'EB Garamond', Georgia, serif;">— No login events recorded yet.</div>
                @endforelse
            </div>

            {{-- Recent failed attempts --}}
            <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #b91c1c;">
                <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
                    <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Recent failed attempts</div>
                    <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:20px; color:#0e2746; font-weight:600; margin:2px 0 0;">Last {{ $failures->count() }}</h2>
                </div>
                @forelse($failures as $r)
                    <div style="padding:10px 20px; border-top:1px solid #f3f4f6; display:grid; grid-template-columns:1fr auto; gap:10px; align-items:baseline;">
                        <div>
                            <div style="font-family:'EB Garamond', Georgia, serif; font-size:14px; color:#0e2746;">
                                @if($r->user_id)
                                    {{ $r->name ?? '—' }}
                                    <span style="display:inline-block; padding:1px 6px; background:{{ $roleColor($r->role_id) }}; color:#fff; font-size:9px; font-weight:600; letter-spacing:.06em; border-radius:2px; text-transform:uppercase; margin-left:4px;">{{ $r->role_name ?? '—' }}</span>
                                @else
                                    <span style="color:#9ca3af; font-style:italic;">Unknown identifier</span>
                                @endif
                            </div>
                            <div style="font-size:11px; color:#6b7280; margin-top:1px;">
                                @if($r->attempted)
                                    tried <span style="font-family:ui-monospace, monospace; background:#fef2f2; padding:1px 4px;">{{ $r->attempted }}</span> ·
                                @endif
                                <span style="font-family:ui-monospace, monospace;">{{ $r->ip_address ?? '—' }}</span> · {{ $r->ua_short }}
                            </div>
                        </div>
                        <div style="font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#b91c1c; font-size:12px;">{{ $r->when->diffForHumans() }}</div>
                    </div>
                @empty
                    <div style="padding:30px; text-align:center; color:#9ca3af; font-style:italic; font-family:'EB Garamond', Georgia, serif;">— No failed attempts recorded.</div>
                @endforelse
            </div>
        </div>

        <div style="text-align:right; font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#6b7280; font-size:14px;">
            For the full historical trail with filters, open <a href="{{ \App\Filament\Resources\AuditLogResource::getUrl('index') }}" style="color:#0e2746; text-decoration:underline;">Login Trail</a>.
        </div>
    </div>
</x-filament-panels::page>
