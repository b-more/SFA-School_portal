<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card &mdash; {{ $student->name }}</title>
    <style>
        /* ==========================================================
           REPORT CARD — corporate stationery
           Palette: midnight #0F2A44 · oxblood #8B1A1A · parchment #F5EFE0
           Typography: DejaVu Serif (headings) + DejaVu Sans (data)
           Both ship with DomPDF; no external fonts.
           ========================================================== */

        /* Margins — DomPDF 3.x ignores @page margin reliably, so we
           enforce printable-area padding on <body> instead. 22mm on the
           left reserves the filing/hole-punch edge; 15mm elsewhere.
           @page must exist and declare size, but margin stays zero. */
        @page {
            size: A4;
            margin: 0;
        }

        html { margin: 0; padding: 0; }
        body {
            margin: 0;
            padding: 15mm 15mm 15mm 22mm;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #111827;
        }
        table { border-collapse: collapse; }
        img { border: 0; }

        /* Faint centred school crest — heritage stationery watermark.
           position:absolute in DomPDF is relative to the paper (NOT the
           padded body), so co-ordinates are absolute from the paper edge:
           paper 210mm wide, watermark 145mm → left (210-145)/2 = 32mm. */
        .watermark {
            position: absolute;
            top: 100mm;
            left: 32mm;
            width: 145mm;
            opacity: 0.04;
            z-index: 0;
        }

        /* ================= HEADER =================
           Classic school-letterhead lockup: crest (seal) at left,
           printed school name + motto + postal details stack to right.
           The crest's baked-in text is redundant with the printed
           name — that's intentional and traditional for stationery,
           it earns the printed lockup its role as the reading anchor. */
        .header {
            width: 100%;
            margin: 0;
        }
        .header td {
            vertical-align: middle;
            padding: 0;
        }
        .header .logo-cell {
            width: 30mm;
            padding-right: 6mm;
        }
        .header .logo {
            display: block;
            height: 24mm;
            width: 30mm; /* native 2304×1856 = 1.241 aspect → 30×24 */
        }
        .school-name {
            font-family: 'DejaVu Serif', serif;
            font-size: 15.5pt;
            font-weight: bold;
            color: #0F2A44;
            letter-spacing: 0.6pt;
            line-height: 1.15;
            white-space: nowrap;
        }
        .school-motto {
            font-family: 'DejaVu Serif', serif;
            font-size: 9.5pt;
            font-style: italic;
            color: #8B1A1A;
            letter-spacing: 0.4pt;
            margin: 3pt 0 5pt;
        }
        .school-meta {
            font-size: 8.5pt;
            color: #4B5563;
            line-height: 1.45;
            letter-spacing: 0.1pt;
        }
        .school-meta .street {
            color: #1F2937;
            font-weight: bold;
        }

        /* Double rule under header — heritage stationery cue. */
        .rule {
            border-top: 2pt solid #8B1A1A;
            border-bottom: 0.5pt solid #0F2A44;
            height: 3pt;
            margin: 4mm 0 0 0;
            font-size: 0;
            line-height: 0;
        }

        /* ================= TITLE BAR ================= */
        .title-bar {
            width: 100%;
            background: #F5EFE0;
            border-top: 0.5pt solid #C9B98E;
            border-bottom: 0.5pt solid #C9B98E;
            margin: 4mm 0 0 0;
        }
        .title-bar td {
            padding: 5pt 10pt 4pt;
            vertical-align: middle;
        }
        .title-bar .tag {
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            color: #6B7280;
        }
        .title-bar .title {
            font-family: 'DejaVu Serif', serif;
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            letter-spacing: 3pt;
            text-transform: uppercase;
            color: #0F2A44;
        }
        .title-bar .ref {
            text-align: right;
            font-size: 8pt;
            color: #8B1A1A;
            letter-spacing: 0.6pt;
        }

        /* ================= STUDENT INFO ================= */
        .student-info { width: 100%; margin-top: 2mm; }
        .student-info td { padding: 1.4mm 8pt 1.4mm 0; vertical-align: bottom; }
        .info-label {
            display: block;
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 1.1pt;
            text-transform: uppercase;
            color: #6B7280;
            margin-bottom: 2pt;
        }
        .info-value {
            display: block;
            font-size: 10pt;
            font-weight: bold;
            color: #0F2A44;
            border-bottom: 0.5pt solid #0F2A44;
            padding-bottom: 3pt;
            padding-right: 10pt;
            min-height: 12pt;
        }

        /* ================= RESULTS TABLE ================= */
        .results { width: 100%; margin-top: 3mm; }
        .results thead th {
            background: #0F2A44;
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 1pt;
            text-transform: uppercase;
            padding: 5pt 8pt;
            border: 0.5pt solid #0F2A44;
            text-align: center;
        }
        .results thead th.subj { text-align: left; padding-left: 10pt; }
        .results tbody td {
            padding: 0.5pt 8pt;
            font-size: 10pt;
            border: 0.5pt solid #D6D3D1;
            text-align: center;
            color: #111827;
            line-height: 1.15;
        }
        .results tbody td.subj {
            text-align: left;
            padding-left: 10pt;
            font-weight: bold;
            color: #0F2A44;
        }
        .results tbody tr:nth-child(even) td { background: #FBF7EC; }
        .results .marks { font-weight: bold; font-size: 10.5pt; }
        .results .grade { font-weight: bold; font-size: 10.5pt; color: #0F2A44; }
        .empty-row td {
            padding: 18pt 12pt !important;
            text-align: center !important;
            color: #9CA3AF;
            font-style: italic;
            background: #ffffff !important;
        }

        /* ================= SUMMARY ================= */
        .summary { width: 100%; margin-top: 1.5mm; }
        .summary td {
            width: 25%;
            padding: 3pt 4pt;
            text-align: center;
            border: 0.5pt solid #0F2A44;
        }
        .summary .value {
            font-family: 'DejaVu Serif', serif;
            font-size: 15pt;
            font-weight: bold;
            color: #0F2A44;
            line-height: 1;
        }
        .summary .unit {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5pt;
            color: #6B7280;
            font-weight: normal;
            letter-spacing: 0;
        }
        .summary .label {
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            color: #6B7280;
            margin-top: 4pt;
        }

        /* ================= ATTENDANCE ================= */
        .attendance { width: 100%; margin-top: 1.5mm; }
        .attendance .head-cell {
            width: 26mm;
            padding: 3pt 4pt;
            background: #0F2A44;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            text-align: center;
            border: 0.5pt solid #0F2A44;
            vertical-align: middle;
        }
        .attendance td {
            padding: 3pt 4pt;
            text-align: center;
            border: 0.5pt solid #0F2A44;
        }
        .attendance .att-value {
            font-family: 'DejaVu Serif', serif;
            font-size: 13pt;
            font-weight: bold;
            color: #0F2A44;
            line-height: 1;
        }
        .attendance .att-value .unit {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #6B7280;
            font-weight: normal;
        }
        .attendance .att-label {
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            color: #6B7280;
            margin-top: 4pt;
        }
        .attendance .att-rate { background: #F5EFE0; }
        .attendance .att-rate .att-value { color: #8B1A1A; }

        /* ================= LEGEND ================= */
        .legend {
            padding: 2.5pt 8pt;
            margin-top: 1.5mm;
            background: #F5EFE0;
            border-left: 3pt solid #8B1A1A;
            font-size: 8.5pt;
            color: #4B5563;
            line-height: 1.35;
        }
        .legend .head {
            font-weight: bold;
            letter-spacing: 1.1pt;
            text-transform: uppercase;
            color: #0F2A44;
            margin-right: 4pt;
        }

        /* ================= COMMENTS ================= */
        /* Comments size to their content — no fixed height. Padding
           gives them presence; content dictates fill. */
        .comments { width: 100%; margin-top: 1mm; }
        .comments td {
            padding: 1.5pt 10pt 2.5pt;
            border: 0.5pt solid #0F2A44;
            vertical-align: top;
        }
        .comment-eyebrow {
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            color: #8B1A1A;
            border-bottom: 0.5pt solid #E5D9BE;
            padding-bottom: 2pt;
            margin-bottom: 3pt;
        }
        .comment-body {
            font-size: 10pt;
            color: #1F2937;
            line-height: 1.45;
        }

        /* ================= SIGNATURES =================
           15mm of empty padding above the printed line for a
           handwritten pen signature. vertical-align: bottom keeps the
           printed name + title anchored below the line. */
        .signatures { width: 100%; margin-top: 0.5mm; }
        .signatures td {
            width: 33.33%;
            padding: 7mm 8pt 0;
            vertical-align: bottom;
        }
        .sig-line {
            border-top: 0.5pt solid #0F2A44;
            padding-top: 2pt;
        }
        .sig-name {
            font-family: 'DejaVu Serif', serif;
            font-size: 9.5pt;
            font-weight: bold;
            color: #0F2A44;
            letter-spacing: 0.3pt;
        }
        .sig-title {
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            color: #6B7280;
            margin-top: 1pt;
        }

        /* No footer block — the document ends with signatures. Ref number
           and issued date already appear in the title bar and the
           student-info Issued field. Fine-print notice belongs on the
           reverse side or letterhead, not eating single-page real estate. */
    </style>
</head>
<body>
    @php
        $isPreview = $isPreview ?? false;
        $logoPath = null;
        if ($schoolSettings && $schoolSettings->school_logo && file_exists(public_path('storage/' . $schoolSettings->school_logo))) {
            $logoPath = $isPreview ? asset('storage/' . $schoolSettings->school_logo) : public_path('storage/' . $schoolSettings->school_logo);
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logoPath = $isPreview ? asset('images/logo.png') : public_path('images/logo.png');
        }
    @endphp

    {{-- Faint centred crest — heritage stationery watermark. --}}
    @if($logoPath)
        <img src="{{ $logoPath }}" class="watermark" alt="">
    @endif

    {{-- ===== HEADER ===== --}}
    @php
        $addressLine = trim(implode(', ', array_filter([
            $schoolSettings->address ?? null,
            $schoolSettings->city ?? null,
            $schoolSettings->country ?? null,
        ])));
        if (!empty($schoolSettings->postal_code)) {
            $addressLine .= ' · P.O. Box ' . $schoolSettings->postal_code;
        }
        $contactLine = implode('  ·  ', array_filter([
            ($schoolSettings->phone ?? null) ? 'Tel ' . $schoolSettings->phone : null,
            ($schoolSettings->email ?? null) ? $schoolSettings->email : null,
            ($schoolSettings->website ?? null) ? $schoolSettings->website : null,
        ]));
    @endphp
    <table class="header">
        <tr>
            <td class="logo-cell">
                @if($logoPath)
                    <img src="{{ $logoPath }}" class="logo" width="113" height="91" alt="School crest">
                @endif
            </td>
            <td>
                <div class="school-name">{{ $schoolSettings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                <div class="school-motto">&ldquo;{{ $schoolSettings->school_motto ?? 'For God and Country' }}&rdquo;</div>
                <div class="school-meta">
                    @if($addressLine !== '')
                        <span class="street">{{ $addressLine }}</span><br>
                    @endif
                    @if($contactLine !== '')
                        {{ $contactLine }}
                    @endif
                </div>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    {{-- ===== TITLE BAR ===== --}}
    <table class="title-bar">
        <tr>
            <td class="tag" width="30%">Confidential</td>
            <td class="title">End of {{ $term->name ?? 'Term' }} Report &mdash; {{ $academicYear->name ?? $year }}</td>
            <td class="ref" width="30%">Ref&nbsp; {{ $reportRef ?? '—' }}</td>
        </tr>
    </table>

    {{-- ===== STUDENT INFO ===== --}}
    <table class="student-info">
        <tr>
            <td colspan="2" width="52%">
                <span class="info-label">Pupil</span>
                <span class="info-value">{{ $student->name }}</span>
            </td>
            <td>
                <span class="info-label">Pupil ID</span>
                <span class="info-value">{{ $student->student_id_number ?? '—' }}</span>
            </td>
            <td>
                <span class="info-label">Sex</span>
                <span class="info-value">{{ ucfirst($student->gender ?? '—') }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="info-label">Class</span>
                <span class="info-value">
                    @if($student->classSection && $student->classSection->grade)
                        {{ $student->classSection->grade->name }} &mdash; {{ $student->classSection->name }}
                    @else
                        —
                    @endif
                </span>
            </td>
            <td>
                <span class="info-label">Class Teacher</span>
                <span class="info-value">{{ $classTeacherName ?? '—' }}</span>
            </td>
            <td>
                <span class="info-label">Issued</span>
                <span class="info-value">{{ $generatedAt->format('d M Y') }}</span>
            </td>
        </tr>
    </table>

    {{-- ===== RESULTS TABLE ===== --}}
    @php
        $subjects = $resultsData['subjects'] ?? [];
    @endphp
    <table class="results">
        <thead>
            <tr>
                <th class="subj" width="44%">Subject</th>
                <th width="12%">Marks</th>
                <th width="12%">Grade</th>
                <th width="32%">Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjects as $index => $subject)
                <tr>
                    <td class="subj">{{ $subject['subject_name'] }}</td>
                    <td class="marks">{{ $subject['combined'] !== null ? number_format($subject['combined'], 0) : '—' }}</td>
                    <td class="grade">{{ (isset($subject['grade']) && $subject['grade'] !== 'N/A') ? $subject['grade'] : '—' }}</td>
                    <td>{{ $subject['remark'] ?? '—' }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="4">No results have been recorded for this term.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===== SUMMARY ===== --}}
    @php
        $combined = $resultsData['combined'] ?? ['average' => 0, 'total' => 0, 'subjects_count' => 0];
        $position = $resultsData['position'] ?? ['position' => null, 'total' => 0];
        $overallGrade = $resultsData['overall_grade'] ?? null;
        $average = $combined['average'] ?? 0;

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
        if ($average >= 80)       { $category = 'excellent'; }
        elseif ($average >= 65)   { $category = 'very_good'; }
        elseif ($average >= 50)   { $category = 'good'; }
        elseif ($average >= 40)   { $category = 'average'; }
        else                      { $category = 'below_average'; }
        $autoClassTeacherComment = $classTeacherComments[$category][array_rand($classTeacherComments[$category])];
        $autoHeadTeacherComment  = $headTeacherComments[$category][array_rand($headTeacherComments[$category])];
    @endphp
    <table class="summary">
        <tr>
            <td>
                <div class="value">{{ number_format($average, 1) }}<span class="unit">%</span></div>
                <div class="label">Average</div>
            </td>
            <td>
                <div class="value">{{ number_format($combined['total'], 0) }}<span class="unit"> / {{ $combined['subjects_count'] * 100 }}</span></div>
                <div class="label">Total Marks</div>
            </td>
            <td>
                <div class="value">
                    @if($position['position'])
                        {{ $position['position'] }}<span class="unit"> / {{ $position['total'] }}</span>
                    @else
                        —
                    @endif
                </div>
                <div class="label">Position in Class</div>
            </td>
            <td>
                <div class="value">{{ $overallGrade ? $overallGrade['grade'] : '—' }}</div>
                <div class="label">Overall Grade</div>
            </td>
        </tr>
    </table>

    {{-- ===== ATTENDANCE ===== --}}
    @if(!empty($attendance) && ($attendance['total'] ?? 0) > 0)
        <table class="attendance">
            <tr>
                <td class="head-cell">Attendance<br>this Term</td>
                <td>
                    <div class="att-value">{{ $attendance['present'] }}<span class="unit"> / {{ $attendance['total'] }}</span></div>
                    <div class="att-label">Days Present</div>
                </td>
                <td>
                    <div class="att-value">{{ $attendance['absent'] }}</div>
                    <div class="att-label">Days Absent</div>
                </td>
                <td>
                    <div class="att-value">{{ $attendance['sick'] + $attendance['excused'] }}</div>
                    <div class="att-label">Sick / Excused</div>
                </td>
                <td class="att-rate">
                    <div class="att-value">{{ number_format($attendance['rate'] ?? 0, 1) }}<span class="unit">%</span></div>
                    <div class="att-label">Attendance Rate</div>
                </td>
            </tr>
        </table>
    @endif

    {{-- ===== LEGEND ===== --}}
    @if($gradingScale)
        <div class="legend">
            <span class="head">Grading Scale</span>
            @foreach($gradingScale->items as $item)
                <strong>{{ $item->grade }}</strong> {{ $item->min_marks }}&ndash;{{ $item->max_marks }}%@if(!$loop->last) &nbsp;·&nbsp; @endif
            @endforeach
        </div>
    @endif

    {{-- ===== COMMENTS ===== --}}
    <table class="comments">
        <tr>
            <td>
                <div class="comment-eyebrow">Class Teacher's Remarks</div>
                <div class="comment-body">
                    {{ ($comments && $comments->class_teacher_comment) ? $comments->class_teacher_comment : $autoClassTeacherComment }}
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="comment-eyebrow">Head Teacher's Remarks</div>
                <div class="comment-body">
                    {{ ($comments && $comments->head_teacher_comment) ? $comments->head_teacher_comment : $autoHeadTeacherComment }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ===== SIGNATURES ===== --}}
    @php
        $__isSecondary = $student->classSection && $student->classSection->grade
            && \App\Models\GradingScale::determineGradeLevelFromGrade($student->classSection->grade) === 'secondary';
        $__headTeacherName = $__isSecondary ? 'Happy Simutowe' : 'Sylvester Lupando';
    @endphp
    <table class="signatures">
        <tr>
            <td>
                <div class="sig-line">
                    <div class="sig-name">{{ $classTeacherName ?? '—' }}</div>
                    <div class="sig-title">Class Teacher</div>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    <div class="sig-name">{{ $__headTeacherName }}</div>
                    <div class="sig-title">Head Teacher</div>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    <div class="sig-name">Blessmore Mulenga</div>
                    <div class="sig-title">School Principal</div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
