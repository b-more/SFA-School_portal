<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Burn-Rate</title>
    @include('pdf.clinic._styles')
</head>
<body>
@include('pdf.clinic._letterhead')

<div class="callout">
    <strong>How to read this:</strong> daily rate is average usage over the last
    <strong>{{ $d['window_days'] }}</strong> days. Days-of-supply is <em>balance ÷ daily rate</em>.
    Items are ranked by urgency — stockouts and &lt; 7 days first.
</div>

<h2>Consumption</h2>
<table class="grid">
    <thead>
        <tr>
            <th>Item</th>
            <th style="width:14mm;" class="num">Balance</th>
            <th style="width:14mm;">Unit</th>
            <th style="width:18mm;" class="num">Used ({{ $d['window_days'] }}d)</th>
            <th style="width:16mm;" class="num">Daily rate</th>
            <th style="width:16mm;" class="num">Days left</th>
            <th style="width:18mm;">Status</th>
        </tr>
    </thead>
    <tbody>
    @foreach($d['rows'] as $r)
        @php
            $pill = match ($r['urgency']) {
                'stockout' => 'pill-red',
                'critical' => 'pill-red',
                'low'      => 'pill-amber',
                'watch'    => 'pill-navy',
                default    => 'pill-green',
            };
            $label = match ($r['urgency']) {
                'stockout' => 'STOCK OUT',
                'critical' => '<7 days',
                'low'      => 'Below reorder',
                'watch'    => '<21 days',
                default    => 'OK',
            };
        @endphp
        <tr>
            <td>{{ $r['item'] }}</td>
            <td class="num">{{ $r['balance'] }}</td>
            <td>{{ $r['unit'] }}</td>
            <td class="num">{{ $r['used'] }}</td>
            <td class="num">{{ $r['daily'] }}</td>
            <td class="num">{{ $r['days_of_supply'] ?? '—' }}</td>
            <td><span class="pill {{ $pill }}">{{ $label }}</span></td>
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
            <div class="sig-title">Procurement · Received</div>
        </td>
    </tr>
</table>
</body>
</html>
