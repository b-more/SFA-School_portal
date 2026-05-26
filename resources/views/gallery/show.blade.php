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

    $resolveImg = function (?string $path) {
        if (! $path) return null;
        $clean = ltrim($path, '/');
        // bundled assets (shipped in public/images/...) — use directly
        if (\Illuminate\Support\Str::startsWith($clean, ['images/', 'imgz/', 'build/'])) {
            return asset($clean);
        }
        // anything else came from storage uploads
        return asset('storage/' . $clean);
    };
    $tiles = collect($photos)->map(function ($p) use ($resolveImg) {
        return [
            'src'      => $resolveImg($p->image_path ?? null),
            'caption'  => $p->caption ?? $p->alt_text ?? $p->title ?? null,
            'featured' => (bool) ($p->featured ?? false),
        ];
    })->filter(fn ($t) => $t['src'])->values();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0e2746">
    <meta name="description" content="{{ $album->title }} — photos at {{ $schoolName }}.">
    <title>{{ $album->title }} — {{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ $logoPath }}">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=eb-garamond:400,500,600|inter:400,500,600&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink:#11151c; --ink-soft:#2c3340; --muted:#5f6675;
            --rule:#d6cfbf; --rule-soft:#ece6d8;
            --paper:#faf6ef; --paper-deep:#f1ebde;
            --navy-deep:#06182f; --crimson:#9c1d2c; --gold:#b08a3e;
            --max:1180px;
            --serif:'EB Garamond',Georgia,serif;
            --sans:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;
        }
        *,*::before,*::after { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; font-family:var(--sans); font-size:17px; line-height:1.65; color:var(--ink); background:var(--paper); }
        img,svg { max-width:100%; display:block; }
        a { color:inherit; text-decoration:none; }
        button { font:inherit; cursor:pointer; border:0; background:none; color:inherit; }
        h1,h2 { margin:0; font-family:var(--serif); font-weight:500; line-height:1.1; letter-spacing:-0.005em; color:var(--ink); }
        p { margin:0; }
        .container { width:100%; max-width:var(--max); margin-inline:auto; padding-inline:1.5rem; }

        .topbar { background:var(--navy-deep); color:rgba(255,255,255,.78); font-size:13px; }
        .topbar-inner { display:flex; flex-wrap:wrap; gap:.4rem 2rem; padding-block:.6rem; align-items:center; justify-content:space-between; }
        .topbar a { color:inherit; }
        .topbar a:hover { color:#fff; }

        .nav { position:sticky; top:0; z-index:60; background:var(--paper); border-bottom:1px solid var(--rule); transition:box-shadow .25s; }
        .nav.scrolled { box-shadow:0 2px 24px -16px rgba(17,21,28,.4); }
        .nav-inner { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding-block:1rem; }
        .brand { display:flex; align-items:center; gap:.85rem; min-width:0; }
        .brand img { width:46px; height:46px; border-radius:50%; object-fit:cover; }
        .brand-name { font-family:var(--serif); font-weight:600; font-size:1.18rem; color:var(--ink); display:block; line-height:1.1; }
        .brand-tag  { font-size:11px; color:var(--muted); letter-spacing:.18em; text-transform:uppercase; margin-top:2px; display:block; }
        .nav-links { display:none; gap:.25rem; }
        .nav-links a { position:relative; padding:.55rem .9rem; font-size:.93rem; font-weight:500; color:var(--ink-soft); }
        .nav-links a::after { content:''; position:absolute; left:.9rem; right:.9rem; bottom:.35rem; height:1px; background:var(--ink); transform:scaleX(0); transform-origin:left; transition:transform .25s; }
        .nav-links a:hover::after, .nav-links a.active::after { transform:scaleX(1); }
        @media (min-width:960px) { .nav-links { display:inline-flex; } }

        /* Page hero — album header */
        .album-hero { background:var(--paper-deep); border-bottom:1px solid var(--rule); padding-block: clamp(2.5rem, 5vw, 3.5rem); }
        .album-hero .crumbs { font-family:var(--sans); font-size:.78rem; letter-spacing:.18em; text-transform:uppercase; color:var(--muted); margin-bottom:.85rem; }
        .album-hero .crumbs a { color:var(--muted); }
        .album-hero .crumbs a:hover { color:var(--ink); }
        .album-hero h1 { font-style:italic; font-size:clamp(2rem,4.5vw,3.2rem); }
        .album-hero p  { font-family:var(--serif); color:var(--ink-soft); margin-top:.85rem; max-width:60ch; font-size:1.15rem; }
        .album-hero .meta { font-family:var(--sans); font-size:.85rem; color:var(--muted); margin-top:1rem; display:flex; gap:1rem; flex-wrap:wrap; }
        .album-hero .meta strong { color:var(--ink); font-weight:500; }

        /* Photos grid — masonry-ish */
        .photos { padding-block: clamp(2rem, 5vw, 3.5rem); }
        .grid {
            display:grid; gap:.6rem;
            grid-template-columns: repeat(2, 1fr);
            grid-auto-rows: 180px;
        }
        @media (min-width:640px) { .grid { grid-template-columns: repeat(3, 1fr); grid-auto-rows: 220px; } }
        @media (min-width:960px) { .grid { grid-template-columns: repeat(4, 1fr); grid-auto-rows: 240px; } }
        .grid figure {
            margin:0; overflow:hidden; position:relative; cursor:zoom-in; background:var(--rule-soft);
        }
        .grid figure.tall { grid-row: span 2; }
        .grid figure.wide { grid-column: span 2; }
        .grid figure img { width:100%; height:100%; object-fit:cover; transition: transform .8s ease; }
        .grid figure:hover img { transform: scale(1.05); }
        .grid figure figcaption {
            position:absolute; left:0; right:0; bottom:0;
            padding:.75rem 1rem; color:#fff;
            background:linear-gradient(to top, rgba(6,24,47,.85) 0%, transparent 100%);
            font-family:var(--sans); font-size:.85rem; opacity:0; transition:opacity .2s;
        }
        .grid figure:hover figcaption { opacity:1; }

        /* Empty */
        .empty { text-align:center; padding: clamp(3rem, 8vw, 6rem) 1rem; }
        .empty p { color:var(--muted); font-family:var(--serif); font-size:1.15rem; }

        /* Lightbox */
        .lb { position:fixed; inset:0; z-index:90; background:rgba(6,24,47,.94); display:none; align-items:center; justify-content:center; padding:1rem; backdrop-filter:blur(8px); }
        .lb.open { display:flex; }
        .lb img { max-width:96vw; max-height:88vh; object-fit:contain; }
        .lb button { position:absolute; width:44px; height:44px; background:rgba(255,255,255,.12); color:#fff; display:inline-flex; align-items:center; justify-content:center; transition:background .15s; }
        .lb button:hover { background:rgba(255,255,255,.22); }
        .lb .close { top:1rem; right:1rem; }
        .lb .prev { left:1rem; top:50%; transform:translateY(-50%); }
        .lb .next { right:1rem; top:50%; transform:translateY(-50%); }
        .lb .caption { position:absolute; bottom:1.2rem; left:1rem; right:1rem; color:rgba(255,255,255,.8); text-align:center; font-family:var(--sans); font-size:.85rem; }
        .lb .pos    { position:absolute; top:1rem; left:50%; transform:translateX(-50%); color:rgba(255,255,255,.7); font-family:var(--sans); font-size:.78rem; letter-spacing:.18em; text-transform:uppercase; }

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
            <div><a href="tel:{{ preg_replace('/\s+/','',$phone) }}">{{ $phone }}</a> &nbsp; · &nbsp; <a href="mailto:{{ $email }}">{{ $email }}</a></div>
            <div><a href="{{ route('gallery') }}">← All albums</a></div>
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
        </div>
    </header>

    <section class="album-hero">
        <div class="container">
            <div class="crumbs">
                <a href="{{ url('/') }}">Home</a> ·
                <a href="{{ route('gallery') }}">Gallery</a> ·
                {{ $album->title }}
            </div>
            <h1>{{ $album->title }}</h1>
            @if($album->description)
                <p>{{ $album->description }}</p>
            @endif
            <div class="meta">
                <span><strong>{{ $tiles->count() }}</strong> {{ \Illuminate\Support\Str::plural('photo', $tiles->count()) }}</span>
                @if($album->updated_at)
                    <span>Updated {{ \Illuminate\Support\Carbon::parse($album->updated_at)->format('j M Y') }}</span>
                @endif
            </div>
        </div>
    </section>

    <main class="photos">
        <div class="container">
            @if($tiles->isEmpty())
                <div class="empty">
                    <p>No photos yet in this album.</p>
                    <p style="margin-top:1rem;"><a href="{{ route('gallery') }}" style="color:var(--crimson); border-bottom:1px solid var(--crimson);">← Back to all albums</a></p>
                </div>
            @else
                <div class="grid" id="grid">
                    @foreach($tiles as $i => $t)
                        @php
                            $cls = '';
                            if ($t['featured']) $cls = 'wide';
                            elseif ($i % 7 === 0) $cls = 'wide';
                            elseif ($i % 5 === 2) $cls = 'tall';
                        @endphp
                        <figure class="{{ $cls }}" data-full="{{ $t['src'] }}" data-caption="{{ $t['caption'] ?? '' }}">
                            <img src="{{ $t['src'] }}" alt="{{ $t['caption'] ?? 'Photo' }}" loading="lazy">
                            @if(!empty($t['caption']))
                                <figcaption>{{ $t['caption'] }}</figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    <div class="lb" id="lb" role="dialog" aria-modal="true" aria-label="Photo viewer">
        <button class="close" id="lbClose" aria-label="Close"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        <button class="prev"  id="lbPrev"  aria-label="Previous"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg></button>
        <button class="next"  id="lbNext"  aria-label="Next"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg></button>
        <span class="pos" id="lbPos"></span>
        <img id="lbImg" alt="">
        <div class="caption" id="lbCap"></div>
    </div>

    <footer class="site">
        <div class="container row">
            <p>© {{ $year }} {{ $schoolName }}. All rights reserved.</p>
            <div class="links">
                <a href="{{ url('/') }}">Home</a>
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

        // Lightbox
        (function () {
            const grid = document.getElementById('grid');
            const lb   = document.getElementById('lb');
            const img  = document.getElementById('lbImg');
            const cap  = document.getElementById('lbCap');
            const pos  = document.getElementById('lbPos');
            const close= document.getElementById('lbClose');
            const prev = document.getElementById('lbPrev');
            const next = document.getElementById('lbNext');
            if (!grid) return;
            const figs = Array.from(grid.querySelectorAll('figure'));
            let i = 0;
            const show = (idx) => {
                i = (idx + figs.length) % figs.length;
                const f = figs[i];
                img.src = f.dataset.full;
                img.alt = f.dataset.caption || '';
                cap.textContent = f.dataset.caption || '';
                pos.textContent = (i + 1) + ' / ' + figs.length;
                lb.classList.add('open');
                document.body.style.overflow = 'hidden';
            };
            const hide = () => { lb.classList.remove('open'); document.body.style.overflow = ''; };
            figs.forEach((f, idx) => f.addEventListener('click', () => show(idx)));
            close.addEventListener('click', hide);
            prev .addEventListener('click', () => show(i - 1));
            next .addEventListener('click', () => show(i + 1));
            lb.addEventListener('click', e => { if (e.target === lb) hide(); });
            document.addEventListener('keydown', e => {
                if (!lb.classList.contains('open')) return;
                if (e.key === 'Escape')     hide();
                if (e.key === 'ArrowLeft')  show(i - 1);
                if (e.key === 'ArrowRight') show(i + 1);
            });
        })();
    </script>
</body>
</html>
