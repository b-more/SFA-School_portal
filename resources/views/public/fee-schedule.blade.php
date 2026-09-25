<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fee Schedule · St. Francis of Assisi Private School</title>
    <style>
        :root {
            --navy: #0e2746;
            --crimson: #8b1a1a;
            --gold: #b08a3e;
            --parchment: #fafaf7;
            --ink: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
            color: var(--ink);
            background: var(--parchment);
            line-height: 1.5;
        }
        .page {
            max-width: 720px;
            margin: 0 auto;
            padding: 48px 20px 60px;
        }
        .kicker {
            font-size: 11px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--crimson);
            font-weight: 700;
        }
        h1 {
            font-family: 'EB Garamond', Georgia, serif;
            font-size: 34px;
            color: var(--navy);
            margin: 6px 0 4px;
            letter-spacing: -0.005em;
        }
        p.lead {
            font-family: 'EB Garamond', Georgia, serif;
            font-style: italic;
            color: var(--muted);
            margin: 0 0 24px;
            font-size: 16px;
        }
        .card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-top: 3px solid var(--navy);
            padding: 28px 26px;
        }
        .row { margin-bottom: 18px; }
        label {
            display: block;
            font-size: 11px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 6px;
        }
        select {
            width: 100%;
            padding: 12px 14px;
            font-size: 15px;
            border: 1px solid var(--border);
            background: #ffffff;
            color: var(--ink);
            font-family: inherit;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3e%3cpath d='M1 1l5 5 5-5' stroke='%230e2746' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }
        select:focus {
            outline: none;
            border-color: var(--navy);
            box-shadow: 0 0 0 2px rgba(14,39,70,0.08);
        }
        button {
            appearance: none;
            border: none;
            background: var(--navy);
            color: #ffffff;
            padding: 14px 24px;
            font-size: 14px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            width: 100%;
        }
        button:hover { background: #071a33; }
        .note {
            font-size: 13px;
            color: var(--muted);
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            line-height: 1.6;
        }
        .note strong { color: var(--navy); }
        .errors {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-left: 3px solid var(--crimson);
            padding: 12px 14px;
            margin-bottom: 18px;
            font-size: 14px;
            color: #7f1d1d;
        }
        .errors ul { margin: 0; padding-left: 18px; }
        .back {
            display: inline-block;
            margin-top: 22px;
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            font-family: 'EB Garamond', Georgia, serif;
            font-style: italic;
        }
        .back:hover { color: var(--navy); }
        /* Now-enrolling strip — matches every other public surface */
        .site-announce { background: #0F2440; color: #E3EAF4; font-size: 14px; font-family: 'Inter', system-ui, sans-serif; }
        .site-announce .row { max-width: 720px; margin: 0 auto; padding: 8px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .site-announce strong { color: #FFC83D; letter-spacing: .02em; }
        .site-announce a { color: #FFC83D; font-weight: 700; text-decoration: none; }
        .site-announce a:hover { color: #fff; }
        .site-announce .contact { display: none; }
        @media (min-width: 720px) { .site-announce .contact { display: inline-flex; gap: 22px; align-items: center; white-space: nowrap; color: rgba(255,255,255,.82); } .site-announce .contact a { color: rgba(255,255,255,.82); font-weight: 400; } }
        @media (prefers-reduced-motion: no-preference) {
            @keyframes site-glow { 0%,100% { text-shadow: 0 0 0 rgba(255,200,61,0); } 50% { text-shadow: 0 0 14px rgba(255,200,61,.55); } }
            .site-announce strong { animation: site-glow 3.2s ease-in-out infinite; }
        }
    </style>
</head>
<body>
    <div class="site-announce">
        <div class="row">
            <span><strong>Now enrolling</strong> · Baby Class through Grade 12 · Applications welcome year-round. <a href="/admissions">Begin an application →</a></span>
            <span class="contact"><a href="tel:+260972266217">+260 972 266 217</a><span>Plot 1310/4 East Kamenza, Chililabombwe</span></span>
        </div>
    </div>
    <div class="page">
        <div class="kicker">Downloads</div>
        <h1>Fee Schedule</h1>
        <p class="lead">Choose a section and a term, and we'll email — well, download — the current fee schedule as a PDF.</p>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <form method="GET" action="{{ route('public.fee-schedule.download') }}">
                <div class="row">
                    <label for="section_id">Section</label>
                    <select name="section_id" id="section_id" required>
                        <option value="">— Choose a section —</option>
                        @foreach ($sections as $s)
                            <option value="{{ $s->id }}" @selected(old('section_id') == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <label for="term_id">Term</label>
                    <select name="term_id" id="term_id" required>
                        <option value="">— Choose a term —</option>
                        @foreach ($terms as $t)
                            <option value="{{ $t->id }}" @selected(old('term_id', $currentTerm?->id) == $t->id)>
                                {{ $t->name }}{{ optional($t->academicYear)->name ? ' · ' . $t->academicYear->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit">Download PDF</button>
            </form>

            <div class="note">
                Payment options are printed on the PDF. <strong>No cash payments are accepted</strong> regardless of the amount — use Mobile Money (<em>*388*719693*amount#</em>) or Bank Deposit only.
            </div>
        </div>

        <a href="/" class="back">← Back to the school website</a>
    </div>
</body>
</html>
