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

        /* Faint centred crest as a page-body watermark. */
        .watermark {
            position: absolute;
            top: 70mm;
            left: 55mm;
            width: 180mm;
            opacity: 0.04;
            z-index: 0;
        }

        /* ================= LETTERHEAD =================
           Crest at left, school name + motto + address stacked to right,
           closed by a heritage double rule (red over navy). Same lockup
           as the report card so every document reads as one system. */
        .letterhead {
            width: 100%;
            margin: 0 0 4pt;
        }
        .letterhead td { vertical-align: middle; padding: 0; }
        .letterhead .logo-cell { width: 28mm; padding-right: 6mm; }
        .letterhead .logo {
            display: block;
            height: 22mm;
            width: 28mm;
        }
        .school-name {
            font-family: 'DejaVu Serif', serif;
            font-size: 15pt;
            font-weight: bold;
            color: #0F2A44;
            letter-spacing: 0.6pt;
            line-height: 1.15;
        }
        .school-motto {
            font-family: 'DejaVu Serif', serif;
            font-size: 9pt;
            font-style: italic;
            color: #8B1A1A;
            letter-spacing: 0.4pt;
            margin: 2pt 0 4pt;
        }
        .school-meta {
            font-size: 8pt;
            color: #4B5563;
            line-height: 1.4;
        }
        .school-meta .street { color: #1F2937; font-weight: bold; }

        .rule {
            border-top: 2pt solid #8B1A1A;
            border-bottom: 0.5pt solid #0F2A44;
            height: 3pt;
            margin: 3pt 0 6pt;
        }

        .doc-title {
            font-family: 'DejaVu Serif', serif;
            font-size: 12pt;
            font-weight: bold;
            color: #0F2A44;
            letter-spacing: 0.4pt;
        }
        .doc-meta {
            font-size: 7.5pt;
            color: #6B7280;
            margin-top: 1pt;
        }
        .doc-strip {
            width: 100%;
            margin-bottom: 6pt;
        }
        .doc-strip td { vertical-align: bottom; padding: 0; }
        .doc-strip .right { text-align: right; }

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
    @php
        $ss = $schoolSettings ?? null;
        $logoPath = null;
        if ($ss && $ss->school_logo && file_exists(public_path('storage/' . $ss->school_logo))) {
            $logoPath = public_path('storage/' . $ss->school_logo);
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logoPath = public_path('images/logo.png');
        }
        $addressLine = trim(implode(', ', array_filter([
            $ss->address ?? null,
            $ss->city    ?? null,
            $ss->country ?? null,
        ])));
        if ($ss && ! empty($ss->postal_code)) {
            $addressLine .= ' · P.O. Box ' . $ss->postal_code;
        }
        $contactLine = implode('  ·  ', array_filter([
            ($ss->phone   ?? null) ? 'Tel ' . $ss->phone : null,
            ($ss->email   ?? null) ? $ss->email          : null,
            ($ss->website ?? null) ? $ss->website        : null,
        ]));
    @endphp

    @if($logoPath)
        <img src="{{ $logoPath }}" class="watermark" alt="">
    @endif

    <table class="letterhead">
        <tr>
            <td class="logo-cell">
                @if($logoPath)
                    <img src="{{ $logoPath }}" class="logo" alt="School crest">
                @endif
            </td>
            <td>
                <div class="school-name">{{ $ss->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                <div class="school-motto">&ldquo;{{ $ss->school_motto ?? 'For God and Country' }}&rdquo;</div>
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

    <table class="doc-strip">
        <tr>
            <td>
                <div class="doc-title">Termly Fee Collection Tracker — {{ $d['year_label'] }}</div>
                <div class="doc-meta">Live from the ledger · Actuals from completed payment transactions</div>
            </td>
            <td class="right">
                <div class="doc-meta">Generated {{ $d['generated_at']->format('d M Y · H:i') }}</div>
                <div class="doc-meta">Ref&nbsp;SFA/FCT/{{ $d['year_label'] }}</div>
            </td>
        </tr>
    </table>

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
                            @if(($r['unbilled'] ?? 0) > 0)
                                <span class="warn">⚠ {{ $r['unbilled'] }} unbilled</span>
                            @endif
                            @if(($r['anomalies'] ?? 0) > 0)
                                <span class="warn">⚠ {{ $r['anomalies'] }} on non-standard fee</span>
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

    <table class="letterhead">
        <tr>
            <td class="logo-cell">
                @if($logoPath)
                    <img src="{{ $logoPath }}" class="logo" alt="School crest">
                @endif
            </td>
            <td>
                <div class="school-name">{{ $ss->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                <div class="school-motto">&ldquo;{{ $ss->school_motto ?? 'For God and Country' }}&rdquo;</div>
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

    <div class="doc-title">School Population by Class — {{ $d['year_label'] }}</div>
    <div class="doc-meta" style="margin-bottom:6pt;">Active enrolment, ordered by section</div>

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
        Fee per Pupil = the published <em>fee_structures.basic_fee</em> that most pupils in the section
        are billed against for that term. Expected = Pupils × Fee per Pupil.
        Actual = the authoritative <em>student_fees.amount_paid</em> column — the same figure used to compute each pupil's balance,
        totalling every payment source (mobile money, office cash, bank transfer, historical imports).
        Salary bills come from the <em>payrolls</em> ledger, bucketed by which term window each month falls into.
        Flags: <span class="warn">⚠ unbilled</span> = pupils on the roll with no StudentFee row this term;
        <span class="warn">⚠ non-standard fee</span> = pupils billed against a different structure than the section's majority.
    </div>
</body>
</html>
