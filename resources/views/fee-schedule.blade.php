<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Schedule — St. Francis of Assisi Private School</title>
    <style>
        @page { margin: 20mm 18mm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            font-size: 11pt;
            line-height: 1.45;
        }
        .header {
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-inner { display: table; width: 100%; }
        .header-logo { display: table-cell; width: 90px; vertical-align: middle; }
        .header-logo img { width: 80px; height: auto; }
        .header-text { display: table-cell; vertical-align: middle; padding-left: 12px; }
        .school-name { font-size: 20pt; font-weight: bold; color: #1e3a5f; margin: 0; }
        .school-tag  { font-size: 10pt; color: #6b7280; margin: 4px 0 0; }
        .doc-title {
            font-size: 16pt;
            font-weight: bold;
            color: #b91c1c;
            text-align: center;
            margin: 20px 0 4px;
            letter-spacing: 0.5px;
        }
        .doc-sub {
            text-align: center;
            font-size: 11pt;
            color: #4b5563;
            margin: 0 0 24px;
        }
        h2 {
            font-size: 13pt;
            color: #1e3a5f;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin: 22px 0 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 18px;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f8fafc;
            color: #1e3a5f;
            font-weight: 600;
            font-size: 10.5pt;
        }
        td.amount { text-align: right; font-weight: 600; }
        td.section-name { font-weight: 600; }
        .note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px 14px;
            margin: 12px 0;
            font-size: 10pt;
            color: #78350f;
        }
        .footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #6b7280;
            text-align: center;
            line-height: 1.6;
        }
        .footer strong { color: #1e3a5f; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-inner">
            @if($logoSrc)
                <div class="header-logo"><img src="{{ $logoSrc }}"></div>
            @endif
            <div class="header-text">
                <p class="school-name">{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</p>
                <p class="school-tag">{{ $settings->address ?? 'Plot No 1310/4, East Kamenza, Chililabombwe, Zambia' }}</p>
                <p class="school-tag">
                    {{ $settings->phone ?? '+260 972 266 217' }}
                    &nbsp;·&nbsp;
                    {{ $settings->email ?? 'info@stfrancisofassisizm.com' }}
                </p>
            </div>
        </div>
    </div>

    <p class="doc-title">SCHOOL FEE SCHEDULE</p>
    <p class="doc-sub">{{ $data['term'] ?? '' }}{{ $data['term'] ? ', ' : '' }}{{ $data['academic_year'] ?? '' }}</p>

    {{-- Tuition per section --}}
    @if(!empty($data['sections']))
        <h2>Tuition Fees</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 60%">Section</th>
                    <th style="width: 25%">Grades</th>
                    <th style="width: 15%; text-align: right;">Term Fee</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['sections'] as $s)
                    @php
                        $lc = strtolower($s['section']);
                        $range = str_contains($lc, 'early')    ? 'ECE'
                               : (str_contains($lc, 'primary') ? 'Grade 1 – 7'
                               : (str_contains($lc, 'second')  ? 'Form 1 – Grade 12'
                               : ''));
                    @endphp
                    <tr>
                        <td class="section-name">{{ $s['section'] }}</td>
                        <td>{{ $range }}</td>
                        <td class="amount">K{{ number_format($s['basic'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Per-grade breakdown (if the school ever adds grade-level rows) --}}
    @if(!empty($data['grades']))
        <h2>Grade-Level Adjustments</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 70%">Grade</th>
                    <th style="width: 15%">Section</th>
                    <th style="width: 15%; text-align: right;">Term Fee</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['grades'] as $g)
                    <tr>
                        <td class="section-name">{{ $g['grade'] }}</td>
                        <td>{{ $g['section'] ?? '' }}</td>
                        <td class="amount">K{{ number_format($g['basic'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Extras --}}
    @if(!empty($data['extras']))
        <h2>Additional Charges (all pupils)</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 60%">Charge</th>
                    <th style="width: 25%">Frequency</th>
                    <th style="width: 15%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['extras'] as $e)
                    <tr>
                        <td class="section-name">{{ $e['name'] }}</td>
                        <td>{{ ucfirst($e['frequency'] ?? '') }}</td>
                        <td class="amount">K{{ number_format($e['amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Bus --}}
    @if(!empty($data['bus']))
        <h2>School Bus</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 60%">Service</th>
                    <th style="width: 25%">Frequency</th>
                    <th style="width: 15%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="section-name">School Bus (all routes)</td>
                    <td>{{ ucfirst($data['bus']['frequency']) }}</td>
                    <td class="amount">K{{ number_format($data['bus']['flat_rate'], 2) }}</td>
                </tr>
            </tbody>
        </table>
        @if(!empty($data['bus']['note']))
            <p style="font-size: 10pt; color: #4b5563; margin: -6px 0 12px;">{{ $data['bus']['note'] }}</p>
        @endif
    @endif

    <div class="note">
        <strong>Please note:</strong> Fees are payable at the start of every term. Bank details and mobile-money payment
        options are available at the school office, on our WhatsApp bot, and in the parent app. For any queries or
        discussion of concessions, kindly contact the office.
    </div>

    <div class="footer">
        <p>
            <strong>{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</strong><br>
            {{ $settings->address ?? 'Plot No 1310/4, East Kamenza, Chililabombwe, Zambia' }}<br>
            Office: {{ $settings->phone ?? '+260 972 266 217' }}
            &nbsp;·&nbsp;
            Email: {{ $settings->email ?? 'info@stfrancisofassisizm.com' }}
            <br><br>
            <em>Generated {{ $generatedAt }} · Prices are indicative and subject to change.</em>
        </p>
    </div>

</body>
</html>
