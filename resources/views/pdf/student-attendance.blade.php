<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report - {{ $student->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }

        .header { text-align: center; border-bottom: 3px solid #1e3a5f; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #1e3a5f; margin-bottom: 2px; }
        .header h2 { font-size: 13px; color: #666; font-weight: normal; margin-bottom: 4px; }
        .header .subtitle { font-size: 11px; color: #888; }
        .logo { width: 60px; height: 60px; margin-bottom: 5px; }

        .student-info { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 12px; margin-bottom: 20px; }
        .student-info table { width: 100%; }
        .student-info td { padding: 3px 8px; font-size: 11px; }
        .student-info .label { color: #666; font-weight: normal; width: 120px; }
        .student-info .value { font-weight: bold; color: #333; }

        .stats-row { margin-bottom: 20px; }
        .stats-row table { width: 100%; border-collapse: collapse; }
        .stats-row td { text-align: center; padding: 10px 5px; border: 1px solid #dee2e6; }
        .stats-row .stat-value { font-size: 20px; font-weight: bold; display: block; }
        .stats-row .stat-label { font-size: 9px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-present { color: #059669; background: #ecfdf5; }
        .stat-absent { color: #dc2626; background: #fef2f2; }
        .stat-late { color: #d97706; background: #fffbeb; }
        .stat-sick { color: #2563eb; background: #eff6ff; }
        .stat-excused { color: #6b7280; background: #f9fafb; }
        .stat-rate { color: #1e3a5f; background: #f0f4ff; }

        .month-section { margin-bottom: 15px; page-break-inside: avoid; }
        .month-title { font-size: 12px; font-weight: bold; color: #1e3a5f; border-bottom: 2px solid #1e3a5f; padding-bottom: 4px; margin-bottom: 8px; }

        table.attendance { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.attendance th { background: #1e3a5f; color: #fff; padding: 6px 8px; text-align: left; font-weight: 600; }
        table.attendance td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        table.attendance tr:nth-child(even) { background: #f9fafb; }

        .status-present { color: #059669; font-weight: bold; }
        .status-absent { color: #dc2626; font-weight: bold; }
        .status-late { color: #d97706; font-weight: bold; }
        .status-sick { color: #2563eb; font-weight: bold; }
        .status-excused { color: #6b7280; font-weight: bold; }

        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #dee2e6; text-align: center; font-size: 9px; color: #999; }
        .legend { margin-top: 15px; font-size: 9px; color: #666; }
        .legend span { margin-right: 12px; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        @if($schoolSettings->school_logo)
            <img src="{{ public_path('storage/' . $schoolSettings->school_logo) }}" alt="Logo" class="logo">
        @endif
        <h1>{{ $schoolSettings->school_name ?? 'St. Francis of Assisi Private School' }}</h1>
        <h2>Student Attendance Report</h2>
        <div class="subtitle">
            {{ $term?->name ?? 'Current Term' }} &mdash; {{ $academicYear?->name ?? date('Y') }} Academic Year
        </div>
    </div>

    {{-- Student Info --}}
    <div class="student-info">
        <table>
            <tr>
                <td class="label">Student Name:</td>
                <td class="value">{{ $student->name }}</td>
                <td class="label">Grade:</td>
                <td class="value">{{ $student->grade?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Class:</td>
                <td class="value">{{ $student->classSection?->name ?? 'N/A' }}</td>
                <td class="label">Parent/Guardian:</td>
                <td class="value">{{ $student->parentGuardian?->name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- Summary Stats --}}
    <div class="stats-row">
        <table>
            <tr>
                <td class="stat-present">
                    <span class="stat-value">{{ $stats['present'] }}</span>
                    <span class="stat-label">Present</span>
                </td>
                <td class="stat-absent">
                    <span class="stat-value">{{ $stats['absent'] }}</span>
                    <span class="stat-label">Absent</span>
                </td>
                <td class="stat-late">
                    <span class="stat-value">{{ $stats['late'] }}</span>
                    <span class="stat-label">Late</span>
                </td>
                <td class="stat-sick">
                    <span class="stat-value">{{ $stats['sick'] }}</span>
                    <span class="stat-label">Sick</span>
                </td>
                <td class="stat-excused">
                    <span class="stat-value">{{ $stats['excused'] }}</span>
                    <span class="stat-label">Excused</span>
                </td>
                <td class="stat-rate">
                    <span class="stat-value">{{ $stats['rate'] }}%</span>
                    <span class="stat-label">Attendance Rate</span>
                </td>
                <td style="text-align: center; padding: 10px 5px; border: 1px solid #dee2e6;">
                    <span class="stat-value">{{ $stats['total'] }}</span>
                    <span class="stat-label">Total Days</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Monthly Breakdown --}}
    @foreach($monthlyRecords as $month => $monthRecords)
        <div class="month-section">
            <div class="month-title">{{ $month }}</div>
            <table class="attendance">
                <thead>
                    <tr>
                        <th style="width: 30px;">#</th>
                        <th style="width: 110px;">Date</th>
                        <th style="width: 80px;">Day</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 70px;">Check In</th>
                        <th style="width: 70px;">Check Out</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthRecords as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $record->attendance_date->format('d M Y') }}</td>
                            <td>{{ $record->attendance_date->format('l') }}</td>
                            <td>
                                <span class="status-{{ $record->status }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td>{{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i') : '-' }}</td>
                            <td>{{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('H:i') : '-' }}</td>
                            <td>{{ $record->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    @if($records->isEmpty())
        <div style="text-align: center; padding: 40px; color: #999;">
            <p style="font-size: 14px;">No attendance records found for this term.</p>
        </div>
    @endif

    {{-- Legend --}}
    <div class="legend">
        <strong>Key:</strong>
        <span class="status-present">P = Present</span>
        <span class="status-absent">X = Absent</span>
        <span class="status-late">Y = Late</span>
        <span class="status-sick">S = Sick</span>
        <span class="status-excused">L = Excused</span>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Generated on {{ $generatedAt->format('d M Y \a\t H:i') }} &mdash; {{ $schoolSettings->school_name ?? 'St. Francis of Assisi Private School' }}
    </div>
</body>
</html>
