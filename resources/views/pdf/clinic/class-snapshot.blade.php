<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class Health Snapshot</title>
    @include('pdf.clinic._styles')
</head>
<body>
@include('pdf.clinic._letterhead')

@php
    $classLabel = $d['class']->grade->name . ' - ' . $d['class']->name;
    $teacher    = $d['class']->classTeacher?->name ?? '—';
@endphp

<table class="grid">
    <tr>
        <th style="width:22%;">Class</th><td>{{ $classLabel }}</td>
        <th style="width:14%;">Class teacher</th><td>{{ $teacher }}</td>
    </tr>
    <tr>
        <th>Period</th><td colspan="3">{{ $d['from']->format('d M Y') }} → {{ $d['to']->format('d M Y') }}</td>
    </tr>
</table>

<div class="kpis">
    <div class="kpi"><div class="val">{{ $d['enrolment'] }}</div><div class="lbl">On roll</div></div>
    <div class="kpi"><div class="val">{{ $d['total_visits'] }}</div><div class="lbl">Total visits</div></div>
    <div class="kpi"><div class="val">{{ $d['unique_visitors'] }}</div><div class="lbl">Pupils seen</div></div>
    <div class="kpi"><div class="val">{{ $d['sick_notes'] }}</div><div class="lbl">Sick notes</div></div>
</div>
<div class="kpis">
    <div class="kpi"><div class="val">{{ $d['sent_home'] }}</div><div class="lbl">Sent home</div></div>
    <div class="kpi"><div class="val">{{ $d['referred'] }}</div><div class="lbl">Referred</div></div>
    <div class="kpi"><div class="val">{{ $d['enrolment'] > 0 ? number_format($d['total_visits'] / $d['enrolment'], 2) : '—' }}</div><div class="lbl">Visits / pupil</div></div>
    <div class="kpi"><div class="val">{{ $d['repeat_visitors']->count() }}</div><div class="lbl">Repeat visitors</div></div>
</div>

<h2>Top complaints</h2>
@if($d['top_complaints']->isEmpty())
    <div class="muted">No complaints logged.</div>
@else
    @php $peak = max(1, $d['top_complaints']->max()); @endphp
    @foreach($d['top_complaints'] as $name => $count)
        @php $pct = max(1, (int) round(($count / $peak) * 100)); @endphp
        <div class="bar-row">
            <span class="bar-label">{{ $name }}</span>
            <span class="bar-track"><span class="bar-fill" style="width: {{ $pct * 1.10 }}mm;"></span></span>
            <span class="bar-value">{{ $count }}</span>
        </div>
    @endforeach
@endif

<h2>Repeat visitors (≥ 2 visits)</h2>
@if($d['repeat_visitors']->isEmpty())
    <div class="muted">None flagged for this period.</div>
@else
    <table class="grid">
        <thead><tr><th>Pupil</th><th class="num" style="width:22mm;">Visits</th></tr></thead>
        <tbody>
        @foreach($d['repeat_visitors'] as $name => $c)
            <tr><td>{{ $name }}</td><td class="num">{{ $c }}</td></tr>
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
            <div class="sig-name">{{ $teacher }}</div>
            <div class="sig-title">Class Teacher · Received</div>
        </td>
    </tr>
</table>
</body>
</html>
