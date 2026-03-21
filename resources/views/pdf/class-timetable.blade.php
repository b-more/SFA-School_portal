<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $schoolName }} - Class Timetable</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 5mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 7px;
            color: #333;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 2px solid #1e3a5f;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 50px;
            vertical-align: middle;
        }

        .logo-section img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .school-info {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .school-name {
            color: #1e3a5f;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .school-address {
            color: #666;
            font-size: 8px;
            margin-bottom: 1px;
        }

        .document-title {
            color: #1e3a5f;
            font-size: 11px;
            font-weight: bold;
            margin-top: 4px;
        }

        .document-subtitle {
            color: #666;
            font-size: 8px;
            margin-top: 2px;
        }

        .class-info {
            background-color: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 8px;
            text-align: center;
        }

        .class-name {
            color: #1e3a5f;
            font-size: 12px;
            font-weight: bold;
        }

        .class-teacher {
            color: #666;
            font-size: 8px;
            margin-top: 2px;
        }

        .timetable-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 5px;
        }

        .timetable-grid th {
            background-color: #1e3a5f;
            color: white;
            padding: 4px 2px;
            text-align: center;
            font-size: 6.5px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #1e3a5f;
            overflow: hidden;
        }

        .timetable-grid th:first-child {
            text-align: left;
            padding-left: 5px;
            width: 7%;
        }

        .timetable-grid th.break-col {
            background-color: #374151;
            width: 3.5%;
        }

        .timetable-grid th.lesson-col {
            width: 7.5%;
        }

        .timetable-grid td {
            padding: 3px 2px;
            border: 1px solid #d1d5db;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            word-wrap: break-word;
        }

        .timetable-grid tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .period-cell {
            background-color: #f3f4f6 !important;
            font-weight: bold;
            text-align: left;
            padding-left: 5px !important;
        }

        .period-name {
            color: #1e3a5f;
            font-size: 7px;
        }

        .subject-name {
            font-weight: bold;
            font-size: 6.5px;
            color: #1e3a5f;
            margin-bottom: 1px;
            line-height: 1.2;
        }

        .teacher-name {
            font-size: 5.5px;
            color: #6b7280;
        }

        .room-info {
            font-size: 5px;
            color: #9ca3af;
        }

        .break-cell {
            color: #92400e;
            font-style: italic;
            font-size: 6px;
            font-weight: 500;
            background-color: #fef3c7 !important;
        }

        .empty-cell {
            color: #d1d5db;
            font-size: 7px;
        }

        .footer {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7px;
            color: #666;
        }

        .footer-note {
            margin-top: 3px;
            font-style: italic;
        }

        .legend {
            margin-top: 8px;
            padding: 5px 8px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 3px;
        }

        .legend-title {
            font-weight: bold;
            font-size: 7px;
            color: #1e3a5f;
            margin-bottom: 3px;
        }

        .legend-item {
            display: inline-block;
            margin-right: 10px;
            font-size: 7px;
        }

        .legend-color {
            display: inline-block;
            width: 10px;
            height: 10px;
            vertical-align: middle;
            margin-right: 3px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-content">
            @if($schoolLogo && file_exists($schoolLogo))
                <div class="logo-section">
                    <img src="{{ $schoolLogo }}" alt="School Logo">
                </div>
            @endif
            <div class="school-info">
                <div class="school-name">{{ $schoolName }}</div>
                @if($schoolAddress)
                    <div class="school-address">{{ $schoolAddress }}</div>
                @endif
                @if($schoolPhone)
                    <div class="school-address">Tel: {{ $schoolPhone }}</div>
                @endif
                <div class="document-title">CLASS TIMETABLE</div>
                <div class="document-subtitle">Academic Year: {{ $academicYear->name }}</div>
            </div>
            @if($schoolLogo && file_exists($schoolLogo))
                <div class="logo-section"></div>
            @endif
        </div>
    </div>

    {{-- Class Info --}}
    <div class="class-info">
        <div class="class-name">{{ $classSection->grade->name ?? 'Unknown Grade' }} - {{ $classSection->name }}</div>
        @if($classSection->classTeacher)
            <div class="class-teacher">Class Teacher: {{ $classSection->classTeacher->name }}</div>
        @endif
    </div>

    {{-- Timetable Grid: Rows = Days, Columns = Periods --}}
    <table class="timetable-grid">
        <thead>
            <tr>
                <th>Day</th>
                @foreach($periods as $period)
                    <th class="{{ $period->isBreak() ? 'break-col' : 'lesson-col' }}">
                        <div>{{ $period->short_name ?? $period->name }}</div>
                        <div style="font-size: 5.5px; font-weight: normal; opacity: 0.8;">
                            {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($days as $day)
                <tr>
                    {{-- Day Name --}}
                    <td class="period-cell">
                        <div class="period-name">{{ $day }}</div>
                    </td>

                    {{-- Period Cells --}}
                    @foreach($periods as $period)
                        @if($period->isBreak())
                            <td class="break-cell">
                                @switch($period->type)
                                    @case('assembly') ASM @break
                                    @case('tea_break') BRK @break
                                    @case('lunch_break') LCH @break
                                    @default BRK
                                @endswitch
                            </td>
                        @else
                            @php
                                $entry = $timetable[$period->id]['days'][$day] ?? null;
                            @endphp
                            <td>
                                @if($entry)
                                    @php
                                        $isPrimary = in_array($classSection->grade?->school_section_id, [1, 2]);
                                        $displayTeacher = $entry->teacher?->name ?? ($isPrimary ? $classSection->classTeacher?->name : null);
                                    @endphp
                                    <div class="subject-name">{{ $entry->subject?->code ?? \Illuminate\Support\Str::limit($entry->subject?->name, 12) ?? '-' }}</div>
                                    <div class="teacher-name">{{ $displayTeacher ? \Illuminate\Support\Str::limit($displayTeacher, 14) : '' }}</div>
                                    @if($entry->room)
                                        <div class="room-info">{{ $entry->room }}</div>
                                    @endif
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

    {{-- Subject Key --}}
    @php
        $subjectKeys = collect();
        foreach ($timetable as $periodData) {
            foreach ($periodData['days'] ?? [] as $entry) {
                if ($entry && $entry->subject) {
                    $abbr = $entry->subject->code ?? \Illuminate\Support\Str::limit($entry->subject->name, 12);
                    $subjectKeys[$abbr] = $entry->subject->name;
                }
            }
        }
        $subjectKeys = $subjectKeys->sort();
    @endphp
    @if($subjectKeys->isNotEmpty())
        <div class="legend">
            <div class="legend-title">Subject Key:</div>
            @foreach($subjectKeys as $abbr => $fullName)
                <span class="legend-item"><strong>{{ $abbr }}</strong> — {{ $fullName }}@if(!$loop->last), @endif</span>
            @endforeach
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div>{{ $schoolName }} | Generated on {{ $generatedAt }}</div>
        <div class="footer-note">This is a computer-generated document.</div>
    </div>
</body>
</html>
