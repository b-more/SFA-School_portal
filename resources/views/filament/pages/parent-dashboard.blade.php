<x-filament-panels::page>
{{-- St. Francis of Assisi — Parent Dashboard
     Corporate Design System · DM Sans · Navy/Red --}}

<div class="sfa-pd">

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&family=JetBrains+Mono:wght@500;600;700&display=swap');

    .sfa-pd {
        --navy: 30,58,95; --red: 220,38,38; --green: 5,150,105;
        --amber: 217,119,6; --blue: 59,130,246; --purple: 124,58,237;
        --card-bg: 255,255,255; --card-border: 229,231,235;
        --text-primary: 17,24,39; --text-secondary: 107,114,128; --text-tertiary: 156,163,175;
        --surface: 249,250,251;
        font-family: 'DM Sans', sans-serif;
    }
    .dark .sfa-pd {
        --card-bg: 31,41,55; --card-border: 55,65,81;
        --text-primary: 243,244,246; --text-secondary: 156,163,175; --text-tertiary: 107,114,128;
        --surface: 17,24,39;
    }

    .sfa-hero { background: linear-gradient(135deg, rgb(var(--navy)) 0%, #2c5282 60%, #1e3a5f 100%); border-radius: 16px; padding: 28px 32px; color: #fff; position: relative; overflow: hidden; }
    .sfa-hero::before { content:''; position:absolute; top:-50%; right:-15%; width:400px; height:400px; border-radius:50%; background:rgba(255,255,255,0.03); }
    .sfa-hero::after { content:''; position:absolute; bottom:-30%; left:10%; width:250px; height:250px; border-radius:50%; background:rgba(255,255,255,0.02); }

    .sfa-kpi-strip { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; margin-top:20px; position:relative; z-index:1; }
    .sfa-kpi { background:rgba(255,255,255,0.08); border-radius:12px; padding:14px 16px; backdrop-filter:blur(4px); text-align:center; }
    .sfa-kpi-val { font-family:'JetBrains Mono',monospace; font-size:1.35rem; font-weight:700; line-height:1; }
    .sfa-kpi-lbl { font-size:0.65rem; text-transform:uppercase; letter-spacing:0.06em; opacity:0.65; margin-top:4px; font-weight:500; }

    .sfa-card { background:rgb(var(--card-bg)); border:1px solid rgb(var(--card-border)); border-radius:14px; overflow:hidden; }
    .sfa-card + .sfa-card { margin-top:16px; }
    .sfa-card-head { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid rgb(var(--card-border)); }
    .sfa-card-title { font-size:0.85rem; font-weight:600; color:rgb(var(--text-primary)); display:flex; align-items:center; gap:8px; }
    .sfa-card-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .sfa-card-link { font-size:0.75rem; font-weight:600; color:rgb(var(--blue)); text-decoration:none; }
    .sfa-card-link:hover { text-decoration:underline; }

    .sfa-child-header { background:linear-gradient(135deg, rgb(var(--navy)) 0%, #2c5282 100%); border-radius:14px 14px 0 0; padding:20px 24px; color:#fff; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
    .sfa-child-avatar { width:48px; height:48px; border-radius:12px; background:rgba(255,255,255,0.15); border:2px solid rgba(255,255,255,0.3); display:flex; align-items:center; justify-content:center; font-size:1.3rem; font-weight:700; flex-shrink:0; }
    .sfa-child-name { font-size:1.1rem; font-weight:700; }
    .sfa-child-meta { font-size:0.8rem; opacity:0.75; margin-top:2px; }
    .sfa-child-stat { text-align:right; }
    .sfa-child-stat-val { font-family:'JetBrains Mono',monospace; font-size:1.15rem; font-weight:700; }
    .sfa-child-stat-lbl { font-size:0.65rem; opacity:0.6; text-transform:uppercase; letter-spacing:0.04em; }

    .sfa-3col { display:grid; grid-template-columns:repeat(3,1fr); }
    .sfa-3col > div { padding:20px; }
    .sfa-3col > div:not(:last-child) { border-right:1px solid rgb(var(--card-border)); }

    .sfa-section-title { font-size:0.78rem; font-weight:600; color:rgb(var(--text-primary)); display:flex; align-items:center; gap:6px; margin-bottom:14px; }
    .sfa-section-dot { width:6px; height:6px; border-radius:50%; }

    .sfa-progress { width:100%; background:rgba(var(--card-border),0.5); border-radius:100px; height:6px; overflow:hidden; }
    .sfa-progress-bar { height:100%; border-radius:100px; transition:width 0.6s ease; }

    .sfa-stat-row { display:flex; align-items:center; gap:8px; font-size:0.78rem; color:rgb(var(--text-secondary)); }
    .sfa-stat-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }

    .sfa-day-grid { display:flex; gap:4px; }
    .sfa-day { display:flex; flex-direction:column; align-items:center; gap:3px; }
    .sfa-day-label { font-size:0.6rem; color:rgb(var(--text-tertiary)); }
    .sfa-day-cell { width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; color:#fff; }

    .sfa-hw-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:10px; }
    .sfa-hw-card { padding:12px; border-radius:10px; border:1px solid rgb(var(--card-border)); background:rgb(var(--surface)); }
    .sfa-hw-title { font-size:0.78rem; font-weight:600; color:rgb(var(--text-primary)); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .sfa-hw-sub { font-size:0.7rem; color:rgb(var(--text-secondary)); margin-top:3px; }

    .sfa-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:0.65rem; font-weight:600; white-space:nowrap; }
    .sfa-badge-green { background:rgba(5,150,105,0.1); color:#059669; }
    .sfa-badge-red { background:rgba(220,38,38,0.1); color:#dc2626; }
    .sfa-badge-amber { background:rgba(217,119,6,0.1); color:#d97706; }
    .sfa-badge-blue { background:rgba(59,130,246,0.1); color:#3b82f6; }
    .sfa-badge-purple { background:rgba(124,58,237,0.1); color:#7c3aed; }
    .dark .sfa-badge-green { background:rgba(5,150,105,0.2); color:#34d399; }
    .dark .sfa-badge-red { background:rgba(220,38,38,0.2); color:#f87171; }
    .dark .sfa-badge-amber { background:rgba(217,119,6,0.2); color:#fbbf24; }
    .dark .sfa-badge-blue { background:rgba(59,130,246,0.2); color:#93c5fd; }

    .sfa-event-row { display:flex; align-items:flex-start; gap:14px; padding:12px 16px; border-bottom:1px solid rgba(var(--card-border),0.5); }
    .sfa-event-row:last-child { border-bottom:none; }
    .sfa-event-date { min-width:46px; text-align:center; background:rgba(var(--purple),0.08); border-radius:8px; padding:6px 4px; flex-shrink:0; }
    .dark .sfa-event-date { background:rgba(var(--purple),0.2); }
    .sfa-event-day { font-family:'JetBrains Mono',monospace; font-size:1.1rem; font-weight:700; color:rgb(var(--purple)); line-height:1; }
    .sfa-event-month { font-size:0.6rem; text-transform:uppercase; color:rgb(var(--purple)); opacity:0.7; }
    .sfa-event-title { font-size:0.85rem; font-weight:600; color:rgb(var(--text-primary)); }
    .sfa-event-meta { font-size:0.72rem; color:rgb(var(--text-secondary)); margin-top:2px; }

    .sfa-payment-row { display:flex; align-items:center; gap:14px; padding:12px 16px; border-bottom:1px solid rgba(var(--card-border),0.5); }
    .sfa-payment-row:last-child { border-bottom:none; }
    .sfa-payment-icon { width:36px; height:36px; border-radius:50%; background:rgba(var(--green),0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .dark .sfa-payment-icon { background:rgba(var(--green),0.2); }
    .sfa-payment-amount { font-family:'JetBrains Mono',monospace; font-size:0.92rem; font-weight:700; color:rgb(var(--green)); text-align:right; }
    .sfa-payment-method { font-size:0.68rem; color:rgb(var(--text-tertiary)); text-align:right; }

    .sfa-empty { padding:36px 20px; text-align:center; color:rgb(var(--text-tertiary)); }
    .sfa-empty svg { width:40px; height:40px; margin:0 auto 10px; opacity:0.35; }

    .sfa-fade { opacity:0; transform:translateY(10px); animation:sfaPdFade 0.4s ease forwards; }
    @keyframes sfaPdFade { to { opacity:1; transform:translateY(0); } }

    .sfa-bottom-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:24px; }

    @media(max-width:1024px) {
        .sfa-3col { grid-template-columns:1fr; }
        .sfa-3col > div:not(:last-child) { border-right:none; border-bottom:1px solid rgb(var(--card-border)); }
    }
    @media(max-width:768px) {
        .sfa-kpi-strip { grid-template-columns:repeat(3,1fr); }
        .sfa-child-header { flex-direction:column; text-align:center; }
        .sfa-child-stat { text-align:center; }
        .sfa-bottom-grid { grid-template-columns:1fr; }
        .sfa-hero { padding:20px 16px; }
        .sfa-card-head { padding:12px 16px; }
        .sfa-payment-row, .sfa-event-row { padding:10px 12px; gap:10px; }
        .sfa-hw-grid { grid-template-columns:1fr; }
    }
    @media(max-width:480px) {
        .sfa-kpi-strip { grid-template-columns:repeat(2,1fr); }
        .sfa-kpi { padding:10px 12px; }
        .sfa-kpi-val { font-size:1.1rem; }
        .sfa-child-name { font-size:0.95rem; }
        .sfa-child-avatar { width:40px; height:40px; font-size:1rem; border-radius:10px; }
        .sfa-day-cell { width:24px; height:24px; font-size:0.6rem; }
        .sfa-day-grid { gap:2px; }
        .sfa-payment-row { flex-wrap:wrap; }
        .sfa-payment-amount { font-size:0.82rem; }
    }
</style>

@php
    $parentGuardian = $this->getParentGuardian();
    $students = $this->getStudents();
    $stats = $this->getDashboardStats();
    $attendanceSummary = $this->getAttendanceSummary();
    $feeSummary = $this->getFeeSummary();
    $academicPerformance = $this->getAcademicPerformance();
    $reportCards = $this->getReportCards();
    $homeworkPerChild = $this->getHomeworkPerChild();
    $feePayments = $this->getFeePayments();
    $upcomingEvents = $this->getUpcomingEvents();
@endphp

{{-- Hero Banner --}}
<div class="sfa-hero sfa-fade" style="animation-delay:0.05s">
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;position:relative;z-index:1">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;flex-shrink:0">
            {{ strtoupper(substr($parentGuardian->name ?? auth()->user()->name, 0, 1)) }}
        </div>
        <div style="flex:1;min-width:0">
            <div style="font-size:1.4rem;font-weight:700">{{ $parentGuardian->name ?? auth()->user()->name }}</div>
            <div style="font-size:0.85rem;opacity:0.7;margin-top:2px">
                Parent / Guardian
                @if($parentGuardian && $parentGuardian->relationship) &middot; {{ ucfirst($parentGuardian->relationship) }} @endif
                &middot; {{ $stats['children_count'] }} {{ $stats['children_count'] === 1 ? 'child' : 'children' }} enrolled
            </div>
        </div>
    </div>

    <div class="sfa-kpi-strip">
        <div class="sfa-kpi">
            <div class="sfa-kpi-val">{{ $stats['children_count'] }}</div>
            <div class="sfa-kpi-lbl">Children</div>
        </div>
        <div class="sfa-kpi">
            <div class="sfa-kpi-val" style="{{ $stats['attendance_rate'] >= 80 ? 'color:#34d399' : ($stats['attendance_rate'] >= 60 ? 'color:#fbbf24' : 'color:#f87171') }}">{{ $stats['attendance_rate'] }}%</div>
            <div class="sfa-kpi-lbl">Attendance</div>
        </div>
        <div class="sfa-kpi">
            <div class="sfa-kpi-val" style="{{ $stats['total_balance'] > 0 ? 'color:#f87171' : 'color:#34d399' }}">K{{ number_format($stats['total_balance'], 0) }}</div>
            <div class="sfa-kpi-lbl">Tuition Balance</div>
        </div>
        <div class="sfa-kpi">
            <div class="sfa-kpi-val">{{ $stats['pending_homework'] }}</div>
            <div class="sfa-kpi-lbl">Pending HW</div>
        </div>
        <div class="sfa-kpi">
            <div class="sfa-kpi-val" style="{{ $stats['overdue_homework'] > 0 ? 'color:#f87171' : '' }}">{{ $stats['overdue_homework'] }}</div>
            <div class="sfa-kpi-lbl">Overdue</div>
        </div>
        <div class="sfa-kpi">
            <div class="sfa-kpi-val">{{ $stats['upcoming_events'] }}</div>
            <div class="sfa-kpi-lbl">Events</div>
        </div>
    </div>
</div>

{{-- Per-Child Sections --}}
@if($students->count() > 0)
    @foreach($students as $index => $student)
        @php
            $attendance = $attendanceSummary[$index] ?? null;
            $fees = $feeSummary[$index] ?? null;
            $performance = $academicPerformance[$index] ?? null;
            $childHomework = $homeworkPerChild[$index] ?? null;
        @endphp

        <div class="sfa-fade" style="animation-delay:{{ 0.1 + ($index * 0.08) }}s;margin-top:24px">
            {{-- Child Header --}}
            <div class="sfa-child-header">
                <div style="display:flex;align-items:center;gap:14px">
                    <div class="sfa-child-avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                    <div>
                        <div class="sfa-child-name">{{ $student->name }}</div>
                        <div class="sfa-child-meta">{{ $student->grade?->name ?? 'No Grade' }}@if($student->classSection) &middot; {{ $student->classSection->name }}@endif</div>
                    </div>
                </div>
                <div style="display:flex;gap:20px">
                    @if($attendance && $attendance['rate'] > 0)
                        <div class="sfa-child-stat">
                            <div class="sfa-child-stat-val">{{ $attendance['rate'] }}%</div>
                            <div class="sfa-child-stat-lbl">Attendance</div>
                        </div>
                    @endif
                    @if($performance && $performance['average'])
                        <div class="sfa-child-stat">
                            <div class="sfa-child-stat-val">{{ $performance['average'] }}%</div>
                            <div class="sfa-child-stat-lbl">Average</div>
                        </div>
                    @endif
                    @if($fees)
                        <div class="sfa-child-stat">
                            <div class="sfa-child-stat-val" style="{{ $fees['total_balance'] > 0 ? 'color:#fca5a5' : 'color:#6ee7b7' }}">K{{ number_format($fees['total_balance'], 0) }}</div>
                            <div class="sfa-child-stat-lbl">Balance</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3 Column Body --}}
            <div class="sfa-card" style="border-radius:0 0 14px 14px;border-top:none">
                <div class="sfa-3col">
                    {{-- Attendance --}}
                    <div>
                        <div class="sfa-section-title">
                            <span class="sfa-section-dot" style="background:rgb(var(--green))"></span>
                            Attendance This Term
                        </div>
                        @if($attendance && $attendance['total'] > 0)
                            <div style="margin-bottom:12px">
                                <div style="display:flex;justify-content:space-between;font-size:0.72rem;color:rgb(var(--text-secondary));margin-bottom:4px">
                                    <span>{{ $attendance['present'] + $attendance['late'] }} / {{ $attendance['total'] }} days</span>
                                    <span style="font-weight:700;{{ $attendance['rate'] >= 80 ? 'color:rgb(var(--green))' : 'color:rgb(var(--red))' }}">{{ $attendance['rate'] }}%</span>
                                </div>
                                <div class="sfa-progress">
                                    <div class="sfa-progress-bar" style="width:{{ min($attendance['rate'], 100) }}%;background:{{ $attendance['rate'] >= 80 ? 'rgb(var(--green))' : ($attendance['rate'] >= 60 ? 'rgb(var(--amber))' : 'rgb(var(--red))') }}"></div>
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:14px">
                                <div class="sfa-stat-row"><span class="sfa-stat-dot" style="background:rgb(var(--green))"></span>Present: {{ $attendance['present'] }}</div>
                                <div class="sfa-stat-row"><span class="sfa-stat-dot" style="background:rgb(var(--red))"></span>Absent: {{ $attendance['absent'] }}</div>
                                <div class="sfa-stat-row"><span class="sfa-stat-dot" style="background:rgb(var(--amber))"></span>Late: {{ $attendance['late'] }}</div>
                                <div class="sfa-stat-row"><span class="sfa-stat-dot" style="background:rgb(var(--blue))"></span>Sick: {{ $attendance['sick'] }}</div>
                            </div>
                            @if($attendance['recent']->count() > 0)
                                <div style="font-size:0.68rem;color:rgb(var(--text-tertiary));margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em;font-weight:600">Last 7 Days</div>
                                <div class="sfa-day-grid">
                                    @foreach($attendance['recent']->take(7) as $day)
                                        @php
                                            $cellBg = match($day->status) { 'present'=>'rgb(var(--green))','absent'=>'rgb(var(--red))','late'=>'rgb(var(--amber))','sick'=>'rgb(var(--blue))','excused'=>'#9ca3af',default=>'#d1d5db' };
                                            $symbol = \App\Models\Attendance::getStatusSymbol($day->status);
                                        @endphp
                                        <div class="sfa-day" title="{{ $day->attendance_date->format('D, M j') }}: {{ ucfirst($day->status) }}">
                                            <span class="sfa-day-label">{{ $day->attendance_date->format('D') }}</span>
                                            <span class="sfa-day-cell" style="background:{{ $cellBg }}">{{ $symbol }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div style="font-size:0.82rem;color:rgb(var(--text-tertiary));padding:20px 0;text-align:center">No attendance records this term</div>
                        @endif
                    </div>

                    {{-- Academic Performance --}}
                    <div>
                        <div class="sfa-section-title">
                            <span class="sfa-section-dot" style="background:rgb(var(--purple))"></span>
                            Academic Performance
                        </div>
                        @if($performance && $performance['total_subjects'] > 0)
                            <div style="display:flex;align-items:center;gap:16px;margin-bottom:14px">
                                <div style="text-align:center">
                                    <div style="font-family:'JetBrains Mono',monospace;font-size:1.8rem;font-weight:700;{{ ($performance['average'] ?? 0) >= 50 ? 'color:rgb(var(--green))' : 'color:rgb(var(--red))' }}">{{ $performance['average'] }}%</div>
                                    <div style="font-size:0.65rem;color:rgb(var(--text-tertiary));text-transform:uppercase;letter-spacing:0.04em">Average</div>
                                </div>
                                <div style="flex:1;font-size:0.75rem;color:rgb(var(--text-secondary))">
                                    <div>Highest: <strong>{{ $performance['highest'] }}%</strong></div>
                                    <div>Lowest: <strong>{{ $performance['lowest'] }}%</strong></div>
                                    <div>{{ $performance['total_subjects'] }} subjects</div>
                                </div>
                            </div>
                            @if($performance['grade_distribution']->count() > 0)
                                <div style="font-size:0.68rem;color:rgb(var(--text-tertiary));margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em;font-weight:600">Grades</div>
                                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:14px">
                                    @foreach($performance['grade_distribution'] as $grade => $count)
                                        @php
                                            $gc = match(true) {
                                                in_array($grade, ['A+','A']) => 'sfa-badge-green',
                                                in_array($grade, ['B+','B']) => 'sfa-badge-blue',
                                                in_array($grade, ['C+','C']) => 'sfa-badge-amber',
                                                default => 'sfa-badge-red',
                                            };
                                        @endphp
                                        <span class="sfa-badge {{ $gc }}">{{ $grade }}: {{ $count }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if($performance['top_subjects']->count() > 0)
                                <div style="font-size:0.68rem;color:rgb(var(--text-tertiary));margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em;font-weight:600">Top Subjects</div>
                                @foreach($performance['top_subjects'] as $result)
                                    <div style="display:flex;justify-content:space-between;font-size:0.78rem;padding:4px 0;border-bottom:1px solid rgba(var(--card-border),0.4)">
                                        <span style="color:rgb(var(--text-primary))">{{ $result->subject?->name ?? 'N/A' }}</span>
                                        <span style="font-weight:600;color:rgb(var(--green))">{{ $result->marks }}% ({{ $result->grade }})</span>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            <div style="font-size:0.82rem;color:rgb(var(--text-tertiary));padding:20px 0;text-align:center">No results recorded yet</div>
                        @endif
                    </div>

                    {{-- Fees --}}
                    <div>
                        <div class="sfa-section-title">
                            <span class="sfa-section-dot" style="background:rgb(var(--amber))"></span>
                            Tuition Fees
                        </div>
                        @if($fees)
                            <div style="display:flex;align-items:center;gap:16px;margin-bottom:14px">
                                <div style="text-align:center">
                                    <div style="font-family:'JetBrains Mono',monospace;font-size:1.8rem;font-weight:700;{{ $fees['total_balance'] > 0 ? 'color:rgb(var(--red))' : 'color:rgb(var(--green))' }}">K{{ number_format($fees['total_balance'], 0) }}</div>
                                    <div style="font-size:0.65rem;color:rgb(var(--text-tertiary));text-transform:uppercase;letter-spacing:0.04em">Outstanding</div>
                                </div>
                                <div style="flex:1;font-size:0.75rem;color:rgb(var(--text-secondary))">
                                    <div>Total: <strong>K{{ number_format($fees['total_owed'], 0) }}</strong></div>
                                    <div>Paid: <strong style="color:rgb(var(--green))">K{{ number_format($fees['total_paid'], 0) }}</strong></div>
                                </div>
                            </div>
                            @if($fees['total_owed'] > 0)
                                @php $paidPct = min(round(($fees['total_paid'] / $fees['total_owed']) * 100), 100); @endphp
                                <div style="margin-bottom:14px">
                                    <div style="display:flex;justify-content:space-between;font-size:0.72rem;color:rgb(var(--text-secondary));margin-bottom:4px">
                                        <span>Payment Progress</span>
                                        <span style="font-weight:700">{{ $paidPct }}%</span>
                                    </div>
                                    <div class="sfa-progress">
                                        <div class="sfa-progress-bar" style="width:{{ $paidPct }}%;background:{{ $paidPct >= 100 ? 'rgb(var(--green))' : ($paidPct >= 50 ? 'rgb(var(--blue))' : 'rgb(var(--amber))') }}"></div>
                                    </div>
                                </div>
                            @endif
                            @if($fees['fees']->count() > 0)
                                <div style="font-size:0.68rem;color:rgb(var(--text-tertiary));margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em;font-weight:600">Fee Records</div>
                                @foreach($fees['fees'] as $fee)
                                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.78rem;padding:6px 8px;border-radius:6px;background:rgb(var(--surface));margin-bottom:4px">
                                        <span style="color:rgb(var(--text-primary))">{{ $fee->term?->name ?? '' }} {{ $fee->academicYear?->name ?? '' }}</span>
                                        @if($fee->payment_status === 'paid')
                                            <span class="sfa-badge sfa-badge-green">Paid</span>
                                        @elseif($fee->payment_status === 'partial')
                                            <span class="sfa-badge sfa-badge-amber">K{{ number_format($fee->balance, 0) }} due</span>
                                        @else
                                            <span class="sfa-badge sfa-badge-red">K{{ number_format($fee->balance, 0) }} due</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        @else
                            <div style="font-size:0.82rem;color:rgb(var(--text-tertiary));padding:20px 0;text-align:center">No fee records found</div>
                        @endif
                    </div>
                </div>

                {{-- Homework Strip --}}
                @if($childHomework && $childHomework['homework']->count() > 0)
                    <div style="padding:16px 20px;border-top:1px solid rgb(var(--card-border))">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                            <div class="sfa-section-title" style="margin-bottom:0">
                                <span class="sfa-section-dot" style="background:rgb(var(--amber))"></span>
                                Recent Homework
                                <span style="font-weight:400;color:rgb(var(--text-tertiary));font-size:0.72rem">({{ $childHomework['total_submitted'] }}/{{ $childHomework['total_assigned'] }} submitted)</span>
                            </div>
                            <a href="{{ route('filament.admin.resources.homework.index') }}" class="sfa-card-link">View All</a>
                        </div>
                        <div class="sfa-hw-grid">
                            @foreach($childHomework['homework'] as $hw)
                                @php
                                    $homework = $hw['homework'];
                                    $isOverdue = $homework->due_date->isPast() && !$hw['submitted'];
                                    $isDueSoon = !$homework->due_date->isPast() && $homework->due_date->diffInDays(now()) <= 2;
                                    $borderStyle = $hw['submitted'] ? 'border-color:rgb(var(--green))' : ($isOverdue ? 'border-color:rgb(var(--red))' : ($isDueSoon ? 'border-color:rgb(var(--amber))' : ''));
                                @endphp
                                <div class="sfa-hw-card" style="{{ $borderStyle }}">
                                    <div class="sfa-hw-title" title="{{ $homework->title }}">{{ $homework->title }}</div>
                                    <div class="sfa-hw-sub">{{ $homework->subject?->name }} &middot; Due {{ $homework->due_date->format('M j') }}</div>
                                    <div style="margin-top:8px;display:flex;align-items:center;justify-content:space-between">
                                        @if($hw['submitted'])
                                            <span class="sfa-badge sfa-badge-green">Submitted @if($hw['marks'] !== null)&middot; {{ $hw['marks'] }}@endif</span>
                                        @elseif($isOverdue)
                                            <span class="sfa-badge sfa-badge-red">Overdue</span>
                                        @elseif($isDueSoon)
                                            <span class="sfa-badge sfa-badge-amber">Due Soon</span>
                                        @else
                                            <span class="sfa-badge sfa-badge-blue">Pending</span>
                                        @endif
                                        @if($homework->homework_file)
                                            <a href="{{ route('homework.download', $homework) }}" style="color:rgb(var(--blue))" title="Download">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@endif

{{-- Report Cards Section --}}
@if($reportCards->where('has_any', true)->isNotEmpty() || $reportCards->isNotEmpty())
<div class="sfa-fade" style="animation-delay:0.25s;margin-top:24px">
    <div class="sfa-card">
        <div class="sfa-card-head">
            <div class="sfa-card-title">
                <span class="sfa-card-dot" style="background:rgb(var(--navy))"></span>
                Report Cards
            </div>
            <a href="{{ route('filament.admin.resources.results.index') }}" class="sfa-card-link">View Results</a>
        </div>
        <div style="padding:16px 20px">
            @foreach($reportCards as $rc)
                @php $rcStudent = $rc['student']; @endphp
                <div style="margin-bottom:16px">
                    @if($reportCards->count() > 1)
                        <div style="font-size:0.82rem;font-weight:600;color:rgb(var(--text-primary));margin-bottom:8px">{{ $rcStudent->name }} &mdash; {{ $rcStudent->grade?->name }}</div>
                    @endif
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px">
                        @foreach($rc['terms'] as $termData)
                            @php $term = $termData['term']; @endphp
                            <div style="padding:12px 14px;border-radius:8px;border:1px solid rgb(var(--card-border));background:rgb(var(--surface));display:flex;align-items:center;justify-content:space-between">
                                <div>
                                    <div style="font-size:0.82rem;font-weight:600;color:rgb(var(--text-primary))">{{ $term->name }}</div>
                                    @if($termData['is_generated'])
                                        <div style="font-size:0.68rem;color:rgb(var(--green));margin-top:2px">Ready to download</div>
                                    @elseif($termData['has_results'])
                                        <div style="font-size:0.68rem;color:rgb(var(--text-tertiary));margin-top:2px">Results entered &mdash; pending</div>
                                    @else
                                        <div style="font-size:0.68rem;color:rgb(var(--text-tertiary));margin-top:2px">Not yet available</div>
                                    @endif
                                </div>
                                <div style="display:flex;gap:6px">
                                    @if($termData['is_generated'])
                                        <a href="{{ $termData['preview_url'] }}" target="_blank"
                                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;font-size:0.7rem;font-weight:600;background:rgba(var(--blue),0.1);color:rgb(var(--blue));text-decoration:none;white-space:nowrap">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            View
                                        </a>
                                        <a href="{{ $termData['download_url'] }}"
                                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;font-size:0.7rem;font-weight:600;background:rgba(var(--green),0.1);color:rgb(var(--green));text-decoration:none;white-space:nowrap">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            PDF
                                        </a>
                                    @else
                                        <span class="sfa-badge" style="background:rgba(var(--card-border),0.5);color:rgb(var(--text-tertiary))">
                                            @if($termData['has_results']) Pending @else N/A @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Bottom: Payments + Events --}}
<div class="sfa-bottom-grid sfa-fade" style="animation-delay:0.3s">
    {{-- Recent Payments --}}
    <div class="sfa-card">
        <div class="sfa-card-head">
            <div class="sfa-card-title"><span class="sfa-card-dot" style="background:rgb(var(--green))"></span>Recent Payments</div>
            <a href="{{ route('filament.admin.resources.payment-transactions.index') }}" class="sfa-card-link">View All</a>
        </div>
        @if($feePayments->count() > 0)
            @foreach($feePayments as $payment)
                <div class="sfa-payment-row">
                    <div class="sfa-payment-icon">
                        <svg width="16" height="16" fill="none" stroke="rgb(var(--green))" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:0.85rem;font-weight:600;color:rgb(var(--text-primary))">{{ $payment->studentFee?->student?->name ?? 'N/A' }}</div>
                        <div style="font-size:0.72rem;color:rgb(var(--text-secondary))">{{ $payment->transaction_date?->format('M j, Y') ?? '' }}@if($payment->reference_number) &middot; {{ $payment->reference_number }}@endif</div>
                    </div>
                    <div>
                        <div class="sfa-payment-amount">K{{ number_format($payment->amount, 2) }}</div>
                        <div class="sfa-payment-method">{{ ucfirst(str_replace('_',' ',$payment->payment_method ?? '')) }}</div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="sfa-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                <div style="font-size:0.85rem;font-weight:500;color:rgb(var(--text-secondary))">No recent payments</div>
            </div>
        @endif
    </div>

    {{-- Upcoming Events --}}
    <div class="sfa-card">
        <div class="sfa-card-head">
            <div class="sfa-card-title"><span class="sfa-card-dot" style="background:rgb(var(--purple))"></span>Upcoming Events</div>
        </div>
        @if($upcomingEvents->count() > 0)
            @foreach($upcomingEvents as $event)
                <div class="sfa-event-row">
                    <div class="sfa-event-date">
                        <div class="sfa-event-day">{{ $event->start_date->format('d') }}</div>
                        <div class="sfa-event-month">{{ $event->start_date->format('M') }}</div>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div class="sfa-event-title">{{ $event->title }}</div>
                        <div class="sfa-event-meta">{{ $event->start_date->format('l, g:i A') }} &middot; {{ $event->start_date->diffForHumans() }}</div>
                        @if($event->description)<div style="font-size:0.72rem;color:rgb(var(--text-tertiary));margin-top:3px">{{ Str::limit($event->description, 80) }}</div>@endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="sfa-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                <div style="font-size:0.85rem;font-weight:500;color:rgb(var(--text-secondary))">No upcoming events</div>
            </div>
        @endif
    </div>
</div>

{{-- No Children Warning --}}
@if($stats['children_count'] === 0)
    <div class="sfa-card sfa-fade" style="margin-top:24px;animation-delay:0.2s">
        <div style="padding:32px;display:flex;align-items:flex-start;gap:16px;background:rgba(var(--amber),0.05)">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(var(--amber),0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="20" height="20" fill="none" stroke="rgb(var(--amber))" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <div>
                <div style="font-size:0.92rem;font-weight:600;color:rgb(var(--text-primary))">No Children Enrolled</div>
                <div style="font-size:0.82rem;color:rgb(var(--text-secondary));margin-top:4px">It looks like you don't have any children enrolled at the school yet. Please contact the school administration to complete the enrollment process.</div>
            </div>
        </div>
    </div>
@endif

</div>{{-- /.sfa-pd --}}
</x-filament-panels::page>
