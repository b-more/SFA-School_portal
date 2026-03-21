<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 9.5px;
            line-height: 1.3;
            color: #1f2937;
            background: #fff;
        }

        .container {
            width: 100%;
            max-width: 186mm;
            margin: 0 auto;
        }

        /* ========== HEADER ========== */
        .header-wrapper {
            border: 2px solid #1e3a5f;
            border-radius: 2px;
            overflow: hidden;
        }

        .header-top {
            display: table;
            width: 100%;
            background-color: #1e3a5f;
        }

        .header-logo-cell {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            text-align: center;
            padding: 8px 5px 8px 10px;
        }

        .logo-ring {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 2px;
            display: inline-block;
        }

        .logo-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: contain;
            background: white;
        }

        .logo-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: white;
            line-height: 49px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .header-text-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 8px 80px 8px 5px;
        }

        .school-name {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #ffffff;
        }

        .school-motto {
            font-size: 8.5px;
            font-style: italic;
            color: rgba(255,255,255,0.7);
            margin: 2px 0 3px;
        }

        .school-address {
            font-size: 7.5px;
            color: rgba(255,255,255,0.6);
            line-height: 1.4;
        }

        /* Red accent bar */
        .accent-bar {
            height: 3px;
            background-color: #dc2626;
        }

        /* Report title */
        .report-title-bar {
            display: table;
            width: 100%;
            background-color: #f8fafc;
        }
        .report-title-cell {
            display: table-cell;
            padding: 5px 12px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1e3a5f;
        }

        /* ========== STUDENT INFO ========== */
        .student-info-table {
            width: 100%;
            border-collapse: collapse;
            border-left: 2px solid #1e3a5f;
            border-right: 2px solid #1e3a5f;
        }

        .student-info-table td {
            border-bottom: 1px solid #cbd5e1;
            padding: 4px 8px;
            font-size: 9px;
        }

        .student-info-table .label {
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.3px;
            width: 17%;
        }

        .student-info-table .value {
            font-weight: 600;
            color: #1f2937;
            width: 33%;
        }

        /* ========== RESULTS TABLE ========== */
        table.results {
            width: 100%;
            border-collapse: collapse;
        }

        table.results th {
            border: 2px solid #1e3a5f;
            padding: 5px 6px;
            text-align: center;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: bold;
            color: #ffffff;
            background-color: #1e3a5f;
        }

        table.results th:first-child {
            text-align: left;
            padding-left: 8px;
        }

        table.results td {
            border: 1px solid #cbd5e1;
            border-left: 2px solid #1e3a5f;
            border-right: 2px solid #1e3a5f;
            padding: 4px 6px;
            text-align: center;
            font-size: 9.5px;
            color: #1f2937;
        }

        table.results td:first-child {
            text-align: left;
            padding-left: 8px;
            font-weight: 500;
        }

        table.results tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        table.results tbody tr:last-child td {
            border-bottom: 2px solid #1e3a5f;
        }

        .grade-badge {
            font-weight: bold;
            font-size: 10px;
        }

        /* ========== SUMMARY BAR ========== */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            border: 2px solid #1e3a5f;
            border-top: none;
            padding: 5px 6px;
            text-align: center;
        }

        .summary-value {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .summary-label {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.3px;
            margin-top: 1px;
        }

        /* ========== GRADING SCALE ========== */
        .grading-scale {
            border: 2px solid #1e3a5f;
            border-top: none;
            padding: 3px 8px;
            font-size: 7.5px;
            color: #475569;
            background-color: #f8fafc;
        }

        .grading-scale-title {
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a5f;
        }

        .scale-item {
            color: #475569;
        }

        /* ========== COMMENTS ========== */
        .comment-box {
            border: 2px solid #1e3a5f;
            border-top: none;
            padding: 5px 8px;
            min-height: 38px;
        }

        .comment-label {
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
            color: #1e3a5f;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }

        .comment-text {
            font-size: 8.5px;
            color: #374151;
            line-height: 1.4;
        }

        /* ========== SIGNATURES ========== */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatures-table td {
            border: 2px solid #1e3a5f;
            border-top: none;
            padding: 5px 8px;
            text-align: center;
            width: 33.33%;
            vertical-align: bottom;
            height: 45px;
        }

        .signature-line {
            border-top: 1px solid #1e3a5f;
            margin-top: 18px;
            padding-top: 3px;
        }

        .signature-name {
            font-size: 8.5px;
            font-weight: bold;
            color: #1f2937;
        }

        .signature-title {
            font-size: 7px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ========== FOOTER ========== */
        .footer-bar {
            display: table;
            width: 100%;
            margin-top: 4px;
        }
        .footer-cell {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 3px 0;
        }
        .footer-text {
            font-size: 7px;
            color: #94a3b8;
        }
        .footer-tagline {
            font-size: 7.5px;
            color: #1e3a5f;
            font-weight: 600;
            font-style: italic;
        }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- ===== HEADER ===== -->
        <div class="header-wrapper">
            <div class="header-top">
                <div class="header-logo-cell">
                    <div class="logo-ring">
                        @php
                            $isPreview = $isPreview ?? false;
                        @endphp
                        @if($schoolSettings && $schoolSettings->school_logo && file_exists(public_path('storage/' . $schoolSettings->school_logo)))
                            <img src="{{ $isPreview ? asset('storage/' . $schoolSettings->school_logo) : public_path('storage/' . $schoolSettings->school_logo) }}" class="logo-img" alt="Logo">
                        @elseif(file_exists(public_path('images/logo.png')))
                            <img src="{{ $isPreview ? asset('images/logo.png') : public_path('images/logo.png') }}" class="logo-img" alt="Logo">
                        @else
                            <div class="logo-placeholder">SFA</div>
                        @endif
                    </div>
                </div>
                <div class="header-text-cell">
                    <div class="school-name">{{ $schoolSettings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                    @if($schoolSettings && $schoolSettings->school_motto)
                        <div class="school-motto">"{{ $schoolSettings->school_motto }}"</div>
                    @endif
                    <div class="school-address">
                        @php
                            $addressParts = array_filter([
                                $schoolSettings->address ?? null,
                                $schoolSettings->city ?? null,
                                $schoolSettings->state_province ?? null,
                                $schoolSettings->country ?? null,
                            ]);
                            $contactParts = array_filter([
                                ($schoolSettings->phone ?? null) ? 'Tel: ' . $schoolSettings->phone : null,
                                ($schoolSettings->email ?? null) ? 'Email: ' . $schoolSettings->email : null,
                                ($schoolSettings->website ?? null) ? 'Web: ' . $schoolSettings->website : null,
                            ]);
                        @endphp
                        @if(!empty($addressParts))
                            {{ implode(', ', $addressParts) }}
                            @if($schoolSettings->postal_code) &middot; P.O. Box {{ $schoolSettings->postal_code }}@endif
                            <br>
                        @endif
                        @if(!empty($contactParts))
                            {{ implode('  |  ', $contactParts) }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="accent-bar"></div>
            <div class="report-title-bar">
                <div class="report-title-cell">
                    Student Report Card &mdash; {{ $term->name ?? 'Term' }}, {{ $year }}
                </div>
            </div>
        </div>

        <!-- ===== STUDENT INFORMATION ===== -->
        <table class="student-info-table">
            <tr>
                <td class="label">Student Name</td>
                <td class="value" style="width: 33%;">{{ $student->name }}</td>
                <td class="label">Student ID</td>
                <td class="value" style="width: 17%;">{{ $student->student_id_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Class</td>
                <td class="value">
                    @if($student->classSection && $student->classSection->grade)
                        {{ $student->classSection->grade->name }} - {{ $student->classSection->name }}
                    @else
                        N/A
                    @endif
                </td>
                <td class="label">Gender</td>
                <td class="value">{{ ucfirst($student->gender ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">Academic Year</td>
                <td class="value">{{ $academicYear->name ?? $year }}</td>
                <td class="label">Class Teacher</td>
                <td class="value">{{ $classTeacherName ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- ===== RESULTS TABLE ===== -->
        @php
            $subjects = $resultsData['subjects'] ?? [];
        @endphp

        <table class="results">
            <thead>
                <tr>
                    <th style="width: 7%;">No.</th>
                    <th style="width: 38%; text-align: left;">Subject</th>
                    <th style="width: 18%;">Marks (%)</th>
                    <th style="width: 15%;">Grade</th>
                    <th style="width: 22%;">Remark</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $index => $subject)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: left;">{{ $subject['subject_name'] }}</td>
                        <td><strong>{{ $subject['final'] !== null ? number_format($subject['final'], 0) : ($subject['combined'] !== null ? number_format($subject['combined'], 0) : '-') }}</strong></td>
                        <td>
                            @if(isset($subject['grade']) && $subject['grade'] !== 'N/A')
                                <span class="grade-badge">{{ $subject['grade'] }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $subject['remark'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 15px;">No results available for this term</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- ===== SUMMARY ===== -->
        @php
            $combined = $resultsData['combined'] ?? ['average' => 0, 'total' => 0, 'subjects_count' => 0];
            $position = $resultsData['position'] ?? ['position' => null, 'total' => 0];
            $overallGrade = $resultsData['overall_grade'] ?? null;
            $average = $combined['average'] ?? 0;
            $positionNum = $position['position'] ?? null;
            $totalStudents = $position['total'] ?? 0;

            $classTeacherComments = [
                'excellent' => [
                    "Outstanding performance! {$student->name} has demonstrated exceptional academic abilities and consistently excels in all subjects. Keep up the excellent work!",
                    "Exceptional results! {$student->name} shows remarkable dedication to studies and maintains high standards across all subjects. A truly exemplary student.",
                    "{$student->name} has performed brilliantly this term. The consistent excellence in academics reflects strong commitment to learning. Well done!",
                ],
                'very_good' => [
                    "{$student->name} has shown very good performance this term. With continued effort and focus, even greater achievements are within reach.",
                    "Very good academic performance! {$student->name} demonstrates strong understanding across subjects and shows great potential for excellence.",
                    "A commendable performance by {$student->name}. The dedication shown this term is evident in the results. Keep striving for excellence!",
                ],
                'good' => [
                    "{$student->name} has performed well this term. There is good potential for improvement with more consistent effort and dedication.",
                    "Good progress shown by {$student->name}. With increased focus on weaker areas, better results can be achieved next term.",
                    "{$student->name} shows satisfactory performance. Encouraging more reading and practice will help achieve better grades.",
                ],
                'average' => [
                    "{$student->name} has shown average performance this term. More effort and attention to studies is needed for improvement.",
                    "There is room for improvement. {$student->name} should focus more on studies and seek help in challenging subjects.",
                    "{$student->name} needs to put in more effort. Regular study habits and completing assignments on time will help improve grades.",
                ],
                'below_average' => [
                    "{$student->name} needs significant improvement. Extra tutorials and more study time at home are strongly recommended.",
                    "Performance below expectations. {$student->name} must work harder and seek assistance from teachers in difficult subjects.",
                    "Urgent attention needed. {$student->name} should dedicate more time to studies and parents should monitor homework completion.",
                ],
            ];

            $headTeacherComments = [
                'excellent' => [
                    "Congratulations on this outstanding achievement! {$student->name} is a role model for other students. Continue to aim high!",
                    "Excellent performance that reflects hard work and dedication. We are proud of {$student->name}'s achievements.",
                    "Remarkable results! {$student->name} has shown what can be achieved through commitment and perseverance.",
                ],
                'very_good' => [
                    "Very good performance. {$student->name} has shown commendable effort. Keep working towards excellence.",
                    "Well done! {$student->name} continues to show strong academic capabilities. Maintain this positive trajectory.",
                    "Impressive results. With continued dedication, {$student->name} can achieve even greater success.",
                ],
                'good' => [
                    "Good effort this term. {$student->name} has potential for greater achievement with consistent application.",
                    "Satisfactory performance. We encourage {$student->name} to set higher goals and work towards them.",
                    "{$student->name} shows promise. More dedication to academics will yield better results.",
                ],
                'average' => [
                    "There is need for improvement. We encourage {$student->name} to be more focused and committed to studies.",
                    "{$student->name} should put in more effort. We recommend regular revision and completing all assignments.",
                    "We expect better performance next term. {$student->name} should work closely with teachers for improvement.",
                ],
                'below_average' => [
                    "Performance needs urgent attention. We request parents to closely monitor {$student->name}'s academic activities.",
                    "Significant improvement required. {$student->name} should attend extra classes and dedicate more time to studies.",
                    "We are concerned about this performance. A parent-teacher meeting is recommended to discuss {$student->name}'s progress.",
                ],
            ];

            if ($average >= 80) {
                $category = 'excellent';
            } elseif ($average >= 65) {
                $category = 'very_good';
            } elseif ($average >= 50) {
                $category = 'good';
            } elseif ($average >= 40) {
                $category = 'average';
            } else {
                $category = 'below_average';
            }

            $autoClassTeacherComment = $classTeacherComments[$category][array_rand($classTeacherComments[$category])];
            $autoHeadTeacherComment = $headTeacherComments[$category][array_rand($headTeacherComments[$category])];
        @endphp

        <table class="summary-table">
            <tr>
                <td style="width: 25%;">
                    <div class="summary-value">{{ number_format($average, 1) }}%</div>
                    <div class="summary-label">Average</div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-value">{{ number_format($combined['total'], 0) }}/{{ $combined['subjects_count'] * 100 }}</div>
                    <div class="summary-label">Total Marks</div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-value">
                        @if($position['position'])
                            {{ $position['position'] }} / {{ $position['total'] }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="summary-label">Class Position</div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-value">
                        @if($overallGrade)
                            {{ $overallGrade['grade'] }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="summary-label">Overall Grade</div>
                </td>
            </tr>
        </table>

        <!-- ===== GRADING SCALE ===== -->
        @if($gradingScale)
            <div class="grading-scale">
                <span class="grading-scale-title">Grading Scale:</span>
                @foreach($gradingScale->items as $item)
                    <span class="scale-item">
                        <strong>{{ $item->grade }}</strong> ({{ $item->min_marks }}-{{ $item->max_marks }}%)
                    </span>
                    @if(!$loop->last) &nbsp;|&nbsp; @endif
                @endforeach
            </div>
        @endif

        <!-- ===== COMMENTS ===== -->
        <div class="comment-box">
            <div class="comment-label">Class Teacher's Comment</div>
            <div class="comment-text">
                {{ ($comments && $comments->class_teacher_comment) ? $comments->class_teacher_comment : $autoClassTeacherComment }}
            </div>
        </div>

        <div class="comment-box">
            <div class="comment-label">Head Teacher's Comment</div>
            <div class="comment-text">
                {{ ($comments && $comments->head_teacher_comment) ? $comments->head_teacher_comment : $autoHeadTeacherComment }}
            </div>
        </div>

        <!-- ===== SIGNATURES ===== -->
        <table class="signatures-table">
            <tr>
                <td>
                    <div class="signature-line">
                        <div class="signature-name">{{ $classTeacherName ?? '________________' }}</div>
                        <div class="signature-title">Class Teacher</div>
                    </div>
                </td>
                <td>
                    <div class="signature-line">
                        <div class="signature-name">Sylvester Lupando</div>
                        <div class="signature-title">Head Teacher</div>
                    </div>
                </td>
                <td>
                    <div class="signature-line">
                        <div class="signature-name">Francis Mulenga</div>
                        <div class="signature-title">Executive Director</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ===== FOOTER ===== -->
        <div class="footer-bar">
            <div class="footer-cell">
                <div class="footer-tagline">Nurturing Excellence, Inspiring the Future!</div>
                <div class="footer-text">
                    Date Issued: {{ $generatedAt->format('d F Y') }}
                    &nbsp;&middot;&nbsp; This is a computer-generated document.
                </div>
            </div>
        </div>

    </div>
</body>
</html>
