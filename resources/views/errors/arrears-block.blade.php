<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report Card Unavailable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f8fafc; color: #1f2937; margin: 0; padding: 40px 20px; }
        .card { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 24px rgba(0,0,0,.08); border-top: 6px solid #dc2626; }
        h1 { color: #dc2626; font-size: 22px; margin: 0 0 8px; }
        .student { font-size: 16px; color: #6b7280; margin-bottom: 24px; }
        .amount-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; text-align: center; margin: 16px 0 24px; }
        .amount-lbl { font-size: 12px; color: #991b1b; text-transform: uppercase; letter-spacing: .08em; font-weight: 600; }
        .amount { font-size: 32px; font-weight: 800; color: #991b1b; margin-top: 4px; }
        .next { font-size: 14px; line-height: 1.6; color: #4b5563; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 18px; background: #1e3a5f; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>⛔ Report Card Unavailable</h1>
        <div class="student">For <strong>{{ $student->name }}</strong></div>

        <div class="amount-box">
            <div class="amount-lbl">Outstanding Balance</div>
            <div class="amount">ZMW {{ number_format($amount, 2) }}</div>
        </div>

        <div class="next">
            School policy requires all tuition fees to be settled before report cards can be issued.
            Please visit the school accounts office to clear the outstanding balance, after which the
            report card will be available for download.
        </div>

        <a href="javascript:history.back()" class="btn">Go Back</a>
    </div>
</body>
</html>
