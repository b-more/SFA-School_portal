<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.6;
            padding: 18mm 16mm 22mm;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            padding-bottom: 6mm;
            margin-bottom: 8mm;
            border-bottom: 3px solid #1e3a5f;
        }
        .header-logo-row {
            display: table;
            width: 100%;
            margin-bottom: 3mm;
        }
        .header-logo-left {
            display: table-cell;
            width: 20mm;
            vertical-align: middle;
        }
        .header-logo-img {
            width: 18mm;
            height: 18mm;
            object-fit: contain;
        }
        .header-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .header-logo-right {
            display: table-cell;
            width: 20mm;
            vertical-align: middle;
        }
        .school-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .school-address {
            font-size: 8pt;
            color: #64748b;
            margin-top: 1mm;
        }
        .guide-title-bar {
            background-color: #1e3a5f;
            color: #ffffff;
            padding: 3mm 5mm;
            margin-top: 4mm;
            border-radius: 3px;
        }
        .guide-title {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .guide-subtitle {
            font-size: 9pt;
            color: #cbd5e1;
            margin-top: 1mm;
        }

        /* ── Section Titles ── */
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #ffffff;
            background-color: #2d5a8e;
            padding: 2.5mm 4mm;
            margin: 8mm 0 4mm;
            border-radius: 2px;
            letter-spacing: 0.3px;
        }

        /* ── Overview paragraph ── */
        .section-title + p {
            font-size: 10pt;
            color: #374151;
            margin-bottom: 4mm;
            padding-left: 1mm;
        }

        /* ── Steps ── */
        .step {
            margin-bottom: 5mm;
            page-break-inside: avoid;
        }
        .step-header {
            display: table;
            width: 100%;
            margin-bottom: 2mm;
        }
        .step-number-cell {
            display: table-cell;
            width: 10mm;
            vertical-align: top;
        }
        .step-number {
            width: 8mm;
            height: 8mm;
            background-color: #1e3a5f;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 8mm;
            font-size: 10pt;
            font-weight: bold;
        }
        .step-title-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 2mm;
        }
        .step-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e3a5f;
        }
        .step-body {
            margin-left: 12mm;
            padding: 3mm 5mm;
            background-color: #f8fafc;
            border-left: 3px solid #3b82f6;
            border-radius: 0 4px 4px 0;
            font-size: 9.5pt;
        }
        .step-body p {
            margin-bottom: 2mm;
        }
        .step-body p:last-child {
            margin-bottom: 0;
        }

        /* ── Lists ── */
        ul {
            margin-left: 5mm;
            margin-bottom: 2mm;
        }
        li {
            margin-bottom: 1.5mm;
            font-size: 9.5pt;
        }
        li strong {
            color: #1e3a5f;
        }

        /* ── Tip Box ── */
        .tip {
            margin: 4mm 0 5mm 12mm;
            padding: 3mm 5mm;
            background-color: #ecfdf5;
            border-left: 3px solid #10b981;
            border-radius: 0 4px 4px 0;
            font-size: 9pt;
        }
        .tip-label {
            font-weight: bold;
            color: #065f46;
        }

        /* ── Warning Box ── */
        .warning {
            margin: 4mm 0 5mm 12mm;
            padding: 3mm 5mm;
            background-color: #fef2f2;
            border-left: 3px solid #dc2626;
            border-radius: 0 4px 4px 0;
            font-size: 9pt;
        }
        .warning-label {
            font-weight: bold;
            color: #991b1b;
        }

        /* ── Info Box ── */
        .info {
            margin: 4mm 0 5mm 12mm;
            padding: 3mm 5mm;
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
            border-radius: 0 4px 4px 0;
            font-size: 9pt;
        }
        .info-label {
            font-weight: bold;
            color: #1e40af;
        }

        /* ── Keyboard Keys ── */
        .key {
            display: inline-block;
            background: #e2e8f0;
            padding: 0.5mm 2.5mm;
            border-radius: 2px;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 8.5pt;
            color: #334155;
            border: 0.5px solid #cbd5e1;
        }

        /* ── Tables in guide content ── */
        .guide-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3mm 0 4mm 12mm;
            font-size: 9pt;
        }
        .guide-table th {
            background-color: #1e3a5f;
            color: #ffffff;
            padding: 2mm 3mm;
            text-align: left;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .guide-table td {
            padding: 2mm 3mm;
            border-bottom: 0.5px solid #e2e8f0;
        }
        .guide-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 8mm;
            left: 16mm;
            right: 16mm;
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 2mm;
        }
        .footer-school {
            font-weight: bold;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logo-row">
            <div class="header-logo-left">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="header-logo-img">
                @endif
            </div>
            <div class="header-center">
                <div class="school-name">{{ $schoolName }}</div>
                <div class="school-address">1310/4 East Kamenza, Chililabombwe, Zambia | Tel: +260 972 266 217</div>
            </div>
            <div class="header-logo-right">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="header-logo-img">
                @endif
            </div>
        </div>
        <div class="guide-title-bar">
            <div class="guide-title">{{ $title }}</div>
            <div class="guide-subtitle">Step-by-Step Tutorial for Teachers</div>
        </div>
    </div>

    @yield('content')

    <div class="footer">
        <span class="footer-school">{{ $schoolName }}</span> &mdash; Portal User Guide &mdash; Generated {{ now()->format('F Y') }}
    </div>
</body>
</html>
