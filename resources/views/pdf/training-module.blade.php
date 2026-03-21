<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Module {{ $module }} - {{ $moduleData['title'] }}</title>
<style>
    @page { size: A4 portrait; margin: 18mm 16mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1a1a2e; line-height: 1.6; }

    .cover { text-align: center; padding: 60px 0 40px; border-bottom: 3px solid #1e3a5f; margin-bottom: 30px; }
    .cover .school { font-size: 20px; font-weight: 700; color: #1e3a5f; text-transform: uppercase; letter-spacing: 2px; }
    .cover .doc-title { font-size: 14px; color: #666; margin-top: 6px; }
    .cover .mod-badge { display: inline-block; margin-top: 20px; background: #1e3a5f; color: white; padding: 10px 30px; border-radius: 6px; font-size: 16px; font-weight: 700; }
    .cover .mod-title { font-size: 18px; color: #1e3a5f; margin-top: 12px; font-weight: 700; }
    .cover .mod-meta { font-size: 10px; color: #888; margin-top: 8px; }

    .lesson-header { background: #f0f7ff; border-left: 4px solid #1e3a5f; padding: 10px 14px; margin: 24px 0 14px; page-break-after: avoid; }
    .lesson-header .lnum { font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; color: #2d5a8e; font-weight: 700; }
    .lesson-header h2 { font-size: 14px; color: #1e3a5f; margin-top: 2px; }
    .lesson-header .lobj { font-size: 9px; color: #666; margin-top: 3px; font-style: italic; }
    .lesson-header .ldur { font-size: 9px; color: #888; }

    h3 { font-size: 11px; color: #1e3a5f; margin: 14px 0 6px; padding-bottom: 3px; border-bottom: 1px solid #e5e7eb; page-break-after: avoid; }
    p { margin-bottom: 8px; font-size: 10px; white-space: pre-line; }

    table { width: 100%; border-collapse: collapse; margin: 8px 0 14px; font-size: 9px; }
    thead th { background: #1e3a5f; color: white; padding: 6px 8px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.4px; font-weight: 700; }
    tbody td { padding: 5px 8px; border: 1px solid #e5e7eb; vertical-align: top; }
    tbody tr:nth-child(even) { background: #f9fafb; }

    .callout { padding: 8px 12px; margin: 8px 0 12px; border-radius: 4px; font-size: 9px; }
    .callout-warn { background: #fefce8; border-left: 3px solid #f59e0b; color: #854d0e; }
    .callout-tip { background: #f0fdf4; border-left: 3px solid #10b981; color: #166534; }
    .callout-info { background: #eff6ff; border-left: 3px solid #3b82f6; color: #1e40af; }

    .quiz-box { background: #fffbeb; border: 1.5px solid #fbbf24; border-radius: 6px; padding: 12px 14px; margin: 16px 0; page-break-inside: avoid; }
    .quiz-title { font-size: 10px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; padding-bottom: 4px; border-bottom: 1px dashed #e5c100; }
    .quiz-q { margin-bottom: 10px; }
    .quiz-q .qt { font-weight: 700; font-size: 10px; color: #422006; margin-bottom: 4px; }
    .quiz-q .qo { padding-left: 14px; font-size: 9px; color: #4b5563; }
    .quiz-q .qo div { margin-bottom: 1px; }
    .quiz-q .qa { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 3px; font-size: 8px; font-weight: 700; margin-top: 3px; display: inline-block; }

    .test-box { background: #fdf2f8; border: 1.5px solid #ec4899; border-radius: 6px; padding: 14px 16px; margin: 20px 0; }
    .test-title { font-size: 12px; font-weight: 700; color: #9d174d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; padding-bottom: 4px; border-bottom: 2px solid #f9a8d4; }
    .test-info { font-size: 9px; color: #be185d; margin-bottom: 12px; font-style: italic; }
    .test-q { margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dotted #f9a8d4; }
    .test-q:last-child { border-bottom: none; }
    .test-q .tqn { font-weight: 700; color: #9d174d; }
    .test-q .tqt { font-weight: 600; font-size: 10px; color: #1a1a2e; margin-bottom: 4px; }
    .test-q .tqo { padding-left: 14px; font-size: 9px; color: #4b5563; }
    .test-q .tqo div { margin-bottom: 1px; }
    .test-q .tqa { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 3px; font-size: 8px; font-weight: 700; margin-top: 3px; display: inline-block; }

    .footer { margin-top: 20px; padding-top: 8px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 8px; color: #888; }
    .page-break { page-break-before: always; }
</style>
</head>
<body>

{{-- Cover --}}
<div class="cover">
    <div class="school">{{ $schoolName }}</div>
    <div class="doc-title">Teacher Portal Training Programme</div>
    <div class="mod-badge">Module {{ $module }}</div>
    <div class="mod-title">{{ $moduleData['title'] }}</div>
    <div class="mod-meta">
        {{ count($moduleData['lessons']) }} Lessons
        &bull;
        {{ collect($moduleData['lessons'])->sum(fn($l) => (int)$l['dur']) }} Minutes
        &bull;
        {{ collect($moduleData['lessons'])->sum(fn($l) => count($l['quiz'])) }} Quiz Questions
        &bull;
        {{ count($moduleData['test']) }} Test Questions
        &bull;
        Generated: {{ $generatedAt }}
    </div>
</div>

{{-- Lessons --}}
@foreach($moduleData['lessons'] as $li => $lesson)
    @if($li > 0)<div class="page-break"></div>@endif

    <div class="lesson-header">
        <div class="lnum">Lesson {{ $lesson['num'] }}</div>
        <h2>{{ $lesson['title'] }}</h2>
        <div class="lobj">Objective: {{ $lesson['objective'] }}</div>
        <div class="ldur">Duration: {{ $lesson['dur'] }}</div>
    </div>

    @foreach($lesson['content'] as $section)
        <h3>{{ $section['heading'] }}</h3>

        @if(isset($section['body']))
            <p>{{ $section['body'] }}</p>
        @endif

        @if(isset($section['table']))
            <table>
                <thead>
                    <tr>
                        @foreach($section['table']['headers'] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($section['table']['rows'] as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{!! nl2br(e($cell)) !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    {{-- Lesson Quiz --}}
    @if(!empty($lesson['quiz']))
        <div class="quiz-box">
            <div class="quiz-title">Lesson {{ $lesson['num'] }} &mdash; Quick Quiz</div>
            @foreach($lesson['quiz'] as $qi => $q)
                <div class="quiz-q">
                    <div class="qt">Q{{ $qi + 1 }}: {{ $q['q'] }}</div>
                    <div class="qo">
                        @foreach($q['opts'] as $opt)
                            <div>{{ $opt }}</div>
                        @endforeach
                    </div>
                    <div class="qa">Answer: {{ $q['ans'] }}</div>
                </div>
            @endforeach
        </div>
    @endif
@endforeach

{{-- Module Test --}}
<div class="page-break"></div>
<div class="test-box">
    <div class="test-title">Module {{ $module }} Test</div>
    <div class="test-info">{{ count($moduleData['test']) }} Multiple Choice Questions &mdash; {{ $moduleData['title'] }}</div>

    @foreach($moduleData['test'] as $qi => $q)
        <div class="test-q">
            <div class="tqt"><span class="tqn">{{ $qi + 1 }}.</span> {{ $q['q'] }}</div>
            <div class="tqo">
                @foreach($q['opts'] as $opt)
                    <div>{{ $opt }}</div>
                @endforeach
            </div>
            <div class="tqa">Answer: {{ $q['ans'] }}</div>
        </div>
    @endforeach
</div>

<div class="footer">
    {{ $schoolName }} | Teacher Portal Training Programme | Module {{ $module }}: {{ $moduleData['title'] }} | Generated: {{ $generatedAt }}
</div>

</body>
</html>
