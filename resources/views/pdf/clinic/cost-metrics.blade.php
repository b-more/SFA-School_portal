<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clinic Cost Analysis</title>
    @include('pdf.clinic._styles')
</head>
<body>
@include('pdf.clinic._letterhead')

<div class="kpis">
    <div class="kpi"><div class="val">K {{ number_format($d['total_spend'], 2) }}</div><div class="lbl">Total spend</div></div>
    <div class="kpi"><div class="val">{{ $d['total_visits'] }}</div><div class="lbl">Visits</div></div>
    <div class="kpi"><div class="val">K {{ number_format($d['cost_per_visit'], 2) }}</div><div class="lbl">Cost / visit</div></div>
    <div class="kpi"><div class="val">K {{ number_format($d['cost_per_pupil'], 2) }}</div><div class="lbl">Cost / pupil</div></div>
</div>

<div class="callout">
    <strong>Basis:</strong> spend = purchases + opening stock valuations over the period, at unit cost captured on the ledger.
    Enrolment used: <strong>{{ $d['enrolment'] }}</strong> active pupils.
</div>

<h2>By school section</h2>
<table class="grid">
    <thead>
        <tr>
            <th>Section</th>
            <th class="num">Pupils</th>
            <th class="num">Visits</th>
            <th class="num">Visits per pupil</th>
        </tr>
    </thead>
    <tbody>
    @foreach($d['by_section'] as $name => $row)
        <tr>
            <td>{{ $name }}</td>
            <td class="num">{{ $row['pupils'] }}</td>
            <td class="num">{{ $row['visits'] }}</td>
            <td class="num">{{ $row['pupils'] > 0 ? number_format($row['visits'] / $row['pupils'], 2) : '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

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
            <div class="sig-name">School Bursar</div>
            <div class="sig-title">Finance · Countersigned</div>
        </td>
    </tr>
</table>
</body>
</html>
