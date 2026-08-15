<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complaint Trend</title>
    @include('pdf.clinic._styles')
</head>
<body>
@include('pdf.clinic._letterhead')

<div class="kpis">
    <div class="kpi"><div class="val">{{ $d['total'] }}</div><div class="lbl">Total visits</div></div>
    <div class="kpi"><div class="val">{{ $d['peak'] }}</div><div class="lbl">Peak month</div></div>
    <div class="kpi"><div class="val">{{ $d['series']->count() }}</div><div class="lbl">Months tracked</div></div>
    <div class="kpi"><div class="val">{{ $d['complaint']?->name ?? 'All' }}</div><div class="lbl">Complaint</div></div>
</div>

<h2>Monthly visits</h2>
@foreach($d['series'] as $ym => $count)
    @php $pct = max(1, (int) round(($count / $d['peak']) * 100)); @endphp
    <div class="bar-row">
        <span class="bar-label">{{ \Carbon\Carbon::createFromFormat('Y-m', $ym)->format('M Y') }}</span>
        <span class="bar-track"><span class="bar-fill" style="width: {{ $count > 0 ? ($pct * 1.10) : 0 }}mm;"></span></span>
        <span class="bar-value">{{ $count }}</span>
    </div>
@endforeach

<div class="callout" style="margin-top:8pt;">
    <strong>Reading this trend:</strong> compare the peak months against the school calendar (term start, exam weeks, cold season).
    Sustained rises over several months warrant either a preventive campaign, a stock review, or a referral pattern check.
</div>

<table class="signature-row" style="width:100%;">
    <tr>
        <td style="width:45%;">
            <div class="sig-line"></div>
            <div class="sig-name">School Clinician</div>
            <div class="sig-title">Prepared by</div>
        </td>
        <td style="width:10%;"></td>
        <td style="width:45%;">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $schoolSettings->principal_name ?? 'Head Teacher' }}</div>
            <div class="sig-title">Principal · Reviewed</div>
        </td>
    </tr>
</table>
</body>
</html>
