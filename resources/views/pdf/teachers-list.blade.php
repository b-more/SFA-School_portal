<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Teachers List' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 15px;
        }

        /* Header Section */
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 6px;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin: 0 auto 4px;
            object-fit: contain;
        }

        .school-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .school-subtitle {
            font-size: 8pt;
            opacity: 0.9;
            margin-bottom: 6px;
        }

        .document-title {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            padding: 5px 0;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* Summary Stats */
        .stats-section {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            background: #f8fafc;
            padding: 8px;
            border-radius: 6px;
        }

        .stat-box {
            display: table-cell;
            text-align: center;
            padding: 5px;
            width: 25%;
        }

        .stat-number {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
        }

        .stat-label {
            font-size: 7pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* Teachers Table */
        .teachers-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .teachers-table thead {
            background: #1e3a8a;
            color: white;
        }

        .teachers-table th {
            padding: 6px 4px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            border: 1px solid #cbd5e1;
        }

        .teachers-table td {
            padding: 5px 4px;
            font-size: 7pt;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .teachers-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .teachers-table tbody tr:hover {
            background: #e0f2fe;
        }

        /* Profile Photo */
        .profile-photo {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #3b82f6;
        }

        .profile-placeholder {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10pt;
            font-weight: bold;
            border: 1px solid #3b82f6;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            background: #3b82f6;
            color: white;
            border-radius: 8px;
            font-size: 6pt;
            margin-right: 3px;
            margin-bottom: 2px;
            white-space: nowrap;
        }

        .badge-green {
            background: #10b981;
        }

        .badge-yellow {
            background: #f59e0b;
        }

        .badge-red {
            background: #ef4444;
        }

        .badge-purple {
            background: #8b5cf6;
        }

        /* Footer */
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 6pt;
        }

        .footer-info {
            margin-bottom: 2px;
        }

        .generated-date {
            font-style: italic;
            color: #94a3b8;
        }

        /* Text utilities */
        .text-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #64748b;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="St Francis Logo" class="logo">
                @else
                    <div style="width: 50px; height: 50px; background: white; border-radius: 50%; margin: 0 auto 4px; display: flex; align-items: center; justify-content: center; color: #1e3a8a; font-size: 16pt; font-weight: bold;">SF</div>
                @endif
                <div class="school-name">St Francis of Assisi</div>
                <div class="school-subtitle">Excellence in Education</div>
            </div>
            <div class="document-title">{{ $title ?? 'TEACHERS LIST' }}</div>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-section">
            <div class="stat-box">
                <div class="stat-number">{{ $teachers->count() }}</div>
                <div class="stat-label">Total Teachers</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $teachers->where('is_active', true)->count() }}</div>
                <div class="stat-label">Active Teachers</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $teachers->where('administrative_role', '!=', 'none')->count() }}</div>
                <div class="stat-label">Administrative Roles</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $teachers->where('is_class_teacher', true)->count() }}</div>
                <div class="stat-label">Class Teachers</div>
            </div>
        </div>

        <!-- Teachers Table -->
        <table class="teachers-table">
            <thead>
                <tr>
                    <th style="width: 35px;">Photo</th>
                    <th style="width: 80px;">Name</th>
                    <th style="width: 50px;">Employee ID</th>
                    <th style="width: 80px;">Qualification</th>
                    <th style="width: 70px;">Contact</th>
                    <th style="width: 60px;">Assignment</th>
                    <th style="width: 90px;">Roles & Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                <tr>
                    <!-- Photo -->
                    <td class="text-center">
                        @if($teacher->profile_photo && Storage::disk('public')->exists($teacher->profile_photo))
                            <img src="{{ Storage::disk('public')->path($teacher->profile_photo) }}" alt="Photo" class="profile-photo">
                        @else
                            <div class="profile-placeholder">
                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                            </div>
                        @endif
                    </td>

                    <!-- Name -->
                    <td>
                        <div class="text-bold">{{ $teacher->name }}</div>
                        @if($teacher->specialization)
                            <div class="text-muted" style="font-size: 6pt;">{{ $teacher->specialization }}</div>
                        @endif
                    </td>

                    <!-- Employee ID -->
                    <td>{{ $teacher->employee_id }}</td>

                    <!-- Qualification -->
                    <td>
                        <div>{{ $teacher->qualification ?? 'N/A' }}</div>
                        @if($teacher->join_date)
                            <div class="text-muted" style="font-size: 6pt;">Joined: {{ $teacher->join_date->format('M Y') }}</div>
                        @endif
                    </td>

                    <!-- Contact -->
                    <td>
                        @if($teacher->phone)
                            <div style="font-size: 6pt;">{{ $teacher->phone }}</div>
                        @endif
                        @if($teacher->email)
                            <div class="text-muted" style="font-size: 6pt;">{{ Str::limit($teacher->email, 20) }}</div>
                        @endif
                    </td>

                    <!-- Assignment -->
                    <td>
                        @if($teacher->classSection)
                            <div style="font-size: 6pt;">
                                <strong>Class:</strong> {{ $teacher->classSection->grade->name ?? '' }} - {{ $teacher->classSection->name }}
                            </div>
                        @elseif($teacher->grade)
                            <div style="font-size: 6pt;">
                                <strong>Grade:</strong> {{ $teacher->grade->name }}
                            </div>
                        @else
                            <div class="text-muted" style="font-size: 6pt;">No assignment</div>
                        @endif
                    </td>

                    <!-- Roles & Status -->
                    <td>
                        @if($teacher->administrative_role !== 'none')
                            <span class="badge badge-purple">{{ $teacher->administrative_role_name }}</span>
                            @if($teacher->section_scope !== 'none')
                                <span class="badge">{{ $teacher->section_scope_name }}</span>
                            @endif
                        @endif
                        @if($teacher->is_class_teacher)
                            <span class="badge badge-green">Class Teacher</span>
                        @endif
                        @if($teacher->is_grade_teacher)
                            <span class="badge">Grade Teacher</span>
                        @endif
                        @if($teacher->is_active)
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-red">Inactive</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        No teachers found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-info">
                St Francis of Assisi School | Excellence in Education
            </div>
            <div class="footer-info">
                Email: info@stfrancisofassisi.tech | Phone: +260 XXX XXX XXX
            </div>
            <div class="generated-date">
                Generated on: {{ $date ?? now()->format('F d, Y \a\t h:i A') }}
            </div>
        </div>
    </div>
</body>
</html>
