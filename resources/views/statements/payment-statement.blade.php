<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Statement - {{ $student->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        .page { max-width: 100%; margin: 0 auto; }

        /* Navy Header Band */
        .header-band {
            background: #1e3a5f;
            color: #fff;
            padding: 24px 32px;
            display: table;
            width: 100%;
        }
        .header-left { display: table-cell; vertical-align: middle; width: 70%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }
        .school-name { font-size: 18px; font-weight: 700; letter-spacing: 0.5px; }
        .school-motto { font-size: 10px; opacity: 0.7; margin-top: 2px; letter-spacing: 0.3px; }
        .school-contact { font-size: 9px; opacity: 0.6; margin-top: 6px; line-height: 1.5; }
        .header-logo { width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.3); text-align: center; line-height: 52px; font-size: 20px; font-weight: 700; display: inline-block; }
        .header-logo img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; }

        /* Red accent line */
        .accent-line { height: 4px; background: linear-gradient(90deg, #dc2626 0%, #dc2626 30%, #1e3a5f 100%); }

        /* Statement Title Bar */
        .title-bar {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 32px;
            display: table;
            width: 100%;
        }
        .title-bar-left { display: table-cell; vertical-align: middle; }
        .title-bar-right { display: table-cell; vertical-align: middle; text-align: right; }
        .title-text { font-size: 16px; font-weight: 700; color: #1e3a5f; letter-spacing: 1px; text-transform: uppercase; }
        .title-sub { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .stmt-number { font-size: 10px; color: #6b7280; }
        .stmt-number strong { color: #1e3a5f; font-size: 12px; }

        /* Info Section */
        .info-section { padding: 20px 32px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { vertical-align: top; padding-bottom: 16px; }
        .info-col { width: 33.33%; }
        .info-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.8px; color: #9ca3af; font-weight: 700; margin-bottom: 4px; }
        .info-heading { font-size: 10px; font-weight: 700; color: #1e3a5f; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 2px solid #dc2626; display: inline-block; }
        .info-row { font-size: 10.5px; line-height: 1.7; color: #374151; }
        .info-row strong { color: #111827; }

        /* Summary Cards */
        .summary-section { padding: 0 32px 20px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-cell { text-align: center; padding: 14px 10px; border: 1px solid #e5e7eb; }
        .summary-cell-navy { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }
        .summary-cell-light { background: #f8fafc; }
        .summary-val { font-size: 16px; font-weight: 700; }
        .summary-val-green { color: #059669; }
        .summary-val-red { color: #dc2626; }
        .summary-lbl { font-size: 8px; text-transform: uppercase; letter-spacing: 0.6px; color: #6b7280; margin-top: 3px; }
        .summary-lbl-white { color: rgba(255,255,255,0.7); }

        /* Payment Table */
        .table-section { padding: 0 32px 20px; }
        .section-heading { font-size: 11px; font-weight: 700; color: #1e3a5f; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; padding-bottom: 4px; border-bottom: 2px solid #e5e7eb; }

        table.fees { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        table.fees thead th {
            background: #1e3a5f; color: #fff; padding: 8px 10px; text-align: left;
            font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px;
        }
        table.fees thead th:first-child { border-radius: 4px 0 0 0; }
        table.fees thead th:last-child { border-radius: 0 4px 0 0; }
        table.fees tbody td { padding: 9px 10px; border-bottom: 1px solid #f3f4f6; }
        table.fees tbody tr:nth-child(even) { background: #fafbfc; }
        table.fees tbody tr:last-child td { border-bottom: 2px solid #1e3a5f; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-gray { color: #9ca3af; }
        .text-navy { color: #1e3a5f; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-unpaid { background: #fee2e2; color: #991b1b; }
        .badge-overpaid { background: #dbeafe; color: #1e40af; }

        .progress { width: 60px; height: 5px; background: #e5e7eb; border-radius: 3px; display: inline-block; vertical-align: middle; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 3px; }
        .progress-green { background: #059669; }
        .progress-amber { background: #d97706; }
        .progress-red { background: #dc2626; }

        /* Balance Box */
        .balance-section { padding: 0 32px 20px; }
        .balance-box { display: table; width: 100%; border: 2px solid #e5e7eb; border-radius: 6px; overflow: hidden; }
        .balance-left { display: table-cell; width: 65%; padding: 14px 18px; vertical-align: middle; background: #f8fafc; }
        .balance-right { display: table-cell; width: 35%; padding: 14px 18px; vertical-align: middle; text-align: center; }
        .balance-right-due { background: #fef2f2; border-left: 3px solid #dc2626; }
        .balance-right-clear { background: #ecfdf5; border-left: 3px solid #059669; }
        .balance-amount { font-size: 20px; font-weight: 700; }

        /* Footer */
        .footer-band { background: #1e3a5f; color: rgba(255,255,255,0.8); padding: 16px 32px; font-size: 9px; line-height: 1.6; }
        .footer-band strong { color: #fff; }
        .footer-divider { height: 1px; background: rgba(255,255,255,0.15); margin: 8px 0; }
        .footer-bottom { display: table; width: 100%; }
        .footer-bottom-left { display: table-cell; vertical-align: middle; }
        .footer-bottom-right { display: table-cell; vertical-align: middle; text-align: right; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { border: none; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header-band">
        <div class="header-left">
            <div class="school-name">{{ $school_info['name'] }}</div>
            <div class="school-motto">Excellence in Education</div>
            <div class="school-contact">
                {{ $school_info['address'] }}<br>
                Tel: {{ $school_info['phone'] }} | {{ $school_info['email'] }}
            </div>
        </div>
        <div class="header-right">
            @if(isset($school_info['logo_path']) && file_exists($school_info['logo_path']))
                <div class="header-logo"><img src="{{ $school_info['logo_path'] }}" alt="Logo"></div>
            @else
                <div class="header-logo">SF</div>
            @endif
        </div>
    </div>
    <div class="accent-line"></div>

    {{-- Title Bar --}}
    <div class="title-bar">
        <div class="title-bar-left">
            <div class="title-text">Fee Statement</div>
            <div class="title-sub">{{ $period_description }} &mdash; Generated {{ $generated_at->format('d M Y') }}</div>
        </div>
        <div class="title-bar-right">
            <div class="stmt-number">Statement No.</div>
            <div class="stmt-number"><strong>#{{ $statement_number }}</strong></div>
        </div>
    </div>

    {{-- Student / Parent / Statement Info --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-col">
                    <div class="info-heading">Student Information</div>
                    <div class="info-row">
                        <strong>{{ $student->name }}</strong><br>
                        ID: {{ $student->student_id_number ?? '—' }}<br>
                        Grade: {{ $student->grade->name ?? '—' }}
                        @if($student->classSection) &mdash; {{ $student->classSection->name }}@endif
                    </div>
                </td>
                <td class="info-col">
                    @if($parent_guardian)
                        <div class="info-heading">Parent / Guardian</div>
                        <div class="info-row">
                            <strong>{{ $parent_guardian->name }}</strong><br>
                            Phone: {{ $parent_guardian->phone ?? '—' }}<br>
                            @if($parent_guardian->email)Email: {{ $parent_guardian->email }}<br>@endif
                            Relationship: {{ ucfirst($parent_guardian->relationship ?? 'Guardian') }}
                        </div>
                    @endif
                </td>
                <td class="info-col">
                    <div class="info-heading">Statement Details</div>
                    <div class="info-row">
                        Period: <strong>{{ $period_description }}</strong><br>
                        @if($current_term)Current Term: {{ $current_term->name }}<br>@endif
                        @if($current_academic_year)Academic Year: {{ $current_academic_year->name }}<br>@endif
                        Date: {{ $generated_at->format('d M Y, h:i A') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Summary Strip --}}
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="summary-cell summary-cell-light">
                    <div class="summary-val text-navy">K {{ number_format($summary['total_fees_charged'], 2) }}</div>
                    <div class="summary-lbl">Tuition Fees</div>
                </td>
                <td class="summary-cell summary-cell-light">
                    <div class="summary-val summary-val-green">K {{ number_format($summary['total_payments_made'], 2) }}</div>
                    <div class="summary-lbl">Total Paid</div>
                </td>
                <td class="summary-cell summary-cell-light">
                    <div class="summary-val {{ $summary['total_outstanding'] > 0 ? 'summary-val-red' : 'summary-val-green' }}">K {{ number_format($summary['total_outstanding'], 2) }}</div>
                    <div class="summary-lbl">Outstanding</div>
                </td>
                <td class="summary-cell summary-cell-navy">
                    <div class="summary-val" style="font-size:22px">{{ number_format($summary['collection_rate'], 1) }}%</div>
                    <div class="summary-lbl summary-lbl-white">Paid</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Payment History Table --}}
    <div class="table-section">
        <div class="section-heading">Payment History</div>

        @if(empty($payment_history))
            <div style="text-align:center;padding:30px;color:#9ca3af;background:#f9fafb;border:1px dashed #d1d5db;border-radius:6px">
                <div style="font-size:13px;font-weight:600;color:#6b7280;margin-bottom:4px">No payment records found</div>
                <div style="font-size:10px">Records will appear here once fees are assigned and payments are recorded.</div>
            </div>
        @else
            <table class="fees">
                <thead>
                    <tr>
                        <th>Term</th>
                        <th>Year</th>
                        <th class="text-right">Tuition Fee</th>
                        <th class="text-right">Paid</th>
                        <th class="text-right">Balance</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Progress</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payment_history as $record)
                        @php
                            $pct = $record['total_fee'] > 0 ? min(100, ($record['amount_paid'] / $record['total_fee']) * 100) : 0;
                            $barClass = $pct >= 100 ? 'progress-green' : ($pct >= 50 ? 'progress-amber' : 'progress-red');
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $record['term'] }}</td>
                            <td>{{ $record['academic_year'] }}</td>
                            <td class="text-right fw-bold">K {{ number_format($record['total_fee'], 2) }}</td>
                            <td class="text-right text-green fw-bold">K {{ number_format($record['amount_paid'], 2) }}</td>
                            <td class="text-right {{ $record['balance'] > 0 ? 'text-red' : 'text-green' }} fw-bold">K {{ number_format($record['balance'], 2) }}</td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($record['payment_status']) {
                                        'paid' => 'badge-paid',
                                        'partial' => 'badge-partial',
                                        'overpaid' => 'badge-overpaid',
                                        default => 'badge-unpaid',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($record['payment_status']) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="progress"><div class="progress-fill {{ $barClass }}" style="width:{{ $pct }}%"></div></div>
                                <span style="font-size:8px;color:#6b7280">{{ number_format($pct, 0) }}%</span>
                            </td>
                            <td>
                                @if($record['payment_date'])
                                    {{ \Carbon\Carbon::parse($record['payment_date'])->format('d M Y') }}
                                @else
                                    <span class="text-gray">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Balance Status Box --}}
    <div class="balance-section">
        <div class="balance-box">
            <div class="balance-left">
                <div style="font-size:10px;color:#6b7280;margin-bottom:4px">Account Summary</div>
                <div style="font-size:10px;line-height:1.8;color:#374151">
                    Total fees charged across <strong>{{ $summary['number_of_terms'] }} term(s)</strong><br>
                    Collection rate: <strong>{{ number_format($summary['collection_rate'], 1) }}%</strong><br>
                    @if($summary['total_outstanding'] > 0)
                        <span style="color:#dc2626">Immediate payment of the outstanding balance is requested.</span>
                    @else
                        <span style="color:#059669">All fee obligations have been met. Thank you.</span>
                    @endif
                </div>
            </div>
            <div class="balance-right {{ $summary['total_outstanding'] > 0 ? 'balance-right-due' : 'balance-right-clear' }}">
                @if($summary['total_outstanding'] > 0)
                    <div style="font-size:8px;text-transform:uppercase;letter-spacing:0.5px;color:#991b1b;font-weight:700;margin-bottom:4px">Amount Due</div>
                    <div class="balance-amount text-red">K {{ number_format($summary['total_outstanding'], 2) }}</div>
                @else
                    <div style="font-size:8px;text-transform:uppercase;letter-spacing:0.5px;color:#065f46;font-weight:700;margin-bottom:4px">Account Status</div>
                    <div class="balance-amount text-green">PAID UP</div>
                    <div style="font-size:9px;color:#059669;margin-top:2px">&#10003; No Balance</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer-band">
        <strong>Official Fee Statement</strong> &mdash; This document is an official record of the student's fee account with {{ $school_info['name'] }}.
        Please retain for your records.
        <div class="footer-divider"></div>
        <div class="footer-bottom">
            <div class="footer-bottom-left">
                {{ $school_info['address'] }}<br>
                Tel: {{ $school_info['phone'] }} | Email: {{ $school_info['email'] }}
            </div>
            <div class="footer-bottom-right">
                Statement #{{ $statement_number }}<br>
                {{ $generated_at->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>

</div>
</body>
</html>
