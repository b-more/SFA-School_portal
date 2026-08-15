<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sick Notes Register</title>
    @include('pdf.clinic._styles')
</head>
<body>
@include('pdf.clinic._letterhead')

<div class="kpis">
    <div class="kpi"><div class="val">{{ $d['count'] }}</div><div class="lbl">Sick notes issued</div></div>
    <div class="kpi"><div class="val">{{ $d['rows']->pluck('student_name')->unique()->count() }}</div><div class="lbl">Unique pupils</div></div>
    <div class="kpi"><div class="val">{{ $d['from']->format('d M') }}</div><div class="lbl">Period start</div></div>
    <div class="kpi"><div class="val">{{ $d['to']->format('d M Y') }}</div><div class="lbl">Period end</div></div>
</div>

<h2>Register</h2>
@if($d['rows']->isEmpty())
    <div class="callout">No sick notes were issued in this period.</div>
@else
    <table class="grid">
        <thead>
            <tr>
                <th style="width:6mm;" class="num">#</th>
                <th style="width:20mm;">Date</th>
                <th>Pupil</th>
                <th style="width:22mm;">Class</th>
                <th>Complaints</th>
                <th style="width:22mm;">Outcome</th>
                <th style="width:30mm;">Signature</th>
            </tr>
        </thead>
        <tbody>
        @foreach($d['rows'] as $i => $v)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($v->visit_date)->format('d M Y') }}</td>
                <td>{{ $v->student_name }}</td>
                <td>{{ $v->grade }}</td>
                <td>{{ $v->complaints->pluck('name')->join(', ') ?: '—' }}</td>
                <td>{{ str($v->outcome ?? 'n/a')->replace('_',' ')->title() }}</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<div class="muted" style="margin-top:6pt;">
    Guardians sign against their child's row on collection of the pupil or receipt of the note.
</div>

<table class="signature-row" style="width:100%;">
    <tr>
        <td style="width:45%;">
            <div class="sig-line"></div>
            <div class="sig-name">School Clinician</div>
            <div class="sig-title">Issued by</div>
        </td>
        <td style="width:10%;"></td>
        <td style="width:45%;">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $schoolSettings->principal_name ?? 'Head Teacher' }}</div>
            <div class="sig-title">Principal · Verified</div>
        </td>
    </tr>
</table>
</body>
</html>
