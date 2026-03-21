<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $schoolName }} - Master Timetable</title>
    <style>
        @page { size: A4 landscape; margin: 10mm 8mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 7px;
            color: #333;
            line-height: 1.3;
        }
        .page-break { page-break-after: always; }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #1e3a5f;
        }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .logo-cell { width: 55px; text-align: left; }
        .school-logo { width: 45px; height: 45px; }
        .school-name { color: #1e3a5f; font-size: 14px; font-weight: bold; margin-bottom: 2px; }
        .document-title { color: #1e3a5f; font-size: 12px; font-weight: bold; margin-top: 3px; }
        .document-subtitle { color: #666; font-size: 9px; margin-top: 2px; }
        .day-header {
            background-color: #1e3a5f;
            color: white;
            padding: 6px 15px;
            margin-bottom: 8px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }
        .timetable-grid { width: 100%; border-collapse: collapse; }
        .timetable-grid th {
            background-color: #1e3a5f;
            color: white;
            padding: 4px 2px;
            text-align: center;
            font-size: 6.5px;
            font-weight: bold;
            border: 1px solid #1e3a5f;
        }
        .timetable-grid th:first-child { text-align: left; padding-left: 5px; width: 65px; }
        .timetable-grid th.break-col { background-color: #374151; width: 30px; }
        .timetable-grid td {
            padding: 3px 2px;
            border: 1px solid #d1d5db;
            text-align: center;
            vertical-align: middle;
            font-size: 6.5px;
        }
        .timetable-grid tr:nth-child(even) { background-color: #f9fafb; }
        .day-cell {
            background-color: #f3f4f6 !important;
            font-weight: bold;
            text-align: left;
            padding-left: 5px !important;
            color: #1e3a5f;
            font-size: 7px;
        }
        .period-time { font-size: 5.5px; font-weight: normal; opacity: 0.8; }
        .subject-name { font-weight: bold; font-size: 6.5px; color: #1e3a5f; }
        .teacher-name { font-size: 5.5px; color: #6b7280; }
        .break-cell-content { color: #92400e; font-style: italic; font-size: 6px; background-color: #fef3c7 !important; }
        .empty-cell { color: #d1d5db; }
        .footer {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7px;
            color: #666;
        }
    </style>
</head>
<body>
    @php
        $yearName = is_object($academicYear) ? $academicYear->name : $academicYear;
        $logoPath = public_path('images/logo.png');
        if (isset($schoolLogo) && $schoolLogo && file_exists($schoolLogo)) {
            $logoPath = $schoolLogo;
        }
        $logoExists = file_exists($logoPath);
        $dateLabel = $generatedAt ?? ($reportDate ?? now()->format('F d, Y'));
        $allPeriodsCollection = $allPeriods ?? $periods;

        // Support both data formats
        $useTimetableByDay = isset($timetableByDay);
    @endphp

    @foreach($classSections as $csIndex => $cs)
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if($logoExists)
                            <img src="{{ $logoPath }}" class="school-logo" alt="Logo">
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div class="school-name">{{ $schoolName }}</div>
                        <div class="document-title">MASTER TIMETABLE - {{ strtoupper(($cs->grade?->name ?? '?') . ' ' . $cs->name) }}</div>
                        <div class="document-subtitle">Academic Year: {{ $yearName }}</div>
                    </td>
                    <td style="width: 55px;"></td>
                </tr>
            </table>
        </div>

        {{-- Grid: Rows = Days, Columns = Periods --}}
        <table class="timetable-grid">
            <thead>
                <tr>
                    <th>Day</th>
                    @foreach($allPeriodsCollection as $period)
                        <th class="{{ $period->isBreak() ? 'break-col' : '' }}">
                            <div>{{ $period->short_name ?? $period->name }}</div>
                            <div class="period-time">
                                {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($days as $day)
                    <tr>
                        <td class="day-cell">{{ $day }}</td>
                        @foreach($allPeriodsCollection as $period)
                            @if($period->isBreak())
                                <td class="break-cell-content">
                                    @switch($period->type)
                                        @case('assembly') ASM @break
                                        @case('tea_break') BRK @break
                                        @case('lunch_break') LCH @break
                                        @default BRK
                                    @endswitch
                                </td>
                            @else
                                @php
                                    if ($useTimetableByDay) {
                                        $key = $cs->id . '-' . $period->id;
                                        $dayData = $timetableByDay[$day] ?? [];
                                        $entry = $dayData[$key] ?? null;
                                    } else {
                                        $entry = $timetableData[$cs->id][$period->id][$day] ?? null;
                                    }
                                @endphp
                                <td>
                                    @if($entry)
                                        <div class="subject-name">{{ $entry->subject?->code ?? \Illuminate\Support\Str::limit($entry->subject?->name, 12) ?? '-' }}</div>
                                        <div class="teacher-name">{{ \Illuminate\Support\Str::limit($entry->teacher?->name, 14) ?? '' }}</div>
                                    @else
                                        <span class="empty-cell">-</span>
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>{{ $schoolName }} | Master Timetable {{ $yearName }} | {{ ($cs->grade?->name ?? '') . ' ' . $cs->name }} | Generated on {{ $dateLabel }}</p>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
