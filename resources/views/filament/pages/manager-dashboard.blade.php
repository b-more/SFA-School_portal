<x-filament-panels::page>
    @php
        $vd = $this->getViewData();
        $g  = $vd['greeting'];
        $s  = $vd['summary'];
        $en = $vd['enrolment'];
        $at = $vd['attendance'];
        $ac = $vd['academic'];
        $ev = $vd['upcomingEvents'];
        $lg = $vd['recentLogins'];

        $roleColor = fn ($rid) => match ($rid) {
            1  => '#0e2746',
            2  => '#3b82f6',
            4  => '#059669',
            5  => '#b08a3e',
            10, 20 => '#8b1a1a',
            default => '#6b7280',
        };
    @endphp

    <div class="space-y-6">
        {{-- ============ HEADER GREETING ============ --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #0e2746; padding:22px 24px; display:flex; justify-content:space-between; align-items:baseline; flex-wrap:wrap; gap:14px;">
            <div>
                <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Manager overview</div>
                <h1 style="font-family:'EB Garamond', Georgia, serif; font-size:28px; color:#0e2746; font-weight:600; margin:2px 0 0; letter-spacing:-.01em;">
                    {{ $g['salutation'] }}, {{ $g['name'] }}.
                </h1>
                <div style="font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#6b7280; margin-top:2px;">{{ $g['today'] }}</div>
            </div>
            <div style="font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#6b7280; text-align:right;">
                — a finance-free view of the school's operational day.
            </div>
        </div>

        {{-- ============ OPERATIONAL COUNTERS ============ --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #0e2746; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Active pupils</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:#0e2746; font-weight:600; margin-top:4px;">{{ $s['active_pupils'] }}</div>
                <div style="font-size:11px; color:#6b7280; font-style:italic; margin-top:2px;">{{ $s['transferred_pupils'] }} transferred (record kept)</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #3b82f6; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Active teachers</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:#3b82f6; font-weight:600; margin-top:4px;">{{ $s['active_teachers'] }}</div>
                <div style="font-size:11px; color:#6b7280; font-style:italic; margin-top:2px;">{{ $s['class_teachers'] }} class teachers assigned</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid {{ $at['rate'] >= 90 ? '#059669' : ($at['rate'] >= 75 ? '#d97706' : '#b91c1c') }}; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Today's attendance</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:{{ $at['rate'] >= 90 ? '#059669' : ($at['rate'] >= 75 ? '#d97706' : '#b91c1c') }}; font-weight:600; margin-top:4px;">{{ $at['rate'] }}%</div>
                <div style="font-size:11px; color:#6b7280; font-style:italic; margin-top:2px;">
                    @if($at['total'] === 0) — not yet marked @else {{ $at['present'] }} present · {{ $at['absent'] }} absent · {{ $at['sick'] }} sick @endif
                </div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-left:3px solid #b08a3e; padding:16px 18px;">
                <div style="font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:#6b7280; font-weight:500;">Results this week</div>
                <div style="font-family:'EB Garamond', Georgia, serif; font-size:32px; color:#b08a3e; font-weight:600; margin-top:4px;">{{ $ac['results_this_week'] }}</div>
                <div style="font-size:11px; color:#6b7280; font-style:italic; margin-top:2px;">for {{ $ac['distinct_pupils_week'] }} pupils</div>
            </div>
        </div>

        {{-- ============ ENROLMENT BY CLASS ============ --}}
        <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #0e2746;">
            <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
                <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Enrolment</div>
                <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:20px; color:#0e2746; font-weight:600; margin:2px 0 0;">Roll by class</h2>
            </div>
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead style="background:#f9fafb;">
                    <tr>
                        <th style="text-align:left;  padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">Class</th>
                        <th style="text-align:left;  padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">Class teacher</th>
                        <th style="text-align:right; padding:10px 16px; font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:#6b7280; font-weight:600;">Active pupils</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($en as $r)
                        <tr style="border-top:1px solid #f3f4f6;">
                            <td style="padding:10px 16px; font-family:'EB Garamond', Georgia, serif; font-size:15px; color:#0e2746;">{{ ($r->grade ?? '—') . ' / ' . ($r->section ?? '—') }}</td>
                            <td style="padding:10px 16px; color:#4b5563;">{{ $r->class_teacher ?? '—' }}</td>
                            <td style="padding:10px 16px; text-align:right; font-family:'EB Garamond', Georgia, serif; font-size:16px; color:#0e2746; font-weight:600;">{{ $r->pupils }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ============ TWO-COL: UPCOMING EVENTS + RECENT LOGINS ============ --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #059669;">
                <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
                    <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Calendar</div>
                    <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:20px; color:#0e2746; font-weight:600; margin:2px 0 0;">Upcoming events</h2>
                </div>
                @forelse($ev as $e)
                    <div style="padding:12px 20px; border-top:1px solid #f3f4f6; display:grid; grid-template-columns:auto 1fr; gap:14px; align-items:baseline;">
                        <div style="font-family:'EB Garamond', Georgia, serif; text-align:center; color:#8b1a1a; min-width:52px;">
                            <div style="font-size:20px; font-weight:600; line-height:1;">{{ \Carbon\Carbon::parse($e->start_date)->format('d') }}</div>
                            <div style="font-size:10px; letter-spacing:.14em; text-transform:uppercase;">{{ \Carbon\Carbon::parse($e->start_date)->format('M') }}</div>
                        </div>
                        <div>
                            <div style="font-family:'EB Garamond', Georgia, serif; font-size:16px; color:#0e2746;">{{ $e->title }}</div>
                            <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                                {{ \Carbon\Carbon::parse($e->start_date)->format('g:i A') }}@if($e->location) · {{ $e->location }} @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="padding:24px 20px; color:#9ca3af; font-style:italic; font-family:'EB Garamond', Georgia, serif; text-align:center;">— No upcoming events on the calendar.</div>
                @endforelse
            </div>

            <div style="background:#fff; border:1px solid #e5e7eb; border-top:3px solid #0e2746;">
                <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
                    <div style="font-size:11px; letter-spacing:.28em; text-transform:uppercase; color:#8b1a1a; font-weight:600;">Access</div>
                    <h2 style="font-family:'EB Garamond', Georgia, serif; font-size:20px; color:#0e2746; font-weight:600; margin:2px 0 0;">Recent logins</h2>
                </div>
                @forelse($lg as $r)
                    <div style="padding:10px 20px; border-top:1px solid #f3f4f6; display:grid; grid-template-columns:1fr auto; gap:10px; align-items:baseline;">
                        <div>
                            <div style="font-family:'EB Garamond', Georgia, serif; font-size:14px; color:#0e2746;">
                                {{ $r->name ?? '—' }}
                                <span style="display:inline-block; padding:1px 6px; background:{{ $roleColor($r->role_name === 'Admin' ? 1 : ($r->role_name === 'Accountant' ? 5 : 0)) }}; color:#fff; font-size:9px; font-weight:600; letter-spacing:.06em; border-radius:2px; text-transform:uppercase; margin-left:4px;">{{ $r->role_name ?? '—' }}</span>
                            </div>
                            <div style="font-size:11px; color:#6b7280; margin-top:1px; font-family:ui-monospace, monospace;">{{ $r->ip_address ?? '—' }}</div>
                        </div>
                        <div style="font-family:'EB Garamond', Georgia, serif; font-style:italic; color:#6b7280; font-size:12px;">{{ $r->when->diffForHumans() }}</div>
                    </div>
                @empty
                    <div style="padding:24px 20px; color:#9ca3af; font-style:italic; font-family:'EB Garamond', Georgia, serif; text-align:center;">— No recent logins recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
