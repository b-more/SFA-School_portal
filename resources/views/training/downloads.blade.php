<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Training Materials - Downloads</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f0f4f8; color: #1a1a2e; min-height: 100vh; }

        .header { background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%); color: white; padding: 40px 20px; text-align: center; }
        .header h1 { font-size: 28px; font-weight: 700; letter-spacing: 1px; }
        .header .school { font-size: 14px; opacity: 0.85; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 2px; }
        .header .subtitle { font-size: 15px; opacity: 0.75; margin-top: 6px; }
        .header .stats { margin-top: 16px; display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; }
        .header .stat { text-align: center; }
        .header .stat-num { font-size: 24px; font-weight: 700; }
        .header .stat-label { font-size: 11px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; }

        .container { max-width: 960px; margin: -30px auto 40px; padding: 0 20px; position: relative; z-index: 1; }

        .download-all { background: white; border-radius: 12px; padding: 24px 28px; margin-bottom: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .download-all .info h2 { font-size: 18px; color: #1e3a5f; }
        .download-all .info p { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .btn-all { display: inline-flex; align-items: center; gap: 8px; background: #1e3a5f; color: white; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.2s; }
        .btn-all:hover { background: #2d5a8e; }
        .btn-all svg { width: 20px; height: 20px; }

        .modules-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }

        .module-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-left: 5px solid #1e3a5f; transition: transform 0.15s, box-shadow 0.15s; }
        .module-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.1); }

        .mc-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .mc-badge { background: #1e3a5f; color: white; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; white-space: nowrap; }
        .mc-title { font-size: 16px; font-weight: 700; color: #1e3a5f; margin-top: 6px; }
        .mc-meta { font-size: 12px; color: #9ca3af; margin-top: 4px; }

        .mc-lessons { margin-top: 14px; padding-top: 14px; border-top: 1px solid #f3f4f6; }
        .mc-lessons ul { list-style: none; display: grid; grid-template-columns: 1fr 1fr; gap: 6px 20px; }
        .mc-lessons li { font-size: 12px; color: #4b5563; padding-left: 16px; position: relative; }
        .mc-lessons li::before { content: ''; position: absolute; left: 0; top: 6px; width: 6px; height: 6px; border-radius: 50%; background: #d1d5db; }

        .mc-actions { margin-top: 16px; display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-mod { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px; transition: all 0.2s; }
        .btn-download { background: #eff6ff; color: #1e3a5f; border: 1px solid #bfdbfe; }
        .btn-download:hover { background: #dbeafe; }
        .btn-download svg { width: 16px; height: 16px; }

        .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; }
        .back-link { display: inline-block; margin-top: 10px; color: #1e3a5f; text-decoration: none; font-weight: 600; font-size: 13px; }
        .back-link:hover { text-decoration: underline; }

        @media (max-width: 640px) {
            .header h1 { font-size: 22px; }
            .mc-lessons ul { grid-template-columns: 1fr; }
            .download-all { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="school">St. Francis of Assisi Private School</div>
    <h1>Teacher Portal Training Programme</h1>
    <div class="subtitle">Downloadable PDF Training Materials</div>
    <div class="stats">
        <div class="stat"><div class="stat-num">5</div><div class="stat-label">Modules</div></div>
        <div class="stat"><div class="stat-num">14</div><div class="stat-label">Lessons</div></div>
        <div class="stat"><div class="stat-num">42</div><div class="stat-label">Quiz Questions</div></div>
        <div class="stat"><div class="stat-num">50</div><div class="stat-label">Test Questions</div></div>
    </div>
</div>

<div class="container">
    <div class="download-all">
        <div class="info">
            <h2>Complete Training Manual</h2>
            <p>Download all 5 modules as a single comprehensive PDF document</p>
        </div>
        <a href="{{ route('training.download.all') }}" class="btn-all">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12V7a2 2 0 00-2-2H5a2 2 0 00-2 2v5"/></svg>
            Download Complete Manual (PDF)
        </a>
    </div>

    <div class="modules-grid">
        @foreach($courseData as $num => $mod)
        <div class="module-card">
            <div class="mc-top">
                <div>
                    <span class="mc-badge">MODULE {{ $num }}</span>
                    <div class="mc-title">{{ $mod['title'] }}</div>
                    <div class="mc-meta">
                        {{ count($mod['lessons']) }} Lessons &bull;
                        {{ collect($mod['lessons'])->sum(fn($l) => (int)$l['dur']) }} Minutes &bull;
                        {{ collect($mod['lessons'])->sum(fn($l) => count($l['quiz'])) }} Quiz Questions &bull;
                        {{ count($mod['test']) }} Test Questions
                    </div>
                </div>
            </div>

            <div class="mc-lessons">
                <ul>
                    @foreach($mod['lessons'] as $lesson)
                    <li>{{ $lesson['num'] }} &mdash; {{ $lesson['title'] }} ({{ $lesson['dur'] }})</li>
                    @endforeach
                </ul>
            </div>

            <div class="mc-actions">
                <a href="{{ route('training.download.module', $num) }}" class="btn-mod btn-download">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Module {{ $num }} PDF
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="footer">
    <p>St. Francis of Assisi Private School &bull; Teacher Portal Training Programme &bull; {{ now()->format('F Y') }}</p>
    <a href="/admin" class="back-link">&larr; Back to Portal</a>
</div>

</body>
</html>
