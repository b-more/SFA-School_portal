<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance-Loss Impact</title>
    @include('pdf.clinic._styles')
</head>
<body>
@include('pdf.clinic._letterhead')

<div class="kpis">
    <div class="kpi"><div class="val">{{ $d['total_visits'] }}</div><div class="lbl">Sent home / referred</div></div>
    <div class="kpi"><div class="val">{{ $d['total_days'] }}</div><div class="lbl">School days missed</div></div>
    <div class="kpi"><div class="val">{{ $d['from']->format('d M') }}</div><div class="lbl">From</div></div>
    <div class="kpi"><div class="val">{{ $d['to']->format('d M Y') }}</div><div class="lbl">To</div></div>
</div>

<div class="callout">
    <strong>Method:</strong> for each pupil sent home or referred out of the clinic, we counted their
    <em>absent / sick / excused</em> attendance rows over the next 3 school days. This estimates instructional time lost to health incidents.
</div>

<h2>Incidents</h2>
@if($d['rows']->isEmpty())
    <div class="callout">No sent-home or referred visits recorded in this period.</div>
@else
    <table class="grid">
        <thead>
            <tr>
                <th style="width:6mm;" class="num">#</th>
                <th style="width:20mm;">Visit date</th>
                <th>Pupil</th>
                <th style="width:22mm;">Class</th>
                <th style="width:22mm;">Outcome</th>
                <th style="width:22mm;" class="num">Days missed</th>
            </tr>
        </thead>
        <tbody>
        @foreach($d['rows'] as $i => $r)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($r['date'])->format('d M Y') }}</td>
                <td>{{ $r['student_name'] }}</td>
                <td>{{ $r['grade'] }}</td>
                <td>{{ str($r['outcome'])->replace('_',' ')->title() }}</td>
                <td class="num">{{ $r['days_missed'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

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
