<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bulk Payslips</title>
    <style>
        @page { margin: 0; }
        body  { margin: 0; padding: 0; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @foreach($payrolls as $index => $payroll)
        @include('payslips.pdf', ['payroll' => $payroll])
        @if(! $loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
