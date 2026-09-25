@php
    // Embed images inline as data-URIs so DomPDF renders them reliably
    // regardless of the working directory / DOMPDF_ENABLE_REMOTE flag.
    $__logoPath = public_path('images/logo.png');
    $__logoData = file_exists($__logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($__logoPath))
        : null;

    $__sigPath = public_path('images/ed_signature.png');
    $__sigData = file_exists($__sigPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($__sigPath))
        : null;

    $__annualLabels     = ['pta', 'maintenance', 'computer'];  // case-insensitive substring match
    $__optionalLabels   = ['bus'];

    // Split the JSON additional_charges into school fees vs. uniform items.
    $additionalCharges = $feeStructure->additional_charges ?? [];
    $schoolFees   = [];
    $uniformItems = [];
    $__isUniformDesc = fn ($desc) => str_starts_with($desc, 'Girls -')
        || str_starts_with($desc, 'Boys -')
        || str_starts_with($desc, 'Sports -')
        || $desc === 'Blazer';

    if (is_array($additionalCharges)) {
        foreach ($additionalCharges as $charge) {
            if (! isset($charge['description'], $charge['amount'])) continue;
            $desc = $charge['description'];
            if ($__isUniformDesc($desc)) {
                $uniformItems[] = $charge;
            } else {
                $schoolFees[] = $charge;
            }
        }
    }

    // Uniform prices are typically only stored on the Term 1 fee structure
    // per section — Term 2 and Term 3 carry no uniform items because
    // parents bought uniforms once at the start of the year. Fall back to
    // a sibling fee structure in the same section so the second page always
    // shows current uniform prices.
    if (empty($uniformItems) && $feeStructure->school_section_id) {
        $sibling = \App\Models\FeeStructure::query()
            ->where('school_section_id', $feeStructure->school_section_id)
            ->when($feeStructure->academic_year_id, fn ($q) => $q->where('academic_year_id', $feeStructure->academic_year_id))
            ->where('id', '!=', $feeStructure->id)
            ->orderBy('term_id')
            ->get();
        foreach ($sibling as $sib) {
            $ac = $sib->additional_charges ?? [];
            if (! is_array($ac)) continue;
            foreach ($ac as $charge) {
                if (! isset($charge['description'], $charge['amount'])) continue;
                if ($__isUniformDesc($charge['description'])) {
                    $uniformItems[] = $charge;
                }
            }
            if (! empty($uniformItems)) break;
        }
    }

    $tagFor = function ($desc) use ($__annualLabels, $__optionalLabels) {
        $needle = strtolower($desc);
        foreach ($__annualLabels as $k)   if (str_contains($needle, $k)) return 'annual';
        foreach ($__optionalLabels as $k) if (str_contains($needle, $k)) return 'optional';
        return null;
    };

    // Termly total excludes items marked "annual" (they aren't billed every term)
    // and items marked "optional" (bus). Basic tuition is always included.
    $termlyTotal = (float) $feeStructure->basic_fee;
    foreach ($schoolFees as $fee) {
        $tag = $tagFor($fee['description']);
        if ($tag === null) $termlyTotal += (float) $fee['amount'];
    }

    $annualExtras = 0.0;
    $optionalExtras = 0.0;
    foreach ($schoolFees as $fee) {
        $tag = $tagFor($fee['description']);
        if ($tag === 'annual')   $annualExtras   += (float) $fee['amount'];
        if ($tag === 'optional') $optionalExtras += (float) $fee['amount'];
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Structure — {{ $grade }} · {{ $term }} · {{ $academicYear }}</title>
    <style>
        @page { margin: 22mm 18mm 20mm 18mm; }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1f2937;
            font-size: 10.5pt;
            line-height: 1.4;
            margin: 0;
        }

        /* -------- Letterhead -------- */
        .letterhead {
            border-bottom: 2px solid #0e2746;
            padding-bottom: 10px;
            margin-bottom: 6px;
        }
        .letterhead-inner { width: 100%; }
        .letterhead-inner td { vertical-align: middle; padding: 0; }
        .crest { width: 62px; height: auto; }
        .school-title {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 18pt;
            font-weight: 700;
            color: #0e2746;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.1;
        }
        .motto {
            font-family: 'Times New Roman', Georgia, serif;
            font-style: italic;
            color: #8b1a1a;
            font-size: 9.5pt;
            margin-top: 2px;
        }
        .contact-strip {
            text-align: right;
            font-size: 8.5pt;
            color: #4b5563;
            line-height: 1.4;
        }
        .contact-strip strong { color: #0e2746; }

        /* -------- Document title bar -------- */
        .doc-title-bar {
            background: #0e2746;
            color: #ffffff;
            padding: 8px 14px;
            margin: 12px 0 4px 0;
        }
        .doc-title-bar .kicker {
            font-size: 8.5pt;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #b08a3e;
            font-weight: 700;
        }
        .doc-title-bar .doc-title {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 15pt;
            font-weight: 700;
            margin-top: 1px;
            letter-spacing: 0.3px;
        }

        /* -------- Meta strip (section, term, year, date) -------- */
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 14px 0;
            font-size: 9.5pt;
        }
        .meta td {
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .meta .label {
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-size: 8pt;
            width: 22%;
        }
        .meta .value { color: #0e2746; font-weight: 600; }

        /* -------- Fee table -------- */
        table.fees {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px 0;
            font-size: 10pt;
        }
        table.fees thead th {
            background: #0e2746;
            color: #ffffff;
            font-weight: 700;
            text-align: left;
            padding: 8px 12px;
            font-size: 9pt;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border: none;
        }
        table.fees tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        table.fees tbody tr:nth-child(even) td { background: #fafaf7; }
        .amount { text-align: right; font-variant-numeric: tabular-nums; }
        .tag {
            display: inline-block;
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: 0.1em;
            padding: 1px 6px;
            margin-left: 6px;
            border-radius: 2px;
            vertical-align: middle;
            text-transform: uppercase;
        }
        .tag.annual   { background: #fdf2e0; color: #8b6a1a; border: 1px solid #e6c98a; }
        .tag.optional { background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; }

        .total-row td {
            background: #f5efe0 !important;
            color: #0e2746;
            font-weight: 700;
            font-size: 10.5pt;
            border-top: 1.5px solid #b08a3e;
            border-bottom: 1.5px solid #b08a3e !important;
        }

        /* -------- Payment options blocks -------- */
        .pay {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 8px 0;
        }
        .pay td {
            vertical-align: top;
            width: 50%;
            padding: 0 6px;
        }
        .pay .box {
            border: 1px solid #d1d5db;
            border-left: 3px solid #0e2746;
            background: #ffffff;
            padding: 10px 12px;
        }
        .pay .box.mobile { border-left-color: #b08a3e; }
        .pay .kicker {
            font-size: 8pt;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #8b1a1a;
            font-weight: 700;
        }
        .pay .box-title {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 12pt;
            color: #0e2746;
            font-weight: 700;
            margin: 2px 0 6px 0;
        }
        .pay .ussd {
            font-family: 'Courier New', monospace;
            background: #0e2746;
            color: #b08a3e;
            padding: 6px 10px;
            font-weight: 700;
            font-size: 12pt;
            letter-spacing: 0.5px;
            display: inline-block;
            margin: 4px 0;
        }
        .pay .row { margin: 2px 0; font-size: 9.5pt; color: #374151; }
        .pay .row strong { color: #0e2746; }
        .pay .row .acct { font-family: 'Courier New', monospace; letter-spacing: 0.5px; }

        /* -------- Notes / signatures -------- */
        .notes {
            margin-top: 10px;
            font-size: 9pt;
            color: #374151;
        }
        .notes .kicker {
            font-size: 8pt;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #8b1a1a;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .notes ul { margin: 4px 0 0 0; padding-left: 16px; }
        .notes li { margin-bottom: 2px; }

        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 22px;
        }
        .signatures td {
            text-align: center;
            padding: 0 20px;
            width: 50%;
            vertical-align: bottom;
        }
        .signature-wrap {
            height: 55px;
            display: block;
            text-align: center;
            margin-bottom: 4px;
        }
        .signature-wrap img {
            max-height: 55px;
            max-width: 130px;
            width: auto;
            height: auto;
        }
        .signature-line {
            border-top: 1px solid #0e2746;
            width: 78%;
            margin: 0 auto 3px auto;
        }
        .signature-name {
            font-family: 'Times New Roman', Georgia, serif;
            font-weight: 700;
            color: #0e2746;
            font-size: 10pt;
        }
        .signature-title {
            font-size: 8.5pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 1px;
        }

        .fine-print {
            text-align: center;
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 7.5pt;
            color: #6b7280;
        }
        .fine-print .disclaimer { font-style: italic; }

        /* -------- Uniform page --------- */
        .page-break { page-break-before: always; }
        table.uniforms {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 8px 0;
            font-size: 10pt;
        }
        table.uniforms thead th {
            background: #0e2746;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 9pt;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        table.uniforms tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        table.uniforms tbody tr:nth-child(even) td { background: #fafaf7; }
        .category-row td {
            background: #f5efe0 !important;
            color: #0e2746;
            font-weight: 700;
            font-size: 9pt;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border-top: 1.5px solid #b08a3e;
        }
        .subtotal-row td {
            background: #fafaf7 !important;
            font-weight: 700;
            font-size: 9.5pt;
            border-top: 1px solid #d1d5db;
        }
    </style>
</head>
<body>

    {{-- ============================================
         PAGE 1 — SCHOOL FEES
         ============================================ --}}

    <div class="letterhead">
        <table class="letterhead-inner">
            <tr>
                <td style="width: 74px;">
                    @if($__logoData)
                        <img class="crest" src="{{ $__logoData }}" alt="Crest">
                    @endif
                </td>
                <td>
                    <div class="school-title">St. Francis of Assisi Private School</div>
                    <div class="motto">For God and Country</div>
                </td>
                <td class="contact-strip">
                    Plot No 1310/4 East Kamenza, Chililabombwe<br>
                    <strong>+260 972 266 217</strong><br>
                    stfrancisofassisi.sfa@gmail.com
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title-bar">
        <div class="kicker">Official Fee Schedule</div>
        <div class="doc-title">{{ $grade }} · {{ $term }} · {{ $academicYear }}</div>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Section</td>
            <td class="value">{{ $grade }}</td>
            <td class="label">Academic Year</td>
            <td class="value">{{ $academicYear }}</td>
        </tr>
        <tr>
            <td class="label">Term</td>
            <td class="value">{{ $term }}</td>
            <td class="label">Issued</td>
            <td class="value">{{ date('j F Y') }}</td>
        </tr>
    </table>

    <table class="fees">
        <thead>
            <tr>
                <th>Description</th>
                <th style="width: 30%; text-align: right;">Amount (ZMW)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Tuition Fee</td>
                <td class="amount">{{ number_format($feeStructure->basic_fee, 2) }}</td>
            </tr>

            @foreach($schoolFees as $fee)
                @php $tag = $tagFor($fee['description']); @endphp
                <tr>
                    <td>
                        {{ $fee['description'] }}
                        @if($tag === 'annual')   <span class="tag annual">Annual</span> @endif
                        @if($tag === 'optional') <span class="tag optional">Optional</span> @endif
                    </td>
                    <td class="amount">{{ number_format($fee['amount'], 2) }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td>Payable this Term (Basic Tuition + Termly Charges)</td>
                <td class="amount">{{ number_format($termlyTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($annualExtras > 0 || $optionalExtras > 0)
        <div style="font-size: 8.5pt; color: #6b7280; margin: -4px 0 10px 0; line-height: 1.5;">
            @if($annualExtras > 0)
                <strong style="color:#8b6a1a;">Annual charges</strong> (PTA, Maintenance, Computer Fee):
                billed once per academic year — <strong>ZMW {{ number_format($annualExtras, 2) }}</strong> total.
            @endif
            @if($optionalExtras > 0)
                <br><strong style="color:#3730a3;">Optional charges</strong> (Bus): only payable if the pupil uses the school bus —
                <strong>ZMW {{ number_format($optionalExtras, 2) }}</strong>.
            @endif
        </div>
    @endif

    {{-- Payment options — MOBILE MONEY first, banks second --}}
    <div style="font-size: 8pt; letter-spacing: 0.18em; text-transform: uppercase; color: #8b1a1a; font-weight: 700; margin-top: 6px;">
        How to Pay
    </div>

    <div style="background: #8b1a1a; color: #ffffff; padding: 8px 12px; margin: 4px 0 8px 0; text-align: center; letter-spacing: 0.06em;">
        <strong style="font-size: 10pt;">NO CASH PAYMENTS</strong>
        <span style="font-size: 9pt; opacity: 0.95;"> · The school does not accept cash for fees or uniforms — regardless of the amount. Pay only via Mobile Money or Bank Deposit.</span>
    </div>
    <table class="pay">
        <tr>
            <td>
                <div class="box mobile">
                    <div class="kicker">Option 1 · Mobile Money</div>
                    <div class="box-title">Pay by USSD</div>
                    <div class="ussd">*543*719693*amount#</div>
                    <div class="row">Dial the code above from any Zambian line, replacing <em>amount</em> with the ZMW amount you are paying. Follow the prompts to confirm.</div>
                    <div class="row" style="margin-top: 4px; color: #6b7280; font-size: 8.5pt;">Example: <strong>*543*719693*3800#</strong> pays K3,800.</div>
                </div>
            </td>
            <td>
                <div class="box">
                    <div class="kicker">Option 2 · Bank Deposit</div>
                    <div class="box-title">Indo Zambia Bank</div>
                    <div class="row"><strong>School Fees Account</strong><br>Acct No. <span class="acct">0172040000103</span></div>
                    <div class="row" style="margin-top: 4px;"><strong>Bus / Uniform Account</strong><br>Acct No. <span class="acct">0172040000104</span></div>
                    <div class="row" style="margin-top: 6px; color: #6b7280; font-size: 8.5pt;">Deposit slip must show the pupil's full name and grade.</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="notes">
        <div class="kicker">Please Note</div>
        <ul>
            <li>All termly fees are due by the first day of the term.</li>
            <li><strong>PTA, Maintenance</strong> and <strong>Computer Fee</strong> are paid <strong>once per academic year</strong>, not every term.</li>
            <li><strong>Bus Fee</strong> is <strong>optional</strong> — payable only if the pupil uses the school bus.</li>
            <li><strong>No cash payments are accepted at the school under any circumstances</strong>, regardless of the amount. Use Mobile Money or Bank Deposit only.</li>
            <li>All queries should be directed to the Accounts Office.</li>
        </ul>
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div class="signature-wrap">
                    @if($__sigData)
                        <img src="{{ $__sigData }}" alt="Executive Director's Signature">
                    @endif
                </div>
                <div class="signature-line"></div>
                <div class="signature-name">Executive Director</div>
                <div class="signature-title">St. Francis of Assisi Private School</div>
            </td>
            <td>
                <div class="signature-wrap"></div>
                <div class="signature-line"></div>
                <div class="signature-name">Accounts Officer</div>
                <div class="signature-title">Bursar's Department</div>
            </td>
        </tr>
    </table>

    <div class="fine-print">
        <div class="disclaimer">This is an official document of {{ $schoolName }}. Any alterations render it invalid.</div>
        &copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.
    </div>

    {{-- ============================================
         PAGE 2 — UNIFORM & SPORTS PRICE LIST
         ============================================ --}}
    @if(count($uniformItems) > 0)
    <div class="page-break"></div>

    <div class="letterhead">
        <table class="letterhead-inner">
            <tr>
                <td style="width: 74px;">
                    @if($__logoData)
                        <img class="crest" src="{{ $__logoData }}" alt="Crest">
                    @endif
                </td>
                <td>
                    <div class="school-title">St. Francis of Assisi Private School</div>
                    <div class="motto">For God and Country</div>
                </td>
                <td class="contact-strip">
                    Plot No 1310/4 East Kamenza, Chililabombwe<br>
                    <strong>+260 972 266 217</strong><br>
                    stfrancisofassisi.sfa@gmail.com
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title-bar">
        <div class="kicker">Price List</div>
        <div class="doc-title">Uniform & Sports Attire — {{ $grade }} · {{ $academicYear }}</div>
    </div>

    @php
        $girlsItems = []; $boysItems = []; $sportsItems = [];
        $girlsTotal = 0; $boysTotal = 0; $sportsTotal = 0;
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
                $sportsItems[] = ['name' => str_replace('Sports - ', '', $desc), 'amount' => $amount];
                $sportsTotal += $amount;
            }
        }
    @endphp

    <table class="uniforms">
        <thead>
            <tr>
                <th style="width: 8%;">S/N</th>
                <th>Item Description</th>
                <th style="width: 22%; text-align: right;">Price (ZMW)</th>
            </tr>
        </thead>
        <tbody>
            @if(count($girlsItems) > 0)
                <tr class="category-row"><td colspan="3">Girls Uniform</td></tr>
                @foreach($girlsItems as $i => $it)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td>{{ $it['name'] }}</td>
                        <td class="amount">{{ number_format($it['amount'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td></td>
                    <td style="text-align: right;">Girls Uniform Total</td>
                    <td class="amount">{{ number_format($girlsTotal, 2) }}</td>
                </tr>
            @endif

            @if(count($boysItems) > 0)
                <tr class="category-row"><td colspan="3">Boys Uniform</td></tr>
                @foreach($boysItems as $i => $it)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td>{{ $it['name'] }}</td>
                        <td class="amount">{{ number_format($it['amount'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td></td>
                    <td style="text-align: right;">Boys Uniform Total</td>
                    <td class="amount">{{ number_format($boysTotal, 2) }}</td>
                </tr>
            @endif

            @if(count($sportsItems) > 0)
                <tr class="category-row"><td colspan="3">Sports Attire</td></tr>
                @foreach($sportsItems as $i => $it)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td>{{ $it['name'] }}</td>
                        <td class="amount">{{ number_format($it['amount'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td></td>
                    <td style="text-align: right;">Sports Attire Total</td>
                    <td class="amount">{{ number_format($sportsTotal, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="notes">
        <div class="kicker">Please Note</div>
        <ul>
            <li>Uniforms and sports attire are purchased separately from school fees.</li>
            <li>Payment for uniforms goes to the <strong>Bus / Uniform</strong> account (see Page 1) or Mobile Money.</li>
            <li>Prices are subject to change without prior notice.</li>
            <li>For enquiries, please contact the school office.</li>
        </ul>
    </div>

    <div class="fine-print">
        <div class="disclaimer">This is an official document of {{ $schoolName }}. Any alterations render it invalid.</div>
        &copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.
    </div>
    @endif

</body>
</html>
