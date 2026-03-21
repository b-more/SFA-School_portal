<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bulk Report Cards</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @foreach($reports as $index => $reportData)
        @include('pdf.report-card', $reportData)
        @if($index < count($reports) - 1)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
