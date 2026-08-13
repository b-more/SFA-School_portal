<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Collection Tracker — {{ $d['year_label'] }}</title>
    <style>
        @page { margin: 12mm 10mm 12mm 10mm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5pt;
            color: #1F2937;
            margin: 0;
        }
        .head {
            border-bottom: 1.5pt solid #0F2A44;
            padding-bottom: 4pt;
            margin-bottom: 6pt;
        }
        .head .name {
            font-family: 'DejaVu Serif', serif;
            font-size: 13pt;
            font-weight: bold;
            color: #0F2A44;
            letter-spacing: 0.4pt;
        }
        .head .sub {
            font-style: italic;
            color: #7f1d1d;
            font-size: 8.5pt;
        }
        .head .title {
            font-family: 'DejaVu Serif', serif;
            font-size: 11pt;
            font-weight: bold;
            color: #0F2A44;
            margin-top: 2pt;
        }
        .meta {
            font-size: 7.5pt;
            color: #6B7280;
            text-align: right;
            margin-top: 1pt;
        }

        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 6pt; }
        table.grid th, table.grid td {
            border: 0.5pt solid #C6BFA5;
            padding: 2pt 4pt;
            text-align: right;
        }
        table.grid th.subj, table.grid td.subj { text-align: left; }
        table.grid thead th {
            background: #0F2A44;
            color: #fff;
            font-weight: bold;
            letter-spacing: 0.3pt;
            font-size: 8pt;
        }
        table.grid tr.section-band td {
            background: #0F2A44;
            color: #fff;
            font-weight: bold;
            letter-spacing: 0.5pt;
            text-align: left;
        }
        table.grid tr.total td { background: #E8E0C8; font-weight: bold; }
        table.grid tr.annual td { background: #DCE4EF; font-weight: bold; }
        .warn { color: #7f1d1d; font-size: 7pt; }

        h2 {
            font-family: 'DejaVu Serif', serif;
            font-size: 10pt;
            color: #0F2A44;
            margin: 8pt 0 3pt;
            border-left: 3pt solid #7f1d1d;
            padding-left: 5pt;
        }
        .footnote {
            font-size: 7pt;
            color: #6B7280;
            margin-top: 4pt;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="head">
        <div class="name">ST. FRANCIS OF ASSISI PRIVATE SCHOOL</div>
        <div class="sub">"For God and Country"</div>
        <div class="title">Termly Fee Collection Tracker — {{ $d['year_label'] }}</div>
        <div class="meta">
            Generated {{ $d['generated_at']->format('d M Y H:i') }} · Actuals from completed payment transactions
        </div>
    </div>

    @foreach($d['terms'] as $term)
        @php $rows = $d['by_term'][$term->id]; $t = $d['term_totals'][$term->id]; @endphp
        <h2>{{ strtoupper($term->name) }} — {{ $d['year_label'] }}</h2>
        <table class="grid">
            <thead>
                <tr>
                    <th class="subj" width="16%">Section</th>
                    <th width="8%">Pupils</th>
                    <th width="12%">Fee / Pupil</th>
                    <th width="13%">Expected</th>
                    <th width="13%">Actual</th>
                    <th width="13%">Shortfall</th>
                    <th width="9%">% Coll.</th>
                    <th width="9%">% Loss</th>
                </tr>
            </thead>
            <tbody>
                @foreach($d['sections'] as $section)
                    @php $r = $rows[$section]; @endphp
                    <tr>
                        <td class="subj">
                            {{ $section }}
                            @if(($r['zero_fee_rows'] ?? 0) > 0)
                                <span class="warn">⚠ {{ $r['zero_fee_rows'] }} K0 fee row(s)</span>
                            @endif
                        </td>
                        <td>{{ number_format($r['pupils']) }}</td>
                        <td>{{ number_format($r['fee_per'], 0) }}</td>
                        <td>{{ number_format($r['expected'], 0) }}</td>
                        <td>{{ number_format($r['actual'], 0) }}</td>
                        <td>{{ number_format($r['shortfall'], 0) }}</td>
                        <td>{{ number_format($r['pct_collected'], 1) }}%</td>
                        <td>{{ number_format($r['pct_loss'], 1) }}%</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td class="subj">TOTAL</td>
                    <td>{{ number_format($t['pupils']) }}</td>
                    <td></td>
                    <td>{{ number_format($t['expected'], 0) }}</td>
                    <td>{{ number_format($t['actual'], 0) }}</td>
                    <td>{{ number_format($t['shortfall'], 0) }}</td>
                    <td>{{ number_format($t['pct_collected'], 1) }}%</td>
                    <td>{{ number_format($t['pct_loss'], 1) }}%</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <h2>Annual Summary — All Three Terms</h2>
    <table class="grid">
        <thead>
            <tr>
                <th class="subj" width="20%">Term</th>
                <th width="16%">Expected</th>
                <th width="16%">Actual</th>
                <th width="16%">Shortfall</th>
                <th width="16%">% Collected</th>
                <th width="16%">% Loss</th>
            </tr>
        </thead>
        <tbody>
            @foreach($d['terms'] as $term)
                @php $t = $d['term_totals'][$term->id]; @endphp
                <tr>
                    <td class="subj">{{ strtoupper($term->name) }}</td>
                    <td>{{ number_format($t['expected'], 0) }}</td>
                    <td>{{ number_format($t['actual'], 0) }}</td>
                    <td>{{ number_format($t['shortfall'], 0) }}</td>
                    <td>{{ number_format($t['pct_collected'], 1) }}%</td>
                    <td>{{ number_format($t['pct_loss'], 1) }}%</td>
                </tr>
            @endforeach
            <tr class="annual">
                <td class="subj">YEAR TOTAL</td>
                <td>{{ number_format($d['annual']['expected'], 0) }}</td>
                <td>{{ number_format($d['annual']['actual'], 0) }}</td>
                <td>{{ number_format($d['annual']['shortfall'], 0) }}</td>
                <td>{{ number_format($d['annual']['pct_collected'], 1) }}%</td>
                <td>{{ number_format($d['annual']['pct_loss'], 1) }}%</td>
            </tr>
        </tbody>
    </table>

    <h2>Can Collections Cover Salaries? (main expense check)</h2>
    <table class="grid">
        <thead>
            <tr>
                <th class="subj" width="10%">Term</th>
                <th width="12%">Salary Bill</th>
                <th class="subj" width="20%">Payroll Months</th>
                <th width="12%">Expected Fees</th>
                <th width="12%">Actual Fees</th>
                <th width="14%">Surplus after Salaries</th>
                <th width="10%">Sal / Expected</th>
                <th width="10%">Sal / Actual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($d['terms'] as $term)
                @php
                    $t  = $d['term_totals'][$term->id];
                    $sb = $d['salary_bill'][$term->id];
                    $sm = $d['salary_meta'][$term->id];
                @endphp
                <tr>
                    <td class="subj">{{ strtoupper($term->name) }}</td>
                    <td>{{ $sb === null ? '—' : number_format($sb, 0) }}</td>
                    <td class="subj" style="font-size:7pt; color:#6B7280;">{{ $sm ?? '—' }}</td>
                    <td>{{ number_format($t['expected'], 0) }}</td>
                    <td>{{ number_format($t['actual'], 0) }}</td>
                    <td>
                        @if($sb !== null)
                            {{ number_format($t['actual'] - $sb, 0) }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($sb !== null && $t['expected'] > 0)
                            {{ number_format(($sb / $t['expected']) * 100, 1) }}%
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($sb !== null && $t['actual'] > 0)
                            {{ number_format(($sb / $t['actual']) * 100, 1) }}%
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-before: always;"></div>

    <div class="head">
        <div class="name">SCHOOL POPULATION BY CLASS — {{ $d['year_label'] }}</div>
        <div class="sub">Active enrolment, ordered by section</div>
    </div>

    <table class="grid">
        <thead>
            <tr>
                <th class="subj" width="18%">Section</th>
                <th class="subj" width="34%">Class</th>
                <th class="subj" width="34%">Class Teacher</th>
                <th width="14%">Enrolment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($d['sections'] as $section)
                @foreach($d['population'][$section] ?? [] as $entry)
                    <tr>
                        <td class="subj">{{ $section }}</td>
                        <td class="subj">{{ $entry['class'] }}</td>
                        <td class="subj">{{ $entry['teacher'] }}</td>
                        <td>{{ number_format($entry['enrolment']) }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td></td>
                    <td class="subj" colspan="2">{{ strtoupper($section) }} TOTAL</td>
                    <td>{{ number_format($d['section_totals'][$section] ?? 0) }}</td>
                </tr>
            @endforeach
            <tr class="annual">
                <td></td>
                <td class="subj" colspan="2">SCHOOL TOTAL</td>
                <td>{{ number_format($d['school_total']) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footnote">
        Actual figures are the sum of completed payment_transactions rows (parent app + WhatsApp bot + office-recorded + QR).
        Salary figures come from the payrolls ledger, bucketed by which term window each payroll month falls into.
        Rows flagged K0 fee mean a StudentFee row was created without a fee structure — worth checking with the accountant.
    </div>
</body>
</html>
