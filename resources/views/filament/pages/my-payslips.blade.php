<x-filament-panels::page>
{{-- ============================================================
     St. Francis of Assisi — My Payslips
     Corporate Design System · DM Sans · Navy/Red
     ============================================================ --}}

<div class="sfa-dash">

{{-- ── Scoped Styles ── --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=JetBrains+Mono:wght@500;600;700&display=swap');

    .sfa-dash {
        --navy: 30,58,95;
        --red: 220,38,38;
        --green: 5,150,105;
        --amber: 217,119,6;
        --blue: 59,130,246;
        --purple: 124,58,237;
        --card-bg: 255,255,255;
        --card-border: 229,231,235;
        --text-primary: 17,24,39;
        --text-secondary: 107,114,128;
        --text-tertiary: 156,163,175;
        --surface: 249,250,251;
        font-family: 'DM Sans', sans-serif;
    }
    .dark .sfa-dash {
        --card-bg: 31,41,55;
        --card-border: 55,65,81;
        --text-primary: 243,244,246;
        --text-secondary: 156,163,175;
        --text-tertiary: 107,114,128;
        --surface: 17,24,39;
    }

    /* Header */
    .sfa-header {
        background: linear-gradient(135deg, rgb(var(--navy)) 0%, #2c5282 100%);
        border-radius: 16px;
        padding: 20px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        color: #fff;
    }
    .sfa-header-greeting { font-size: 1.35rem; font-weight: 700; }
    .sfa-header-sub { font-size: 0.82rem; opacity: 0.75; margin-top: 2px; }
    .sfa-header-badge {
        background: rgba(255,255,255,0.15);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }

    /* KPI Strip */
    .sfa-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-top: 20px;
    }
    @media (max-width: 768px) { .sfa-kpi-grid { grid-template-columns: repeat(2, 1fr); } }

    .sfa-kpi {
        background: rgb(var(--card-bg));
        border: 1px solid rgb(var(--card-border));
        border-radius: 12px;
        padding: 18px 20px;
        border-left: 4px solid var(--kpi-color, rgb(var(--navy)));
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .sfa-kpi:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-1px); }
    .sfa-kpi-label { font-size: 0.75rem; font-weight: 500; color: rgb(var(--text-secondary)); text-transform: uppercase; letter-spacing: 0.04em; }
    .sfa-kpi-value { font-size: 1.75rem; font-weight: 700; color: rgb(var(--text-primary)); margin-top: 4px; font-family: 'JetBrains Mono', monospace; }
    .sfa-kpi-sub { font-size: 0.7rem; color: rgb(var(--text-tertiary)); margin-top: 2px; }

    /* Card */
    .sfa-card {
        background: rgb(var(--card-bg));
        border: 1px solid rgb(var(--card-border));
        border-radius: 14px;
        overflow: hidden;
    }
    .sfa-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid rgb(var(--card-border));
    }
    .sfa-card-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: rgb(var(--text-primary));
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sfa-card-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .sfa-card-body { padding: 0; }

    /* Payslip Row */
    .sfa-payslip-row {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(var(--card-border), 0.5);
        gap: 16px;
        transition: background 0.15s;
    }
    .sfa-payslip-row:last-child { border-bottom: none; }
    .sfa-payslip-row:hover { background: rgba(var(--surface), 0.5); }

    .sfa-month-badge {
        min-width: 56px;
        text-align: center;
        background: rgba(var(--navy), 0.08);
        border-radius: 10px;
        padding: 8px 6px;
        flex-shrink: 0;
    }
    .dark .sfa-month-badge { background: rgba(var(--navy), 0.3); }
    .sfa-month-badge-month { font-size: 0.85rem; font-weight: 700; color: rgb(var(--navy)); }
    .dark .sfa-month-badge-month { color: rgb(var(--blue)); }
    .sfa-month-badge-year { font-size: 0.65rem; font-weight: 500; color: rgb(var(--text-secondary)); }

    .sfa-payslip-info { flex: 1; min-width: 0; }
    .sfa-payslip-title { font-size: 0.88rem; font-weight: 600; color: rgb(var(--text-primary)); }
    .sfa-payslip-sub { font-size: 0.75rem; color: rgb(var(--text-secondary)); margin-top: 2px; }

    .sfa-payslip-amount {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1rem;
        font-weight: 700;
        color: rgb(var(--green));
        text-align: right;
        min-width: 120px;
        flex-shrink: 0;
    }
    .sfa-payslip-amount-label { font-size: 0.65rem; color: rgb(var(--text-tertiary)); font-family: 'DM Sans', sans-serif; font-weight: 500; }

    /* Badges */
    .sfa-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .sfa-badge-green { background: rgba(5,150,105,0.1); color: #059669; }
    .sfa-badge-yellow { background: rgba(217,119,6,0.1); color: #d97706; }
    .dark .sfa-badge-green { background: rgba(5,150,105,0.2); color: #34d399; }
    .dark .sfa-badge-yellow { background: rgba(217,119,6,0.2); color: #fbbf24; }

    /* Action Buttons */
    .sfa-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .sfa-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    .sfa-action-btn svg { width: 14px; height: 14px; }
    .sfa-action-btn-view {
        background: rgba(var(--navy), 0.08);
        color: rgb(var(--navy));
    }
    .sfa-action-btn-view:hover { background: rgba(var(--navy), 0.15); }
    .dark .sfa-action-btn-view { background: rgba(var(--blue), 0.15); color: rgb(var(--blue)); }
    .sfa-action-btn-download {
        background: rgba(var(--green), 0.1);
        color: rgb(var(--green));
    }
    .sfa-action-btn-download:hover { background: rgba(var(--green), 0.18); }
    .dark .sfa-action-btn-download { background: rgba(var(--green), 0.2); color: #34d399; }

    /* Empty State */
    .sfa-empty {
        padding: 48px 24px;
        text-align: center;
        color: rgb(var(--text-tertiary));
    }
    .sfa-empty svg { width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.4; }
    .sfa-empty-title { font-size: 1rem; font-weight: 600; color: rgb(var(--text-secondary)); margin-bottom: 4px; }
    .sfa-empty-sub { font-size: 0.82rem; }

    /* Animation */
    .sfa-fade { opacity: 0; transform: translateY(12px); animation: sfaFadeUp 0.4s ease forwards; }
    @keyframes sfaFadeUp { to { opacity: 1; transform: translateY(0); } }
</style>

@php
    $payslips = $this->getPayslips();
    $employee = $this->getEmployee();
    $totalPaid = $payslips->where('payment_status', 'paid')->sum('net_salary');
    $totalPending = $payslips->where('payment_status', '!=', 'paid')->sum('net_salary');
    $latestPayslip = $payslips->first();
@endphp

{{-- ── Header Bar ── --}}
<div class="sfa-header sfa-fade" style="animation-delay:0.05s">
    <div>
        <div class="sfa-header-greeting">My Payslips</div>
        <div class="sfa-header-sub">
            @if($employee)
                {{ $employee->name }} &middot; {{ $employee->position ?? 'Staff' }} &middot; {{ ucfirst(str_replace('_', ' ', $employee->department ?? '')) }}
            @else
                {{ auth()->user()->name }}
            @endif
        </div>
    </div>
    <div>
        <span class="sfa-header-badge">{{ $payslips->count() }} Payslip{{ $payslips->count() !== 1 ? 's' : '' }}</span>
    </div>
</div>

{{-- ── KPI Strip ── --}}
<div class="sfa-kpi-grid sfa-fade" style="animation-delay:0.1s">
    <div class="sfa-kpi" style="--kpi-color: rgb(var(--blue))">
        <div class="sfa-kpi-label">Total Payslips</div>
        <div class="sfa-kpi-value">{{ $payslips->count() }}</div>
    </div>
    <div class="sfa-kpi" style="--kpi-color: rgb(var(--green))">
        <div class="sfa-kpi-label">Total Paid</div>
        <div class="sfa-kpi-value">{{ number_format($totalPaid, 0) }}</div>
        <div class="sfa-kpi-sub">ZMW</div>
    </div>
    <div class="sfa-kpi" style="--kpi-color: rgb(var(--amber))">
        <div class="sfa-kpi-label">Pending</div>
        <div class="sfa-kpi-value">{{ number_format($totalPending, 0) }}</div>
        <div class="sfa-kpi-sub">ZMW</div>
    </div>
    <div class="sfa-kpi" style="--kpi-color: rgb(var(--navy))">
        <div class="sfa-kpi-label">Latest Net Pay</div>
        <div class="sfa-kpi-value">{{ $latestPayslip ? number_format($latestPayslip->net_salary, 0) : '0' }}</div>
        <div class="sfa-kpi-sub">{{ $latestPayslip ? $latestPayslip->month . ' ' . $latestPayslip->year : 'N/A' }}</div>
    </div>
</div>

{{-- ── Payslips List ── --}}
<div class="sfa-card sfa-fade" style="margin-top:20px;animation-delay:0.15s">
    <div class="sfa-card-head">
        <div class="sfa-card-title">
            <span class="sfa-card-dot" style="background:rgb(var(--green))"></span>
            Salary History
        </div>
    </div>
    <div class="sfa-card-body">
        @forelse($payslips as $payslip)
            <div class="sfa-payslip-row">
                <div class="sfa-month-badge">
                    <div class="sfa-month-badge-month">{{ substr($payslip->month, 0, 3) }}</div>
                    <div class="sfa-month-badge-year">{{ $payslip->year }}</div>
                </div>

                <div class="sfa-payslip-info">
                    <div class="sfa-payslip-title">{{ $payslip->month }} {{ $payslip->year }}</div>
                    <div class="sfa-payslip-sub">
                        Gross: ZMW {{ number_format($payslip->gross_salary, 2) }}
                        &middot; Deductions: ZMW {{ number_format(collect($payslip->deductions ?? [])->sum('amount'), 2) }}
                        @if($payslip->academicYear)
                            &middot; AY {{ $payslip->academicYear->name }}
                        @endif
                    </div>
                </div>

                <div class="sfa-payslip-amount">
                    <div class="sfa-payslip-amount-label">Net Pay</div>
                    ZMW {{ number_format($payslip->net_salary, 2) }}
                </div>

                <span class="sfa-badge {{ $payslip->payment_status === 'paid' ? 'sfa-badge-green' : 'sfa-badge-yellow' }}">
                    {{ ucfirst($payslip->payment_status) }}
                </span>

                <div class="sfa-actions">
                    <a href="{{ route('payslips.view', $payslip) }}" target="_blank" class="sfa-action-btn sfa-action-btn-view">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        View
                    </a>
                    <a href="{{ route('payslips.download', $payslip) }}" class="sfa-action-btn sfa-action-btn-download">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        PDF
                    </a>
                </div>
            </div>
        @empty
            <div class="sfa-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                <div class="sfa-empty-title">No Payslips Yet</div>
                <div class="sfa-empty-sub">Your payslips will appear here once they are generated by the administration.</div>
            </div>
        @endforelse
    </div>
</div>

</div>{{-- /.sfa-dash --}}
</x-filament-panels::page>
