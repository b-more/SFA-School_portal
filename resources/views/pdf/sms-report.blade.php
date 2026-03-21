<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SMS Report - {{ $schoolName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.4;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .logo-section {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
        }
        .logo-section img {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }
        .title-section {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .header h1 {
            color: #2563eb;
            font-size: 18px;
            margin-bottom: 3px;
        }
        .header h2 {
            color: #1e40af;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 9px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-card {
            display: table-cell;
            width: 20%;
            padding: 10px;
            text-align: center;
            border: 2px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .summary-card .label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .summary-card .value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        .summary-card .value.success { color: #059669; }
        .summary-card .value.danger { color: #dc2626; }
        .summary-card .value.primary { color: #2563eb; }
        .summary-card .value.warning { color: #d97706; }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8px;
        }
        table.data-table thead th {
            background-color: #2563eb;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.data-table tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-sent { background-color: #dbeafe; color: #1d4ed8; }
        .status-delivered { background-color: #d1fae5; color: #065f46; }
        .status-failed { background-color: #fee2e2; color: #991b1b; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            background-color: #f3f4f6;
            color: #374151;
        }
        .message-cell {
            max-width: 250px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            @if($schoolLogo && file_exists($schoolLogo))
                <div class="logo-section">
                    <img src="{{ $schoolLogo }}" alt="School Logo">
                </div>
            @endif
            <div class="title-section">
                <h1>{{ $schoolName }}</h1>
                <h2>SMS Report</h2>
                <p>Period: {{ $startDate }} &mdash; {{ $endDate }} | Generated: {{ $reportDate }}</p>
            </div>
            @if($schoolLogo && file_exists($schoolLogo))
                <div class="logo-section"></div>
            @endif
        </div>
    </div>

    @php
        $typeLabels = [
            'homework_notification' => 'Homework',
            'result_notification' => 'Results',
            'fee_reminder' => 'Fee Reminder',
            'event_notification' => 'Event',
            'general' => 'General',
            'broadcast' => 'Broadcast',
            'student_credentials' => 'Student Credentials',
            'staff_credentials' => 'Staff Credentials',
            'leave_notification' => 'Leave',
            'other' => 'Other',
        ];
    @endphp

    {{-- Summary Statistics --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total SMS</div>
            <div class="value primary">{{ number_format($summary['total']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Sent / Delivered</div>
            <div class="value success">{{ number_format($summary['sent']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Failed</div>
            <div class="value danger">{{ number_format($summary['failed']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Pending</div>
            <div class="value warning">{{ number_format($summary['pending']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Cost</div>
            <div class="value">ZMW {{ number_format($summary['total_cost'], 2) }}</div>
        </div>
    </div>

    {{-- Breakdown by Type --}}
    @if(count($typeBreakdown) > 0)
    <h3 style="font-size: 11px; color: #1f2937; margin-bottom: 8px;">Breakdown by Message Type</h3>
    <table class="data-table" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th>Message Type</th>
                <th style="text-align: center;">Count</th>
                <th style="text-align: center;">Sent</th>
                <th style="text-align: center;">Failed</th>
                <th style="text-align: right;">Cost (ZMW)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($typeBreakdown as $type)
            <tr>
                <td><span class="type-badge">{{ $typeLabels[$type->message_type] ?? ucwords(str_replace('_', ' ', $type->message_type)) }}</span></td>
                <td style="text-align: center;">{{ $type->count }}</td>
                <td style="text-align: center;">{{ $type->sent_count }}</td>
                <td style="text-align: center;">{{ $type->failed_count }}</td>
                <td style="text-align: right;">{{ number_format($type->total_cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- SMS Log Details --}}
    <h3 style="font-size: 11px; color: #1f2937; margin-bottom: 8px;">SMS Log Details ({{ number_format($logs->count()) }} records)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Date & Time</th>
                <th style="width: 12%;">Recipient</th>
                <th style="width: 36%;">Message</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 8%; text-align: right;">Cost</th>
                <th style="width: 14%;">Sent By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                <td>{{ $log->recipient }}</td>
                <td class="message-cell">{{ \Illuminate\Support\Str::limit($log->message, 80) }}</td>
                <td><span class="type-badge">{{ $typeLabels[$log->message_type] ?? ucwords(str_replace('_', ' ', $log->message_type ?? 'N/A')) }}</span></td>
                <td>
                    <span class="status-badge status-{{ $log->status }}">
                        {{ ucfirst($log->status) }}
                    </span>
                </td>
                <td style="text-align: right;">{{ $log->cost ? number_format($log->cost, 2) : '-' }}</td>
                <td>{{ $log->sender?->name ?? 'System' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #9ca3af;">No SMS records found for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{ $schoolName }} &bull; SMS Report &bull; Generated on {{ $reportDate }}
    </div>
</body>
</html>
