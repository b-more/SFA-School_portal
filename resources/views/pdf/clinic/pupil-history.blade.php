<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pupil Medical History — {{ $d['student']->name }}</title>
    @include('pdf.clinic._styles')
</head>
<body>
@include('pdf.clinic._letterhead')

@php
    $s = $d['student'];
    $classLabel = $s->classSection?->grade?->name . ' - ' . $s->classSection?->name;
@endphp

<table class="grid" style="margin-top:4pt;">
    <tr>
        <th style="width:22%;">Pupil</th><td>{{ $s->name }}</td>
        <th style="width:14%;">Class</th><td>{{ $classLabel }}</td>
    </tr>
    <tr>
        <th>Student No.</th><td>{{ $s->student_number }}</td>
        <th>DOB</th><td>{{ optional($s->date_of_birth)->format('d M Y') ?: '—' }}</td>
    </tr>
    <tr>
        <th>Guardian</th><td colspan="3">{{ $s->parentGuardian?->name ?? '—' }} · {{ $s->parentGuardian?->phone ?? '—' }}</td>
    </tr>
</table>

<div class="kpis">
    <div class="kpi"><div class="val">{{ $d['total'] }}</div><div class="lbl">Total visits</div></div>
    <div class="kpi"><div class="val">{{ $d['sick_notes'] }}</div><div class="lbl">Sick notes</div></div>
    <div class="kpi"><div class="val">{{ $d['sent_home'] }}</div><div class="lbl">Sent home</div></div>
    <div class="kpi"><div class="val">{{ $d['referred'] }}</div><div class="lbl">Referred out</div></div>
</div>

<div class="muted">
    First seen {{ $d['first_seen'] ? \Carbon\Carbon::parse($d['first_seen'])->format('d M Y') : '—' }}
    · Last seen {{ $d['last_seen'] ? \Carbon\Carbon::parse($d['last_seen'])->format('d M Y') : '—' }}
</div>

<h2>Visit ledger</h2>
@if($d['visits']->isEmpty())
    <div class="callout">No clinic visits recorded for this pupil.</div>
@else
    <table class="grid">
        <thead>
            <tr>
                <th style="width:18mm;">Date</th>
                <th>Complaints</th>
                <th style="width:42mm;">Findings / Rx</th>
                <th style="width:22mm;">Outcome</th>
                <th style="width:14mm;" class="num">Sick note</th>
            </tr>
        </thead>
        <tbody>
        @foreach($d['visits'] as $v)
            <tr>
                <td>{{ \Carbon\Carbon::parse($v->visit_date)->format('d M Y') }}</td>
                <td>{{ $v->complaints->pluck('name')->join(', ') ?: '—' }}</td>
                <td>{{ trim(($v->findings ?: '') . ($v->treatment ? ' · ' . $v->treatment : '')) ?: '—' }}</td>
                <td>
                    @php $pill = match($v->outcome) { 'sent_home'=>'pill-red', 'referred'=>'pill-amber', 'treated'=>'pill-green', default=>'pill-grey' }; @endphp
                    <span class="pill {{ $pill }}">{{ str($v->outcome ?? 'n/a')->replace('_',' ')->title() }}</span>
                </td>
                <td class="num">{{ $v->sick_note_issued ? 'Yes' : '' }}</td>
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
            <div class="sig-title">Principal · Countersigned</div>
        </td>
    </tr>
</table>

<div class="muted" style="margin-top:6pt;">
    <em>Confidential medical record. To be released only to the parent/guardian, referring clinician, or authorised school personnel.</em>
</div>
</body>
</html>
