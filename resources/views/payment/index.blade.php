@php
    $settings   = \App\Models\SchoolSettings::first();
    $schoolName = $settings->school_name ?? 'St. Francis of Assisi';
    $shortName  = 'St. Francis of Assisi';
    $motto      = $settings->school_motto ?? 'Faith · Family · Future';
    $phone      = $settings->phone        ?? '+260 977 000 000';
    $email      = $settings->email        ?? 'info@stfrancisofassisizm.com';
    $logoPath   = $settings && $settings->school_logo
                    ? asset('storage/' . ltrim($settings->school_logo, '/'))
                    : asset('images/logo.png');
    $year       = now()->year;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b2545">
    <meta name="description" content="Pay {{ $schoolName }} school fees securely via mobile money — no login required.">
    <meta name="robots" content="noindex">
    <title>Pay School Fees — {{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ $logoPath }}">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy-900:#0b2545; --navy-800:#13315c; --navy-700:#1e3a5f;
            --crimson:#c8102e; --crimson-dark:#8e0a20;
            --gold:#c9a227;
            --green:#059669; --green-dark:#047857;
            --amber:#d97706;
            --red:#dc2626;
            --ink:#0f172a; --slate:#475569; --muted:#64748b;
            --line:#e2e8f0; --bg:#fff; --bg-soft:#f8fafc; --bg-tint:#eef2f7;
            --radius:14px; --radius-sm:10px;
            --shadow-sm:0 1px 2px rgba(15,23,42,.06),0 1px 3px rgba(15,23,42,.05);
            --shadow:0 4px 6px -1px rgba(15,23,42,.08),0 2px 4px -2px rgba(15,23,42,.06);
            --shadow-lg:0 20px 40px -20px rgba(11,37,69,.35),0 10px 20px -15px rgba(11,37,69,.25);
            --max:1200px;
        }
        *,*::before,*::after { box-sizing:border-box; }
        html { -webkit-text-size-adjust:100%; scroll-behavior:smooth; }
        @media (prefers-reduced-motion: reduce) { *,*::before,*::after { animation-duration:.001s !important; transition-duration:.001s !important; } }
        body { margin:0; font-family:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,sans-serif; font-size:16px; line-height:1.6; color:var(--ink); background:var(--bg-soft); -webkit-font-smoothing:antialiased; }
        img,svg { max-width:100%; display:block; }
        a { color:inherit; text-decoration:none; }
        button { font:inherit; cursor:pointer; border:0; background:none; }
        h1,h2,h3 { margin:0; line-height:1.15; letter-spacing:-0.02em; color:var(--navy-900); font-weight:700; }
        p { margin:0; }
        input,select { font:inherit; color:inherit; }
        .container { width:100%; max-width:var(--max); margin-inline:auto; padding-inline:1.25rem; }
        .hidden { display:none !important; }

        /* Topbar */
        .topbar { background:var(--navy-900); color:rgba(255,255,255,.85); font-size:13px; }
        .topbar-inner { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.5rem 1.5rem; padding-block:.55rem; }
        .topbar a { color:inherit; }
        .topbar a:hover { color:#fff; }
        .topbar .meta { display:flex; flex-wrap:wrap; gap:1.25rem; align-items:center; }
        .topbar .meta span { display:inline-flex; align-items:center; gap:.45rem; }
        @media (max-width:640px) { .topbar .hide-sm { display:none; } }

        /* Nav */
        .nav { position:sticky; top:0; z-index:60; background:rgba(255,255,255,.92); backdrop-filter:saturate(180%) blur(12px); -webkit-backdrop-filter:saturate(180%) blur(12px); border-bottom:1px solid var(--line); transition:box-shadow .25s; }
        .nav.scrolled { box-shadow:var(--shadow); }
        .nav-inner { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding-block:.8rem; }
        .brand { display:flex; align-items:center; gap:.75rem; min-width:0; }
        .brand img { width:44px; height:44px; border-radius:50%; object-fit:cover; }
        .brand .b-name { font-weight:700; color:var(--navy-900); font-size:.98rem; line-height:1.15; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:60vw; }
        .brand .b-tag  { font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.12em; line-height:1.15; }
        .nav-links { display:none; align-items:center; gap:.25rem; }
        .nav-links a { padding:.55rem .85rem; border-radius:8px; font-weight:500; font-size:.92rem; color:var(--slate); transition:color .15s,background .15s; }
        .nav-links a:hover, .nav-links a.active { color:var(--navy-900); background:var(--bg-soft); }
        .nav-cta { display:none; }
        @media (min-width:960px) { .nav-links, .nav-cta { display:inline-flex; gap:.5rem; } }

        /* Buttons */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.85rem 1.25rem; border-radius:999px; font-weight:600; font-size:.95rem; transition:transform .15s,background .15s,box-shadow .15s,color .15s; white-space:nowrap; width:100%; }
        .btn:focus-visible { outline:2px solid var(--gold); outline-offset:2px; }
        .btn:disabled { opacity:.6; cursor:not-allowed; transform:none !important; }
        .btn-primary { background:var(--crimson); color:#fff; box-shadow:0 4px 12px rgba(200,16,46,.25); }
        .btn-primary:hover:not(:disabled) { background:var(--crimson-dark); transform:translateY(-1px); }
        .btn-success { background:var(--green); color:#fff; box-shadow:0 4px 12px rgba(5,150,105,.25); }
        .btn-success:hover:not(:disabled) { background:var(--green-dark); transform:translateY(-1px); }
        .btn-navy { background:var(--navy-900); color:#fff; }
        .btn-navy:hover:not(:disabled) { background:var(--navy-700); transform:translateY(-1px); }
        .btn-ghost { color:var(--navy-900); background:transparent; border:1px solid var(--line); }
        .btn-ghost:hover:not(:disabled) { background:var(--bg-soft); }

        /* Hero */
        .pg-hero { background:linear-gradient(120deg, var(--navy-900) 0%, var(--navy-700) 100%); color:#fff; padding-block:clamp(2.5rem,6vw,4rem) clamp(1.5rem,4vw,2.5rem); position:relative; overflow:hidden; }
        .pg-hero::after { content:''; position:absolute; right:-100px; top:-100px; width:280px; height:280px; background:radial-gradient(circle,rgba(201,162,39,.25),transparent 65%); pointer-events:none; }
        .pg-hero .crumbs { font-size:.78rem; letter-spacing:.12em; text-transform:uppercase; color:rgba(255,255,255,.7); margin-bottom:.65rem; }
        .pg-hero .crumbs a { color:rgba(255,255,255,.7); }
        .pg-hero .crumbs a:hover { color:#fff; }
        .pg-hero h1 { color:#fff; font-size:clamp(1.65rem,3.5vw,2.4rem); }
        .pg-hero p  { color:rgba(255,255,255,.85); margin-top:.6rem; max-width:600px; font-size:1rem; }
        .pg-hero .pill { display:inline-flex; align-items:center; gap:.4rem; margin-top:1rem; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.25); color:#fff; padding:.35rem .8rem; border-radius:999px; font-size:.8rem; font-weight:500; letter-spacing:.05em; }
        .pg-hero .pill svg { color:var(--gold); }

        /* Layout */
        .pay-shell { padding-block:2rem 4rem; }
        .pay-grid { max-width:560px; margin-inline:auto; display:grid; gap:1.25rem; }

        /* Cards */
        .card { background:#fff; border:1px solid var(--line); border-radius:var(--radius); padding:1.75rem; box-shadow:var(--shadow-sm); }
        .card-head { display:flex; align-items:center; gap:.75rem; margin-bottom:1.5rem; }
        .card-head .ico { width:42px; height:42px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; flex:none; }
        .card-head h2 { font-size:1.1rem; }
        .ico-navy { background:var(--bg-tint); color:var(--navy-900); }
        .ico-green { background:rgba(5,150,105,.1); color:var(--green); }
        .ico-gold { background:rgba(201,162,39,.12); color:var(--gold); }

        /* Form fields */
        .field { margin-bottom:1.25rem; }
        .field label { display:block; font-size:.85rem; font-weight:600; color:var(--slate); margin-bottom:.5rem; }
        .field input { width:100%; padding:.85rem 1rem; border-radius:var(--radius-sm); border:1px solid var(--line); background:#fff; font-size:1rem; color:var(--ink); transition:border-color .15s,box-shadow .15s; }
        .field input:focus { outline:0; border-color:var(--navy-700); box-shadow:0 0 0 3px rgba(11,37,69,.12); }
        .field .help { color:var(--muted); font-size:.78rem; margin-top:.4rem; }

        /* Detail rows */
        .detail-list { display:grid; gap:.75rem; }
        .detail-row { display:flex; justify-content:space-between; align-items:center; gap:1rem; padding-bottom:.7rem; border-bottom:1px solid var(--line); }
        .detail-row:last-child { border-bottom:0; padding-bottom:0; }
        .detail-row .lbl { color:var(--muted); font-size:.85rem; font-weight:500; }
        .detail-row .val { color:var(--ink); font-weight:600; font-size:.95rem; text-align:right; }
        .balance-row { background:linear-gradient(135deg, var(--bg-tint), #fff); border:1px solid var(--line); padding:1rem 1.1rem; border-radius:var(--radius-sm); }
        .balance-row .val { color:var(--crimson); font-size:1.4rem; }

        /* Alerts */
        .alert { padding:.85rem 1rem; border-radius:var(--radius-sm); font-size:.9rem; margin-bottom:1rem; border:1px solid; }
        .alert-error { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
        .alert-info  { background:#eff6ff; border-color:#bfdbfe; color:#1e3a8a; }

        /* Status section specifics */
        .status-icon-wrap { display:flex; justify-content:center; margin-bottom:1.25rem; }
        .status-icon { width:72px; height:72px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; box-shadow:var(--shadow-lg); }
        .countdown-card { background:linear-gradient(135deg, #fff7ed, #ffedd5); border:1px solid #fed7aa; padding:1.5rem; border-radius:var(--radius); text-align:center; margin-block:1rem; }
        .countdown-card .top { color:#9a3412; font-weight:600; font-size:.85rem; margin-bottom:.5rem; }
        .countdown-card .num { color:var(--amber); font-size:3rem; font-weight:800; line-height:1; font-variant-numeric:tabular-nums; }
        .countdown-card.warn .num { color:var(--red); }
        .countdown-card .bot { color:#9a3412; font-size:.75rem; margin-top:.4rem; }
        .instructions { background:#eff6ff; border:1px solid #bfdbfe; padding:1rem 1.1rem; border-radius:var(--radius-sm); margin-block:1rem; }
        .instructions h4 { color:#1e3a8a; font-size:.9rem; margin-bottom:.5rem; }
        .instructions ol { color:#1e40af; font-size:.85rem; margin:0; padding-left:1.25rem; line-height:1.5; }
        .ref-card { background:var(--bg-soft); border:1px solid var(--line); padding:.85rem 1rem; border-radius:var(--radius-sm); }
        .ref-card .lbl { font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:var(--muted); font-weight:600; }
        .ref-card .val { font-family:ui-monospace,SFMono-Regular,'Cascadia Code',Menlo,monospace; font-weight:700; color:var(--ink); margin-top:.25rem; word-break:break-all; }

        /* Spinner */
        .spinner { width:18px; height:18px; border:2.5px solid rgba(255,255,255,.35); border-top-color:#fff; border-radius:50%; animation:spin 1s linear infinite; }
        .btn-ghost .spinner, .btn-navy .spinner { border-color:rgba(15,23,42,.2); border-top-color:var(--navy-900); }
        @keyframes spin { from { transform:rotate(0); } to { transform:rotate(360deg); } }

        /* Receipt */
        .receipt-success { text-align:center; margin-bottom:1.5rem; }
        .receipt-success .check { width:84px; height:84px; border-radius:50%; background:linear-gradient(135deg, #34d399, var(--green)); color:#fff; display:inline-flex; align-items:center; justify-content:center; box-shadow:var(--shadow-lg); margin-bottom:1rem; }
        .receipt-success h2 { color:var(--green); font-size:1.6rem; }
        .receipt-success p { color:var(--slate); margin-top:.35rem; }
        .receipt-frame { border-block:3px solid var(--green); padding-block:1.5rem; }
        .receipt-head { text-align:center; margin-bottom:1.25rem; }
        .receipt-head img { width:60px; height:60px; margin:0 auto .5rem; object-fit:contain; }
        .receipt-head h3 { color:var(--navy-900); font-size:1.1rem; }
        .receipt-head p  { color:var(--muted); font-size:.85rem; }
        .receipt-summary { margin-top:1rem; background:linear-gradient(135deg, var(--bg-tint), #fff); border:1px solid var(--line); padding:1.25rem; border-radius:var(--radius); }
        .receipt-summary .row { display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; }
        .receipt-summary .row strong { color:var(--ink); font-size:.95rem; }
        .receipt-summary .row .amt { font-weight:700; }
        .receipt-summary .row .amt.green { color:var(--green); font-size:1.5rem; }
        .receipt-summary .row .amt.balance { color:var(--navy-900); font-size:1.5rem; }
        .receipt-summary hr { border:0; border-top:1px solid var(--line); margin:.75rem 0; }
        .save-note { margin-top:1rem; padding:.75rem 1rem; background:#fffbeb; border:1px solid #fde68a; border-radius:var(--radius-sm); color:#92400e; font-size:.8rem; text-align:center; }
        .save-note svg { vertical-align:-3px; margin-right:.3rem; }

        /* Footer */
        footer.site { background:var(--navy-900); color:rgba(255,255,255,.78); padding-block:2rem 1.25rem; font-size:.88rem; }
        footer.site a { color:rgba(255,255,255,.78); }
        footer.site a:hover { color:#fff; }
        footer.site .row { display:flex; flex-wrap:wrap; gap:1rem 1.5rem; justify-content:space-between; align-items:center; }
        footer.site .links { display:flex; flex-wrap:wrap; gap:1rem 1.25rem; }

        /* Print */
        @media print {
            body { background:#fff !important; }
            .topbar, .nav, .pg-hero, footer.site, button, #searchSection, #paymentSection, #statusSection { display:none !important; }
            #receiptSection { display:block !important; }
            #receiptSection .card { box-shadow:none; border:0; padding:0; }
            .pay-shell { padding:0; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container topbar-inner">
            <div class="meta">
                <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 5 9.18 19.79 19.79 0 0 1 1.92 4.55 2 2 0 0 1 4 2.5h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 10.91a16 16 0 0 0 6 6l1.77-1.16a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg> <a href="tel:{{ preg_replace('/\s+/','',$phone) }}">{{ $phone }}</a></span>
                <span class="hide-sm"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> <a href="mailto:{{ $email }}">{{ $email }}</a></span>
            </div>
            <div><a href="{{ url('/') }}">← Back to home</a></div>
        </div>
    </div>

    <header class="nav" id="nav">
        <div class="container nav-inner">
            <a class="brand" href="{{ url('/') }}">
                <img src="{{ $logoPath }}" alt="" onerror="this.style.display='none'">
                <span><span class="b-name">{{ $shortName }}</span><br><span class="b-tag">{{ $motto }}</span></span>
            </a>
            <nav class="nav-links" aria-label="Primary">
                <a href="{{ url('/#about') }}">About</a>
                <a href="{{ url('/#programs') }}">Programs</a>
                <a href="{{ url('/#why') }}">Why Us</a>
                <a href="{{ route('gallery') }}">Gallery</a>
                <a href="{{ url('/#portal') }}">Portal</a>
                <a href="{{ url('/pay') }}" class="active">Pay Fees</a>
            </nav>
            <div class="nav-cta">
                <a class="btn btn-ghost" style="width:auto;" href="{{ url('/admin/login') }}">Sign In</a>
            </div>
        </div>
    </header>

    <section class="pg-hero">
        <div class="container">
            <div class="crumbs"><a href="{{ url('/') }}">Home</a> · Pay Fees</div>
            <h1>Secure School Fee Payment</h1>
            <p>Pay {{ $shortName }} school fees instantly via mobile money. No login required — receipts emailed automatically.</p>
            <span class="pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Encrypted · CGrate-powered
            </span>
        </div>
    </section>

    <main class="pay-shell">
        <div class="container">
            <div class="pay-grid">

                <!-- ===== Search Student ===== -->
                <section id="searchSection" class="card">
                    <div class="card-head">
                        <span class="ico ico-navy" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <h2>Find Student</h2>
                    </div>

                    <div class="field">
                        <label for="studentSearch">Student ID or Name</label>
                        <input type="text" id="studentSearch" placeholder="e.g. STD0001/2025 or Charles Mwaba" autocomplete="off">
                        <p class="help">Enter your child's student ID or full name to look up the fee balance.</p>
                    </div>

                    <div id="searchError" class="alert alert-error hidden"></div>

                    <button id="searchBtn" class="btn btn-navy">
                        <span id="searchBtnText">Search Student</span>
                        <span id="searchSpinner" class="spinner hidden"></span>
                    </button>
                </section>

                <!-- ===== Payment (hidden initially) ===== -->
                <section id="paymentSection" class="hidden" style="display:grid; gap:1.25rem;">

                    <!-- Student Info -->
                    <div class="card">
                        <div class="card-head">
                            <span class="ico ico-green" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <h2>Student Information</h2>
                        </div>

                        <div class="detail-list">
                            <div class="detail-row"><span class="lbl">Student ID</span><span class="val" id="displayStudentId"></span></div>
                            <div class="detail-row"><span class="lbl">Name</span><span class="val" id="displayName"></span></div>
                            <div class="detail-row"><span class="lbl">Class</span><span class="val" id="displayGrade"></span></div>
                            <div class="detail-row"><span class="lbl">Academic Year</span><span class="val" id="displayYear"></span></div>
                            <div class="detail-row"><span class="lbl">Term</span><span class="val" id="displayTerm"></span></div>
                            <div class="detail-row"><span class="lbl">Total Fees</span><span class="val" id="displayTotal"></span></div>
                            <div class="detail-row"><span class="lbl">Amount Paid</span><span class="val" id="displayPaid" style="color:var(--green);"></span></div>
                            <div class="detail-row balance-row"><span class="lbl" style="color:var(--ink);font-weight:600;">Balance Due</span><span class="val" id="displayBalance"></span></div>
                        </div>

                        <button id="changeStudentBtn" class="btn btn-ghost" style="margin-top:1.25rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            Search a different student
                        </button>
                    </div>

                    <!-- Pay Form -->
                    <div class="card">
                        <div class="card-head">
                            <span class="ico ico-gold" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            </span>
                            <h2>Make Payment</h2>
                        </div>

                        <form id="paymentForm">
                            <input type="hidden" id="studentId" name="student_id">

                            <div class="field">
                                <label for="mobileNumber">Mobile money number</label>
                                <input type="tel" id="mobileNumber" name="mobile_number" placeholder="0977123456" required inputmode="numeric" autocomplete="tel-national">
                                <p class="help">Enter the mobile money number to be charged. You will get a prompt on this phone.</p>
                            </div>

                            <div class="field">
                                <label for="amount">Amount to pay (ZMW)</label>
                                <input type="number" id="amount" name="amount" step="0.01" min="1" placeholder="0.00" required inputmode="decimal">
                                <p class="help">Enter any amount up to the balance due.</p>
                            </div>

                            <div id="paymentError" class="alert alert-error hidden"></div>

                            <button type="submit" id="payBtn" class="btn btn-success">
                                <span id="payBtnText">Process Payment</span>
                                <span id="payBtnTimer" class="hidden" style="font-family:ui-monospace,monospace;background:rgba(255,255,255,.18);padding:.15rem .55rem;border-radius:6px;font-size:.95rem;">2:30</span>
                            </button>
                        </form>
                    </div>
                </section>

                <!-- ===== Payment Status (hidden initially) ===== -->
                <section id="statusSection" class="hidden">
                    <div class="card" style="text-align:center;">
                        <div class="status-icon-wrap">
                            <span class="status-icon" style="background:linear-gradient(135deg,#dbeafe,#93c5fd); color:#1e3a8a;" aria-hidden="true">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </span>
                        </div>

                        <h2 style="font-size:1.4rem;">Payment Initiated</h2>
                        <p style="color:var(--slate); margin-top:.5rem;" id="statusMessage">Please check your phone to complete the payment.</p>

                        <!-- Countdown -->
                        <div class="countdown-card" id="countdownCard">
                            <div class="top">Complete payment within</div>
                            <div class="num" id="countdown">2:30</div>
                            <div class="bot">Time remaining</div>
                        </div>

                        <!-- Instructions -->
                        <div class="instructions" style="text-align:left;">
                            <h4>What to do now:</h4>
                            <ol>
                                <li>Check your mobile phone for a payment prompt.</li>
                                <li>Enter your mobile money PIN to authorise the payment.</li>
                                <li>Wait for confirmation — this page updates automatically.</li>
                            </ol>
                        </div>

                        <div class="ref-card" style="text-align:left;">
                            <div class="lbl">Payment reference</div>
                            <div class="val" id="paymentReference"></div>
                        </div>

                        <div id="statusCheckSection" style="margin-top:1.25rem;">
                            <button id="checkStatusBtn" class="btn btn-navy">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span id="checkStatusBtnText">Check Payment Status</span>
                                <span id="checkStatusSpinner" class="spinner hidden"></span>
                            </button>
                            <p class="help" style="text-align:center; margin-top:.6rem;">Click after you've authorised the payment on your phone.</p>
                        </div>

                        <button id="newPaymentBtn" class="btn btn-ghost" style="margin-top:1rem;">Cancel & go back</button>
                    </div>
                </section>

                <!-- ===== Receipt (hidden initially) ===== -->
                <section id="receiptSection" class="hidden">
                    <div class="card">
                        <div class="receipt-success">
                            <span class="check" aria-hidden="true">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <h2>Payment Successful</h2>
                            <p>Your payment has been processed and recorded.</p>
                        </div>

                        <div class="receipt-frame">
                            <div class="receipt-head">
                                <img src="{{ $logoPath }}" alt="" onerror="this.style.display='none'">
                                <h3>{{ $schoolName }}</h3>
                                <p>Payment Receipt</p>
                            </div>

                            <div class="detail-list">
                                <div class="detail-row"><span class="lbl">Receipt Number</span><span class="val" id="receiptNumber"></span></div>
                                <div class="detail-row"><span class="lbl">Date &amp; Time</span><span class="val" id="receiptDateTime"></span></div>
                                <div class="detail-row"><span class="lbl">Payment Reference</span><span class="val" id="receiptPaymentRef" style="font-family:ui-monospace,monospace;font-size:.85rem;"></span></div>
                                <div class="detail-row"><span class="lbl">Student ID</span><span class="val" id="receiptStudentId"></span></div>
                                <div class="detail-row"><span class="lbl">Student Name</span><span class="val" id="receiptStudentName"></span></div>
                                <div class="detail-row"><span class="lbl">Class</span><span class="val" id="receiptGrade"></span></div>
                                <div class="detail-row"><span class="lbl">Academic Year</span><span class="val" id="receiptYear"></span></div>
                                <div class="detail-row"><span class="lbl">Term</span><span class="val" id="receiptTerm"></span></div>
                                <div class="detail-row"><span class="lbl">Mobile Number</span><span class="val" id="receiptMobile"></span></div>
                                <div class="detail-row"><span class="lbl">Transaction ID</span><span class="val" id="receiptTransactionId" style="font-family:ui-monospace,monospace;font-size:.78rem;"></span></div>
                            </div>

                            <div class="receipt-summary">
                                <div class="row"><strong>Amount Paid</strong><span class="amt green" id="receiptAmountPaid"></span></div>
                                <div class="row"><strong>Previous Balance</strong><span class="amt" id="receiptPreviousBalance" style="color:var(--slate);"></span></div>
                                <hr>
                                <div class="row"><strong>New Balance</strong><span class="amt balance" id="receiptNewBalance"></span></div>
                            </div>
                        </div>

                        <div style="display:grid; gap:.75rem; margin-top:1.5rem;">
                            <button onclick="window.print()" class="btn btn-navy">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                Print Receipt
                            </button>
                            <button id="newPaymentBtn2" class="btn btn-ghost">Make another payment</button>
                        </div>

                        <div class="save-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Please save or screenshot this receipt for your records.
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </main>

    <footer class="site">
        <div class="container row">
            <p>© {{ $year }} {{ $schoolName }}. All rights reserved.</p>
            <div class="links">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/#about') }}">About</a>
                <a href="{{ route('gallery') }}">Gallery</a>
                <a href="{{ url('/#contact') }}">Contact</a>
                <a href="{{ url('/admin/login') }}">Sign In</a>
            </div>
        </div>
    </footer>

    <script>
        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Elements
        const searchSection      = document.getElementById('searchSection');
        const paymentSection     = document.getElementById('paymentSection');
        const statusSection      = document.getElementById('statusSection');
        const searchBtn          = document.getElementById('searchBtn');
        const searchBtnText      = document.getElementById('searchBtnText');
        const searchSpinner      = document.getElementById('searchSpinner');
        const studentSearch      = document.getElementById('studentSearch');
        const searchError        = document.getElementById('searchError');
        const paymentForm        = document.getElementById('paymentForm');
        const payBtn             = document.getElementById('payBtn');
        const payBtnText         = document.getElementById('payBtnText');
        const payBtnTimer        = document.getElementById('payBtnTimer');
        const paymentError       = document.getElementById('paymentError');
        const changeStudentBtn   = document.getElementById('changeStudentBtn');
        const newPaymentBtn      = document.getElementById('newPaymentBtn');
        const newPaymentBtn2     = document.getElementById('newPaymentBtn2');
        const checkStatusBtn     = document.getElementById('checkStatusBtn');
        const checkStatusBtnText = document.getElementById('checkStatusBtnText');
        const checkStatusSpinner = document.getElementById('checkStatusSpinner');

        let currentStudent   = null;
        let currentPaymentId = null;
        let countdownInterval = null;
        let timeRemaining = 150; // 2:30
        let paymentData = {};

        // Sticky nav shadow
        (function () {
            const nav = document.getElementById('nav');
            const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 8);
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();

        // Search Student
        searchBtn.addEventListener('click', async () => {
            const search = studentSearch.value.trim();
            if (!search) { showError(searchError, 'Please enter a Student ID or Name'); return; }
            setLoading(searchBtn, searchBtnText, searchSpinner, true);
            hideError(searchError);
            try {
                const response = await fetch('{{ route("payment.search-student") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ search })
                });
                const data = await response.json();
                if (data.success) {
                    currentStudent = data.student;
                    displayStudentInfo(data.student);
                    showSection('payment');
                } else {
                    showError(searchError, data.message);
                }
            } catch (e) {
                showError(searchError, 'An error occurred. Please try again.');
            } finally {
                setLoading(searchBtn, searchBtnText, searchSpinner, false);
            }
        });
        studentSearch.addEventListener('keydown', (e) => { if (e.key === 'Enter') searchBtn.click(); });

        // Submit Payment
        paymentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                student_id: document.getElementById('studentId').value,
                mobile_number: document.getElementById('mobileNumber').value,
                amount: parseFloat(document.getElementById('amount').value)
            };
            if (formData.amount > currentStudent.balance) {
                showError(paymentError, 'Amount cannot exceed the balance due (ZMW ' + currentStudent.balance.toFixed(2) + ')');
                return;
            }
            setLoading(payBtn, payBtnText, payBtnTimer, true);
            hideError(paymentError);
            startCountdown();
            try {
                const response = await fetch('{{ route("payment.process") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(formData)
                });
                const data = await response.json();
                if (data.success) {
                    currentPaymentId = data.qr_payment_id;
                    paymentData = { reference: data.payment_reference, amount: formData.amount, mobile: formData.mobile_number };
                    document.getElementById('paymentReference').textContent = data.payment_reference;
                    document.getElementById('statusMessage').textContent = data.message;
                    showSection('status');
                } else {
                    stopTimers();
                    const errorMessage = data.message || 'Payment initiation failed. Please try again.';
                    if (/timeout|Network|service/i.test(errorMessage)) {
                        currentPaymentId = null;
                        document.getElementById('paymentReference').textContent = 'N/A';
                        showSection('status');
                        showFailedPayment(errorMessage);
                    } else {
                        showError(paymentError, errorMessage);
                    }
                }
            } catch (e) {
                stopTimers();
                currentPaymentId = null;
                document.getElementById('paymentReference').textContent = 'N/A';
                showSection('status');
                showFailedPayment('Unable to process your request. Please check your internet connection and try again.');
            } finally {
                setLoading(payBtn, payBtnText, payBtnTimer, false);
            }
        });

        // Check status
        checkStatusBtn.addEventListener('click', async () => {
            if (!currentPaymentId) return;
            setLoading(checkStatusBtn, checkStatusBtnText, checkStatusSpinner, true);
            try {
                const response = await fetch('{{ route("payment.check-status") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ payment_id: currentPaymentId })
                });
                const data = await response.json();
                if (data.success && data.status === 'completed') {
                    paymentData.transactionId = data.transaction_id;
                    paymentData.completedAt   = data.completed_at;
                    stopTimers();
                    showReceipt();
                } else if (data.success && data.status === 'failed') {
                    stopTimers();
                    showFailedPayment(data.message || 'Payment was not successful. Please try again.');
                } else {
                    const messages = {
                        pending:    'Payment is still pending. Please check your phone and authorise the payment, then check again.',
                        processing: 'Payment is being processed. Please wait a moment and check again.'
                    };
                    alert(messages[data.status] || 'Payment is still being processed. Please wait and try again.');
                }
            } catch (e) {
                alert('Unable to check payment status. Please check your internet connection and try again.');
            } finally {
                setLoading(checkStatusBtn, checkStatusBtnText, checkStatusSpinner, false);
            }
        });

        // Show failed payment
        function showFailedPayment(message) {
            stopTimers();
            const cd = document.getElementById('countdownCard');
            if (cd) cd.style.display = 'none';

            const isTimeout = /timeout|timed out|delays|expired/i.test(message);
            const title = isTimeout ? 'Payment request timed out' : 'Payment failed';
            const accentBg = isTimeout ? 'linear-gradient(135deg,#fff7ed,#fed7aa)' : 'linear-gradient(135deg,#fef2f2,#fecaca)';
            const accentBorder = isTimeout ? '#fdba74' : '#fca5a5';
            const iconBg   = isTimeout ? 'var(--amber)' : 'var(--red)';
            const textCol  = isTimeout ? '#9a3412' : '#991b1b';
            const iconSvg  = isTimeout
                ? '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'
                : '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';

            const card = document.querySelector('#statusSection > .card');
            card.innerHTML = `
                <div style="text-align:center;">
                    <div style="background:${accentBg}; border:1px solid ${accentBorder}; border-radius:var(--radius); padding:2rem 1.5rem; margin-bottom:1.5rem;">
                        <div class="status-icon-wrap">
                            <span class="status-icon" style="background:${iconBg}; color:#fff;" aria-hidden="true">${iconSvg}</span>
                        </div>
                        <h2 style="color:${textCol}; font-size:1.4rem;">${title}</h2>
                        <p style="color:${textCol}; margin-top:.5rem;">${message}</p>
                    </div>
                    <div style="display:grid; gap:.75rem;">
                        <button onclick="location.reload()" class="btn btn-navy">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                            Try again
                        </button>
                        <a href="{{ url('/') }}" class="btn btn-ghost">Back to home</a>
                    </div>
                </div>
            `;
        }

        // Reset
        changeStudentBtn.addEventListener('click', () => { showSection('search'); studentSearch.value=''; currentStudent=null; });
        newPaymentBtn .addEventListener('click', resetPayment);
        newPaymentBtn2.addEventListener('click', resetPayment);
        function resetPayment() { stopTimers(); showSection('search'); studentSearch.value=''; currentStudent=null; currentPaymentId=null; paymentData={}; timeRemaining=150; paymentForm.reset(); }

        // Countdown
        function startCountdown() {
            timeRemaining = 150;
            updateCountdownDisplay();
            countdownInterval = setInterval(() => {
                timeRemaining--;
                updateCountdownDisplay();
                if (timeRemaining <= 0) handlePaymentTimeout();
            }, 1000);
        }
        async function handlePaymentTimeout() {
            stopTimers();
            if (currentPaymentId) {
                try {
                    const r = await fetch('{{ route("payment.check-status") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ payment_id: currentPaymentId })
                    });
                    const d = await r.json();
                    if (d.success && d.status === 'completed') { showReceipt(); return; }
                } catch (e) {}
            }
            showFailedPayment('Payment time expired. The transaction may have timed out. Please check with the school if the payment was deducted from your account.');
        }
        function updateCountdownDisplay() {
            const m = Math.floor(timeRemaining/60);
            const s = timeRemaining % 60;
            const display = `${m}:${s.toString().padStart(2,'0')}`;
            const countdownEl = document.getElementById('countdown');
            if (countdownEl) countdownEl.textContent = display;
            if (payBtnTimer && !payBtnTimer.classList.contains('hidden')) payBtnTimer.textContent = display;
            const card = document.getElementById('countdownCard');
            if (card && timeRemaining <= 60) card.classList.add('warn');
        }
        function stopTimers() { if (countdownInterval) { clearInterval(countdownInterval); countdownInterval = null; } }

        // Receipt
        function showReceipt() {
            const now = new Date();
            const fmt = (d) => d.toLocaleString('en-GB', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit', hour12:false });
            const dateTimeStr = paymentData.completedAt ? fmt(new Date(paymentData.completedAt)) : fmt(now);

            document.getElementById('receiptNumber').textContent       = 'RCP-' + now.getFullYear() + '-' + String(now.getTime()).slice(-6);
            document.getElementById('receiptDateTime').textContent     = dateTimeStr;
            document.getElementById('receiptPaymentRef').textContent   = paymentData.reference || document.getElementById('paymentReference').textContent;
            document.getElementById('receiptStudentId').textContent    = currentStudent.student_id;
            document.getElementById('receiptStudentName').textContent  = currentStudent.name;
            document.getElementById('receiptGrade').textContent        = currentStudent.grade;
            document.getElementById('receiptYear').textContent         = currentStudent.academic_year;
            document.getElementById('receiptTerm').textContent         = currentStudent.term;
            document.getElementById('receiptMobile').textContent       = paymentData.mobile || document.getElementById('mobileNumber').value;
            document.getElementById('receiptTransactionId').textContent = paymentData.transactionId || 'N/A';

            const amountPaid     = paymentData.amount || parseFloat(document.getElementById('amount').value);
            const previousBalance = currentStudent.balance;
            const newBalance      = Math.max(0, previousBalance - amountPaid);
            document.getElementById('receiptAmountPaid').textContent    = 'ZMW ' + amountPaid.toFixed(2);
            document.getElementById('receiptPreviousBalance').textContent = 'ZMW ' + previousBalance.toFixed(2);
            document.getElementById('receiptNewBalance').textContent    = 'ZMW ' + newBalance.toFixed(2);

            showSection('receipt');
        }

        // Helpers
        function displayStudentInfo(student) {
            document.getElementById('studentId').value          = student.id;
            document.getElementById('displayStudentId').textContent = student.student_id;
            document.getElementById('displayName').textContent      = student.name;
            document.getElementById('displayGrade').textContent     = student.grade;
            document.getElementById('displayYear').textContent      = student.academic_year;
            document.getElementById('displayTerm').textContent      = student.term;
            document.getElementById('displayTotal').textContent     = 'ZMW ' + student.total_amount.toFixed(2);
            document.getElementById('displayPaid').textContent      = 'ZMW ' + student.amount_paid.toFixed(2);
            document.getElementById('displayBalance').textContent   = 'ZMW ' + student.balance.toFixed(2);
            document.getElementById('mobileNumber').value           = student.parent_mobile;
        }
        function showSection(section) {
            searchSection.classList.add('hidden');
            paymentSection.classList.add('hidden');
            statusSection.classList.add('hidden');
            document.getElementById('receiptSection').classList.add('hidden');
            if (section === 'search')  searchSection.classList.remove('hidden');
            if (section === 'payment') paymentSection.classList.remove('hidden');
            if (section === 'status')  statusSection.classList.remove('hidden');
            if (section === 'receipt') document.getElementById('receiptSection').classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function setLoading(btn, btnText, indicator, loading) {
            btn.disabled = loading;
            if (!btnText.dataset.originalText) btnText.dataset.originalText = btnText.textContent;
            if (loading) {
                if (indicator && indicator.id === 'payBtnTimer')        { btnText.textContent = 'Processing payment'; indicator.classList.remove('hidden'); }
                else if (indicator && indicator.id === 'checkStatusSpinner') { btnText.textContent = 'Checking…';      indicator.classList.remove('hidden'); }
                else if (indicator)                                     { btnText.textContent = 'Please wait…';       indicator.classList.remove('hidden'); }
            } else {
                btnText.textContent = btnText.dataset.originalText || 'Submit';
                if (indicator) indicator.classList.add('hidden');
            }
        }
        function showError(el, message) { el.textContent = message; el.classList.remove('hidden'); }
        function hideError(el)          { el.classList.add('hidden'); }

        searchBtnText.dataset.originalText      = searchBtnText.textContent;
        payBtnText.dataset.originalText         = payBtnText.textContent;
        checkStatusBtnText.dataset.originalText = checkStatusBtnText.textContent;
    </script>
</body>
</html>
