<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Teacher Portal Training - Complete Manual</title>
<style>
    @page { size: A4 portrait; margin: 18mm 16mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1a1a2e; line-height: 1.6; }

    .cover { text-align: center; padding: 80px 0 50px; }
    .cover .school { font-size: 22px; font-weight: 700; color: #1e3a5f; text-transform: uppercase; letter-spacing: 2px; }
    .cover .main-title { font-size: 20px; color: #1e3a5f; margin-top: 20px; font-weight: 700; }
    .cover .subtitle { font-size: 12px; color: #666; margin-top: 6px; }
    .cover .badge { display: inline-block; margin-top: 25px; background: #1e3a5f; color: white; padding: 8px 20px; border-radius: 20px; font-size: 10px; font-weight: 600; letter-spacing: 1px; }
    .cover .meta { margin-top: 20px; font-size: 10px; color: #888; }
    .cover .stats { margin-top: 15px; font-size: 11px; color: #4b5563; }
    .cover .stats strong { color: #1e3a5f; }
    .cover-line { border-top: 3px solid #1e3a5f; margin: 30px auto 0; width: 60%; }

    .toc { margin-top: 20px; }
    .toc h2 { font-size: 14px; color: #1e3a5f; border-bottom: 2px solid #1e3a5f; padding-bottom: 4px; margin-bottom: 12px; }
    .toc-mod { margin-bottom: 10px; }
    .toc-mod-title { font-weight: 700; color: #1e3a5f; font-size: 11px; }
    .toc-lessons { padding-left: 16px; font-size: 10px; color: #4b5563; margin-top: 2px; }

    .module-cover { text-align: center; padding: 40px 0 30px; border-bottom: 2px solid #1e3a5f; margin-bottom: 20px; }
    .module-cover .mc-badge { display: inline-block; background: #1e3a5f; color: white; padding: 8px 24px; border-radius: 6px; font-size: 14px; font-weight: 700; }
    .module-cover .mc-title { font-size: 16px; color: #1e3a5f; margin-top: 10px; font-weight: 700; }
    .module-cover .mc-meta { font-size: 9px; color: #888; margin-top: 6px; }

    .lesson-header { background: #f0f7ff; border-left: 4px solid #1e3a5f; padding: 10px 14px; margin: 20px 0 12px; page-break-after: avoid; }
    .lesson-header .lnum { font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; color: #2d5a8e; font-weight: 700; }
    .lesson-header h2 { font-size: 13px; color: #1e3a5f; margin-top: 2px; }
    .lesson-header .lobj { font-size: 9px; color: #666; margin-top: 2px; font-style: italic; }
    .lesson-header .ldur { font-size: 8px; color: #888; }

    h3 { font-size: 11px; color: #1e3a5f; margin: 12px 0 5px; padding-bottom: 2px; border-bottom: 1px solid #e5e7eb; page-break-after: avoid; }
    p { margin-bottom: 7px; font-size: 10px; white-space: pre-line; }

    table { width: 100%; border-collapse: collapse; margin: 6px 0 12px; font-size: 9px; }
    thead th { background: #1e3a5f; color: white; padding: 5px 7px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.4px; font-weight: 700; }
    tbody td { padding: 4px 7px; border: 1px solid #e5e7eb; vertical-align: top; }
    tbody tr:nth-child(even) { background: #f9fafb; }

    .quiz-box { background: #fffbeb; border: 1.5px solid #fbbf24; border-radius: 5px; padding: 10px 12px; margin: 14px 0; page-break-inside: avoid; }
    .quiz-title { font-size: 9px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; padding-bottom: 3px; border-bottom: 1px dashed #e5c100; }
    .quiz-q { margin-bottom: 8px; }
    .quiz-q .qt { font-weight: 700; font-size: 9px; color: #422006; margin-bottom: 3px; }
    .quiz-q .qo { padding-left: 12px; font-size: 8.5px; color: #4b5563; }
    .quiz-q .qo div { margin-bottom: 1px; }
    .quiz-q .qa { background: #d1fae5; color: #065f46; padding: 2px 7px; border-radius: 3px; font-size: 7.5px; font-weight: 700; margin-top: 2px; display: inline-block; }

    .test-box { background: #fdf2f8; border: 1.5px solid #ec4899; border-radius: 5px; padding: 12px 14px; margin: 16px 0; }
    .test-title { font-size: 11px; font-weight: 700; color: #9d174d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; padding-bottom: 3px; border-bottom: 2px solid #f9a8d4; }
    .test-info { font-size: 8px; color: #be185d; margin-bottom: 10px; font-style: italic; }
    .test-q { margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px dotted #f9a8d4; }
    .test-q:last-child { border-bottom: none; }
    .test-q .tqn { font-weight: 700; color: #9d174d; }
    .test-q .tqt { font-weight: 600; font-size: 9.5px; color: #1a1a2e; margin-bottom: 3px; }
    .test-q .tqo { padding-left: 12px; font-size: 8.5px; color: #4b5563; }
    .test-q .tqo div { margin-bottom: 1px; }
    .test-q .tqa { background: #d1fae5; color: #065f46; padding: 2px 7px; border-radius: 3px; font-size: 7.5px; font-weight: 700; margin-top: 2px; display: inline-block; }

    .footer { margin-top: 16px; padding-top: 6px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 7px; color: #999; }
    .page-break { page-break-before: always; }
</style>
</head>
<body>

{{-- Cover Page --}}
<div class="cover">
    <div class="school">{{ $schoolName }}</div>
    <div class="main-title">Teacher Portal Training Manual</div>
    <div class="subtitle">School Management Information System (MIS)</div>
    <div class="badge">Complete Training Programme</div>
    <div class="stats">
        <strong>5</strong> Modules &bull;
        <strong>14</strong> Lessons &bull;
        <strong>42</strong> Quiz Questions &bull;
        <strong>50</strong> Test Questions
    </div>
    <div class="meta">Audience: All Teaching Staff (Primary & Secondary) | Generated: {{ $generatedAt }}</div>
    <div class="cover-line"></div>
</div>

{{-- Table of Contents --}}
<div class="toc">
    <h2>Programme Structure</h2>
    @foreach($courseData as $modNum => $mod)
        <div class="toc-mod">
            <div class="toc-mod-title">Module {{ $modNum }}: {{ $mod['title'] }}</div>
            <div class="toc-lessons">
                @foreach($mod['lessons'] as $lesson)
                    <div>Lesson {{ $lesson['num'] }} &mdash; {{ $lesson['title'] }} ({{ $lesson['dur'] }})</div>
                @endforeach
                <div>Module {{ $modNum }} Test ({{ count($mod['test']) }} Questions)</div>
            </div>
        </div>
    @endforeach
</div>

{{-- Each Module --}}
@foreach($courseData as $modNum => $mod)
    <div class="page-break"></div>

    <div class="module-cover">
        <div class="mc-badge">Module {{ $modNum }}</div>
        <div class="mc-title">{{ $mod['title'] }}</div>
        <div class="mc-meta">
            {{ count($mod['lessons']) }} Lessons &bull;
            {{ collect($mod['lessons'])->sum(fn($l) => (int)$l['dur']) }} Minutes &bull;
            {{ collect($mod['lessons'])->sum(fn($l) => count($l['quiz'])) }} Quiz Questions &bull;
            {{ count($mod['test']) }} Test Questions
        </div>
    </div>

    @foreach($mod['lessons'] as $li => $lesson)
        @if($li > 0)<div style="margin-top: 20px;"></div>@endif

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
                    <thead><tr>@foreach($section['table']['headers'] as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
                    <tbody>
                        @foreach($section['table']['rows'] as $row)
                            <tr>@foreach($row as $cell)<td>{!! nl2br(e($cell)) !!}</td>@endforeach</tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        @if(!empty($lesson['quiz']))
            <div class="quiz-box">
                <div class="quiz-title">Lesson {{ $lesson['num'] }} Quiz</div>
                @foreach($lesson['quiz'] as $qi => $q)
                    <div class="quiz-q">
                        <div class="qt">Q{{ $qi+1 }}: {{ $q['q'] }}</div>
                        <div class="qo">@foreach($q['opts'] as $opt)<div>{{ $opt }}</div>@endforeach</div>
                        <div class="qa">Answer: {{ $q['ans'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach

    {{-- Module Test --}}
    <div class="test-box">
        <div class="test-title">Module {{ $modNum }} Test</div>
        <div class="test-info">{{ count($mod['test']) }} Questions &mdash; {{ $mod['title'] }}</div>
        @foreach($mod['test'] as $qi => $q)
            <div class="test-q">
                <div class="tqt"><span class="tqn">{{ $qi+1 }}.</span> {{ $q['q'] }}</div>
                <div class="tqo">@foreach($q['opts'] as $opt)<div>{{ $opt }}</div>@endforeach</div>
                <div class="tqa">Answer: {{ $q['ans'] }}</div>
            </div>
        @endforeach
    </div>
@endforeach

<div class="footer">
    {{ $schoolName }} | Teacher Portal Training Programme | Complete Manual | {{ $generatedAt }}
</div>

</body>
</html>
