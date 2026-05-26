@php
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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0e2746">
    <meta name="description" content="{{ $schoolName }} — campus albums: classrooms, sport, ceremonies and student life.">
    <title>Albums — {{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ $logoPath }}">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=eb-garamond:400,500,600|inter:400,500,600&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink:#11151c; --ink-soft:#2c3340; --muted:#5f6675;
            --rule:#d6cfbf; --rule-soft:#ece6d8;
            --paper:#faf6ef; --paper-deep:#f1ebde;
            --navy:#0e2746; --navy-deep:#06182f;
            --crimson:#9c1d2c; --gold:#b08a3e;
            --max:1180px;
            --serif:'EB Garamond',Georgia,serif;
            --sans:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;
        }
        *,*::before,*::after { box-sizing:border-box; }
        html { scroll-behavior:smooth; -webkit-text-size-adjust:100%; }
        body { margin:0; font-family:var(--sans); font-size:17px; line-height:1.65; color:var(--ink); background:var(--paper); -webkit-font-smoothing:antialiased; }
        img,svg { max-width:100%; display:block; }
        a { color:inherit; text-decoration:none; }
        button { font:inherit; cursor:pointer; border:0; background:none; color:inherit; }
        h1,h2,h3 { margin:0; font-family:var(--serif); font-weight:500; line-height:1.1; letter-spacing:-0.005em; color:var(--ink); }
        p { margin:0; }
        .container { width:100%; max-width:var(--max); margin-inline:auto; padding-inline:1.5rem; }

        /* Topbar */
        .topbar { background:var(--navy-deep); color:rgba(255,255,255,.78); font-size:13px; }
        .topbar-inner { display:flex; flex-wrap:wrap; gap:.4rem 2rem; padding-block:.6rem; align-items:center; justify-content:space-between; }
        .topbar a { color:inherit; }
        .topbar a:hover { color:#fff; }
        .topbar .meta { display:flex; gap:1.5rem; flex-wrap:wrap; align-items:center; }

        /* Nav */
        .nav { position:sticky; top:0; z-index:60; background:var(--paper); border-bottom:1px solid var(--rule); transition:box-shadow .25s; }
        .nav.scrolled { box-shadow:0 2px 24px -16px rgba(17,21,28,.4); }
        .nav-inner { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding-block:1rem; }
        .brand { display:flex; align-items:center; gap:.85rem; min-width:0; }
        .brand img { width:46px; height:46px; border-radius:50%; object-fit:cover; }
        .brand-name { font-family:var(--serif); font-weight:600; font-size:1.18rem; color:var(--ink); display:block; line-height:1.1; }
        .brand-tag  { font-size:11px; color:var(--muted); letter-spacing:.18em; text-transform:uppercase; margin-top:2px; display:block; }
        .nav-links { display:none; gap:.25rem; }
        .nav-links a { position:relative; padding:.55rem .9rem; font-size:.93rem; font-weight:500; color:var(--ink-soft); transition:color .15s; }
        .nav-links a::after { content:''; position:absolute; left:.9rem; right:.9rem; bottom:.35rem; height:1px; background:var(--ink); transform:scaleX(0); transform-origin:left; transition:transform .25s; }
        .nav-links a:hover::after, .nav-links a.active::after { transform:scaleX(1); }
        .btn { display:inline-flex; align-items:center; gap:.5rem; padding:.78rem 1.4rem; font-size:.92rem; font-weight:500; background:var(--ink); color:#fff; border:1px solid var(--ink); border-radius:0; transition:background .2s,color .2s,border-color .2s; }
        .btn:hover { background:transparent; color:var(--ink); }
        .btn-ghost { background:transparent; color:var(--ink); }
        .btn-ghost:hover { background:var(--ink); color:#fff; }
        @media (min-width:960px) { .nav-links { display:inline-flex; } }

        /* Page hero */
        .pg-hero { background:var(--paper-deep); border-bottom:1px solid var(--rule); padding-block: clamp(2.5rem, 6vw, 4rem); }
        .pg-hero .crumbs { font-family:var(--sans); font-size:.78rem; letter-spacing:.18em; text-transform:uppercase; color:var(--muted); margin-bottom:.75rem; }
        .pg-hero .crumbs a { color:var(--muted); }
        .pg-hero .crumbs a:hover { color:var(--ink); }
        .pg-hero h1 { font-style:italic; font-size:clamp(2.2rem,5vw,3.4rem); }
        .pg-hero p  { font-family:var(--serif); color:var(--ink-soft); margin-top:.85rem; max-width: 56ch; font-size:1.2rem; }

        /* Albums grid */
        .albums-section { padding-block: clamp(3rem, 6vw, 4.5rem); }
        .albums-grid { display:grid; gap: clamp(1.5rem, 3vw, 2.5rem); grid-template-columns: 1fr; }
        @media (min-width:720px) { .albums-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width:1024px) { .albums-grid { grid-template-columns: repeat(3, 1fr); } }
        .album-card {
            display:block; group:hover; text-decoration:none;
            transition: transform .3s ease;
        }
        .album-card:hover { transform: translateY(-2px); }
        .album-card .ph {
            position:relative; aspect-ratio: 4/3; overflow:hidden; background:var(--rule-soft);
        }
        .album-card .ph img { width:100%; height:100%; object-fit:cover; transition: transform .8s ease; filter: grayscale(.05); }
        .album-card:hover .ph img { transform: scale(1.04); filter: grayscale(0); }
        .album-card .ph-empty {
            position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
            color:var(--muted); font-family:var(--serif); font-style:italic; font-size:1.5rem;
        }
        .album-card .count-pill {
            position:absolute; left:1rem; bottom:1rem;
            background:rgba(6,24,47,.85); color:#fff; padding:.3rem .7rem;
            font-family:var(--sans); font-size:.75rem; letter-spacing:.12em; text-transform:uppercase; font-weight:500;
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
        }
        .album-card .meta {
            margin-top: 1rem; display:flex; align-items:baseline; gap:.6rem;
            font-family:var(--sans); font-size:.78rem; letter-spacing:.16em; text-transform:uppercase; color:var(--muted);
        }
        .album-card h3 {
            margin-top: .5rem; font-size: 1.55rem;
            transition: color .2s;
        }
        .album-card:hover h3 { color: var(--crimson); }
        .album-card p {
            margin-top: .55rem; color:var(--ink-soft); font-size:.95rem; line-height:1.55;
            display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
        }
        .album-card .read {
            display:inline-flex; align-items:center; gap:.4rem; margin-top: .85rem;
            font-family:var(--sans); font-size:.78rem; letter-spacing:.18em; text-transform:uppercase;
            color:var(--ink); border-bottom:1px solid var(--ink); padding-bottom:.15rem;
        }

        /* Empty state */
        .empty {
            text-align:center; padding: clamp(3rem, 8vw, 6rem) 1rem;
            border:1px dashed var(--rule); background:#fff; max-width:560px; margin-inline:auto;
        }
        .empty h3 { font-style:italic; }
        .empty p  { color:var(--ink-soft); margin-top:.85rem; font-family:var(--serif); font-size:1.1rem; }

        /* Footer */
        footer.site { background:var(--navy-deep); color:rgba(255,255,255,.7); padding-block:2.5rem 1.25rem; font-size:.92rem; }
        footer.site a { color:rgba(255,255,255,.7); }
        footer.site a:hover { color:#fff; }
        footer.site .row { display:flex; flex-wrap:wrap; gap:1rem 1.5rem; justify-content:space-between; align-items:center; }
        footer.site .links { display:flex; flex-wrap:wrap; gap:1rem 1.25rem; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container topbar-inner">
            <div class="meta">
                <span><a href="tel:{{ preg_replace('/\s+/','',$phone) }}">{{ $phone }}</a></span>
                <span><a href="mailto:{{ $email }}">{{ $email }}</a></span>
            </div>
            <div><a href="{{ url('/') }}">← Back to home</a></div>
        </div>
    </div>

    <header class="nav" id="nav">
        <div class="container nav-inner">
            <a class="brand" href="{{ url('/') }}">
                <img src="{{ $logoPath }}" alt="" onerror="this.style.display='none'">
                <span><span class="brand-name">{{ $shortName }}</span><span class="brand-tag">{{ $motto }}</span></span>
            </a>
            <nav class="nav-links" aria-label="Primary">
                <a href="{{ url('/#welcome') }}">Welcome</a>
                <a href="{{ url('/#programs') }}">Programs</a>
                <a href="{{ url('/#learning') }}">Approach</a>
                <a href="{{ route('gallery') }}" class="active">Gallery</a>
                <a href="{{ url('/#portal') }}">Portal</a>
                <a href="{{ url('/#contact') }}">Contact</a>
            </nav>
            <a class="btn btn-ghost" href="{{ url('/pay') }}" style="display:none;">Pay Fees</a>
        </div>
    </header>

    <section class="pg-hero">
        <div class="container">
            <div class="crumbs"><a href="{{ url('/') }}">Home</a> · Gallery</div>
            <h1>Albums</h1>
            <p>Photo collections from across {{ $shortName }} — academic events, sport, ceremonies, and everyday life on campus.</p>
        </div>
    </section>

    <main>
        <section class="albums-section">
            <div class="container">
                @php
                    $totalAlbums = $albums->count() + (($legacyCount ?? 0) > 0 ? 1 : 0);
                @endphp

                @if($totalAlbums === 0)
                    <div class="empty">
                        <h3>No albums yet</h3>
                        <p>Photo collections will appear here once an administrator publishes them. In the meantime, you can <a href="{{ url('/') }}#contact" style="color:var(--crimson); border-bottom:1px solid var(--crimson);">get in touch</a>.</p>
                    </div>
                @else
                    <div class="albums-grid">
                        @php
                            $resolveImg = function (?string $path) {
                                if (! $path) return null;
                                $clean = ltrim($path, '/');
                                if (\Illuminate\Support\Str::startsWith($clean, ['images/', 'imgz/', 'build/'])) {
                                    return asset($clean);
                                }
                                return asset('storage/' . $clean);
                            };
                        @endphp
                        @foreach($albums as $album)
                            @php
                                $cover = $resolveImg($album->cover_image);
                                if (!$cover) {
                                    $first = $album->photos()->orderBy('order')->orderBy('id')->first();
                                    if ($first && $first->image_path) {
                                        $cover = $resolveImg($first->image_path);
                                    }
                                }
                            @endphp
                            <a class="album-card" href="{{ route('gallery.show', $album->slug) }}">
                                <div class="ph">
                                    @if($cover)
                                        <img src="{{ $cover }}" alt="{{ $album->title }} cover" loading="lazy">
                                    @else
                                        <div class="ph-empty">No cover yet</div>
                                    @endif
                                    <span class="count-pill">{{ $album->photos_count }} {{ \Illuminate\Support\Str::plural('photo', $album->photos_count) }}</span>
                                </div>
                                <div class="meta">
                                    <span>{{ \Illuminate\Support\Carbon::parse($album->updated_at)->format('M Y') }}</span>
                                </div>
                                <h3>{{ $album->title }}</h3>
                                @if($album->description)
                                    <p>{{ $album->description }}</p>
                                @endif
                                <span class="read">Open album →</span>
                            </a>
                        @endforeach

                        @if(($legacyCount ?? 0) > 0)
                            <a class="album-card" href="{{ route('gallery.show', 'other-photos') }}">
                                <div class="ph">
                                    <div class="ph-empty">Uncategorised photos</div>
                                    <span class="count-pill">{{ $legacyCount }} photos</span>
                                </div>
                                <div class="meta"><span>Mixed</span></div>
                                <h3>Other photos</h3>
                                <p>Photos uploaded outside an album.</p>
                                <span class="read">Open →</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    </main>

    <footer class="site">
        <div class="container row">
            <p>© {{ $year }} {{ $schoolName }}. All rights reserved.</p>
            <div class="links">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/#programs') }}">Programs</a>
                <a href="{{ route('gallery') }}">Gallery</a>
                <a href="{{ url('/#contact') }}">Contact</a>
                <a href="{{ url('/admin/login') }}">Sign in</a>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const nav = document.getElementById('nav');
            const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 8);
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
    </script>
</body>
</html>
