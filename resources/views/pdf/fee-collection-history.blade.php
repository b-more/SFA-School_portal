<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $schoolName }} - Annual Fee Collection History</title>
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
            padding: 12px;
            text-align: center;
            border: 2px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .summary-card.primary {
            background-color: #eff6ff;
            border-color: #2563eb;
        }
        .summary-card.success {
            background-color: #d1fae5;
            border-color: #059669;
        }
        .summary-card.danger {
            background-color: #fee2e2;
            border-color: #dc2626;
        }
        .summary-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 3px;
        }
        .summary-subtitle {
            font-size: 7px;
            color: #9ca3af;
        }
        .section-title {
            background-color: #1e40af;
            color: white;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #1e40af;
            color: white;
            padding: 5px 4px;
            text-align: left;
            font-size: 7px;
            font-weight: bold;
        }
        .data-table td {
            padding: 4px;
            border: 1px solid #e5e7eb;
            font-size: 7px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .data-table tfoot td {
            background-color: #1e40af;
            color: white;
            font-weight: bold;
            padding: 6px 4px;
        }
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .grade-table th {
            background-color: #eff6ff;
            color: #1e40af;
            padding: 6px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #bfdbfe;
        }
        .grade-table td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            font-size: 8px;
        }
        .grade-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .grade-table tfoot td {
            background-color: #1e40af;
            color: white;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $schoolName }}</h1>
        <h2>Annual Fee Collection Report &mdash; {{ $academicYear->name }}</h2>
        <p>Generated on {{ $reportDate }}</p>
    </div>

    {{-- Summary Cards --}}
    @php
        $collectionRate = $summary['total_expected'] > 0
            ? round(($summary['total_paid'] / $summary['total_expected']) * 100, 1)
            : 0;
    @endphp
    <div class="summary-grid">
        <div class="summary-card primary">
            <div class="summary-label">Total Students</div>
            <div class="summary-value">{{ $summary['total_students'] }}</div>
            <div class="summary-subtitle">with fee records</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Expected</div>
            <div class="summary-value">ZMW {{ number_format($summary['total_expected'], 2) }}</div>
            <div class="summary-subtitle">across all terms</div>
        </div>
        <div class="summary-card success">
            <div class="summary-label">Total Collected</div>
            <div class="summary-value" style="color: #059669;">ZMW {{ number_format($summary['total_paid'], 2) }}</div>
            <div class="summary-subtitle">{{ $collectionRate }}% collection rate</div>
        </div>
        <div class="summary-card danger">
            <div class="summary-label">Outstanding</div>
            <div class="summary-value" style="color: #dc2626;">ZMW {{ number_format($summary['total_balance'], 2) }}</div>
            <div class="summary-subtitle">pending payment</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Collection Rate</div>
            <div class="summary-value">{{ $collectionRate }}%</div>
            <div class="summary-subtitle">
                @if($collectionRate >= 80)
                    Excellent
                @elseif($collectionRate >= 60)
                    Good
                @elseif($collectionRate >= 40)
                    Fair
                @else
                    Needs Attention
                @endif
            </div>
        </div>
    </div>

    {{-- Student Table --}}
    <div class="section-title">Student Fee Collection Details</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">Student ID</th>
                <th style="width: 16%;">Student Name</th>
                <th style="width: 8%;">Grade</th>
                @foreach($terms as $term)
                    <th class="text-right">{{ $term->name }}</th>
                @endforeach
                <th class="text-right">Expected</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Balance</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php
                    $totalExpected = $student->fees->sum(fn($f) => (float)($f->feeStructure->total_fee ?? 0));
                    $totalPaid = $student->fees->sum(fn($f) => (float)$f->amount_paid);
                    $totalBalance = $student->fees->sum(fn($f) => (float)$f->balance);
                    $allCleared = $student->fees->every(fn($f) => in_array($f->payment_status, ['paid', 'overpaid']));
                    $anyPaid = $student->fees->contains(fn($f) => (float)$f->amount_paid > 0);
                    $status = $student->fees->isEmpty() ? 'N/A' : ($allCleared ? 'Cleared' : ($anyPaid ? 'Partial' : 'Unpaid'));
                @endphp
                <tr>
                    <td>{{ $student->student_id_number }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->grade->name ?? 'N/A' }}</td>
                    @foreach($terms as $term)
                        @php $fee = $student->fees->firstWhere('term_id', $term->id); @endphp
                        <td class="text-right">{{ $fee ? number_format((float)$fee->amount_paid, 2) : '-' }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($totalExpected, 2) }}</td>
                    <td class="text-right" style="color: #059669;">{{ number_format($totalPaid, 2) }}</td>
                    <td class="text-right" style="color: {{ $totalBalance > 0 ? '#dc2626' : '#059669' }};">{{ number_format($totalBalance, 2) }}</td>
                    <td>
                        @if($status === 'Cleared')
                            <span class="badge badge-success">Cleared</span>
                        @elseif($status === 'Partial')
                            <span class="badge badge-warning">Partial</span>
                        @elseif($status === 'Unpaid')
                            <span class="badge badge-danger">Unpaid</span>
                        @else
                            <span class="badge badge-info">N/A</span>
                        @endif
                    </td>
                </tr>

                @if(($index + 1) % 25 === 0 && $index + 1 < $students->count())
                    </tbody>
                </table>
                <div class="page-break"></div>

                <div class="header">
                    <h1>{{ $schoolName }}</h1>
                    <h2>Annual Fee Collection Report &mdash; {{ $academicYear->name }} (Continued)</h2>
                    <p>Page {{ ceil(($index + 1) / 25) + 1 }}</p>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Student ID</th>
                            <th style="width: 16%;">Student Name</th>
                            <th style="width: 8%;">Grade</th>
                            @foreach($terms as $term)
                                <th class="text-right">{{ $term->name }}</th>
                            @endforeach
                            <th class="text-right">Expected</th>
                            <th class="text-right">Paid</th>
                            <th class="text-right">Balance</th>
                            <th style="width: 7%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>TOTALS:</strong></td>
                @foreach($terms as $term)
                    @php
                        $termTotal = $students->sum(fn($s) => (float)($s->fees->firstWhere('term_id', $term->id)?->amount_paid ?? 0));
                    @endphp
                    <td class="text-right"><strong>ZMW {{ number_format($termTotal, 2) }}</strong></td>
                @endforeach
                <td class="text-right"><strong>ZMW {{ number_format($summary['total_expected'], 2) }}</strong></td>
                <td class="text-right"><strong>ZMW {{ number_format($summary['total_paid'], 2) }}</strong></td>
                <td class="text-right"><strong>ZMW {{ number_format($summary['total_balance'], 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Grade-wise Summary --}}
    @if($students->count() > 0)
    <div class="section-title">Grade-wise Fee Collection Summary</div>

    <table class="grade-table">
        <thead>
            <tr>
                <th>Grade</th>
                <th class="text-right">Students</th>
                @foreach($terms as $term)
                    <th class="text-right">{{ $term->name }}</th>
                @endforeach
                <th class="text-right">Total Expected</th>
                <th class="text-right">Total Collected</th>
                <th class="text-right">Outstanding</th>
                <th class="text-right">Collection Rate</th>
            </tr>
        </thead>
        <tbody>
            @php
                $gradeGroups = $students->groupBy(fn($s) => $s->grade->name ?? 'Unassigned')->sortKeys();
            @endphp
            @foreach($gradeGroups as $gradeName => $gradeStudents)
                @php
                    $gradeExpected = $gradeStudents->sum(fn($s) => $s->fees->sum(fn($f) => (float)($f->feeStructure->total_fee ?? 0)));
                    $gradePaid = $gradeStudents->sum(fn($s) => $s->fees->sum(fn($f) => (float)$f->amount_paid));
                    $gradeBalance = $gradeStudents->sum(fn($s) => $s->fees->sum(fn($f) => (float)$f->balance));
                    $gradeRate = $gradeExpected > 0 ? round(($gradePaid / $gradeExpected) * 100, 1) : 0;
                @endphp
                <tr>
                    <td><strong>{{ $gradeName }}</strong></td>
                    <td class="text-right">{{ $gradeStudents->count() }}</td>
                    @foreach($terms as $term)
                        @php
                            $gradeTermPaid = $gradeStudents->sum(fn($s) => (float)($s->fees->firstWhere('term_id', $term->id)?->amount_paid ?? 0));
                        @endphp
                        <td class="text-right">ZMW {{ number_format($gradeTermPaid, 2) }}</td>
                    @endforeach
                    <td class="text-right">ZMW {{ number_format($gradeExpected, 2) }}</td>
                    <td class="text-right" style="color: #059669;">ZMW {{ number_format($gradePaid, 2) }}</td>
                    <td class="text-right" style="color: #dc2626;">ZMW {{ number_format($gradeBalance, 2) }}</td>
                    <td class="text-right">
                        <strong>{{ $gradeRate }}%</strong>
                        @if($gradeRate >= 80)
                            <span class="badge badge-success">Excellent</span>
                        @elseif($gradeRate >= 60)
                            <span class="badge badge-info">Good</span>
                        @elseif($gradeRate >= 40)
                            <span class="badge badge-warning">Fair</span>
                        @else
                            <span class="badge badge-danger">Poor</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ $summary['total_students'] }}</strong></td>
                @foreach($terms as $term)
                    @php
                        $termTotal = $students->sum(fn($s) => (float)($s->fees->firstWhere('term_id', $term->id)?->amount_paid ?? 0));
                    @endphp
                    <td class="text-right"><strong>ZMW {{ number_format($termTotal, 2) }}</strong></td>
                @endforeach
                <td class="text-right"><strong>ZMW {{ number_format($summary['total_expected'], 2) }}</strong></td>
                <td class="text-right"><strong>ZMW {{ number_format($summary['total_paid'], 2) }}</strong></td>
                <td class="text-right"><strong>ZMW {{ number_format($summary['total_balance'], 2) }}</strong></td>
                <td class="text-right"><strong>{{ $collectionRate }}%</strong></td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p><strong>{{ $schoolName }}</strong></p>
        <p>Plot No 1310/4 East Kamenza, Chililabombwe, Zambia</p>
        <p>Phone: +260 972 266 217 | Email: info@stfrancisofassisizm.com</p>
        <p style="margin-top: 8px;">Generated on {{ $reportDate }} | Total Records: {{ $students->count() }} | Confidential - For Internal Use Only</p>
    </div>
</body>
</html>
