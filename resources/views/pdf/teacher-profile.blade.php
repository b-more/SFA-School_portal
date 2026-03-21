<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Profile — {{ $teacher->name }}</title>
    <style>
        /* ═══════════════════════════════════════════════════
           ST. FRANCIS OF ASSISI — TEACHER PROFILE DOSSIER
           Navy & Gold Corporate Design
           ═══════════════════════════════════════════════════ */
        @page {
            margin: 0;
            padding: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.35;
            color: #2d3748;
            background: #ffffff;
        }

        /* ── Master Layout: sidebar + main ── */
        .page-wrap {
            display: table;
            width: 100%;
            min-height: 100%;
        }
        .sidebar {
            display: table-cell;
            width: 195px;
            background: #1a2744;
            color: #e2e8f0;
            vertical-align: top;
            padding: 0;
        }
        .main {
            display: table-cell;
            vertical-align: top;
            padding: 0;
            background: #ffffff;
        }

        /* ── Sidebar ── */
        .sb-header {
            text-align: center;
            padding: 22px 16px 14px;
            border-bottom: 1px solid rgba(201, 168, 76, 0.25);
        }
        .sb-logo {
            width: 48px;
            height: 48px;
            margin: 0 auto 6px;
            object-fit: contain;
        }
        .sb-logo-fallback {
            width: 48px;
            height: 48px;
            margin: 0 auto 6px;
            background: rgba(201, 168, 76, 0.15);
            border: 2px solid #c9a84c;
            border-radius: 50%;
            text-align: center;
            line-height: 44px;
            color: #c9a84c;
            font-size: 16pt;
            font-weight: bold;
        }
        .sb-school {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #c9a84c;
            line-height: 1.2;
        }
        .sb-motto {
            font-size: 6pt;
            font-style: italic;
            color: rgba(201, 168, 76, 0.65);
            margin-top: 3px;
        }

        .sb-photo-area {
            text-align: center;
            padding: 16px 20px 12px;
        }
        .sb-photo {
            width: 110px;
            height: 110px;
            border-radius: 8px;
            border: 3px solid #c9a84c;
            object-fit: cover;
            margin: 0 auto;
        }
        .sb-photo-placeholder {
            width: 110px;
            height: 110px;
            border-radius: 8px;
            border: 3px solid #c9a84c;
            background: linear-gradient(160deg, #243356 0%, #1a2744 100%);
            margin: 0 auto;
            text-align: center;
            line-height: 104px;
            color: #c9a84c;
            font-size: 36pt;
            font-weight: bold;
        }
        .sb-name {
            font-size: 11pt;
            font-weight: bold;
            color: #ffffff;
            margin-top: 10px;
            line-height: 1.15;
        }
        .sb-position {
            font-size: 7.5pt;
            color: #c9a84c;
            margin-top: 2px;
            font-weight: 600;
        }
        .sb-emp-id {
            font-size: 6.5pt;
            color: #94a3b8;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }

        .sb-divider {
            height: 1px;
            background: rgba(201, 168, 76, 0.2);
            margin: 10px 16px;
        }

        .sb-section {
            padding: 6px 16px 10px;
        }
        .sb-section-title {
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #c9a84c;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid rgba(201, 168, 76, 0.15);
        }
        .sb-item {
            margin-bottom: 5px;
        }
        .sb-item-label {
            font-size: 5.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 1px;
        }
        .sb-item-value {
            font-size: 7.5pt;
            color: #e2e8f0;
            font-weight: 600;
        }
        .sb-item-value-sm {
            font-size: 7pt;
            color: #cbd5e1;
        }

        .sb-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-right: 2px;
            margin-bottom: 3px;
        }
        .sb-badge-active {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .sb-badge-role {
            background: rgba(201, 168, 76, 0.15);
            color: #c9a84c;
            border: 1px solid rgba(201, 168, 76, 0.25);
        }
        .sb-badge-class {
            background: rgba(96, 165, 250, 0.15);
            color: #93c5fd;
            border: 1px solid rgba(96, 165, 250, 0.25);
        }

        /* ── Main Content ── */
        .main-header {
            background: #f7f8fa;
            padding: 14px 20px 10px;
            border-bottom: 2px solid #c9a84c;
        }
        .main-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1a2744;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .main-subtitle {
            font-size: 7pt;
            color: #94a3b8;
            margin-top: 1px;
        }

        .main-body {
            padding: 12px 20px 10px;
        }

        /* Section blocks */
        .section {
            margin-bottom: 10px;
        }
        .section-head {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .section-icon-cell {
            display: table-cell;
            width: 18px;
            vertical-align: middle;
        }
        .section-icon {
            width: 14px;
            height: 14px;
            border-radius: 3px;
            background: #1a2744;
            text-align: center;
            line-height: 14px;
            color: #c9a84c;
            font-size: 7pt;
            font-weight: bold;
        }
        .section-title-cell {
            display: table-cell;
            vertical-align: middle;
        }
        .section-title {
            font-size: 8.5pt;
            font-weight: bold;
            color: #1a2744;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .section-line {
            height: 1px;
            background: linear-gradient(90deg, #c9a84c 0%, #e8dcc0 40%, transparent 100%);
            margin-bottom: 6px;
        }

        /* Two-col info grid */
        .info-2col {
            display: table;
            width: 100%;
        }
        .info-2col-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 8px;
        }
        .info-2col-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 8px;
        }

        .field {
            margin-bottom: 4px;
        }
        .field-label {
            font-size: 5.5pt;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 0.5px;
        }
        .field-value {
            font-size: 7.5pt;
            color: #1e293b;
            font-weight: 600;
        }
        .field-value-light {
            font-size: 7.5pt;
            color: #64748b;
            font-style: italic;
        }

        /* Key-value table rows */
        .kv-table {
            display: table;
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        .kv-row {
            display: table-row;
        }
        .kv-row:nth-child(even) .kv-label,
        .kv-row:nth-child(even) .kv-value {
            background: #f9fafb;
        }
        .kv-label {
            display: table-cell;
            width: 38%;
            padding: 4px 8px;
            font-size: 7pt;
            font-weight: 700;
            color: #475569;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
        }
        .kv-value {
            display: table-cell;
            padding: 4px 8px;
            font-size: 7.5pt;
            color: #1e293b;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }
        .kv-empty {
            color: #c0c7d0;
            font-style: italic;
        }

        /* Biography box */
        .bio-box {
            background: #f8fafc;
            padding: 8px 10px;
            border-left: 3px solid #c9a84c;
            border-radius: 0 4px 4px 0;
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.45;
        }

        /* Footer */
        .main-footer {
            padding: 8px 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .footer-gold-line {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #c9a84c 30%, #c9a84c 70%, transparent 100%);
            margin-bottom: 6px;
        }
        .footer-school {
            font-size: 6.5pt;
            font-weight: bold;
            color: #1a2744;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .footer-contact {
            font-size: 6pt;
            color: #94a3b8;
            margin-top: 2px;
        }
        .footer-generated {
            font-size: 5.5pt;
            color: #c0c7d0;
            font-style: italic;
            margin-top: 3px;
        }
        .footer-badge {
            display: inline-block;
            background: #1a2744;
            color: #c9a84c;
            padding: 1px 8px;
            border-radius: 8px;
            font-size: 5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
    </style>
</head>
<body>
@php
    $np = function($val) { return $val ?: 'Not provided'; };
    $settings = \App\Models\SchoolSettings::first();
    $schoolName = $settings->school_name ?? 'St. Francis of Assisi Private School';
    $contactParts = array_filter([
        ($settings->phone ?? null) ? 'Tel: ' . $settings->phone : null,
        ($settings->email ?? null) ? $settings->email : null,
        ($settings->website ?? null) ? $settings->website : null,
    ]);
    $addressParts = array_filter([
        $settings->address ?? null,
        $settings->city ?? null,
    ]);

    // Build cropped face photo as base64 data URI
    $photoDataUri = null;
    $logoDataUri = null;

    // Process profile photo — crop to square from top (face area) and resize
    if ($teacher->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($teacher->profile_photo)) {
        try {
            $photoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($teacher->profile_photo);
            $mime = mime_content_type($photoPath);
            $srcImg = null;
            if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
                $srcImg = @imagecreatefromjpeg($photoPath);
            } elseif ($mime === 'image/png') {
                $srcImg = @imagecreatefrompng($photoPath);
            }
            if ($srcImg) {
                $origW = imagesx($srcImg);
                $origH = imagesy($srcImg);
                // Crop to square from top-center (face focus)
                $cropSize = min($origW, $origH);
                $srcX = intval(($origW - $cropSize) / 2);
                // Offset from top by 10% of height for face centering
                $srcY = intval(min($origH - $cropSize, $origH * 0.1));
                // Create cropped + resized image (150x150 — matches 110px CSS render)
                $targetSize = 150;
                $destImg = imagecreatetruecolor($targetSize, $targetSize);
                imagecopyresampled($destImg, $srcImg, 0, 0, $srcX, $srcY, $targetSize, $targetSize, $cropSize, $cropSize);
                // Output to buffer as JPEG
                ob_start();
                imagejpeg($destImg, null, 75);
                $imgData = ob_get_clean();
                $photoDataUri = 'data:image/jpeg;base64,' . base64_encode($imgData);
                imagedestroy($srcImg);
                imagedestroy($destImg);
            }
        } catch (\Exception $e) {
            $photoDataUri = null;
        }
    }

    // Process school logo — resize to 80px for PDF
    $logoPath = null;
    if ($settings && $settings->school_logo && file_exists(public_path('storage/' . $settings->school_logo))) {
        $logoPath = public_path('storage/' . $settings->school_logo);
    } elseif (file_exists(public_path('images/logo.png'))) {
        $logoPath = public_path('images/logo.png');
    }
    if ($logoPath) {
        try {
            $logoMime = mime_content_type($logoPath);
            $logoSrc = null;
            if (str_contains($logoMime, 'png')) {
                $logoSrc = @imagecreatefrompng($logoPath);
            } elseif (str_contains($logoMime, 'jpeg') || str_contains($logoMime, 'jpg')) {
                $logoSrc = @imagecreatefromjpeg($logoPath);
            }
            if ($logoSrc) {
                $lw = imagesx($logoSrc); $lh = imagesy($logoSrc);
                $logoTarget = 80;
                $ratio = $logoTarget / max($lw, $lh);
                $nw = intval($lw * $ratio); $nh = intval($lh * $ratio);
                $logoDest = imagecreatetruecolor($nw, $nh);
                imagealphablending($logoDest, false);
                imagesavealpha($logoDest, true);
                $transparent = imagecolorallocatealpha($logoDest, 0, 0, 0, 127);
                imagefilledrectangle($logoDest, 0, 0, $nw, $nh, $transparent);
                imagecopyresampled($logoDest, $logoSrc, 0, 0, 0, 0, $nw, $nh, $lw, $lh);
                ob_start();
                imagepng($logoDest, null, 6);
                $logoData = ob_get_clean();
                $logoDataUri = 'data:image/png;base64,' . base64_encode($logoData);
                imagedestroy($logoSrc);
                imagedestroy($logoDest);
            } else {
                $logoDataUri = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        } catch (\Exception $e) {
            $logoDataUri = null;
        }
    }
@endphp

<div class="page-wrap">
    {{-- ═══════════ LEFT SIDEBAR ═══════════ --}}
    <div class="sidebar">
        {{-- School Branding --}}
        <div class="sb-header">
            @if($logoDataUri)
                <img src="{{ $logoDataUri }}" alt="Logo" class="sb-logo">
            @else
                <div class="sb-logo-fallback">SF</div>
            @endif
            <div class="sb-school">{{ $schoolName }}</div>
            @if($settings && $settings->school_motto)
                <div class="sb-motto">"{{ $settings->school_motto }}"</div>
            @endif
        </div>

        {{-- Profile Photo & Identity --}}
        <div class="sb-photo-area">
            @if($photoDataUri)
                <img src="{{ $photoDataUri }}" alt="Photo" class="sb-photo">
            @else
                <div class="sb-photo-placeholder">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
            @endif
            <div class="sb-name">{{ $teacher->name }}</div>
            <div class="sb-position">Teacher</div>
            <div class="sb-emp-id">{{ $teacher->employee_id ?? '' }}</div>
            <div style="margin-top: 6px;">
                @if($teacher->is_active)
                    <span class="sb-badge sb-badge-active">Active</span>
                @endif
                @if($teacher->is_class_teacher)
                    <span class="sb-badge sb-badge-class">Class Teacher</span>
                @endif
                @if($teacher->is_grade_teacher)
                    <span class="sb-badge sb-badge-role">Grade Teacher</span>
                @endif
            </div>
        </div>

        <div class="sb-divider"></div>

        {{-- Quick Reference --}}
        <div class="sb-section">
            <div class="sb-section-title">Quick Reference</div>
            <div class="sb-item">
                <div class="sb-item-label">Qualification</div>
                <div class="sb-item-value">{{ $teacher->qualification ?? 'Not provided' }}</div>
            </div>
            <div class="sb-item">
                <div class="sb-item-label">Date Joined</div>
                <div class="sb-item-value">{{ $teacher->join_date ? $teacher->join_date->format('d M Y') : 'Not provided' }}</div>
            </div>
            @if($teacher->specialization)
            <div class="sb-item">
                <div class="sb-item-label">Specialization</div>
                <div class="sb-item-value">{{ $teacher->specialization }}</div>
            </div>
            @endif
            @if($teacher->department)
            <div class="sb-item">
                <div class="sb-item-label">Department</div>
                <div class="sb-item-value">{{ ucfirst(str_replace('_', ' ', $teacher->department)) }}</div>
            </div>
            @endif
        </div>

        <div class="sb-divider"></div>

        {{-- Teaching Assignment --}}
        @if($teacher->grade || $teacher->classSection)
        <div class="sb-section">
            <div class="sb-section-title">Assignment</div>
            @if($teacher->grade)
            <div class="sb-item">
                <div class="sb-item-label">Assigned Grade</div>
                <div class="sb-item-value">{{ $teacher->grade->name }}</div>
            </div>
            @endif
            @if($teacher->classSection)
            <div class="sb-item">
                <div class="sb-item-label">Class Section</div>
                <div class="sb-item-value">{{ ($teacher->classSection->grade->name ?? '') }} - {{ $teacher->classSection->name }}</div>
            </div>
            @endif
        </div>
        <div class="sb-divider"></div>
        @endif

        {{-- Contact Quick --}}
        <div class="sb-section">
            <div class="sb-section-title">Contact</div>
            <div class="sb-item">
                <div class="sb-item-label">Email</div>
                <div class="sb-item-value-sm">{{ $teacher->email ?? ($user->email ?? 'Not provided') }}</div>
            </div>
            @if($teacher->phone)
            <div class="sb-item">
                <div class="sb-item-label">Phone</div>
                <div class="sb-item-value-sm">{{ $teacher->phone }}</div>
            </div>
            @endif
            @if($teacher->address)
            <div class="sb-item">
                <div class="sb-item-label">Address</div>
                <div class="sb-item-value-sm">{{ $teacher->address }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════ MAIN CONTENT ═══════════ --}}
    <div class="main">
        {{-- Header Bar --}}
        <div class="main-header">
            <div class="main-title">Teacher Profile</div>
            <div class="main-subtitle">Confidential Personnel Record &mdash; {{ $schoolName }}</div>
        </div>

        <div class="main-body">
            {{-- ── Personal Information ── --}}
            <div class="section">
                <div class="section-head">
                    <div class="section-icon-cell"><div class="section-icon">P</div></div>
                    <div class="section-title-cell"><div class="section-title">Personal Information</div></div>
                </div>
                <div class="section-line"></div>
                <div class="info-2col">
                    <div class="info-2col-left">
                        <div class="field">
                            <div class="field-label">Full Name</div>
                            <div class="field-value">{{ $teacher->name }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Employee ID</div>
                            <div class="{{ $teacher->employee_id ? 'field-value' : 'field-value-light' }}">{{ $np($teacher->employee_id) }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Email Address</div>
                            <div class="field-value">{{ $teacher->email ?? ($user->email ?? 'Not provided') }}</div>
                        </div>
                    </div>
                    <div class="info-2col-right">
                        <div class="field">
                            <div class="field-label">Phone Number</div>
                            <div class="{{ $teacher->phone ? 'field-value' : 'field-value-light' }}">{{ $np($teacher->phone) }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Address</div>
                            <div class="{{ $teacher->address ? 'field-value' : 'field-value-light' }}">{{ $np($teacher->address) }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Date Joined</div>
                            <div class="{{ $teacher->join_date ? 'field-value' : 'field-value-light' }}">{{ $teacher->join_date ? $teacher->join_date->format('d M Y') : 'Not provided' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Identification & Banking ── --}}
            <div class="section">
                <div class="section-head">
                    <div class="section-icon-cell"><div class="section-icon">B</div></div>
                    <div class="section-title-cell"><div class="section-title">Identification & Banking</div></div>
                </div>
                <div class="section-line"></div>
                <div class="kv-table">
                    <div class="kv-row">
                        <div class="kv-label">NRC Number</div>
                        <div class="kv-value">{{ $teacher->nrc ?: '<span class="kv-empty">Not provided</span>' }}</div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label">TPIN</div>
                        <div class="kv-value">{{ $teacher->tpin ?: '<span class="kv-empty">Not provided</span>' }}</div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label">Bank Name</div>
                        <div class="kv-value">{{ $teacher->bank_name ?: '<span class="kv-empty">Not provided</span>' }}</div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label">Bank Branch</div>
                        <div class="kv-value">{{ $teacher->bank_branch ?: '<span class="kv-empty">Not provided</span>' }}</div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label">Account Number</div>
                        <div class="kv-value">{{ $teacher->account_number ?: '<span class="kv-empty">Not provided</span>' }}</div>
                    </div>
                </div>
            </div>

            {{-- ── Teaching Assignment ── --}}
            @if($teacher->grade || $teacher->classSection)
            <div class="section">
                <div class="section-head">
                    <div class="section-icon-cell"><div class="section-icon">T</div></div>
                    <div class="section-title-cell"><div class="section-title">Teaching Assignment</div></div>
                </div>
                <div class="section-line"></div>
                <div class="info-2col">
                    <div class="info-2col-left">
                        @if($teacher->grade)
                        <div class="field">
                            <div class="field-label">Assigned Grade</div>
                            <div class="field-value">{{ $teacher->grade->name }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="info-2col-right">
                        @if($teacher->classSection)
                        <div class="field">
                            <div class="field-label">Class Section</div>
                            <div class="field-value">{{ ($teacher->classSection->grade->name ?? '') }} - {{ $teacher->classSection->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Qualifications ── --}}
            <div class="section">
                <div class="section-head">
                    <div class="section-icon-cell"><div class="section-icon">Q</div></div>
                    <div class="section-title-cell"><div class="section-title">Qualifications & Expertise</div></div>
                </div>
                <div class="section-line"></div>
                <div class="info-2col">
                    <div class="info-2col-left">
                        <div class="field">
                            <div class="field-label">Qualification</div>
                            <div class="{{ $teacher->qualification ? 'field-value' : 'field-value-light' }}">{{ $np($teacher->qualification) }}</div>
                        </div>
                    </div>
                    <div class="info-2col-right">
                        <div class="field">
                            <div class="field-label">Specialization</div>
                            <div class="{{ $teacher->specialization ? 'field-value' : 'field-value-light' }}">{{ $np($teacher->specialization) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Document Status ── --}}
            <div class="section">
                <div class="section-head">
                    <div class="section-icon-cell"><div class="section-icon">D</div></div>
                    <div class="section-title-cell"><div class="section-title">Document Status</div></div>
                </div>
                <div class="section-line"></div>
                <div class="kv-table">
                    <div class="kv-row">
                        <div class="kv-label">Curriculum Vitae</div>
                        <div class="kv-value">
                            @if($teacher->cv_document)
                                <span style="color:#059669;font-weight:bold;">Uploaded</span>
                            @else
                                <span class="kv-empty">Not uploaded</span>
                            @endif
                        </div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label">Police Clearance</div>
                        <div class="kv-value">
                            @if($teacher->police_clearance)
                                <span style="color:#059669;font-weight:bold;">Uploaded</span>
                            @else
                                <span class="kv-empty">Not uploaded</span>
                            @endif
                        </div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label">Teaching License</div>
                        <div class="kv-value">
                            @if($teacher->teaching_license)
                                <span style="color:#059669;font-weight:bold;">Uploaded</span>
                            @else
                                <span class="kv-empty">Not uploaded</span>
                            @endif
                        </div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label">NRC Copy</div>
                        <div class="kv-value">
                            @if($teacher->nrc_copy)
                                <span style="color:#059669;font-weight:bold;">Uploaded</span>
                            @else
                                <span class="kv-empty">Not uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Biography ── --}}
            @if($teacher->biography)
            <div class="section">
                <div class="section-head">
                    <div class="section-icon-cell"><div class="section-icon">N</div></div>
                    <div class="section-title-cell"><div class="section-title">Biography & Notes</div></div>
                </div>
                <div class="section-line"></div>
                <div class="bio-box">{{ $teacher->biography }}</div>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="main-footer">
            <div class="footer-gold-line"></div>
            <div class="footer-school">{{ $schoolName }}</div>
            @if(!empty($addressParts))
                <div class="footer-contact">{{ implode(', ', $addressParts) }}@if($settings->postal_code ?? null) &middot; P.O. Box {{ $settings->postal_code }}@endif</div>
            @endif
            @if(!empty($contactParts))
                <div class="footer-contact">{{ implode(' &middot; ', $contactParts) }}</div>
            @endif
            <div class="footer-generated">Generated on {{ now()->format('l, F d, Y') }} at {{ now()->format('h:i A') }}</div>
            <div class="footer-badge">Confidential</div>
        </div>
    </div>
</div>

</body>
</html>
