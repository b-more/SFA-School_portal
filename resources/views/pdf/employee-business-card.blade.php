<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Cards - {{ $employee->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: white;
        }

        .grid {
            width: 180mm;
            margin: 0 auto;
        }
        .grid-row {
            width: 180mm;
            height: 89mm;
            display: table;
            table-layout: fixed;
        }
        .grid-col {
            display: table-cell;
            width: 90mm;
            height: 89mm;
            vertical-align: middle;
        }

        .card {
            width: 90mm;
            border: 0.25px solid #ccc;
        }

        /* Top section: sidebar + content */
        .card-top {
            display: table;
            width: 100%;
            height: 55mm;
        }
        .card-top-row {
            display: table-row;
        }

        /* Sidebar cell */
        .sb {
            display: table-cell;
            width: 22mm;
            background-color: #162d4a;
            vertical-align: top;
            padding: 4mm 2mm 3mm;
            text-align: center;
            border-right: 0.7mm solid #b91c1c;
        }

        .logo-ring {
            width: 13mm;
            height: 13mm;
            margin: 0 auto 2.5mm;
            border-radius: 50%;
            border: 0.3mm solid rgba(255,255,255,0.18);
            padding: 0.8mm;
        }
        .logo-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: contain;
            background: white;
        }
        .logo-text {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: white;
            line-height: 11.4mm;
            font-size: 7pt;
            font-weight: 800;
            color: #162d4a;
            text-align: center;
        }
        .sb-school {
            color: rgba(255,255,255,0.92);
            font-size: 4.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            line-height: 1.4;
        }
        .sb-motto {
            color: rgba(255,255,255,0.38);
            font-size: 3.5pt;
            font-style: italic;
            margin-top: 1mm;
            line-height: 1.3;
        }

        /* Content cell */
        .ct {
            display: table-cell;
            vertical-align: top;
            padding: 5mm 5mm 3mm 5mm;
            background-color: #ffffff;
        }

        .emp-name {
            font-size: 10.5pt;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.1pt;
            line-height: 1.15;
        }
        .emp-role {
            font-size: 5pt;
            font-weight: 700;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: 1.2pt;
            margin-top: 1mm;
            line-height: 1;
        }
        .emp-dept {
            font-size: 4.5pt;
            color: #9ca3af;
            margin-top: 0.3mm;
        }

        .rule {
            width: 10mm;
            height: 0.3mm;
            background-color: #b91c1c;
            margin: 2mm 0;
        }

        .cq {
            display: table;
            width: 100%;
        }
        .cq-left {
            display: table-cell;
            vertical-align: top;
        }
        .cq-right {
            display: table-cell;
            width: 15mm;
            vertical-align: top;
            text-align: right;
        }

        .ci { margin-bottom: 1.2mm; }
        .ci:last-child { margin-bottom: 0; }
        .ci-l {
            font-size: 3.5pt;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
        }
        .ci-v {
            font-size: 5pt;
            color: #374151;
            line-height: 1.25;
            margin-top: 0.15mm;
        }

        .cq-right img {
            width: 13mm;
            height: 13mm;
            border: 0.25mm solid #e5e7eb;
            border-radius: 1mm;
            padding: 0.5mm;
        }
        .qr-t {
            font-size: 2.8pt;
            color: #d1d5db;
            margin-top: 0.3mm;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="grid">
        @for($row = 0; $row < 3; $row++)
        <div class="grid-row">
            @for($col = 0; $col < 2; $col++)
            <div class="grid-col">
                <div class="card">
                    {{-- Card Top: Sidebar + Content (CSS table) --}}
                    <div class="card-top">
                        <div class="card-top-row">
                            <div class="sb">
                                <div class="logo-ring">
                                    @if($settings && $settings->school_logo && file_exists(public_path('storage/' . $settings->school_logo)))
                                        <img src="{{ public_path('storage/' . $settings->school_logo) }}" class="logo-img">
                                    @elseif(file_exists(public_path('images/logo.png')))
                                        <img src="{{ public_path('images/logo.png') }}" class="logo-img">
                                    @else
                                        <div class="logo-text">SFA</div>
                                    @endif
                                </div>
                                <div class="sb-school">{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                                @if($settings && $settings->school_motto)
                                    <div class="sb-motto">"{{ $settings->school_motto }}"</div>
                                @endif
                            </div>

                            <div class="ct">
                                <div class="emp-name">{{ $employee->name }}</div>
                                <div class="emp-role">{{ $employee->position ?? 'Staff' }}</div>
                                @if($employee->department)
                                    <div class="emp-dept">{{ ucfirst(str_replace('_', ' ', $employee->department)) }}</div>
                                @endif

                                <div class="rule"></div>

                                <div class="cq">
                                    <div class="cq-left">
                                        @if($employee->phone)
                                        <div class="ci">
                                            <div class="ci-l">Tel</div>
                                            <div class="ci-v">{{ $employee->phone }}</div>
                                        </div>
                                        @endif
                                        @if($employee->email)
                                        <div class="ci">
                                            <div class="ci-l">Email</div>
                                            <div class="ci-v">{{ $employee->email }}</div>
                                        </div>
                                        @endif
                                        @php
                                            $addressParts = array_filter([
                                                $settings->address ?? null,
                                                $settings->city ?? null,
                                            ]);
                                        @endphp
                                        @if(!empty($addressParts))
                                        <div class="ci">
                                            <div class="ci-l">Address</div>
                                            <div class="ci-v">{{ implode(', ', $addressParts) }}</div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="cq-right">
                                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR">
                                        <div class="qr-t">Scan Profile</div>
                                    </div>
                                </div>

                                {{-- Tagline --}}
                                <div style="border-top: 0.4mm solid #b91c1c; margin-top: 2mm; padding-top: 1.5mm; text-align: center;">
                                    <span style="font-size: 5pt; color: #162d4a; font-style: italic; letter-spacing: 0.2pt; font-weight: 600;">Nurturing Excellence, Inspiring the Future!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
        @endfor
    </div>
</body>
</html>
