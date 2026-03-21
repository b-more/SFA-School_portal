<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee ID - {{ $employee->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; }
        .page { width: 100%; padding: 10mm; }

        .card-wrapper {
            width: 100%;
            margin-bottom: 12mm;
            page-break-inside: avoid;
        }
        .cut-guide {
            border: 1px dashed #ccc;
            border-radius: 10px;
            overflow: hidden;
            width: 86mm;
            margin: 0 auto;
        }

        /* ═══════════ ID CARD ═══════════ */
        .id-card {
            width: 86mm;
            height: 54mm;
            background: white;
            position: relative;
            overflow: hidden;
        }

        /* Header */
        .id-header {
            background: #1e3a5f;
            padding: 3mm 5mm 2.5mm;
            text-align: center;
            color: white;
        }
        .id-header-inner {
            display: table;
            width: 100%;
        }
        .id-logo-cell {
            display: table-cell;
            width: 12mm;
            vertical-align: middle;
        }
        .id-logo {
            width: 10mm;
            height: 10mm;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 1px;
        }
        .id-logo-placeholder {
            width: 10mm;
            height: 10mm;
            border-radius: 50%;
            background: white;
            text-align: center;
            line-height: 10mm;
            font-size: 9pt;
            font-weight: bold;
            color: #1e3a5f;
        }
        .id-header-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .id-school-name {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .id-motto {
            font-size: 5pt;
            font-style: italic;
            opacity: 0.8;
            margin-top: 0.5px;
        }
        .id-label {
            background: #dc2626;
            color: white;
            font-size: 5.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 1.2mm 0;
            text-align: center;
        }

        /* Body */
        .id-body {
            padding: 2.5mm 4mm;
            display: table;
            width: 100%;
        }
        .id-photo-cell {
            display: table-cell;
            width: 18mm;
            vertical-align: top;
            padding-right: 3mm;
        }
        .id-photo {
            width: 16mm;
            height: 20mm;
            border-radius: 2px;
            border: 1px solid #1e3a5f;
            object-fit: cover;
        }
        .id-photo-placeholder {
            width: 16mm;
            height: 20mm;
            border-radius: 2px;
            border: 1px solid #1e3a5f;
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            text-align: center;
            line-height: 20mm;
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a5f;
        }
        .id-details-cell {
            display: table-cell;
            vertical-align: top;
        }
        .id-qr-cell {
            display: table-cell;
            width: 18mm;
            vertical-align: top;
            text-align: center;
        }
        .id-name {
            font-size: 9pt;
            font-weight: bold;
            color: #1e3a5f;
            line-height: 1.2;
            margin-bottom: 1mm;
        }
        .id-info-row {
            margin-bottom: 0.6mm;
        }
        .id-info-label {
            font-size: 5pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 600;
        }
        .id-info-value {
            font-size: 6.5pt;
            color: #1f2937;
            font-weight: 600;
        }
        .id-qr img {
            width: 16mm;
            height: 16mm;
        }
        .id-qr-label {
            font-size: 4pt;
            color: #94a3b8;
            margin-top: 0.5mm;
        }

        /* Footer */
        .id-footer {
            background: #f1f5f9;
            border-top: 0.5px solid #e2e8f0;
            padding: 1.2mm 4mm;
            display: table;
            width: 100%;
        }
        .id-footer-left {
            display: table-cell;
            vertical-align: middle;
        }
        .id-footer-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        .id-footer-text {
            font-size: 4.5pt;
            color: #64748b;
        }
        .id-employee-number {
            font-size: 6pt;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 0.5px;
        }
        .id-status-badge {
            display: inline-block;
            padding: 0.5mm 2mm;
            border-radius: 3px;
            font-size: 4.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .id-status-active {
            background: #dcfce7;
            color: #15803d;
        }
    </style>
</head>
<body>
    <div class="page">
        @for($i = 0; $i < 4; $i++)
        <div class="card-wrapper">
            <div class="cut-guide">
                <div class="id-card">
                    {{-- Header --}}
                    <div class="id-header">
                        <div class="id-header-inner">
                            <div class="id-logo-cell">
                                @if($settings && $settings->school_logo && file_exists(public_path('storage/' . $settings->school_logo)))
                                    <img src="{{ public_path('storage/' . $settings->school_logo) }}" alt="Logo" class="id-logo">
                                @elseif(file_exists(public_path('images/logo.png')))
                                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="id-logo">
                                @else
                                    <div class="id-logo-placeholder">S</div>
                                @endif
                            </div>
                            <div class="id-header-text">
                                <div class="id-school-name">{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                                @if($settings && $settings->school_motto)
                                    <div class="id-motto">"{{ $settings->school_motto }}"</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Staff ID Label --}}
                    <div class="id-label">Staff Identification Card</div>

                    {{-- Body: Photo + Details + QR --}}
                    <div class="id-body">
                        <div class="id-photo-cell">
                            @if($employee->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->profile_photo))
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->path($employee->profile_photo) }}" alt="Photo" class="id-photo">
                            @else
                                <div class="id-photo-placeholder">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                            @endif
                        </div>
                        <div class="id-details-cell">
                            <div class="id-name">{{ $employee->name }}</div>

                            <div class="id-info-row">
                                <div class="id-info-label">Position</div>
                                <div class="id-info-value">{{ $employee->position ?? 'Staff' }}</div>
                            </div>

                            @if($employee->department)
                            <div class="id-info-row">
                                <div class="id-info-label">Department</div>
                                <div class="id-info-value">{{ ucfirst(str_replace('_', ' ', $employee->department)) }}</div>
                            </div>
                            @endif

                            <div class="id-info-row">
                                <div class="id-info-label">Employee No.</div>
                                <div class="id-info-value">{{ $employee->employee_id ?: $employee->employee_number ?: 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="id-qr-cell">
                            <div class="id-qr">
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                            </div>
                            <div class="id-qr-label">Scan to verify</div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="id-footer">
                        <div class="id-footer-left">
                            <div class="id-employee-number">ID: {{ $employee->employee_id ?: $employee->employee_number ?: $employee->id }}</div>
                            @if($employee->joining_date)
                                <div class="id-footer-text">Since {{ $employee->joining_date->format('M Y') }}</div>
                            @endif
                        </div>
                        <div class="id-footer-right">
                            @if($employee->status === 'active')
                                <span class="id-status-badge id-status-active">Active</span>
                            @endif
                            @php
                                $contactParts = array_filter([
                                    $settings->phone ?? null,
                                    $settings->email ?? null,
                                ]);
                            @endphp
                            @if(!empty($contactParts))
                                <div class="id-footer-text">{{ implode(' | ', $contactParts) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</body>
</html>
