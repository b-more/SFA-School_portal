<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bus Roster — {{ $driver->name ?? 'Driver' }}</title>
    <style>
        @page { margin: 16mm 12mm; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #1f2937; font-size: 11px; line-height: 1.4; margin: 0; }

        .hdr { border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; margin-bottom: 12px; }
        .hdr-row { width: 100%; }
        .hdr-row td { vertical-align: middle; }
        .logo { width: 56px; }
        .school .name { font-size: 17px; font-weight: bold; color: #1e3a5f; }
        .school .motto { font-size: 10px; font-style: italic; color: #6b7280; }
        .school .addr { font-size: 9px; color: #6b7280; }

        .meta { background: #f3f4f6; padding: 8px 10px; border-radius: 4px; margin-bottom: 10px; font-size: 10px; }
        .meta b { color: #1e3a5f; }

        .route-hdr {
            background: #1e3a5f; color: #fff; padding: 6px 9px; margin: 14px 0 0;
            border-radius: 4px 4px 0 0; font-weight: bold; font-size: 11px;
        }
        .route-hdr .ct { float: right; background: #fff; color: #1e3a5f; padding: 1px 7px; border-radius: 9999px; font-size: 10px; }

        table.list { width: 100%; border-collapse: collapse; }
        table.list th { background: #e5e7eb; color: #1f2937; font-size: 10px; text-align: left; padding: 5px 7px; }
        table.list td { padding: 6px 7px; border-bottom: 1px solid #e5e7eb; font-size: 11px; vertical-align: top; }
        table.list tr:nth-child(even) td { background: #fafafa; }

        .chip { display: inline-block; padding: 1px 6px; border-radius: 9px; font-size: 9px; font-weight: 700; }
        .chip-ECE { background: #fef3c7; color: #92400e; }
        .chip-PRI { background: #dbeafe; color: #1e40af; }
        .chip-SEC { background: #dcfce7; color: #166534; }

        .ph { font-weight: bold; color: #16a34a; font-size: 12px; }
        .ph-alt { color: #6b7280; font-size: 10px; }

        .ftr { margin-top: 14px; padding-top: 6px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #6b7280; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="hdr">
        <table class="hdr-row"><tr>
            @if ($settings && $settings->school_logo)
                <td style="width: 66px;"><img class="logo" src="{{ public_path('storage/' . $settings->school_logo) }}" alt=""></td>
            @endif
            <td class="school">
                <div class="name">{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                <div class="motto">For God and Country</div>
                <div class="addr">{{ $settings->school_address ?? '1310/4 East Kamenza, Chililabombwe, Zambia' }}</div>
            </td>
            <td class="right" style="font-size: 9px; color: #6b7280;">
                Generated<br>{{ $generatedAt->format('d M Y · H:i') }}
            </td>
        </tr></table>
    </div>

    <div class="meta">
        <b>Driver:</b> {{ $driver->name }}
        &nbsp;·&nbsp;
        <b>Period:</b> {{ $term?->name ? $term->name . ' · ' : '' }}{{ $period }}
        &nbsp;·&nbsp;
        <b>Total:</b> {{ $rows->count() }} {{ \Illuminate\Support\Str::plural('student', $rows->count()) }}
    </div>

    @php $byRoute = $rows->groupBy('bus_fare_structure_id'); @endphp

    @foreach ($routes as $route)
        @php $rs = $byRoute->get($route->id, collect()); @endphp
        <div class="route-hdr">
            {{ $route->route_name }}
            <span class="ct">{{ $rs->count() }}</span>
        </div>

        @if ($rs->isEmpty())
            <table class="list">
                <tr><td style="text-align: center; padding: 14px; color: #6b7280;">No paid-up students on this route.</td></tr>
            </table>
        @else
            <table class="list">
                <thead>
                <tr>
                    <th style="width: 22px;">#</th>
                    <th>Student</th>
                    <th style="width: 44px;">Grade</th>
                    <th>Pickup Address</th>
                    <th>Parent / Guardian</th>
                    <th style="width: 110px;">Phone</th>
                    <th style="width: 72px;">Expires</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($rs as $i => $r)
                    @php
                        $section = $r->student?->grade?->schoolSection?->code ?? 'PRI';
                        $expiresAt = $r->expires_at;
                        $expColor = '#6b7280';
                        $expText = '—';
                        if ($expiresAt) {
                            $daysLeft = (int) round(now()->startOfDay()->diffInDays($expiresAt->startOfDay(), false));
                            $expText = $expiresAt->format('d M');
                            if ($daysLeft < 0)          { $expColor = '#991b1b'; $expText = 'Expired ' . $expiresAt->format('d M'); }
                            elseif ($daysLeft <= 3)     { $expColor = '#991b1b'; }
                            elseif ($daysLeft <= 7)     { $expColor = '#92400e'; }
                            elseif ($daysLeft <= 14)    { $expColor = '#1e40af'; }
                        }
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <b>{{ $r->student?->name ?? '—' }}</b>
                        </td>
                        <td>
                            <span class="chip chip-{{ $section }}">{{ $section }}</span>
                            <div style="font-size: 9px; color: #6b7280; margin-top: 2px;">{{ $r->student?->grade?->name ?? '' }}</div>
                        </td>
                        <td>{{ $r->student?->address ?? '—' }}</td>
                        <td>{{ $r->student?->parentGuardian?->name ?? '—' }}</td>
                        <td>
                            <div class="ph">{{ $r->student?->parentGuardian?->phone ?? '—' }}</div>
                            @if ($r->student?->parentGuardian?->alternate_phone)
                                <div class="ph-alt">Alt: {{ $r->student->parentGuardian->alternate_phone }}</div>
                            @endif
                        </td>
                        <td style="color: {{ $expColor }}; font-weight: 600;">{{ $expText }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="ftr">
        Roster lists only students whose bus payments are recorded as paid or partial for the current period.
        If a student is missing, ask their parent to settle the bus fee at the school office.
    </div>
</body>
</html>
