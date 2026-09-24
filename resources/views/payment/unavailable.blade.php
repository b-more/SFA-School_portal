@php
    $settings   = \App\Models\SchoolSettings::first();
    $schoolName = $settings->school_name ?? 'St. Francis of Assisi';
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
    <meta name="theme-color" content="#0b2545">
    <meta name="robots" content="noindex">
    <title>Online Payments Unavailable — {{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ $logoPath }}">
    <style>
        :root {
            --navy-900:#0b2545; --crimson:#c8102e; --ink:#0f172a;
            --slate:#475569; --muted:#64748b; --line:#e2e8f0; --bg-soft:#f8fafc;
        }
        *,*::before,*::after { box-sizing:border-box; }
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
            font-family:system-ui,-apple-system,'Segoe UI',Roboto,sans-serif; color:var(--ink);
            background:var(--bg-soft); padding:1.5rem; }
        .card { width:100%; max-width:520px; background:#fff; border:1px solid var(--line);
            border-radius:16px; box-shadow:0 20px 40px -20px rgba(11,37,69,.35); overflow:hidden; }
        .head { background:var(--navy-900); color:#fff; padding:1.75rem 1.5rem; text-align:center; }
        .head img { height:56px; margin:0 auto .75rem; display:block; }
        .head h1 { margin:0; font-size:1.05rem; font-weight:700; letter-spacing:-.01em; }
        .body { padding:1.75rem 1.5rem; text-align:center; }
        .icon { width:64px; height:64px; margin:0 auto 1rem; border-radius:50%;
            background:#fff5f5; color:var(--crimson); display:flex; align-items:center; justify-content:center; }
        .icon svg { width:32px; height:32px; }
        .body h2 { margin:0 0 .5rem; font-size:1.25rem; color:var(--navy-900); }
        .body p { margin:0 0 .75rem; color:var(--slate); line-height:1.6; }
        .contact { margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--line);
            font-size:.95rem; color:var(--muted); }
        .contact a { color:var(--navy-900); font-weight:600; text-decoration:none; }
        .foot { text-align:center; font-size:.8rem; color:var(--muted); padding:1rem; }
    </style>
</head>
<body>
    <main class="card">
        <div class="head">
            <img src="{{ $logoPath }}" alt="{{ $schoolName }} logo">
            <h1>{{ $schoolName }}</h1>
        </div>
        <div class="body">
            <div class="icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h2>Online Payments Unavailable</h2>
            <p>Our online fee payment service is temporarily closed for maintenance.</p>
            <p>Please try again later, or contact the school office to make a payment.</p>
            <div class="contact">
                Call <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a><br>
                Email <a href="mailto:{{ $email }}">{{ $email }}</a>
            </div>
        </div>
        <div class="foot">&copy; {{ $year }} {{ $schoolName }}</div>
    </main>
</body>
</html>
