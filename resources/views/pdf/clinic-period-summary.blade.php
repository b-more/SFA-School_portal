<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clinic — {{ $subtitle }}</title>
    <style>
        @page { margin: 12mm 12mm 12mm 12mm; }
        body  { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1F2937; margin: 0; }

        .letterhead { width:100%; margin:0 0 4pt; }
        .letterhead td { vertical-align:middle; padding:0; }
        .letterhead .logo-cell { width:26mm; padding-right:6mm; }
        .letterhead .logo { display:block; height:20mm; width:26mm; }
        .school-name  { font-family:'DejaVu Serif',serif; font-size:14pt; font-weight:bold; color:#0F2A44; letter-spacing:0.6pt; line-height:1.15; }
        .school-motto { font-family:'DejaVu Serif',serif; font-size:9pt; font-style:italic; color:#8B1A1A; letter-spacing:0.4pt; margin:2pt 0 4pt; }
        .school-meta  { font-size:8pt; color:#4B5563; line-height:1.4; }
        .rule { border-top: 2pt solid #8B1A1A; border-bottom: 0.5pt solid #0F2A44; height: 3pt; margin: 3pt 0 6pt; }

        .doc-title { font-family:'DejaVu Serif',serif; font-size:12pt; font-weight:bold; color:#0F2A44; }
        .doc-meta  { font-size:8pt; color:#6B7280; }

        h2 { font-family:'DejaVu Serif',serif; font-size:11pt; color:#0F2A44; margin: 10pt 0 3pt; border-left: 3pt solid #7f1d1d; padding-left: 5pt; }

        table.grid { width:100%; border-collapse:collapse; margin-bottom:6pt; }
        table.grid th, table.grid td { border: 0.5pt solid #C6BFA5; padding: 2pt 5pt; text-align: right; }
        table.grid th.subj, table.grid td.subj { text-align:left; }
        table.grid thead th { background:#0F2A44; color:#fff; font-weight:bold; letter-spacing:0.3pt; }

        .kpis { display:table; width:100%; margin: 4pt 0 8pt; }
        .kpi  { display:table-cell; width:25%; text-align:center; padding:6pt; border:0.5pt solid #E5D9BE; }
        .kpi .val { font-family:'DejaVu Serif',serif; font-size:16pt; font-weight:bold; color:#0F2A44; }
        .kpi .lbl { font-size:7.5pt; color:#6B7280; text-transform:uppercase; letter-spacing:0.5pt; margin-top:2pt; }

        .signature-row td { padding-top:12mm; vertical-align:bottom; }
        .sig-line { border-top:0.5pt solid #0F2A44; padding-top:2pt; }
        .sig-name { font-family:'DejaVu Serif',serif; font-size:9.5pt; font-weight:bold; color:#0F2A44; }
        .sig-title { font-size:7.5pt; letter-spacing:1.2pt; text-transform:uppercase; color:#6B7280; margin-top:1pt; }
    </style>
</head>
<body>
    @php
        $ss = $schoolSettings;
        $logoPath = null;
        if ($ss && $ss->school_logo && file_exists(public_path('storage/' . $ss->school_logo))) {
            $logoPath = public_path('storage/' . $ss->school_logo);
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logoPath = public_path('images/logo.png');
        }
        $addressLine = trim(implode(', ', array_filter([$ss->address ?? null, $ss->city ?? null, $ss->country ?? null])));
        if ($ss && ! empty($ss->postal_code)) $addressLine .= ' · P.O. Box ' . $ss->postal_code;
        $contactLine = implode('  ·  ', array_filter([
            ($ss->phone ?? null) ? 'Tel ' . $ss->phone : null,
            ($ss->email ?? null) ? $ss->email : null,
        ]));
    @endphp

    <table class="letterhead">
        <tr>
            <td class="logo-cell">@if($logoPath)<img src="{{ $logoPath }}" class="logo" alt="">@endif</td>
            <td>
                <div class="school-name">{{ $ss->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                <div class="school-motto">&ldquo;{{ $ss->school_motto ?? 'For God and Country' }}&rdquo;</div>
                <div class="school-meta">
                    @if($addressLine) <strong>{{ $addressLine }}</strong><br>@endif
                    @if($contactLine) {{ $contactLine }} @endif
                </div>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    <div class="doc-title">School Clinic · {{ $subtitle }}</div>
    <div class="doc-meta">
        {{ $d['from']->format('d M Y') }} → {{ $d['to']->format('d M Y') }}
        · Generated {{ now()->format('d M Y H:i') }}
        · Ref {{ $refCode }}
    </div>

    <div class="kpis">
        <div class="kpi"><div class="val">{{ $d['visits']['total_visits'] }}</div><div class="lbl">Total visits</div></div>
        <div class="kpi"><div class="val">{{ $d['visits']['unique_students'] }}</div><div class="lbl">Unique students</div></div>
        <div class="kpi"><div class="val">{{ $d['visits']['sick_notes'] }}</div><div class="lbl">Sick notes ({{ $d['visits']['sick_notes_pct'] }}%)</div></div>
        <div class="kpi"><div class="val">K{{ number_format($d['stock']['total_spend'], 2) }}</div><div class="lbl">Medical spend</div></div>
    </div>

    <h2>Top complaints</h2>
    <table class="grid">
        <thead><tr><th class="subj">Complaint</th><th>Visits</th><th>% of total</th></tr></thead>
        <tbody>
        @php $total = max(1, $d['visits']['total_visits']); @endphp
        @foreach($d['visits']['by_complaint']->take(10) as $name => $count)
            <tr><td class="subj">{{ $name }}</td><td>{{ $count }}</td><td>{{ round(($count/$total)*100,1) }}%</td></tr>
        @endforeach
        @if($d['visits']['by_complaint']->isEmpty())
            <tr><td class="subj" colspan="3">No visits recorded in this period.</td></tr>
        @endif
        </tbody>
    </table>

    <h2>Visits by day</h2>
    <table class="grid">
        <thead><tr><th class="subj">Date</th><th>Visits</th></tr></thead>
        <tbody>
        @foreach($d['visits']['by_day'] as $day => $c)
            <tr><td class="subj">{{ \Carbon\Carbon::parse($day)->format('D d M Y') }}</td><td>{{ $c }}</td></tr>
        @endforeach
        @if($d['visits']['by_day']->isEmpty())
            <tr><td class="subj" colspan="2">No visits recorded.</td></tr>
        @endif
        </tbody>
    </table>

    <h2>Outcomes</h2>
    <table class="grid">
        <thead><tr><th class="subj">Outcome</th><th>Count</th></tr></thead>
        <tbody>
        @foreach($d['visits']['by_outcome'] as $o => $c)
            <tr><td class="subj">{{ ucwords(str_replace('_',' ',$o)) }}</td><td>{{ $c }}</td></tr>
        @endforeach
        @if($d['visits']['by_outcome']->isEmpty())
            <tr><td class="subj" colspan="2">No outcomes recorded.</td></tr>
        @endif
        </tbody>
    </table>

    <h2>Stock — opening / in / out / closing</h2>
    <table class="grid">
        <thead>
            <tr><th class="subj">Item</th><th>Opening</th><th>In</th><th>Out</th><th>Closing</th></tr>
        </thead>
        <tbody>
        @foreach($d['stock']['movement'] as $m)
            <tr>
                <td class="subj">{{ $m['item'] }}{{ $m['low'] ? '  ⚠ low' : '' }}</td>
                <td>{{ $m['opening'] }}</td><td>{{ $m['in'] }}</td><td>{{ $m['out'] }}</td>
                <td><strong>{{ $m['closing'] }} {{ $m['unit'] }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($d['visits']['repeat_visitors']->isNotEmpty())
        <h2>Repeat visitors (≥3 visits — welfare flag)</h2>
        <table class="grid">
            <thead><tr><th class="subj">Student</th><th>Visits</th></tr></thead>
            <tbody>
            @foreach($d['visits']['repeat_visitors'] as $rv)
                <tr><td class="subj">{{ $rv->student_name }}</td><td>{{ $rv->c }}</td></tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <table class="signature-row" style="width:100%; margin-top:14pt;">
        <tr>
            <td style="width:50%;">
                <div class="sig-line"><div class="sig-name">School Clinician</div><div class="sig-title">Signature · Date</div></div>
            </td>
            <td style="width:8mm;"></td>
            <td style="width:50%;">
                <div class="sig-line"><div class="sig-name">School Principal</div><div class="sig-title">Signature · Date</div></div>
            </td>
        </tr>
    </table>

    <div style="margin-top:8pt; font-size:8pt; color:#6B7280; font-style:italic;">
        Prepared by the School Clinician for the Head Teacher / Board. Visit records live in the portal Clinic module — this is a summary only.
    </div>
</body>
</html>
