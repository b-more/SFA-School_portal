<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Pass - {{ $busPayment->student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
        }

        .bus-pass {
            max-width: 400px;
            margin: 0 auto;
            border: 2px solid #667eea;
            border-radius: 12px;
            overflow: hidden;
        }

        .pass-header {
            background: #667eea;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .school-logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 10px;
        }

        .school-logo img {
            width: 70px;
            height: 70px;
        }

        .school-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .pass-type {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .pass-body {
            padding: 20px;
        }

        .student-name {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            color: #222;
            margin-bottom: 5px;
        }

        .student-id {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }

        .pass-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .pass-details td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .pass-details .label {
            color: #666;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            width: 40%;
        }

        .pass-details .value {
            color: #222;
            font-weight: 600;
            text-align: right;
        }

        .status-section {
            text-align: center;
            padding: 15px;
            background: #11998e;
            color: white;
        }

        .status-badge {
            display: inline-block;
            background: white;
            color: #11998e;
            padding: 8px 25px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .verification-code {
            font-size: 12px;
            margin-top: 8px;
        }

        .info-text {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="bus-pass">
        <!-- Header -->
        <div class="pass-header">
            <div class="school-logo">
                @php
                    $logoPath = null;
                    if ($settings && $settings->school_logo && file_exists(storage_path('app/public/' . $settings->school_logo))) {
                        $logoPath = storage_path('app/public/' . $settings->school_logo);
                    } elseif (file_exists(public_path('images/logo.png'))) {
                        $logoPath = public_path('images/logo.png');
                    }
                @endphp
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="School Logo">
                @endif
            </div>
            <div class="school-name">{{ $settings->school_name ?? 'St. Francis of Assisi School' }}</div>
            <div class="pass-type">School Bus Pass</div>
        </div>

        <!-- Body -->
        <div class="pass-body">
            <div class="student-name">{{ $busPayment->student->name }}</div>
            <div class="student-id">ID: {{ $busPayment->student->student_id_number ?? 'N/A' }}</div>

            <table class="pass-details">
                <tr>
                    <td class="label">Route</td>
                    <td class="value">{{ $busPayment->busFareStructure->route_name }}</td>
                </tr>
                <tr>
                    <td class="label">Valid For</td>
                    <td class="value">
                        @if($busPayment->month)
                            {{ $busPayment->month }} {{ $busPayment->year }}
                        @else
                            Full Term {{ $busPayment->year }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Expires On</td>
                    <td class="value">
                        @if($busPayment->month)
                            @php
                                $monthNumber = date('n', strtotime($busPayment->month . ' 1'));
                                $expiryDate = \Carbon\Carbon::create($busPayment->year, $monthNumber)->endOfMonth();
                            @endphp
                            {{ $expiryDate->format('d M Y') }}
                        @else
                            @php
                                $expiryDate = $busPayment->due_date ?? \Carbon\Carbon::create($busPayment->year, 12, 31);
                            @endphp
                            {{ \Carbon\Carbon::parse($expiryDate)->format('d M Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Grade</td>
                    <td class="value">{{ $busPayment->student->grade->name ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Status Section -->
        <div class="status-section">
            <div class="status-badge">
                @if($busPayment->payment_status === 'paid')
                    VALID
                @elseif($busPayment->payment_status === 'partial')
                    PARTIAL PAYMENT
                @endif
            </div>

            <div class="verification-code">
                Verification Code: BUS-{{ str_pad($busPayment->id, 6, '0', STR_PAD_LEFT) }}
            </div>

            <div class="info-text">
                Present this pass when boarding the bus.<br>
                Valid for {{ $busPayment->busFareStructure->route_name }} route only.
            </div>
        </div>
    </div>
</body>
</html>
