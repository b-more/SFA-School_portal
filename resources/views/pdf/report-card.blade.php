<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $student->name }}</title>
    <style>
        /* ============================================================
           REPORT CARD — Corporate stationery style
           Palette: midnight navy #0F2A44 · oxblood #8B1A1A · parchment #F5EFE0
           Type: DejaVu Sans (single family) with disciplined weight +
                 letter-spacing to create ceremonial hierarchy.
           ============================================================ */

        @page {
            size: A4 portrait;
            margin: 12mm 14mm 10mm 14mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #111827;
            background: #ffffff;
        }

        /* Faint school crest behind the whole page — the one bold move,
           printed at ~6% opacity so it reads as institutional stationery
           rather than decoration. */
        .watermark {
            position: absolute;
            top: 105mm;
            left: 40mm;
            width: 110mm;
            height: auto;
            opacity: 0.035;
            z-index: -1;
        }

        /* ================= HEADER ================= */
        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .header td {
            vertical-align: middle;
            padding: 0;
        }
        .header .logo-cell {
            width: 30mm;
            padding-right: 6mm;
        }
        .header .logo-img {
            width: 26mm;
            height: auto;
        }
        .header .text-cell {
            text-align: left;
        }
        .school-name {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #0F2A44;
            line-height: 1.1;
        }
        .school-motto {
            font-size: 9.5px;
            font-style: italic;
            color: #8B1A1A;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }
        .school-meta {
            font-size: 8.5px;
            color: #4B5563;
            margin-top: 5px;
            line-height: 1.55;
        }

        /* Double rule under header — oxblood over navy — a heritage
           stationery tell that costs almost nothing visually. */
        .double-rule {
            border-top: 2.5px solid #8B1A1A;
            border-bottom: 0.75px solid #0F2A44;
            height: 3px;
            margin: 8px 0 0;
            font-size: 0;
            line-height: 0;
        }

        /* ================= REPORT TITLE ================= */
        .report-title {
            text-align: center;
            padding: 9px 0 8px;
            margin-top: 6px;
            background: #F5EFE0;
            font-size: 11.5px;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #0F2A44;
            border-top: 0.5px solid #C9B98E;
            border-bottom: 0.5px solid #C9B98E;
        }

        /* ================= STUDENT INFO ================= */
        .student-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .student-info td {
            padding: 6px 10px 6px 0;
            vertical-align: bottom;
            font-size: 10px;
        }
        .info-label {
            display: block;
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #6B7280;
            margin-bottom: 2px;
        }
        .info-value {
            display: block;
            font-size: 11px;
            font-weight: bold;
            color: #0F2A44;
            border-bottom: 0.75px solid #0F2A44;
            padding-bottom: 3px;
            padding-right: 12px;
            min-height: 14px;
        }

        /* ================= RESULTS TABLE ================= */
        .results {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .results thead th {
            background: #0F2A44;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 8px 8px;
            text-align: center;
            border: 0.5px solid #0F2A44;
        }
        .results thead th.subj { text-align: left; padding-left: 12px; }
        .results tbody td {
            padding: 9px 8px;
            font-size: 10px;
            border: 0.5px solid #D6D3D1;
            color: #111827;
            text-align: center;
        }
        .results tbody td.subj {
            text-align: left;
            padding-left: 12px;
            font-weight: bold;
            color: #0F2A44;
        }
        .results tbody td.no {
            font-family: 'DejaVu Sans', sans-serif;
            font-weight: normal;
            color: #6B7280;
            width: 8%;
        }
        .results tbody tr:nth-child(even) td { background: #FBF7EC; }
        .results .marks { font-weight: bold; font-size: 11px; }
        .results .grade { font-weight: bold; font-size: 11px; color: #0F2A44; }
        .empty-row td {
            padding: 26px 12px !important;
            text-align: center !important;
            color: #9CA3AF;
            font-style: italic;
            background: #ffffff !important;
        }

        /* ================= SUMMARY ================= */
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .summary td {
            width: 25%;
            padding: 12px 6px;
            text-align: center;
            border: 0.5px solid #0F2A44;
            background: #ffffff;
        }
        .summary .value {
            font-size: 18px;
            font-weight: bold;
            color: #0F2A44;
            letter-spacing: 0.5px;
            line-height: 1;
        }
        .summary .value .unit {
            font-size: 10px;
            color: #6B7280;
            font-weight: normal;
            letter-spacing: 0;
        }
        .summary .label {
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #6B7280;
            margin-top: 6px;
        }

        /* ================= GRADING LEGEND ================= */
        .legend {
            padding: 6px 10px;
            margin-top: 8px;
            background: #F5EFE0;
            border-left: 3px solid #8B1A1A;
            font-size: 8.5px;
            color: #4B5563;
            line-height: 1.5;
        }
        .legend .head {
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #0F2A44;
            margin-right: 4px;
        }

        /* ================= COMMENTS ================= */
        .comments {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .comments td {
            padding: 12px 14px 18px;
            border: 0.5px solid #0F2A44;
            vertical-align: top;
            background: #ffffff;
            height: 30mm;
        }
        .comment-eyebrow {
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #8B1A1A;
            border-bottom: 0.5px solid #E5D9BE;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .comment-body {
            font-size: 10px;
            color: #1F2937;
            line-height: 1.55;
        }

        /* ================= SIGNATURES ================= */
        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        .signatures td {
            width: 33.33%;
            padding: 0 8px;
            vertical-align: bottom;
            height: 30mm;
        }
        .sig-line {
            border-top: 0.75px solid #0F2A44;
            padding-top: 6px;
        }
        .sig-name {
            font-size: 10.5px;
            font-weight: bold;
            color: #0F2A44;
            letter-spacing: 0.3px;
        }
        .sig-title {
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #6B7280;
            margin-top: 2px;
        }

        /* ================= FOOTER ================= */
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 0.5px solid #D6D3D1;
            text-align: center;
        }
        .footer .tagline {
            font-size: 9px;
            font-style: italic;
            color: #8B1A1A;
            letter-spacing: 0.5px;
        }
        .footer .fine {
            font-size: 7.5px;
            color: #9CA3AF;
            margin-top: 3px;
            letter-spacing: 0.3px;
        }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
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

    {{-- ===== HEADER: logo (natural aspect) + school info ===== --}}
    <table class="header">
        <tr>
            <td class="logo-cell">
                @if($logoPath)
                    <img src="{{ $logoPath }}" class="logo-img" alt="School crest">
                @endif
            </td>
            <td class="text-cell">
                <div class="school-name">{{ $schoolSettings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                @if($schoolSettings && $schoolSettings->school_motto)
                    <div class="school-motto">{{ $schoolSettings->school_motto }}</div>
                @endif
                <div class="school-meta">
                    @php
                        $addressParts = array_filter([
                            $schoolSettings->address ?? null,
                            $schoolSettings->city ?? null,
                            $schoolSettings->state_province ?? null,
                            $schoolSettings->country ?? null,
                        ]);
                        $contactParts = array_filter([
                            ($schoolSettings->phone ?? null) ? 'Tel ' . $schoolSettings->phone : null,
                            ($schoolSettings->email ?? null) ? $schoolSettings->email : null,
                            ($schoolSettings->website ?? null) ? $schoolSettings->website : null,
                        ]);
                    @endphp
                    @if(!empty($addressParts))
                        {{ implode(', ', $addressParts) }}@if(!empty($schoolSettings->postal_code)) &middot; P.O. Box {{ $schoolSettings->postal_code }}@endif<br>
                    @endif
                    @if(!empty($contactParts))
                        {{ implode('  ·  ', $contactParts) }}
                    @endif
                </div>
            </td>
        </tr>
    </table>
    <div class="double-rule"></div>

    {{-- ===== REPORT TITLE ===== --}}
    <div class="report-title">
        End of {{ $term->name ?? 'Term' }} Report &mdash; {{ $academicYear->name ?? $year }}
    </div>

    {{-- ===== STUDENT INFORMATION ===== --}}
    <table class="student-info">
        <tr>
            <td colspan="2" style="width:52%">
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

        <!-- ===== RESULTS TABLE ===== -->
        @php
            $subjects = $resultsData['subjects'] ?? [];
        @endphp

        <table class="results">
            <thead>
                <tr>
                    <th class="subj" style="width: 44%;">Subject</th>
                    <th style="width: 12%;">Marks</th>
                    <th style="width: 12%;">Grade</th>
                    <th style="width: 32%;">Remark</th>
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
                    <div class="label">Class Position</div>
                </td>
                <td>
                    <div class="value">
                        {{ $overallGrade ? $overallGrade['grade'] : '—' }}
                    </div>
                    <div class="label">Overall Grade</div>
                </td>
            </tr>
        </table>

        {{-- ===== GRADING LEGEND ===== --}}
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

        {{-- ===== FOOTER ===== --}}
        <div class="footer">
            <div class="tagline">Nurturing Excellence, Inspiring the Future.</div>
            <div class="fine">
                Issued {{ $generatedAt->format('d F Y') }} &middot; This is a computer-generated document; a facsimile signature is not required for authenticity.
            </div>
        </div>
</body>
</html>
