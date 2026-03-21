<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 18mm 15mm 20mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.5; }

        .header {
            text-align: center;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 8mm;
            margin-bottom: 8mm;
        }
        .header-logo {
            display: table;
            width: 100%;
            margin-bottom: 4mm;
        }
        .header-logo-cell {
            display: table-cell;
            width: 15mm;
            vertical-align: middle;
        }
        .header-logo-img {
            width: 12mm;
            height: 12mm;
            object-fit: contain;
        }
        .header-text-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
        }
        .guide-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1e3a5f;
            margin-top: 4mm;
        }
        .guide-subtitle {
            font-size: 10pt;
            color: #64748b;
            margin-top: 1mm;
        }

        .step {
            margin-bottom: 6mm;
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
            padding: 3mm 4mm;
            background-color: #f8fafc;
            border-left: 2px solid #dc2626;
            border-radius: 0 4px 4px 0;
        }
        .step-body p {
            margin-bottom: 2mm;
        }
        .step-body p:last-child {
            margin-bottom: 0;
        }

        .tip {
            margin: 4mm 0 6mm 12mm;
            padding: 3mm 4mm;
            background-color: #fef3c7;
            border-left: 2px solid #f59e0b;
            border-radius: 0 4px 4px 0;
            font-size: 9pt;
        }
        .tip-label {
            font-weight: bold;
            color: #92400e;
        }

        .warning {
            margin: 4mm 0 6mm 12mm;
            padding: 3mm 4mm;
            background-color: #fef2f2;
            border-left: 2px solid #dc2626;
            border-radius: 0 4px 4px 0;
            font-size: 9pt;
        }
        .warning-label {
            font-weight: bold;
            color: #991b1b;
        }

        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1e3a5f;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 2mm;
            margin: 8mm 0 4mm;
        }

        ul {
            margin-left: 5mm;
            margin-bottom: 2mm;
        }
        li {
            margin-bottom: 1mm;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 2mm;
        }

        .key {
            display: inline-block;
            background: #e2e8f0;
            padding: 0.5mm 2mm;
            border-radius: 2px;
            font-family: monospace;
            font-size: 9pt;
            color: #334155;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $schoolName }}</div>
        <div class="guide-title">{{ $title }}</div>
        <div class="guide-subtitle">Step-by-Step Tutorial for Teachers</div>
    </div>

    @yield('content')

    <div class="footer">
        {{ $schoolName }} &mdash; Portal User Guide &mdash; Generated {{ now()->format('F Y') }}
    </div>
</body>
</html>
