<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Pass - {{ $busPayment->student->name }}</title>
    @if($settings && $settings->getLogoUrl('favicon'))
        <link rel="icon" href="{{ $settings->getLogoUrl('favicon') }}" type="image/x-icon">
    @elseif(file_exists(public_path('images/logo.png')))
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    @endif
    @php
        $monthNumber = null;
        $expiryDate = null;
        if ($busPayment->month) {
            $monthNumber = date('n', strtotime($busPayment->month . ' 1'));
            $expiryDate = \Carbon\Carbon::create($busPayment->year, $monthNumber)->endOfMonth();
        } else {
            $expiryDate = $busPayment->due_date
                ? \Carbon\Carbon::parse($busPayment->due_date)
                : \Carbon\Carbon::create($busPayment->year, 12, 31);
        }
    @endphp
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            padding: 24px;
        }

        /* ── Screen-only controls ── */
        .screen-controls {
            max-width: 800px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .screen-controls h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }

        .btn-group { display: flex; gap: 10px; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-primary {
            background: #003366;
            color: white;
        }
        .btn-primary:hover { background: #002244; }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .btn-secondary:hover { background: #f9fafb; }

        /* ── Preview on screen: show one pass ── */
        .screen-preview {
            max-width: 420px;
            margin: 0 auto;
        }

        /* ── Print grid: 2x2 on A4 ── */
        .print-grid {
            display: none;
        }

        /* ══════════════════════════════════════
           Bus pass card styles
           ══════════════════════════════════════ */
        .bus-pass {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            overflow: hidden;
            background: white;
            font-size: 11px;
        }

        /* Header — school blue with bigger logo */
        .pass-header {
            background: #003366;
            color: white;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pass-logo {
            width: 52px;
            height: 52px;
            min-width: 52px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .pass-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 3px;
        }

        .pass-logo-text {
            font-weight: 800;
            font-size: 15px;
            color: #003366;
        }

        .pass-header-text {
            flex: 1;
            min-width: 0;
        }

        .pass-school-name {
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0.3px;
        }

        .pass-motto {
            font-size: 7.5px;
            font-style: italic;
            opacity: 0.85;
            margin-top: 1px;
            line-height: 1.2;
        }

        .pass-contact-line {
            font-size: 7px;
            opacity: 0.8;
            line-height: 1.3;
            margin-top: 1px;
        }

        .pass-contact-sep {
            margin: 0 3px;
            opacity: 0.5;
        }

        /* Label bar */
        .pass-label-bar {
            background: #e8f0fe;
            color: #003366;
            text-align: center;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 4px 12px;
            border-bottom: 1px solid #d1d9e6;
        }

        .pass-label-icon {
            font-size: 6px;
            vertical-align: middle;
            margin-right: 4px;
        }

        /* Body content */
        .pass-body {
            padding: 10px 12px;
            display: flex;
            gap: 10px;
        }

        .pass-photo {
            width: 56px;
            height: 56px;
            min-width: 56px;
            border-radius: 8px;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            color: #003366;
            overflow: hidden;
            border: 1px solid #bfdbfe;
        }

        .pass-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pass-info { flex: 1; min-width: 0; }

        .pass-student-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
            margin-bottom: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pass-student-id {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .pass-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px 10px;
        }

        .pass-detail-label {
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .pass-detail-value {
            font-size: 10.5px;
            color: #1f2937;
            font-weight: 600;
            line-height: 1.3;
        }

        /* Route + validity strip */
        .pass-strip {
            display: flex;
            align-items: stretch;
            border-top: 1px solid #e5e7eb;
        }

        .pass-route {
            flex: 1;
            padding: 7px 12px;
            background: #f8fafc;
        }

        .pass-route-label {
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .pass-route-value {
            font-size: 12px;
            font-weight: 700;
            color: #003366;
        }

        .pass-validity {
            padding: 7px 12px;
            text-align: right;
            background: #f8fafc;
            border-left: 1px solid #e5e7eb;
        }

        .pass-validity-label {
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .pass-validity-value {
            font-size: 11px;
            font-weight: 700;
            color: #1f2937;
        }

        /* Footer with status + QR */
        .pass-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-top: 1px solid #e5e7eb;
        }

        .pass-status-area { flex: 1; }

        .pass-status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .pass-status-valid {
            background: #dcfce7;
            color: #15803d;
        }

        .pass-status-partial {
            background: #fef9c3;
            color: #a16207;
        }

        .pass-verification {
            font-size: 8px;
            color: #9ca3af;
            margin-top: 3px;
        }

        .pass-qr {
            width: 52px;
            height: 52px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .pass-qr img {
            width: 100%;
            height: 100%;
        }

        .pass-bottom-note {
            text-align: center;
            font-size: 7.5px;
            color: #9ca3af;
            padding: 4px 12px 6px;
            border-top: 1px dashed #e5e7eb;
            line-height: 1.4;
        }

        /* ── Print styles ── */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .screen-controls,
            .screen-preview {
                display: none !important;
            }

            .print-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                grid-template-rows: 1fr 1fr;
                width: 100%;
                height: 100vh;
                gap: 0;
            }

            .print-cell {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 6mm;
                border: 0.5px dashed #bbb;
                overflow: hidden;
            }

            .print-cell .bus-pass {
                width: 100%;
                max-width: 95mm;
                border-radius: 6px;
                border-color: #ccc;
            }

            .pass-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .pass-label-bar {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .pass-status-valid,
            .pass-status-partial {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .pass-photo {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .pass-strip {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .pass-route,
            .pass-validity {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4 portrait;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>
    {{-- Screen: controls + single preview --}}
    <div class="screen-controls">
        <h1>Bus Pass &mdash; {{ $busPayment->student->name }}</h1>
        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
                Print 4-up
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="screen-preview">
        @include('bus-passes._pass-card', ['busPayment' => $busPayment, 'settings' => $settings, 'expiryDate' => $expiryDate])
    </div>

    {{-- Print: 4 copies in 2x2 grid --}}
    <div class="print-grid">
        @for($i = 0; $i < 4; $i++)
            <div class="print-cell">
                @include('bus-passes._pass-card', ['busPayment' => $busPayment, 'settings' => $settings, 'expiryDate' => $expiryDate])
            </div>
        @endfor
    </div>
</body>
</html>
