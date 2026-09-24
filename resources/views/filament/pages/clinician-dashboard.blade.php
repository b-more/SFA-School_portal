<x-filament-panels::page>
    @php
        $t       = $this->todayStats;
        $w       = $this->weekStats;
        $low     = $this->lowStock;
        $recent  = $this->recentVisits;
        $repeats = $this->repeatVisitors;
        $spend   = $this->monthSpend;

        $user    = auth()->user();
        $today   = now();

        $moneyFmt = fn ($n) => 'K' . number_format((float) $n, 2);

        // Brand palette (matches the report cards + fee tracker + clinic PDFs)
        $navy      = '#0F2A44';
        $red       = '#8B1A1A';
        $parch     = '#F5EFE0';
        $parchDeep = '#E5D9BE';
    @endphp

    <style>
        .clinic-hero {
            background: linear-gradient(135deg, {{ $navy }} 0%, #16385b 100%);
            color: {{ $parch }};
            border-radius: 12px;
            padding: 28px 32px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 24px -8px rgba(15,42,68,0.35);
        }
        .clinic-hero::before {
            content: "";
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, {{ $red }} 0%, {{ $red }} 60%, {{ $parch }} 60%);
        }
        .clinic-hero h1 { font-family: 'Georgia', 'DejaVu Serif', serif; font-size: 22pt; font-weight: 700; letter-spacing: 0.4pt; margin: 0 0 4px; color: #fff; }
        .clinic-hero .motto { font-family: Georgia, serif; font-style: italic; color: {{ $parch }}; opacity: 0.85; font-size: 10.5pt; }
        .clinic-hero .date-strip {
            display: inline-block; margin-top: 12px;
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.14);
            padding: 6px 14px; border-radius: 999px; font-size: 10pt;
            letter-spacing: 0.4pt; color: {{ $parch }};
        }
        .clinic-hero .quick-row { margin-top: 22px; display: flex; flex-wrap: wrap; gap: 10px; }
        .clinic-hero a.qa {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; color: {{ $navy }};
            border-radius: 8px; padding: 10px 18px;
            font-weight: 600; font-size: 10pt; letter-spacing: 0.2pt;
            text-decoration: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.12);
            transition: transform 120ms ease, box-shadow 120ms ease;
        }
        .clinic-hero a.qa:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,0.18); }
        .clinic-hero a.qa.primary { background: {{ $red }}; color: #fff; }
        .clinic-hero a.qa .ico { font-size: 14pt; line-height: 1; }

        /* KPI tiles */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-top: 22px; }
        @media (max-width: 900px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }

        .kpi {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-left: 4px solid var(--kpi-accent, {{ $navy }});
            border-radius: 10px;
            padding: 18px 20px;
            display: flex; align-items: center; gap: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        :root[data-theme="dark"] .kpi, :root:not([data-theme="light"]):is(.dark) .kpi { background: rgb(31,41,55); border-color: #374151; }

        .kpi .icon-tile {
            width: 44px; height: 44px; border-radius: 10px;
            background: var(--kpi-tile-bg, {{ $parch }}); color: var(--kpi-tile-fg, {{ $navy }});
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .kpi .icon-tile svg { width: 22px; height: 22px; }
        .kpi .num { font-family: Georgia, 'DejaVu Serif', serif; font-size: 26pt; font-weight: 700; color: {{ $navy }}; line-height: 1; }
        :root[data-theme="dark"] .kpi .num, :root:not([data-theme="light"]):is(.dark) .kpi .num { color: #F3F4F6; }
        .kpi .lbl { font-size: 8.5pt; letter-spacing: 1.2pt; text-transform: uppercase; color: #6B7280; margin-top: 6px; font-weight: 600; }

        .kpi.warning { --kpi-accent: #d97706; --kpi-tile-bg: #FEF3C7; --kpi-tile-fg: #92400e; }
        .kpi.danger  { --kpi-accent: {{ $red }}; --kpi-tile-bg: #FEE2E2; --kpi-tile-fg: {{ $red }}; }
        .kpi.success { --kpi-accent: #059669; --kpi-tile-bg: #D1FAE5; --kpi-tile-fg: #065f46; }

        /* Panel card */
        .panel {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 20px 22px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        :root[data-theme="dark"] .panel, :root:not([data-theme="light"]):is(.dark) .panel { background: rgb(31,41,55); border-color: #374151; }
        .panel-heading {
            display: flex; justify-content: space-between; align-items: baseline;
            margin-bottom: 14px; padding-bottom: 10px;
            border-bottom: 1px solid #E5E7EB;
        }
        :root[data-theme="dark"] .panel-heading, :root:not([data-theme="light"]):is(.dark) .panel-heading { border-color: #374151; }
        .panel-heading h3 { font-family: Georgia, 'DejaVu Serif', serif; font-size: 13pt; font-weight: 600; color: {{ $navy }}; letter-spacing: 0.2pt; margin: 0; border-left: 3px solid {{ $red }}; padding-left: 10px; line-height: 1.2; }
        :root[data-theme="dark"] .panel-heading h3, :root:not([data-theme="light"]):is(.dark) .panel-heading h3 { color: #E5E7EB; }
        .panel-heading .sub { font-size: 8.5pt; color: #6B7280; letter-spacing: 0.3pt; }

        /* Bar chart */
        .bars { display: flex; align-items: flex-end; gap: 12px; height: 180px; padding: 8px 0 4px; }
        .bars .col { flex: 1; display: flex; flex-direction: column; align-items: center; }
        .bars .count { font-size: 10pt; font-weight: 700; color: {{ $navy }}; margin-bottom: 4px; }
        :root[data-theme="dark"] .bars .count, :root:not([data-theme="light"]):is(.dark) .bars .count { color: #E5E7EB; }
        .bars .bar {
            width: 100%; border-radius: 6px 6px 0 0;
            background: linear-gradient(180deg, {{ $navy }} 0%, #16385b 100%);
            transition: filter 120ms ease;
        }
        .bars .bar:hover { filter: brightness(1.1); }
        .bars .col.zero .bar { background: {{ $parchDeep }}; }
        .bars .day { font-size: 8pt; letter-spacing: 1pt; color: #6B7280; text-transform: uppercase; margin-top: 6px; }

        /* Table */
        .visits-table { width: 100%; font-size: 10pt; }
        .visits-table thead th { text-align: left; padding: 8px 10px; background: {{ $parch }}; color: {{ $navy }}; font-size: 8.5pt; letter-spacing: 0.5pt; text-transform: uppercase; font-weight: 700; border-bottom: 1px solid {{ $parchDeep }}; }
        .visits-table tbody td { padding: 10px; border-bottom: 1px solid #F3F4F6; }
        :root[data-theme="dark"] .visits-table tbody td, :root:not([data-theme="light"]):is(.dark) .visits-table tbody td { border-color: #374151; }
        .visits-table tbody tr:hover { background: rgba(139,26,26,0.04); }
        .visits-table .name { font-weight: 600; color: {{ $navy }}; }
        :root[data-theme="dark"] .visits-table .name, :root:not([data-theme="light"]):is(.dark) .visits-table .name { color: #F3F4F6; }
        .visits-table .out-pill { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 8pt; letter-spacing: 0.3pt; font-weight: 600; text-transform: uppercase; }
        .out-returned { background: #D1FAE5; color: #065f46; }
        .out-sent     { background: #FEF3C7; color: #92400e; }
        .out-referred { background: #FEE2E2; color: {{ $red }}; }
        .sick-flag { display: inline-block; margin-left: 6px; padding: 1px 8px; border-radius: 999px; font-size: 7.5pt; background: {{ $parch }}; color: {{ $red }}; letter-spacing: 0.4pt; }
        .visits-table a.open { color: {{ $red }}; font-size: 9pt; font-weight: 600; text-decoration: none; }
        .visits-table a.open:hover { text-decoration: underline; }

        /* Alert cards */
        .alert-card {
            background: #fff; border-radius: 10px; padding: 16px 18px;
            border-left: 4px solid var(--alert-color, {{ $navy }});
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            border-top: 1px solid #E5E7EB; border-right: 1px solid #E5E7EB; border-bottom: 1px solid #E5E7EB;
        }
        :root[data-theme="dark"] .alert-card, :root:not([data-theme="light"]):is(.dark) .alert-card { background: rgb(31,41,55); border-color: #374151; }
        .alert-card + .alert-card { margin-top: 14px; }
        .alert-card h4 { font-family: Georgia, serif; font-size: 11pt; margin: 0 0 8px; color: {{ $navy }}; display: flex; justify-content: space-between; align-items: baseline; }
        :root[data-theme="dark"] .alert-card h4, :root:not([data-theme="light"]):is(.dark) .alert-card h4 { color: #E5E7EB; }
        .alert-card .badge { font-size: 8.5pt; padding: 1px 8px; border-radius: 999px; font-weight: 600; }
        .alert-card.danger  { --alert-color: {{ $red }}; }
        .alert-card.danger  .badge { background: #FEE2E2; color: {{ $red }}; }
        .alert-card.warning { --alert-color: #d97706; }
        .alert-card.warning .badge { background: #FEF3C7; color: #92400e; }
        .alert-card.success { --alert-color: #059669; }
        .alert-card.success .badge { background: #D1FAE5; color: #065f46; }

        .stock-list { font-size: 10pt; }
        .stock-list li { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #E5E7EB; }
        :root[data-theme="dark"] .stock-list li, :root:not([data-theme="light"]):is(.dark) .stock-list li { border-color: #374151; }
        .stock-list li:last-child { border-bottom: 0; }
        .stock-list .qty { font-weight: 700; color: {{ $red }}; }

        .spend-card { text-align: center; padding: 22px 20px; }
        .spend-card .amount { font-family: Georgia, serif; font-size: 26pt; font-weight: 700; color: {{ $navy }}; letter-spacing: 0.2pt; }
        :root[data-theme="dark"] .spend-card .amount, :root:not([data-theme="light"]):is(.dark) .spend-card .amount { color: #E5E7EB; }
        .spend-card .caption { font-size: 8.5pt; color: #6B7280; letter-spacing: 0.6pt; text-transform: uppercase; margin-top: 6px; }

        .main-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 22px; margin-top: 22px; }
        @media (max-width: 900px) { .main-grid { grid-template-columns: 1fr; } }
    </style>

    {{-- ============ HERO ============ --}}
    <div class="clinic-hero">
        <h1>Good {{ $today->format('a') === 'am' ? 'morning' : ($today->hour < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', $user->name)[0] ?? 'Clinician' }}</h1>
        <div class="motto">School Clinic · St. Francis of Assisi Private School</div>
        <div class="date-strip">{{ $today->format('l, d F Y') }}</div>

        <div class="quick-row">
            <a class="qa primary" href="{{ route('filament.admin.resources.clinic-visits.create') }}">
                <span class="ico">＋</span> Record a visit
            </a>
            <a class="qa" href="{{ route('filament.admin.resources.stock-transactions.create') }}">
                <span class="ico">🧾</span> Stock movement
            </a>
            <a class="qa" href="{{ route('filament.admin.pages.clinic-reports') }}">
                <span class="ico">📊</span> Reports
            </a>
        </div>
    </div>

    {{-- ============ KPI TILES ============ --}}
    <div class="kpi-grid">
        <div class="kpi">
            <div class="icon-tile">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 0 0 .95.69h4.171c.969 0 1.371 1.24.588 1.81l-3.375 2.453a1 1 0 0 0-.363 1.118l1.287 3.966c.3.921-.755 1.688-1.539 1.118l-3.375-2.454a1 1 0 0 0-1.176 0l-3.375 2.454c-.783.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 0 0-.363-1.118L2.05 9.393c-.783-.57-.38-1.81.588-1.81h4.17a1 1 0 0 0 .951-.69l1.286-3.966z"/></svg>
            </div>
            <div>
                <div class="num">{{ $t['total'] }}</div>
                <div class="lbl">Visits today</div>
            </div>
        </div>
        <div class="kpi {{ $t['sick'] > 0 ? 'warning' : '' }}">
            <div class="icon-tile">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
            </div>
            <div>
                <div class="num">{{ $t['sick'] }}</div>
                <div class="lbl">Sick notes issued</div>
            </div>
        </div>
        <div class="kpi {{ $t['sent_home'] > 0 ? 'warning' : '' }}">
            <div class="icon-tile">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/></svg>
            </div>
            <div>
                <div class="num">{{ $t['sent_home'] }}</div>
                <div class="lbl">Sent home</div>
            </div>
        </div>
        <div class="kpi {{ $t['referred'] > 0 ? 'danger' : '' }}">
            <div class="icon-tile">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>
            <div>
                <div class="num">{{ $t['referred'] }}</div>
                <div class="lbl">Referred out</div>
            </div>
        </div>
    </div>

    {{-- ============ MAIN GRID ============ --}}
    <div class="main-grid">

        {{-- LEFT: trend + recent visits --}}
        <div>
            <div class="panel">
                <div class="panel-heading">
                    <h3>This week · visits by day</h3>
                    <span class="sub">{{ $w['from']->format('D d M') }} → {{ $w['to']->format('D d M Y') }} · {{ $w['total'] }} total</span>
                </div>
                @if($w['total'] === 0)
                    <div style="text-align:center; color:#6B7280; padding:36px 0; font-style:italic; font-size:10pt;">
                        A quiet week so far. When you record a visit it lands here.
                    </div>
                @else
                    <div class="bars">
                        @foreach($w['by_day'] as $day => $count)
                            @php $pct = $w['peak'] > 0 ? max(6, ($count / $w['peak']) * 100) : 6; @endphp
                            <div class="col {{ $count === 0 ? 'zero' : '' }}">
                                <div class="count">{{ $count }}</div>
                                <div class="bar" style="height: {{ $pct }}%; min-height: 4px;"></div>
                                <div class="day">{{ $day }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="panel" style="margin-top: 22px;">
                <div class="panel-heading">
                    <h3>Recent visits</h3>
                    <a href="{{ route('filament.admin.resources.clinic-visits.index') }}" style="font-size:9pt; color:{{ $red }}; text-decoration:none; font-weight:600;">View all →</a>
                </div>
                @if($recent->isEmpty())
                    <div style="text-align:center; color:#6B7280; padding:28px 0; font-style:italic; font-size:10pt;">
                        No visits recorded yet — click <strong>Record a visit</strong> above.
                    </div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="visits-table">
                            <thead>
                                <tr>
                                    <th>Date</th><th>Pupil</th><th>Grade</th><th>Complaints</th><th>Outcome</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent as $v)
                                    <tr>
                                        <td style="white-space:nowrap; color:#6B7280;">{{ \Carbon\Carbon::parse($v->visit_date)->format('d M') }}</td>
                                        <td><span class="name">{{ $v->student_name }}</span></td>
                                        <td style="color:#6B7280;">{{ $v->grade }}</td>
                                        <td style="color:#4B5563;">{{ $v->complaints->pluck('name')->take(3)->join(', ') ?: '—' }}</td>
                                        <td>
                                            @if($v->outcome === 'sent_home')
                                                <span class="out-pill out-sent">Sent home</span>
                                            @elseif($v->outcome === 'referred')
                                                <span class="out-pill out-referred">Referred</span>
                                            @else
                                                <span class="out-pill out-returned">Returned</span>
                                            @endif
                                            @if($v->sick_note_issued) <span class="sick-flag">sick note</span> @endif
                                        </td>
                                        <td style="text-align:right;">
                                            <a class="open" href="{{ route('filament.admin.resources.clinic-visits.edit', $v) }}">Open →</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: alerts sidebar --}}
        <div>
            <div class="alert-card {{ $low->isNotEmpty() ? 'danger' : 'success' }}">
                <h4>
                    Low stock
                    <span class="badge">{{ $low->count() }} {{ $low->count() === 1 ? 'item' : 'items' }}</span>
                </h4>
                @if($low->isEmpty())
                    <div style="font-size:10pt; color:#065f46;">All items above their reorder level.</div>
                @else
                    <ul class="stock-list" style="list-style:none; padding:0; margin:0;">
                        @foreach($low as $item)
                            <li>
                                <span>{{ $item->name }}</span>
                                <span class="qty">{{ $item->current_balance }} {{ $item->unit }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('filament.admin.resources.stock-transactions.create') }}"
                       style="display:inline-block; margin-top:12px; padding:8px 14px; background:{{ $red }}; color:#fff; border-radius:6px; font-size:9.5pt; font-weight:600; text-decoration:none; letter-spacing:0.2pt;">
                        Record a purchase →
                    </a>
                @endif
            </div>

            <div class="alert-card {{ $repeats->isNotEmpty() ? 'warning' : 'success' }}">
                <h4>
                    Repeat visitors this month
                    <span class="badge">{{ $repeats->count() }} flagged</span>
                </h4>
                @if($repeats->isEmpty())
                    <div style="font-size:10pt; color:#065f46;">No pupils flagged this month.</div>
                @else
                    <div style="font-size:8.5pt; color:#92400e; margin-bottom:8px; letter-spacing:0.2pt;">≥ 3 visits — worth a welfare check</div>
                    <ul class="stock-list" style="list-style:none; padding:0; margin:0;">
                        @foreach($repeats as $rv)
                            <li>
                                <span>{{ $rv->student_name }}</span>
                                <span class="qty" style="color:#92400e;">{{ $rv->c }} visits</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="alert-card">
                <h4>This month's medicine spend</h4>
                <div class="spend-card" style="padding: 0;">
                    <div class="amount">{{ $moneyFmt($spend) }}</div>
                    <div class="caption">{{ now()->startOfMonth()->format('d M') }} → {{ now()->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
