<!DOCTYPE html>
<html>
<head>
    <title>Fee Structure</title>
    <style>
        /* Base styling for clean, professional look */
        body {
            font-family: Arial, sans-serif;
            margin: 15px;
            padding: 0;
            color: #333;
            font-size: 11pt;
            line-height: 1.3;
        }

        /* Header section with logo and title */
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo {
            max-width: 70px;
            margin-bottom: 5px;
        }
        .school-title {
            font-size: 16pt;
            font-weight: bold;
            color: #003366;
            margin: 5px 0 2px 0;
        }
        .document-title {
            font-size: 14pt;
            color: #4b86c4;
            margin: 2px 0;
        }

        /* School contact info */
        .school-info {
            text-align: center;
            color: #555;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .school-info p {
            margin: 0;
            padding: 0;
        }

        /* Line separator */
        .divider {
            border-bottom: 1px solid #4b86c4;
            margin: 10px 0;
        }

        /* Student info section */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 10px;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .info-item {
            margin: 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 120px;
        }

        /* Fee table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
        }
        th {
            background-color: #4b86c4;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 7px 10px;
            border: 1px solid #ddd;
        }
        td {
            padding: 5px 10px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        .amount-column {
            text-align: right;
        }
        .total-row {
            background-color: #e6f2ff !important;
            font-weight: bold;
        }
        .subtotal-row {
            background-color: #f0f0f0 !important;
            font-weight: bold;
            font-size: 9pt;
        }
        .category-row {
            background-color: #4b86c4 !important;
            color: white;
            font-weight: bold;
            font-size: 9pt;
        }
        .category-row td {
            border-color: #3a75b3;
        }

        /* Additional information section */
        .additional-info {
            margin-top: 15px;
            font-size: 10pt;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .additional-info p {
            margin: 0;
        }

        /* Footer sections */
        .footer-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 20px;
            margin-top: 15px;
            font-size: 9pt;
        }

        /* Notes section */
        .notes-section {
            margin-top: 0;
        }
        .notes-section p {
            margin: 0 0 3px 0;
            font-weight: bold;
        }
        .notes-section ul {
            margin: 0;
            padding-left: 15px;
        }
        .notes-section li {
            margin-bottom: 2px;
        }

        /* Signatures section */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 40px;
            margin-top: 20px;
        }
        .signature-block {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto 2px auto;
        }
        .signature-img {
            height: 60px;
            margin-bottom: 5px;
            max-width: 150px;
        }

        /* Fine print section */
        .fine-print {
            text-align: center;
            margin-top: 15px;
            font-size: 8pt;
            color: #666;
        }
        .disclaimer {
            font-style: italic;
            margin-bottom: 2px;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    {{-- ============================================ --}}
    {{-- PAGE 1: SCHOOL FEES --}}
    {{-- ============================================ --}}
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="School Logo" class="logo">
        <div class="school-title">St. Francis Of Assisi Private School</div>
        <div class="document-title">Fee Structure</div>
    </div>

    <div class="school-info">
        <p>Plot No 1310/4 East Kamenza, Chililabombwe, Zambia</p>
        <p>Phone: +260 972 266 217, Email: info@stfrancisofassisi.tech</p>
    </div>

    <div class="divider"></div>

    <div class="info-section">
        <p class="info-item"><span class="info-label">Section:</span> {{ $grade }}</p>
        <p class="info-item"><span class="info-label">Term:</span> {{ $term }}</p>
        <p class="info-item"><span class="info-label">Academic Year:</span> {{ $academicYear }}</p>
        <p class="info-item"><span class="info-label">Date Generated:</span> {{ date('F j, Y') }}</p>
    </div>

    @php
        $additionalCharges = $feeStructure->additional_charges;
        $schoolFees = [];
        $uniformItems = [];

        if (is_array($additionalCharges)) {
            foreach ($additionalCharges as $charge) {
                if (!isset($charge['description']) || !isset($charge['amount'])) continue;

                $desc = $charge['description'];
                // Uniform/sports items go to page 2
                if (str_starts_with($desc, 'Girls -') ||
                    str_starts_with($desc, 'Boys -') ||
                    str_starts_with($desc, 'Sports -') ||
                    $desc === 'Blazer') {
                    $uniformItems[] = $charge;
                } else {
                    $schoolFees[] = $charge;
                }
            }
        }

        // Calculate school fees total (page 1)
        $schoolFeesTotal = (float) $feeStructure->basic_fee;
        foreach ($schoolFees as $fee) {
            $schoolFeesTotal += (float) $fee['amount'];
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="width: 30%; text-align: right;">Amount (ZMW)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Tuition Fee</td>
                <td class="amount-column">{{ number_format($feeStructure->basic_fee, 2) }}</td>
            </tr>

            @foreach($schoolFees as $fee)
                <tr>
                    <td>{{ $fee['description'] }}</td>
                    <td class="amount-column">{{ number_format($fee['amount'], 2) }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td>Total School Fees</td>
                <td class="amount-column">{{ number_format($schoolFeesTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="additional-info">
        <div class="section-title">Additional Information:</div>
        <p>{{ $feeStructure->description ?? $grade . ' ' . $term . ' ' . $academicYear . ' Fee Structure' }}</p>
    </div>

    <div class="footer-content">
        <div class="notes-section">
            <p><strong>Bank Payment Details (No Cash Payments):</strong></p>
            <ul>
                <li><strong>School Fees:</strong> Indo Zambia Bank - Account No: 0172040000103</li>
                <li><strong>Bus/Uniform:</strong> Indo Zambia Bank - Account No: 0172040000104</li>
            </ul>
            <p>Please note:</p>
            <ul>
                <li>All fees must be paid by the first day of the term</li>
                <li>Late payments may incur a 5% penalty fee</li>
                <li>Payment can be made via bank transfer or mobile money only. No cash payments accepted.</li>
                <li>For any fee-related inquiries, please contact the accounts department</li>
            </ul>
        </div>
    </div>

    <div class="signatures">
        <div class="signature-block">
            <img src="{{ public_path('images/ed_signature.png') }}" alt="Executive Director Signature" class="signature-img">
            <div class="signature-line"></div>
            <p>Executive Director's Signature</p>
        </div>
        <div class="signature-block">
            <div style="height: 60px;"></div>
            <div class="signature-line"></div>
            <p>Accounts Officer's Signature</p>
        </div>
    </div>

    <div class="fine-print">
        <p class="disclaimer">This is an official document of {{ $schoolName }}. Any alterations render it invalid.</p>
        <p>&copy; {{ date('Y') }} {{ $schoolName }}. All Rights Reserved.</p>
    </div>

    {{-- ============================================ --}}
    {{-- PAGE 2: UNIFORM & SPORTS PRICE LIST --}}
    {{-- ============================================ --}}
    @if(count($uniformItems) > 0)
    <div class="page-break"></div>

    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="School Logo" class="logo">
        <div class="school-title">St. Francis Of Assisi Private School</div>
        <div class="document-title">Uniform & Sports Attire Price List</div>
    </div>

    <div class="school-info">
        <p>Plot No 1310/4 East Kamenza, Chililabombwe, Zambia</p>
        <p>Phone: +260 972 266 217, Email: info@stfrancisofassisi.tech</p>
    </div>

    <div class="divider"></div>

    <div class="info-section">
        <p class="info-item"><span class="info-label">Section:</span> {{ $grade }}</p>
        <p class="info-item"><span class="info-label">Academic Year:</span> {{ $academicYear }}</p>
    </div>

    @php
        $girlsItems = [];
        $boysItems = [];
        $sportsItems = [];
        $girlsTotal = 0;
        $boysTotal = 0;
        $sportsTotal = 0;

        foreach ($uniformItems as $item) {
            $desc = $item['description'];
            $amount = (float) $item['amount'];

            if (str_starts_with($desc, 'Girls -')) {
                $girlsItems[] = ['name' => str_replace('Girls - ', '', $desc), 'amount' => $amount];
                $girlsTotal += $amount;
            } elseif (str_starts_with($desc, 'Boys -')) {
                $boysItems[] = ['name' => str_replace('Boys - ', '', $desc), 'amount' => $amount];
                $boysTotal += $amount;
            } else {
                // Sports items and Blazer
                $name = str_replace('Sports - ', '', $desc);
                $sportsItems[] = ['name' => $name, 'amount' => $amount];
                $sportsTotal += $amount;
            }
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">S/N</th>
                <th>Item Description</th>
                <th style="width: 25%; text-align: right;">Price (ZMW)</th>
            </tr>
        </thead>
        <tbody>
            {{-- Girls Section --}}
            @if(count($girlsItems) > 0)
            <tr class="category-row">
                <td colspan="3">GIRLS UNIFORM</td>
            </tr>
            @foreach($girlsItems as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="amount-column">{{ number_format($item['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td></td>
                <td style="text-align: right;">Girls Uniform Total</td>
                <td class="amount-column">{{ number_format($girlsTotal, 2) }}</td>
            </tr>
            @endif

            {{-- Boys Section --}}
            @if(count($boysItems) > 0)
            <tr class="category-row">
                <td colspan="3">BOYS UNIFORM</td>
            </tr>
            @foreach($boysItems as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="amount-column">{{ number_format($item['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td></td>
                <td style="text-align: right;">Boys Uniform Total</td>
                <td class="amount-column">{{ number_format($boysTotal, 2) }}</td>
            </tr>
            @endif

            {{-- Sports Section --}}
            @if(count($sportsItems) > 0)
            <tr class="category-row">
                <td colspan="3">SPORTS ATTIRE</td>
            </tr>
            @foreach($sportsItems as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="amount-column">{{ number_format($item['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td></td>
                <td style="text-align: right;">Sports Attire Total</td>
                <td class="amount-column">{{ number_format($sportsTotal, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-content">
        <div class="notes-section">
            <p><strong>Bank Payment Details for Uniform/Bus (No Cash Payments):</strong></p>
            <ul>
                <li><strong>Bus/Uniform:</strong> Indo Zambia Bank - Account No: 0172040000104</li>
            </ul>
            <p>Please note:</p>
            <ul>
                <li>Uniform and sports attire are purchased separately from school fees</li>
                <li>Payments for uniforms must be deposited into the Bus/Uniform account listed above</li>
                <li>Prices are subject to change without prior notice</li>
                <li>For inquiries, please contact the school office</li>
            </ul>
        </div>
    </div>

    <div class="fine-print">
        <p class="disclaimer">This is an official document of {{ $schoolName }}. Any alterations render it invalid.</p>
        <p>&copy; {{ date('Y') }} {{ $schoolName }}. All Rights Reserved.</p>
    </div>
    @endif
</body>
</html>
