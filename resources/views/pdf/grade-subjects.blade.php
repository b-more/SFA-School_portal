<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Subjects & Teacher Assignments</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 15px;
        }

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 6px;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin: 0 auto 4px;
            object-fit: contain;
        }

        .school-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .school-subtitle {
            font-size: 8pt;
            opacity: 0.9;
            margin-bottom: 6px;
        }

        .document-title {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            padding: 5px 0;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        }

        .stats-section {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            background: #f8fafc;
            padding: 8px;
            border-radius: 6px;
        }

        .stat-box {
            display: table-cell;
            text-align: center;
            padding: 5px;
            width: 20%;
        }

        .stat-number {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
        }

        .stat-label {
            font-size: 7pt;
            color: #64748b;
            margin-top: 2px;
        }

        .grade-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .grade-header {
            background: #1e3a5f;
            color: white;
            padding: 6px 10px;
            font-size: 10pt;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }

        .grade-header .section-name {
            font-size: 7pt;
            font-weight: normal;
            opacity: 0.85;
        }

        .subjects-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .subjects-table thead {
            background: #e2e8f0;
        }

        .subjects-table th {
            padding: 5px 6px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            color: #1e3a5f;
            border: 1px solid #cbd5e1;
        }

        .subjects-table td {
            padding: 4px 6px;
            font-size: 7pt;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .subjects-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 8px;
            font-size: 6pt;
            margin-right: 2px;
            margin-bottom: 1px;
            white-space: nowrap;
        }

        .badge-mandatory {
            background: #10b981;
            color: white;
        }

        .badge-optional {
            background: #94a3b8;
            color: white;
        }

        .teacher-item {
            margin-bottom: 2px;
        }

        .teacher-name {
            font-weight: bold;
            font-size: 7pt;
        }

        .teacher-class {
            font-size: 6pt;
            color: #64748b;
        }

        .no-teacher {
            color: #ef4444;
            font-style: italic;
            font-size: 7pt;
        }

        .multi-teacher-row {
            background: #fef3c7 !important;
            border-left: 3px solid #f59e0b;
        }

        .multi-teacher-badge {
            display: inline-block;
            padding: 1px 5px;
            background: #f59e0b;
            color: white;
            border-radius: 8px;
            font-size: 6pt;
            font-weight: bold;
            margin-left: 4px;
        }

        .flag-section {
            margin-bottom: 15px;
            border: 2px solid #f59e0b;
            border-radius: 6px;
            overflow: hidden;
        }

        .flag-header {
            background: #f59e0b;
            color: white;
            padding: 6px 10px;
            font-size: 9pt;
            font-weight: bold;
        }

        .flag-table {
            width: 100%;
            border-collapse: collapse;
        }

        .flag-table th {
            padding: 5px 6px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .flag-table td {
            padding: 4px 6px;
            font-size: 7pt;
            border: 1px solid #fde68a;
        }

        .flag-table tbody tr:nth-child(even) {
            background: #fffbeb;
        }

        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 6pt;
        }

        .footer-info {
            margin-bottom: 2px;
        }

        .generated-date {
            font-style: italic;
            color: #94a3b8;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                @php
                    $logoPath = null;
                    if ($settings && $settings->school_logo && file_exists(storage_path('app/public/' . $settings->school_logo))) {
                        $logoPath = storage_path('app/public/' . $settings->school_logo);
                    } elseif (file_exists(public_path('images/logo.png'))) {
                        $logoPath = public_path('images/logo.png');
                    }
                @endphp
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="School Logo" class="logo">
                @endif
                <div class="school-name">{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                <div class="school-subtitle">{{ $settings->school_motto ?? 'Excellence in Education' }}</div>
            </div>
            <div class="document-title">GRADE SUBJECTS & TEACHER ASSIGNMENTS</div>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-section">
            <div class="stat-box">
                <div class="stat-number">{{ $totalGrades }}</div>
                <div class="stat-label">Grades</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $totalSubjects }}</div>
                <div class="stat-label">Unique Subjects</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $totalAssignments }}</div>
                <div class="stat-label">Total Assignments</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $totalTeachers }}</div>
                <div class="stat-label">Teachers Assigned</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" style="{{ count($multiTeacherFlags) > 0 ? 'color: #f59e0b;' : 'color: #10b981;' }}">{{ count($multiTeacherFlags) }}</div>
                <div class="stat-label">Multi-Teacher Subjects</div>
            </div>
        </div>

        <!-- Multi-Teacher Flags Summary -->
        @if(count($multiTeacherFlags) > 0)
            <div class="flag-section">
                <div class="flag-header">
                    ⚠ MULTIPLE TEACHERS ASSIGNED ({{ count($multiTeacherFlags) }} subject(s) across grades)
                </div>
                <table class="flag-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">Grade</th>
                            <th style="width: 20%;">Subject</th>
                            <th style="width: 10%;">Teachers</th>
                            <th style="width: 45%;">Teacher Names</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($multiTeacherFlags as $index => $flag)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><strong>{{ $flag['grade'] }}</strong></td>
                                <td>{{ $flag['subject'] }}</td>
                                <td class="text-center"><strong>{{ $flag['teacher_count'] }}</strong></td>
                                <td>{{ $flag['teachers'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Grade Sections -->
        @foreach($grades as $grade)
            <div class="grade-section">
                <div class="grade-header">
                    {{ $grade['name'] }}
                    <span class="section-name">| {{ $grade['section'] }} | {{ $grade['subject_count'] }} subject(s)</span>
                </div>
                <table class="subjects-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 18%;">Subject</th>
                            <th style="width: 10%;">Code</th>
                            <th style="width: 10%;">Type</th>
                            <th style="width: 57%;">Teachers & Class Sections</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grade['subjects'] as $index => $subject)
                            <tr class="{{ $subject['has_multiple_teachers'] ? 'multi-teacher-row' : '' }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $subject['name'] }}</strong>
                                    @if($subject['has_multiple_teachers'])
                                        <span class="multi-teacher-badge">{{ $subject['unique_teacher_count'] }} teachers</span>
                                    @endif
                                </td>
                                <td>{{ $subject['code'] ?? '—' }}</td>
                                <td>
                                    @if($subject['is_mandatory'])
                                        <span class="badge badge-mandatory">Mandatory</span>
                                    @else
                                        <span class="badge badge-optional">Optional</span>
                                    @endif
                                </td>
                                <td>
                                    @if(count($subject['teachers']) > 0)
                                        @foreach($subject['teachers'] as $teacher)
                                            <div class="teacher-item">
                                                <span class="teacher-name">{{ $teacher['name'] }}</span>
                                                <span class="teacher-class">({{ $teacher['class_section'] }})</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="no-teacher">No teacher assigned</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 10px; color: #94a3b8;">
                                    No subjects assigned to this grade.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach

        <!-- Footer -->
        <div class="footer">
            <div class="footer-info">
                {{ $settings->school_name ?? 'St. Francis of Assisi Private School' }} | {{ $settings->address ?? '' }}{{ $settings->city ? ', ' . $settings->city : '' }}
            </div>
            <div class="footer-info">
                Email: {{ $settings->email ?? '' }} | Phone: {{ $settings->phone ?? '' }}
            </div>
            <div class="generated-date">
                Generated on: {{ now()->format('F d, Y \a\t h:i A') }}
            </div>
        </div>
    </div>
</body>
</html>
