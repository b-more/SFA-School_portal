<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $schoolName }} - Parents & Guardians List</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.4;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .header-table { width: 100%; margin-bottom: 5px; }
        .header-table td { vertical-align: middle; }
        .logo-cell { width: 70px; text-align: left; }
        .school-logo { width: 55px; height: 55px; }
        .header h1 { color: #2563eb; font-size: 18px; margin-bottom: 3px; }
        .header h2 { color: #1e40af; font-size: 14px; margin-bottom: 5px; }
        .header p { color: #6b7280; font-size: 9px; }

        .summary-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .summary-box h3 { color: #1e40af; font-size: 11px; margin-bottom: 8px; }
        .summary-grid { display: table; width: 100%; }
        .summary-item { display: table-cell; width: 20%; padding: 5px; text-align: center; }
        .summary-label { font-size: 8px; color: #6b7280; text-transform: uppercase; }
        .summary-value { font-size: 14px; font-weight: bold; color: #1e40af; margin-top: 3px; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th {
            background-color: #1e40af;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
        }
        .data-table td {
            padding: 5px 4px;
            border: 1px solid #e5e7eb;
            font-size: 8px;
        }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-info { background-color: #dbeafe; color: #1e40af; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-gray { background-color: #f3f4f6; color: #374151; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" class="school-logo" alt="Logo">
                    @endif
                </td>
                <td style="text-align: center;">
                    <h1>{{ $schoolName }}</h1>
                    <h2>Parents & Guardians List</h2>
                    <p>Generated on {{ $reportDate }}</p>
                </td>
                <td style="width: 70px;"></td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <h3>Summary Statistics</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total</div>
                <div class="summary-value">{{ $parents->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Fathers</div>
                <div class="summary-value" style="color: #2563eb;">{{ $parents->where('relationship', 'father')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Mothers</div>
                <div class="summary-value" style="color: #059669;">{{ $parents->where('relationship', 'mother')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Guardians</div>
                <div class="summary-value" style="color: #d97706;">{{ $parents->where('relationship', 'guardian')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">With Portal</div>
                <div class="summary-value" style="color: #6b7280;">{{ $parents->whereNotNull('user_id')->count() }}</div>
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 18%;">Name</th>
                <th style="width: 10%;">Relationship</th>
                <th style="width: 12%;">Phone</th>
                <th style="width: 12%;">Alt. Phone</th>
                <th style="width: 14%;">Email</th>
                <th style="width: 10%;">NRC</th>
                <th style="width: 10%;">Occupation</th>
                <th style="width: 10%;">Children</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parents as $index => $parent)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $parent->name }}</td>
                <td>
                    @if($parent->relationship === 'father')
                        <span class="badge badge-info">Father</span>
                    @elseif($parent->relationship === 'mother')
                        <span class="badge badge-success">Mother</span>
                    @elseif($parent->relationship === 'guardian')
                        <span class="badge badge-warning">Guardian</span>
                    @else
                        <span class="badge badge-gray">{{ ucfirst($parent->relationship ?? 'N/A') }}</span>
                    @endif
                </td>
                <td>{{ $parent->phone ?? 'N/A' }}</td>
                <td>{{ $parent->alternate_phone ?? '-' }}</td>
                <td>{{ $parent->email ?? '-' }}</td>
                <td>{{ $parent->nrc ?? '-' }}</td>
                <td>{{ $parent->occupation ?? '-' }}</td>
                <td>{{ $parent->students->pluck('name')->join(', ') ?: 'None' }}</td>
            </tr>
            @if(($index + 1) % 30 === 0 && $index + 1 < $parents->count())
                </tbody>
            </table>
            <div class="page-break"></div>

            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="logo-cell">
                            @if(file_exists(public_path('images/logo.png')))
                                <img src="{{ public_path('images/logo.png') }}" class="school-logo" alt="Logo">
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <h1>{{ $schoolName }}</h1>
                            <h2>Parents & Guardians List (Continued)</h2>
                            <p>Page {{ ceil(($index + 1) / 30) + 1 }}</p>
                        </td>
                        <td style="width: 70px;"></td>
                    </tr>
                </table>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 18%;">Name</th>
                        <th style="width: 10%;">Relationship</th>
                        <th style="width: 12%;">Phone</th>
                        <th style="width: 12%;">Alt. Phone</th>
                        <th style="width: 14%;">Email</th>
                        <th style="width: 10%;">NRC</th>
                        <th style="width: 10%;">Occupation</th>
                        <th style="width: 10%;">Children</th>
                    </tr>
                </thead>
                <tbody>
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $schoolName }} | Generated on {{ $reportDate }}</p>
        <p>Total Records: {{ $parents->count() }} | This is a computer-generated document.</p>
    </div>
</body>
</html>
