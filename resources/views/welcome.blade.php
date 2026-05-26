@php
    $settings   = $settings ?? \App\Models\SchoolSettings::first();
    $schoolName = $settings->school_name   ?? 'St. Francis of Assisi';
    $shortName  = 'St. Francis of Assisi';
    $motto      = $settings->school_motto  ?? 'Faith · Family · Future';
    $vision     = $settings->school_vision ?? 'To nurture confident, compassionate learners who lead with integrity and serve with purpose.';
    $mission    = $settings->school_mission ?? 'We provide a Christ-centred education built on academic excellence, character formation, and holistic development — preparing every learner to thrive in a changing world.';
    $phone      = $settings->phone         ?? '+260 977 000 000';
    $altPhone   = $settings->alternate_phone ?? null;
    $email      = $settings->email         ?? 'info@stfrancisofassisizm.com';
    $website    = $settings->website       ?? 'https://stfrancisofassisizm.com';
    $address    = trim(collect([
                    $settings->address ?? null,
                    $settings->city ?? null,
                    $settings->state_province ?? null,
                    $settings->country ?? 'Zambia',
                ])->filter()->implode(', ')) ?: 'Lusaka, Zambia';
    $logoPath   = $settings && $settings->school_logo
                    ? asset('storage/' . ltrim($settings->school_logo, '/'))
                    : asset('images/logo.png');
    $social     = (array) ($settings->social_media_links ?? []);
    $facebook   = $social['facebook']  ?? '#';
    $twitter    = $social['twitter']   ?? '#';
    $instagram  = $social['instagram'] ?? '#';
    $youtube    = $social['youtube']   ?? '#';

    // External app URLs (configurable via env)
    $parentApp  = env('PARENT_APP_URL',  'https://parent.stfrancisofassisizm.com');
    $teacherApp = env('TEACHER_APP_URL', 'https://teacher.stfrancisofassisizm.com');

    $year = now()->year;

    /* ---- Admin-managed landing overrides (SchoolSettings.custom_settings.landing.*) ---- */
    $custom  = (array) ($settings->custom_settings ?? []);
    $landing = (array) ($custom['landing'] ?? []);

    /* Helper: resolve an admin-uploaded image (storage/) with a bundled fallback. */
    $img = function ($value, $bundledFallback = null) {
        if (filled($value)) {
            return asset('storage/' . ltrim($value, '/'));
        }
        return $bundledFallback ? asset($bundledFallback) : null;
    };

    /* ---- HERO ---- */
    $eyebrow            = $landing['eyebrow']             ?? ($year . ' · Admissions Open');
    $heroHeadline       = $landing['hero_headline']       ?? 'Educating minds.';
    $heroAccent         = $landing['hero_headline_accent'] ?? 'Forming character. Inspiring purpose.';
    $heroSub            = $landing['hero_subheadline']    ?? $mission;
    $heroImage          = $img($landing['hero_image'] ?? null, 'images/campus/campus1.jpg');
    $ctaPrimaryLabel    = $landing['cta_primary_label']   ?? 'Apply for Admission';
    $ctaPrimaryUrl      = $landing['cta_primary_url']     ?? '#contact';
    $ctaSecondaryLabel  = $landing['cta_secondary_label'] ?? 'Access Portal';
    $ctaSecondaryUrl    = $landing['cta_secondary_url']   ?? '#portal';
    $announcementText   = trim((string) ($landing['announcement_text'] ?? ''));
    $announcementUrl    = $landing['announcement_url']    ?? null;
    $heroStats          = collect($landing['hero_stats'] ?? [
        ['value' => 'K–12',  'label' => 'Early Years · Primary · Secondary'],
        ['value' => '1:18',  'label' => 'Teacher · Student Ratio'],
        ['value' => 'ECZ',   'label' => 'Accredited Curriculum'],
        ['value' => '24/7',  'label' => 'Parent & Teacher Portal'],
    ])->filter(fn ($s) => filled($s['value'] ?? null) || filled($s['label'] ?? null));

    /* ---- ABOUT ---- */
    $aboutImage         = $img($landing['about_image'] ?? null, 'images/campus/campus2.jpg');
    $aboutVision        = $landing['about_vision']  ?? $vision;
    $aboutMission       = $landing['about_mission'] ?? $mission;
    $aboutValues        = $landing['about_values']  ?? 'Faith, integrity, excellence, service, and respect — the pillars on which our community stands.';
    $aboutBadgeValue    = $landing['about_badge_value'] ?? '200+';
    $aboutBadgeLabel    = $landing['about_badge_label'] ?? 'Active Students';

    /* ---- STATS ---- */
    $statsYears         = (int) ($landing['stats_years'] ?? 25);
    $statsLabels = [
        'students' => $landing['stats_label_students'] ?? 'Active Learners',
        'teachers' => $landing['stats_label_teachers'] ?? 'Qualified Teachers',
        'year'     => $landing['stats_label_year']     ?? 'Current Academic Year',
        'years'    => $landing['stats_label_years']    ?? 'Years of Excellence',
    ];

    /* ---- PROGRAMS (defaults: 3 cards using bundled images) ---- */
    $programDefaults = [
        ['title' => 'Early Years & Reception', 'age_range' => 'Ages 3 – 6',  'description' => 'Play-based, language-rich learning that builds confidence, curiosity, and foundational literacy and numeracy.', 'image' => null, '_bundled' => 'images/primary/primary2.jpg', 'cta_label' => 'Discuss enrolment', 'cta_url' => '#contact'],
        ['title' => 'Primary School',          'age_range' => 'Grades 1 – 7','description' => 'A robust academic core in literacy, numeracy and the sciences, alongside arts, sport, ICT and Christian formation.', 'image' => null, '_bundled' => 'images/primary/primary5.jpg', 'cta_label' => 'Discuss enrolment', 'cta_url' => '#contact'],
        ['title' => 'Secondary School',        'age_range' => 'Grades 8 – 12','description' => 'ECZ-aligned curriculum with strong sciences, languages and humanities — preparing learners for university and life.', 'image' => null, '_bundled' => 'images/secondary/secondary2.jpg', 'cta_label' => 'Discuss enrolment', 'cta_url' => '#contact'],
    ];
    $programs = collect($landing['programs'] ?? $programDefaults)
        ->map(function ($p, $i) use ($img, $programDefaults) {
            $bundled = $programDefaults[$i]['_bundled'] ?? 'images/campus/campus3.jpg';
            return [
                'title'       => $p['title'] ?? '',
                'age_range'   => $p['age_range'] ?? '',
                'description' => $p['description'] ?? '',
                'image'       => $img($p['image'] ?? null, $bundled),
                'cta_label'   => $p['cta_label'] ?? 'Learn more',
                'cta_url'     => $p['cta_url']   ?? '#contact',
            ];
        })
        ->filter(fn ($p) => filled($p['title']));

    /* ---- "What you should know" — verified facts, not labels ---- */
    $featureDefaults = [
        [
            'icon'        => 'users',
            'title'       => 'Capped at 25 children per classroom.',
            'description' => 'Personalised attention isn\'t a slogan here — it\'s structural. Every classroom is intentionally small so teachers know each child by name, by strength, and by struggle.',
        ],
        [
            'icon'        => 'graduation-cap',
            'title'       => '100% pass rate at ECZ — Class of 2023.',
            'description' => 'Our most recent Grade 12 cohort placed every single learner through their national examinations.',
        ],
        [
            'icon'        => 'star',
            'title'       => 'First place at the National Science Fair.',
            'description' => 'Our Grade 10 team won top honours nationally for an innovative renewable-energy project — selected from entries across the country.',
        ],
        [
            'icon'        => 'monitor',
            'title'       => 'A new, fully-equipped computer lab.',
            'description' => 'Recently opened — modern workstations and software supporting digital literacy from upper primary through to senior secondary.',
        ],
    ];
    $features = collect($landing['features'] ?? $featureDefaults)
        ->filter(fn ($f) => filled($f['title'] ?? null));

    /* ---- ACCREDITATIONS ---- */
    $accreditationDefaults = [
        ['label' => 'Ministry of Education',     'logo' => null, '_bundled' => 'images/slides/moe.png'],
        ['label' => 'ECZ',                       'logo' => null, '_bundled' => 'images/slides/eczlogo.png'],
        ['label' => 'Catholic Mission Council',  'logo' => null, '_bundled' => 'images/slides/cmc.png'],
        ['label' => 'Apostolic recognition',     'logo' => null, '_bundled' => 'images/slides/apostle.png'],
    ];
    $accreditations = collect($landing['accreditations'] ?? $accreditationDefaults)
        ->map(function ($a, $i) use ($img, $accreditationDefaults) {
            $bundled = $accreditationDefaults[$i]['_bundled'] ?? null;
            return [
                'label' => $a['label'] ?? '',
                'logo'  => $img($a['logo'] ?? null, $bundled),
            ];
        })
        ->filter(fn ($a) => filled($a['logo']));
    $accreditationHeading = $landing['accreditation_heading'] ?? 'Recognised & Accredited By';

    /* ---- PORTAL CARDS ---- */
    $portalDefaults = [
        ['icon' => 'parent',  'title' => 'Parent Portal',     'description' => 'Track attendance, results, homework and fees. Pay online and submit assignments from your phone.', 'cta_label' => 'Open Parent App',  'cta_url' => $parentApp,           'open_in_new_tab' => true],
        ['icon' => 'teacher', 'title' => 'Teacher Portal',    'description' => 'Mark attendance, post homework, enter results, manage CPD and message parents — all in one place.',    'cta_label' => 'Open Teacher App', 'cta_url' => $teacherApp,          'open_in_new_tab' => true],
        ['icon' => 'lock',    'title' => 'Staff & Admin',     'description' => 'Administrators, accountants, librarians and heads of school manage every aspect of operations here.', 'cta_label' => 'Sign in',          'cta_url' => url('/admin/login'),  'open_in_new_tab' => false],
        ['icon' => 'card',    'title' => 'Pay School Fees',   'description' => 'Pay securely via mobile money, card or bank transfer. Receipts are emailed instantly — no login required.', 'cta_label' => 'Pay now', 'cta_url' => url('/pay'),          'open_in_new_tab' => false],
    ];
    $portalCards = collect($landing['portal_cards'] ?? $portalDefaults)
        ->filter(fn ($c) => filled($c['title'] ?? null) && filled($c['cta_url'] ?? null));

    /* ---- CTA BANNER ---- */
    $ctaBannerTitle    = $landing['cta_banner_title']  ?? "Ready to join the {$shortName} family?";
    $ctaBannerBody     = $landing['cta_banner_body']   ?? "Admissions are open for the {$year} academic year. Schedule a campus tour or begin your application today.";
    $ctaBannerPriLabel = $landing['cta_banner_primary_label']   ?? 'Apply now';
    $ctaBannerPriUrl   = $landing['cta_banner_primary_url']     ?? '#contact';
    $ctaBannerSecLabel = $landing['cta_banner_secondary_label'] ?? 'Call us';
    $ctaBannerSecUrl   = $landing['cta_banner_secondary_url']   ?? 'tel:' . preg_replace('/\s+/', '', $phone);

    /* ---- SECTION TOGGLES ---- */
    $showTrustStrip   = $landing['show_trust_strip']  ?? true;
    $showStats        = ($landing['show_stats']       ?? true);
    $showPrograms     = ($landing['show_programs']    ?? true) && $programs->isNotEmpty();
    $showFeatures     = ($landing['show_features']    ?? true) && $features->isNotEmpty();
    $showPortal       = ($landing['show_portal']      ?? true) && $portalCards->isNotEmpty();
    $showGallery      = ($landing['show_gallery']     ?? true);
    $showCtaBanner    = ($landing['show_cta_banner']  ?? true);
    $showNews         = ($landing['show_news']        ?? true) && isset($latestNews)     && $latestNews->count();
    $showEvents       = ($landing['show_events']      ?? true) && isset($upcomingEvents) && $upcomingEvents->count();
    $showTestimonials = ($landing['show_testimonials'] ?? true);

    /* ---- GALLERY ---- */
    $galleryImages = collect((array) ($landing['gallery_images'] ?? []))
        ->filter()
        ->map(fn ($p) => asset('storage/' . ltrim($p, '/')))
        ->values();
    if ($galleryImages->isEmpty()) {
        $galleryImages = collect([
            asset('images/campus/campus3.jpg'),
            asset('images/primary/primary3.jpg'),
            asset('images/secondary/secondary3.jpg'),
            asset('images/primary/primary7.jpg'),
            asset('images/campus/campus4.jpg'),
            asset('images/secondary/secondary5.jpg'),
            asset('images/secondary/secondary7.jpg'),
        ]);
    }

    /* ---- LIVE DATA ---- */
    $stats          = $stats ?? [];
    $studentCount   = (int) ($stats['students'] ?? 0);
    $teacherCount   = (int) ($stats['teachers'] ?? 0);
    $currentAY      = (string) ($stats['academic_year'] ?? '');
    $testimonials   = $testimonials   ?? collect();
    $latestNews     = $latestNews     ?? collect();
    $upcomingEvents = $upcomingEvents ?? collect();

    /* ---- ICON LIBRARY (admin selects by key, blade renders the SVG) ---- */
    $iconPaths = [
        'graduation-cap' => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
        'shield'         => '<path d="M12 2 4 5v6c0 5 3.4 9.6 8 11 4.6-1.4 8-6 8-11V5l-8-3z"/>',
        'users'          => '<path d="M16 11a4 4 0 1 0-8 0"/><path d="M2 21v-2a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'monitor'        => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
        'globe'          => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 0 1 0 20a15 15 0 0 1 0-20"/>',
        'home'           => '<path d="M3 10l9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'book'           => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
        'star'           => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'heart'          => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
        'parent'         => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'teacher'        => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
        'lock'           => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'card'           => '<rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>',
    ];
    $renderIcon = function (?string $key, int $size = 24) use ($iconPaths) {
        $paths = $iconPaths[$key] ?? '<circle cx="12" cy="12" r="9"/>';
        return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
    };

    /* For the editorial layout we use a "headline statement" instead of the eyebrow+H2 pattern. */
    $heroStatement = $heroHeadline . ($heroAccent ? ' ' . $heroAccent : '');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0e2746">
    <meta name="description" content="{{ $schoolName }} — {{ $motto }}. A Christ-centred school in Zambia.">
    <meta name="robots" content="index,follow">
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="{{ $schoolName }}">
    <meta property="og:description" content="{{ $motto }} — A Christ-centred school in Zambia.">
    <meta property="og:image"       content="{{ $heroImage }}">

    <title>{{ $schoolName }}</title>

    <link rel="icon" type="image/png" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" href="{{ $logoPath }}">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Editorial pairing: warm serif (EB Garamond) for display + Inter for body --}}
    <link href="https://fonts.bunny.net/css?family=eb-garamond:400,500,600,700|inter:400,500,600&display=swap" rel="stylesheet">

    <script type="application/ld+json">
    {!! json_encode([
        '@context'    => 'https://schema.org',
        '@type'       => 'EducationalOrganization',
        'name'        => $schoolName,
        'slogan'      => $motto,
        'url'         => url('/'),
        'logo'        => $logoPath,
        'image'       => $heroImage,
        'email'       => $email,
        'telephone'   => $phone,
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $settings->address ?? null,
            'addressLocality' => $settings->city ?? null,
            'addressRegion'   => $settings->state_province ?? null,
            'addressCountry'  => $settings->country ?? 'Zambia',
            'postalCode'      => $settings->postal_code ?? null,
        ],
        'sameAs' => array_values(array_filter([$facebook, $twitter, $instagram, $youtube], fn ($u) => $u && $u !== '#')),
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <style>
        /* ============== TOKENS — warm, editorial, restrained ============== */
        :root {
            --ink:        #11151c;
            --ink-soft:   #2c3340;
            --muted:      #5f6675;
            --rule:       #d6cfbf;
            --rule-soft:  #ece6d8;
            --paper:      #faf6ef;
            --paper-deep: #f1ebde;
            --navy:       #0e2746;
            --navy-deep:  #06182f;
            --crimson:    #9c1d2c;
            --crimson-d:  #6f1320;
            --gold:       #b08a3e;
            --max:        1180px;
            --gap:        clamp(1rem, 3vw, 2rem);
            --serif:      'EB Garamond', Georgia, 'Times New Roman', serif;
            --sans:       'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }

        /* ============== BASE ============== */
        *,*::before,*::after { box-sizing: border-box; }
        html { -webkit-text-size-adjust:100%; scroll-behavior:smooth; }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior:auto; } *,*::before,*::after { animation-duration:.001s !important; transition-duration:.001s !important; } }
        body {
            margin:0; font-family:var(--sans); font-size:17px; line-height:1.65;
            color:var(--ink); background:var(--paper);
            -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
        }
        img,svg { max-width:100%; display:block; }
        a { color:inherit; text-decoration:none; }
        button { font:inherit; cursor:pointer; border:0; background:none; color:inherit; }
        h1,h2,h3,h4 { margin:0; font-family:var(--serif); font-weight:500; line-height:1.1; letter-spacing:-0.005em; color:var(--ink); }
        h1 { font-size: clamp(2.4rem, 6vw, 4.6rem); }
        h2 { font-size: clamp(1.9rem, 4.2vw, 3rem); }
        h3 { font-size: clamp(1.35rem, 2.6vw, 1.7rem); }
        h4 { font-size: 1.15rem; }
        p { margin:0; }
        .container { width:100%; max-width:var(--max); margin-inline:auto; padding-inline:1.5rem; }

        /* Skip link */
        .skip { position:absolute; left:-9999px; }
        .skip:focus { left:1rem; top:1rem; padding:.6rem 1rem; background:var(--navy); color:#fff; z-index:1000; }

        /* ============== TOP BAR — quiet, single-line ============== */
        .topbar {
            background:var(--navy-deep); color:rgba(255,255,255,.78);
            font-family:var(--sans); font-size:13px; letter-spacing:.01em;
        }
        .topbar-inner { display:flex; flex-wrap:wrap; gap:.4rem 2rem; padding-block:.6rem; align-items:center; justify-content:space-between; }
        .topbar a { color:inherit; }
        .topbar a:hover { color:#fff; }
        .topbar .meta { display:flex; gap:1.5rem; flex-wrap:wrap; align-items:center; }
        .topbar .social { display:flex; gap:.6rem; }
        .topbar .social a { color:rgba(255,255,255,.65); transition:color .15s; }
        .topbar .social a:hover { color:#fff; }
        @media (max-width:640px) { .topbar .meta .hide-sm { display:none; } }

        /* ============== NAV — masthead, not SaaS sticky bar ============== */
        .nav {
            position:sticky; top:0; z-index:60;
            background:var(--paper);
            border-bottom:1px solid var(--rule);
            transition:box-shadow .25s, background .25s;
        }
        .nav.scrolled { box-shadow:0 2px 24px -16px rgba(17,21,28,.4); }
        .nav-inner { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding-block:1rem; }
        .brand { display:flex; align-items:center; gap:.85rem; min-width:0; }
        .brand img { width:46px; height:46px; border-radius:50%; object-fit:cover; }
        .brand-text { line-height:1.1; }
        .brand-name { font-family:var(--serif); font-weight:600; font-size:1.18rem; color:var(--ink); }
        .brand-tag  { font-size:11px; color:var(--muted); letter-spacing:.18em; text-transform:uppercase; margin-top:2px; }

        .nav-links { display:none; gap:.25rem; }
        .nav-links a {
            position:relative; padding:.55rem .9rem; font-size:.93rem; font-weight:500;
            color:var(--ink-soft); transition:color .15s;
        }
        .nav-links a::after {
            content:''; position:absolute; left:.9rem; right:.9rem; bottom:.35rem;
            height:1px; background:var(--ink); transform:scaleX(0); transform-origin:left;
            transition:transform .25s;
        }
        .nav-links a:hover::after, .nav-links a:focus-visible::after, .nav-links a.active::after { transform:scaleX(1); }
        .nav-links a:hover { color:var(--ink); }

        .nav-cta { display:none; }

        .btn {
            display:inline-flex; align-items:center; gap:.55rem;
            padding:.78rem 1.4rem; font-size:.92rem; font-weight:500; letter-spacing:.01em;
            background:var(--ink); color:#fff; border:1px solid var(--ink);
            border-radius:0; transition:background .2s, color .2s, border-color .2s, transform .15s;
            white-space:nowrap;
        }
        .btn:hover { background:transparent; color:var(--ink); }
        .btn:focus-visible { outline:2px solid var(--gold); outline-offset:2px; }
        .btn-ghost { background:transparent; color:var(--ink); border-color:var(--ink); }
        .btn-ghost:hover { background:var(--ink); color:#fff; }
        .btn-light { background:#fff; color:var(--ink); border-color:#fff; }
        .btn-light:hover { background:transparent; color:#fff; border-color:#fff; }
        .btn-paper { background:var(--paper); color:var(--ink); border-color:var(--paper); }
        .btn-paper:hover { background:transparent; color:var(--paper); border-color:var(--paper); }

        .menu-btn {
            width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center;
            color:var(--ink); border:1px solid var(--rule); background:transparent;
        }

        @media (min-width:960px) {
            .nav-links, .nav-cta { display:inline-flex; gap:.6rem; align-items:center; }
            .menu-btn { display:none; }
        }

        /* Mobile drawer */
        .mobile-menu {
            position:fixed; inset:0; z-index:80; background:var(--navy-deep); color:#fff;
            display:flex; flex-direction:column; padding:1.25rem;
            transform:translateX(100%); transition:transform .25s;
        }
        .mobile-menu.open { transform:translateX(0); }
        .mobile-menu-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
        .mobile-menu-head .brand-name { color:#fff; }
        .mobile-menu-head .brand-tag  { color:rgba(255,255,255,.65); }
        .mobile-menu-head button { color:#fff; border-color:rgba(255,255,255,.25); }
        .mobile-menu nav { display:flex; flex-direction:column; gap:0; margin-top:.5rem; }
        .mobile-menu nav a {
            padding:1rem .25rem; font-family:var(--serif); font-size:1.5rem; color:#fff;
            border-bottom:1px solid rgba(255,255,255,.12);
        }
        .mobile-menu .actions { margin-top:auto; display:grid; gap:.5rem; }
        body.menu-open { overflow:hidden; }

        /* ============== ANNOUNCEMENT (when present) ============== */
        .announce {
            background:var(--ink); color:#fff;
            font-size:.85rem; letter-spacing:.02em; font-family:var(--sans);
        }
        .announce-inner { padding-block:.55rem; text-align:center; }
        .announce a { color:#fff; text-decoration:underline; text-underline-offset:3px; }

        /* ============== HERO — full-bleed photo, single editorial statement ============== */
        .hero { position:relative; isolation:isolate; color:#fff; overflow:hidden; }
        .hero-bg { position:absolute; inset:0; z-index:-2; }
        .hero-bg img { width:100%; height:100%; object-fit:cover; }
        .hero::before {
            content:''; position:absolute; inset:0; z-index:-1;
            background:linear-gradient(180deg, rgba(6,24,47,.45) 0%, rgba(6,24,47,.65) 100%);
        }
        .hero-inner {
            min-height: clamp(560px, 88vh, 820px);
            display:grid; align-content:end; padding-block: 7rem 3.5rem;
        }
        .hero-deck {
            font-family:var(--sans); text-transform:uppercase; letter-spacing:.22em;
            font-size:.78rem; font-weight:500; color:rgba(255,255,255,.85);
            margin-bottom:1.25rem;
        }
        .hero h1 {
            color:#fff; font-weight:500; font-size: clamp(2.4rem, 7vw, 5.4rem);
            max-width: 18ch; line-height:1.04; letter-spacing:-0.01em;
        }
        .hero h1 em { font-style: italic; color:#f3e9c8; }
        .hero .lede {
            font-family:var(--serif); font-size: clamp(1.15rem, 1.8vw, 1.4rem); font-weight:400;
            color:rgba(255,255,255,.92); margin-top:1.5rem; max-width: 62ch; line-height:1.5;
        }
        .hero-actions { display:flex; flex-wrap:wrap; gap:1rem; margin-top:2rem; }
        .hero-foot {
            display:flex; flex-wrap:wrap; gap:.4rem 1.5rem; align-items:baseline;
            margin-top: 4rem; padding-top: 1.5rem;
            border-top:1px solid rgba(255,255,255,.2);
            color:rgba(255,255,255,.78); font-family:var(--sans); font-size:.86rem;
        }
        .hero-foot strong { color:#fff; font-weight:500; font-family:var(--serif); font-size:1.05rem; letter-spacing:.005em; }

        /* ============== WELCOME LETTER — replaces "About" icon-card pattern ============== */
        .welcome { padding-block: clamp(4rem, 8vw, 6.5rem); }
        .welcome-grid {
            display:grid; gap: clamp(2rem, 5vw, 4.5rem);
            grid-template-columns: 1fr;
            align-items:start;
        }
        @media (min-width: 880px) {
            .welcome-grid { grid-template-columns: 5fr 7fr; }
        }
        .welcome figure { margin:0; }
        .welcome figure img {
            width:100%; aspect-ratio: 4/5; object-fit:cover;
            filter: grayscale(.15) contrast(1.05);
        }
        .welcome figcaption {
            margin-top: 1rem; font-family:var(--sans); font-size:.78rem;
            letter-spacing:.18em; text-transform:uppercase; color:var(--muted);
        }
        .welcome .body { padding-top: .5rem; }
        .welcome .label {
            font-family:var(--sans); font-size:.78rem; font-weight:500;
            letter-spacing:.22em; text-transform:uppercase; color:var(--crimson);
        }
        .welcome h2 { margin-top:.85rem; }
        .welcome .text {
            margin-top: 1.5rem; font-family:var(--serif); font-size: 1.25rem; line-height:1.5;
            color:var(--ink-soft); max-width: 60ch;
        }
        .welcome .text + .text { margin-top: 1rem; }
        .welcome .text .dropcap {
            float:left; font-size: 4.4rem; line-height:.9; padding-right:.55rem; padding-top:.3rem;
            font-weight:500; color:var(--crimson);
        }
        .welcome .signoff {
            margin-top: 2rem; font-family:var(--serif);
            display:flex; flex-direction:column; gap:.15rem;
        }
        .welcome .signoff strong { font-style:italic; font-weight:500; font-size:1.2rem; }
        .welcome .signoff span   { font-family:var(--sans); font-size:.85rem; color:var(--muted); }

        /* ============== NUMBERS — static, large, integrated into prose ============== */
        .numbers { background:var(--paper-deep); border-block:1px solid var(--rule); padding-block: clamp(3rem, 6vw, 4.5rem); }
        .numbers-grid { display:grid; gap:2.5rem 3rem; grid-template-columns: repeat(2, 1fr); }
        @media (min-width:880px) { .numbers-grid { grid-template-columns: repeat(4, 1fr); } }
        .num-cell { display:flex; flex-direction:column; gap:.75rem; }
        .num-cell .n {
            font-family:var(--serif); font-weight:500;
            font-size: clamp(2.6rem, 5vw, 3.6rem); line-height:1;
            color:var(--ink); letter-spacing:-.01em;
        }
        .num-cell .l {
            font-family:var(--sans); font-size: .92rem; color:var(--ink-soft);
            line-height:1.4; max-width: 22ch;
        }
        .num-cell .l strong { color:var(--ink); font-weight:600; }

        /* ============== "How learning happens" — alternating editorial blocks ============== */
        .learning { padding-block: clamp(4rem, 8vw, 6.5rem); }
        .learning-head { max-width: 56ch; margin-bottom: clamp(2.5rem, 5vw, 4rem); }
        .learning-head .label { font-family:var(--sans); font-size:.78rem; font-weight:500; letter-spacing:.22em; text-transform:uppercase; color:var(--crimson); }
        .learning-head h2 { margin-top:.75rem; }
        .learning-head p {
            margin-top:1.25rem; font-family:var(--serif); font-size:1.2rem; color:var(--ink-soft); line-height:1.55;
            max-width: 56ch;
        }
        .learning-list { display:grid; gap: clamp(2rem, 5vw, 4rem); }
        .learn-row {
            display:grid; gap: clamp(1.5rem, 4vw, 3rem);
            grid-template-columns: 1fr; align-items:center;
        }
        @media (min-width: 880px) {
            .learn-row { grid-template-columns: 6fr 5fr; }
            .learn-row.flip { grid-template-columns: 5fr 6fr; }
            .learn-row.flip .learn-img { order: 2; }
        }
        .learn-img img { width:100%; aspect-ratio: 4/3; object-fit:cover; }
        .learn-text .num { font-family:var(--serif); font-size: 1.15rem; color:var(--crimson); font-style:italic; }
        .learn-text h3   { margin-top: .5rem; max-width: 22ch; }
        .learn-text p    { margin-top: 1rem; color:var(--ink-soft); font-size:1.02rem; max-width: 50ch; }

        /* ============== PROGRAMS — editorial cards, no hover-grow gimmick ============== */
        .programs { background:var(--paper-deep); border-block:1px solid var(--rule); padding-block: clamp(4rem, 8vw, 6.5rem); }
        .programs-head { display:flex; align-items:end; justify-content:space-between; gap:2rem; margin-bottom:2.5rem; flex-wrap:wrap; }
        .programs-head h2 { max-width: 16ch; }
        .programs-head p  { font-family:var(--serif); color:var(--ink-soft); font-size:1.15rem; max-width:50ch; }
        .programs-grid { display:grid; gap: 2rem; grid-template-columns: 1fr; }
        @media (min-width:720px) { .programs-grid { grid-template-columns: repeat(3, 1fr); } }
        .program {
            display:flex; flex-direction:column; gap:1rem;
        }
        .program .ph { aspect-ratio: 4/5; overflow:hidden; background:var(--rule-soft); }
        .program .ph img { width:100%; height:100%; object-fit:cover; transition: transform .8s ease; }
        .program:hover .ph img { transform: scale(1.03); }
        .program .age {
            font-family:var(--serif); font-style:italic; color:var(--crimson); font-size:1rem;
        }
        .program h3 { font-size: 1.45rem; }
        .program p  { color:var(--ink-soft); font-size:.97rem; line-height:1.55; }
        .program a.read {
            display:inline-flex; align-items:center; gap:.4rem;
            font-family:var(--sans); font-size:.85rem; letter-spacing:.06em; text-transform:uppercase;
            color:var(--ink); border-bottom:1px solid var(--ink); padding-bottom:.15rem;
            align-self:flex-start; transition:color .15s, border-color .15s;
        }
        .program a.read:hover { color:var(--crimson); border-bottom-color:var(--crimson); }

        /* ============== PORTAL ACCESS — quiet utility row ============== */
        .portal { padding-block: clamp(3.5rem, 7vw, 5rem); }
        .portal-head { max-width: 56ch; margin-bottom:2.5rem; }
        .portal-head h2 { font-size: clamp(1.6rem, 3.5vw, 2.2rem); }
        .portal-head p  { color:var(--ink-soft); margin-top:.85rem; font-family:var(--serif); font-size:1.1rem; }
        .portal-grid { display:grid; gap:0; border-top:1px solid var(--rule); }
        @media (min-width:880px) { .portal-grid { grid-template-columns: repeat(2, 1fr); } }
        .portal-card {
            display:flex; gap:1.25rem; align-items:flex-start;
            padding: 1.5rem 1.25rem; border-bottom:1px solid var(--rule);
            transition: background .2s;
        }
        @media (min-width:880px) { .portal-card:nth-child(odd) { border-right:1px solid var(--rule); } }
        .portal-card:hover { background: var(--paper-deep); }
        .portal-card .ico {
            flex:none; width:40px; height:40px; color:var(--crimson);
        }
        .portal-card .copy h3 { font-size:1.2rem; }
        .portal-card .copy p  { color:var(--ink-soft); margin-top:.4rem; font-size:.93rem; line-height:1.55; }
        .portal-card .copy .go {
            display:inline-block; margin-top:.85rem;
            font-family:var(--sans); font-size:.78rem; letter-spacing:.2em; text-transform:uppercase;
            color:var(--ink); border-bottom:1px solid var(--ink); padding-bottom:.15rem;
        }

        /* ============== GALLERY — full-bleed strip ============== */
        .gallery-section { padding-block: clamp(3rem, 6vw, 4.5rem); }
        .gallery-head { display:flex; justify-content:space-between; align-items:end; gap:2rem; flex-wrap:wrap; margin-bottom:1.5rem; }
        .gallery-strip {
            display:grid; grid-template-columns: repeat(2, 1fr); gap:.5rem;
            grid-auto-rows: 180px;
        }
        @media (min-width:720px) { .gallery-strip { grid-template-columns: repeat(4, 1fr); grid-auto-rows: 220px; } }
        @media (min-width:1100px){ .gallery-strip { grid-template-columns: repeat(6, 1fr); grid-auto-rows: 220px; } }
        .gallery-strip figure { margin:0; overflow:hidden; }
        .gallery-strip figure img { width:100%; height:100%; object-fit:cover; transition: transform .8s; }
        .gallery-strip figure:hover img { transform: scale(1.04); }
        .gallery-strip figure.span2 { grid-column: span 2; }

        /* ============== TESTIMONIAL PULL-QUOTE ============== */
        .voices { background:var(--navy-deep); color:#fff; padding-block: clamp(4rem, 8vw, 6.5rem); }
        .voices .label {
            font-family:var(--sans); font-size:.78rem; font-weight:500;
            letter-spacing:.22em; text-transform:uppercase; color:rgba(255,255,255,.7);
        }
        .voice {
            max-width: 880px; margin-inline:auto; text-align:center;
            padding-block: clamp(2rem, 5vw, 3.5rem);
        }
        .voice blockquote {
            margin:0; font-family:var(--serif); font-style:italic; font-weight:400;
            font-size: clamp(1.5rem, 3vw, 2.4rem); line-height:1.3; color:#fff;
            letter-spacing:-.005em;
        }
        .voice blockquote::before { content:'"'; font-size:1.2em; color:var(--gold); margin-right:.05em; }
        .voice blockquote::after  { content:'"'; font-size:1.2em; color:var(--gold); margin-left:.05em; }
        .voice .who { margin-top: 2rem; font-family:var(--sans); }
        .voice .who strong { color:#fff; font-weight:500; }
        .voice .who span   { color:rgba(255,255,255,.7); margin-left:.5rem; font-size:.92rem; }
        .voice-nav { display:flex; gap:.5rem; justify-content:center; margin-top: 2rem; }
        .voice-dot {
            width:8px; height:8px; border-radius:50%;
            background:rgba(255,255,255,.25); transition:background .2s, transform .2s;
        }
        .voice-dot.active { background:var(--gold); transform:scale(1.3); }

        /* ============== NEWS / EVENTS — newspaper style ============== */
        .ne { padding-block: clamp(4rem, 8vw, 6rem); }
        .ne-head { display:flex; align-items:end; justify-content:space-between; gap:2rem; flex-wrap:wrap; padding-bottom:1.25rem; border-bottom:2px solid var(--ink); margin-bottom:2.5rem; }
        .ne-head h2 { font-style:italic; }
        .ne-head .all {
            font-family:var(--sans); font-size:.78rem; letter-spacing:.18em; text-transform:uppercase; color:var(--ink);
            border-bottom:1px solid var(--ink); padding-bottom:.15rem;
        }
        .ne-grid { display:grid; gap: 2.5rem; grid-template-columns: 1fr; }
        @media (min-width:720px) { .ne-grid { grid-template-columns: repeat(3, 1fr); } }
        .ne-item .ph { aspect-ratio: 16/10; overflow:hidden; background:var(--rule-soft); margin-bottom:1rem; }
        .ne-item .ph img { width:100%; height:100%; object-fit:cover; }
        .ne-item .meta { font-family:var(--sans); font-size:.78rem; letter-spacing:.16em; text-transform:uppercase; color:var(--muted); }
        .ne-item h3 { margin-top:.65rem; font-size:1.35rem; line-height:1.2; max-width: 22ch; }
        .ne-item p  { margin-top:.7rem; color:var(--ink-soft); font-size:.97rem; line-height:1.55; }
        .ne-item .when { display:flex; gap:.4rem; align-items:baseline; color:var(--crimson); font-family:var(--serif); font-size:1.1rem; font-style:italic; margin-bottom:.4rem; }
        .ne-item .when .d { font-size:2rem; line-height:1; font-style:normal; }

        /* ============== CTA strip (replaces "Apply now" gradient banner) ============== */
        .cta {
            background:var(--ink); color:#fff;
            padding-block: clamp(3rem, 6vw, 4.5rem);
        }
        .cta-grid { display:grid; gap:2rem; align-items:end; }
        @media (min-width:720px) { .cta-grid { grid-template-columns: 3fr 2fr; } }
        .cta h2 { color:#fff; max-width:18ch; line-height:1.05; font-size: clamp(1.8rem, 4vw, 3rem); }
        .cta p  { font-family:var(--serif); color:rgba(255,255,255,.82); margin-top:1rem; max-width:50ch; font-size:1.15rem; }
        .cta .actions { display:flex; flex-wrap:wrap; gap:.75rem; }

        /* ============== CONTACT ============== */
        .contact { padding-block: clamp(4rem, 8vw, 6rem); }
        .contact-grid { display:grid; gap: 3rem; }
        @media (min-width:880px) { .contact-grid { grid-template-columns: 5fr 7fr; } }
        .contact-info h2 { max-width:14ch; }
        .contact-info p  { color:var(--ink-soft); margin-top:1rem; font-family:var(--serif); font-size:1.15rem; max-width:40ch; }
        .contact-list { display:grid; gap:1.5rem; margin-top:2.5rem; padding-top:2rem; border-top:1px solid var(--rule); }
        .contact-list dt {
            font-family:var(--sans); font-size:.72rem; letter-spacing:.2em; text-transform:uppercase;
            color:var(--muted); font-weight:500; margin-bottom:.3rem;
        }
        .contact-list dd { margin:0; font-family:var(--serif); font-size:1.2rem; color:var(--ink); }
        .contact-list dd a:hover { color:var(--crimson); }

        .contact-form { display:grid; gap:1.25rem; }
        .field label { display:block; font-family:var(--sans); font-size:.72rem; letter-spacing:.18em; text-transform:uppercase; color:var(--muted); font-weight:500; margin-bottom:.4rem; }
        .field input, .field textarea, .field select {
            width:100%; padding:.85rem 0; border:0; border-bottom:1px solid var(--rule);
            background:transparent; font: inherit; color:var(--ink);
            transition:border-color .2s;
        }
        .field input:focus, .field textarea:focus, .field select:focus { outline:0; border-bottom-color:var(--ink); }
        .field textarea { min-height: 100px; resize:vertical; }
        @media (min-width:640px) { .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; } }
        .form-row.full { grid-template-columns:1fr; }
        .contact-submit { display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between; margin-top:1rem; }
        .contact-submit small { color:var(--muted); font-size:.82rem; }

        /* ============== FOOTER ============== */
        footer.site { background:var(--navy-deep); color:rgba(255,255,255,.7); padding-block: 3rem 1.25rem; font-family:var(--sans); font-size:.92rem; }
        footer.site a { color:rgba(255,255,255,.7); }
        footer.site a:hover { color:#fff; }
        footer.site .grid { display:grid; gap:2.5rem; }
        @media (min-width:720px) { footer.site .grid { grid-template-columns: 1.3fr 1fr 1fr 1fr; } }
        footer.site h5 { color:#fff; font-family:var(--serif); font-weight:500; font-size:1.1rem; letter-spacing:0; text-transform:none; margin-bottom:1rem; }
        footer.site ul { list-style:none; padding:0; margin:0; display:grid; gap:.55rem; }
        footer.site .blurb { max-width: 30ch; margin-block: 1rem 1.25rem; }
        footer.site .social-row { display:flex; gap:1rem; }
        footer.site .social-row a { color:rgba(255,255,255,.6); }
        footer.site .social-row a:hover { color:#fff; }
        footer.site .recognised {
            margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,.1);
            display:flex; flex-wrap:wrap; gap: 1.5rem 2rem; align-items:center; justify-content:center;
        }
        footer.site .recognised span { font-family:var(--sans); font-size:.72rem; letter-spacing:.2em; text-transform:uppercase; color:rgba(255,255,255,.5); }
        footer.site .recognised img { height:34px; width:auto; opacity:.78; filter:brightness(0) invert(1); }
        footer.site .recognised img:hover { opacity:1; }
        footer.site .legal {
            margin-top: 1.5rem; padding-top: 1.25rem; border-top:1px solid rgba(255,255,255,.1);
            display:flex; flex-wrap:wrap; gap:.75rem 1.5rem; justify-content:space-between; align-items:center;
            font-size:.78rem; color:rgba(255,255,255,.5);
        }
        footer.site .legal a { color:rgba(255,255,255,.6); }

        /* Back-to-top — minimal */
        .to-top {
            position:fixed; right:1.5rem; bottom:1.5rem; z-index:70;
            width:42px; height:42px; background:var(--ink); color:#fff;
            display:inline-flex; align-items:center; justify-content:center;
            opacity:0; transform:translateY(8px); pointer-events:none;
            transition:opacity .2s, transform .2s;
        }
        .to-top.show { opacity:1; transform:translateY(0); pointer-events:auto; }
        .to-top:hover { background:var(--crimson); }
    </style>
</head>
<body>
    <a class="skip" href="#main">Skip to content</a>

    @if($announcementText !== '')
        <div class="announce" role="region" aria-label="Announcement">
            <div class="container announce-inner">
                @if($announcementUrl)
                    <a href="{{ $announcementUrl }}">{{ $announcementText }}</a>
                @else
                    <span>{{ $announcementText }}</span>
                @endif
            </div>
        </div>
    @endif

    <div class="topbar">
        <div class="container topbar-inner">
            <div class="meta">
                <span><a href="tel:{{ preg_replace('/\s+/','',$phone) }}">{{ $phone }}</a></span>
                <span class="hide-sm"><a href="mailto:{{ $email }}">{{ $email }}</a></span>
                <span class="hide-sm">Mon — Fri · 07:30 — 16:30</span>
            </div>
            <div class="social">
                <a href="{{ $facebook }}"  aria-label="Facebook"  target="_blank" rel="noopener"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9V14.9H8V12h2.4V9.8c0-2.4 1.4-3.7 3.6-3.7c1 0 2.1.2 2.1.2v2.3h-1.2c-1.2 0-1.5.7-1.5 1.5V12h2.6l-.4 2.9h-2.2v6.9A10 10 0 0 0 22 12Z"/></svg></a>
                <a href="{{ $instagram }}" aria-label="Instagram" target="_blank" rel="noopener"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                <a href="{{ $youtube }}"   aria-label="YouTube"   target="_blank" rel="noopener"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1C24 15.9 24 12 24 12s0-3.9-.5-5.8zM9.6 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg></a>
            </div>
        </div>
    </div>

    <header class="nav" id="nav">
        <div class="container nav-inner">
            <a class="brand" href="/" aria-label="{{ $schoolName }} home">
                <img src="{{ $logoPath }}" alt="" onerror="this.style.display='none'">
                <span class="brand-text">
                    <span class="brand-name">{{ $shortName }}</span>
                    <span class="brand-tag">{{ $motto }}</span>
                </span>
            </a>

            <nav class="nav-links" aria-label="Primary">
                <a href="#welcome">Welcome</a>
                <a href="#programs">Programs</a>
                <a href="#learning">Approach</a>
                <a href="{{ route('gallery') }}">Gallery</a>
                <a href="#portal">Portal</a>
                <a href="#contact">Contact</a>
            </nav>

            <div class="nav-cta">
                <a class="btn btn-ghost" href="{{ url('/pay') }}">Pay Fees</a>
                <a class="btn" href="{{ url('/admin/login') }}">Sign In</a>
            </div>

            <button class="menu-btn" id="menu-toggle" aria-controls="mobile-menu" aria-expanded="false" aria-label="Open menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><line x1="3" y1="7" x2="21" y2="7"/><line x1="3" y1="17" x2="21" y2="17"/></svg>
            </button>
        </div>
    </header>

    <div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Mobile menu">
        <div class="mobile-menu-head">
            <span class="brand">
                <span class="brand-text">
                    <span class="brand-name">{{ $shortName }}</span>
                    <span class="brand-tag">{{ $motto }}</span>
                </span>
            </span>
            <button class="menu-btn" id="menu-close" aria-label="Close menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <nav>
            <a href="#welcome">Welcome</a>
            <a href="#programs">Programs</a>
            <a href="#learning">Approach</a>
            <a href="{{ route('gallery') }}">Gallery</a>
            <a href="#portal">Portal</a>
            <a href="#contact">Contact</a>
        </nav>
        <div class="actions">
            <a class="btn btn-light" href="{{ url('/pay') }}">Pay Fees</a>
            <a class="btn btn-paper" href="{{ url('/admin/login') }}">Sign in to portal</a>
        </div>
    </div>

    <main id="main">
        {{-- ===== HERO ===== --}}
        <section class="hero">
            <div class="hero-bg">
                <img src="{{ $heroImage }}" alt="{{ $schoolName }} campus" loading="eager">
            </div>
            <div class="container hero-inner">
                <div class="hero-deck">{{ $shortName }} · Lusaka, Zambia</div>
                <h1>A school where every child is <em>known.</em></h1>
                <p class="lede">{{ $heroSub }}</p>
                <div class="hero-actions">
                    <a class="btn btn-light" href="{{ $ctaPrimaryUrl }}">{{ $ctaPrimaryLabel }}</a>
                    <a class="btn btn-paper" href="{{ $ctaSecondaryUrl }}">{{ $ctaSecondaryLabel }}</a>
                </div>

                @if($heroStats->isNotEmpty())
                <div class="hero-foot">
                    @foreach($heroStats as $i => $hs)
                        <span><strong>{{ $hs['value'] ?? '' }}</strong> &nbsp; {{ $hs['label'] ?? '' }}</span>
                        @if(!$loop->last) <span aria-hidden="true">·</span> @endif
                    @endforeach
                </div>
                @endif
            </div>
        </section>

        {{-- ===== WELCOME / LETTER ===== --}}
        <section class="welcome" id="welcome">
            <div class="container welcome-grid">
                <figure>
                    <img src="{{ $aboutImage }}" alt="At {{ $shortName }}" loading="lazy">
                    <figcaption>The campus, this term</figcaption>
                </figure>
                <div class="body">
                    <span class="label">A word from us</span>
                    <h2>Education is, above all, a relationship.</h2>
                    <p class="text"><span class="dropcap">A</span>t {{ $shortName }}, every learner is met by name. Our teachers know who each child is, where they're from, what they love, and where they need to grow. We've built our days around that conviction — small classes, attentive pastoral care, and a curriculum that asks more than memorisation.</p>
                    <p class="text">{{ $aboutMission }}</p>
                    @if(filled($aboutValues))
                        <p class="text"><em>{{ $aboutValues }}</em></p>
                    @endif

                    <div class="signoff">
                        <strong>The {{ $shortName }} community</strong>
                        <span>Teachers, parents and learners — Chililabombwe, Zambia</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== NUMBERS — static, prose-integrated ===== --}}
        @if($showStats)
        <section class="numbers">
            <div class="container numbers-grid">
                <div class="num-cell">
                    <div class="n">{{ $studentCount > 0 ? $studentCount : '480+' }}</div>
                    <div class="l"><strong>{{ $statsLabels['students'] }}</strong> across early years, primary and secondary.</div>
                </div>
                <div class="num-cell">
                    <div class="n">{{ $teacherCount > 0 ? $teacherCount : '40' }}</div>
                    <div class="l"><strong>{{ $statsLabels['teachers'] }}</strong> — all qualified, many with a decade or more in classrooms.</div>
                </div>
                <div class="num-cell">
                    <div class="n">{{ $currentAY ?: 'AY ' . $year }}</div>
                    <div class="l"><strong>{{ $statsLabels['year'] }}</strong>. Term-by-term reporting, transparent fees.</div>
                </div>
                <div class="num-cell">
                    <div class="n">{{ $statsYears }}+</div>
                    <div class="l"><strong>{{ $statsLabels['years'] }}</strong> of forming young minds in our community.</div>
                </div>
            </div>
        </section>
        @endif

        {{-- ===== HOW LEARNING HAPPENS — alternating editorial blocks ===== --}}
        @if($showFeatures && $features->isNotEmpty())
        <section class="learning" id="learning">
            <div class="container">
                <div class="learning-head">
                    <span class="label">Worth knowing</span>
                    <h2>Four things, plainly.</h2>
                    <p>Less promise, more practice. Four facts about how this school actually runs and what its learners have done lately.</p>
                </div>

                <div class="learning-list">
                    @foreach($features as $i => $f)
                        @php
                            $altImage = match($i % 4) {
                                0 => 'images/primary/primary5.jpg',
                                1 => 'images/secondary/secondary3.jpg',
                                2 => 'images/campus/campus3.jpg',
                                3 => 'images/primary/primary12.jpg',
                            };
                        @endphp
                        <div class="learn-row {{ $i % 2 === 1 ? 'flip' : '' }}">
                            <figure class="learn-img">
                                <img src="{{ asset($altImage) }}" alt="" loading="lazy">
                            </figure>
                            <div class="learn-text">
                                <span class="num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} —</span>
                                <h3>{{ $f['title'] }}</h3>
                                <p>{{ $f['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ===== PROGRAMS ===== --}}
        @if($showPrograms)
        <section class="programs" id="programs">
            <div class="container">
                <div class="programs-head">
                    <h2>Programs that grow with the child.</h2>
                    <p>From early years to school-leaving examinations — every stage is built for what your child is becoming, not just where they are now.</p>
                </div>
                <div class="programs-grid">
                    @foreach($programs as $p)
                        <article class="program">
                            <div class="ph"><img src="{{ $p['image'] }}" alt="{{ $p['title'] }}" loading="lazy"></div>
                            <span class="age">{{ $p['age_range'] }}</span>
                            <h3>{{ $p['title'] }}</h3>
                            <p>{{ $p['description'] }}</p>
                            @if(filled($p['cta_label']))
                                <a class="read" href="{{ $p['cta_url'] }}">{{ $p['cta_label'] }} →</a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ===== PORTAL — quiet utility row ===== --}}
        @if($showPortal)
        <section class="portal" id="portal">
            <div class="container">
                <div class="portal-head">
                    <h2>Portal access.</h2>
                    <p>Real-time results, attendance, homework, fees and communication — for parents, teachers and staff.</p>
                </div>
                <div class="portal-grid">
                    @foreach($portalCards as $c)
                        @php $newTab = ($c['open_in_new_tab'] ?? false) || \Illuminate\Support\Str::startsWith($c['cta_url'], ['http://', 'https://']); @endphp
                        <a class="portal-card" href="{{ $c['cta_url'] }}" @if($newTab) target="_blank" rel="noopener" @endif>
                            <span class="ico" aria-hidden="true">{!! $renderIcon($c['icon'] ?? null, 32) !!}</span>
                            <div class="copy">
                                <h3>{{ $c['title'] }}</h3>
                                <p>{{ $c['description'] ?? '' }}</p>
                                <span class="go">{{ $c['cta_label'] ?? 'Open' }} →</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ===== GALLERY STRIP ===== --}}
        @if($showGallery && $galleryImages->isNotEmpty())
        <section class="gallery-section">
            <div class="container">
                <div class="gallery-head">
                    <h2 style="font-style:italic;">Campus, in moments.</h2>
                    <a class="ne-head .all" href="{{ route('gallery') }}" style="font-family:var(--sans); font-size:.78rem; letter-spacing:.18em; text-transform:uppercase; color:var(--ink); border-bottom:1px solid var(--ink); padding-bottom:.15rem;">View full gallery →</a>
                </div>
            </div>
            <div class="container">
                <div class="gallery-strip">
                    @foreach($galleryImages->take(8) as $i => $src)
                        <figure class="{{ $i === 0 ? 'span2' : '' }}">
                            <img src="{{ $src }}" alt="{{ $shortName }} campus" loading="lazy">
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ===== TESTIMONIAL PULL-QUOTE ===== --}}
        @if($showTestimonials && $testimonials->isNotEmpty())
        <section class="voices" id="voices">
            <div class="container">
                <div style="text-align:center; margin-bottom:1rem;">
                    <span class="label">Voices</span>
                </div>
                @foreach($testimonials as $i => $t)
                    <div class="voice" data-voice-index="{{ $i }}" @if($i > 0) hidden @endif>
                        <blockquote>{{ $t->quote }}</blockquote>
                        <p class="who">
                            <strong>{{ $t->name }}</strong>
                            @if(!empty($t->role))<span>{{ $t->role }}</span>@endif
                        </p>
                    </div>
                @endforeach
                @if($testimonials->count() > 1)
                    <div class="voice-nav" role="tablist" aria-label="Testimonials">
                        @foreach($testimonials as $i => $t)
                            <button class="voice-dot {{ $i === 0 ? 'active' : '' }}" data-voice-dot="{{ $i }}" aria-label="Quote {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
        @endif

        {{-- ===== NEWS — newspaper style ===== --}}
        @if($showNews)
        <section class="ne" id="news">
            <div class="container">
                <div class="ne-head">
                    <h2>Latest from the school</h2>
                    <a class="all" href="#contact">All news →</a>
                </div>
                <div class="ne-grid">
                    @foreach($latestNews as $n)
                        <article class="ne-item">
                            @if($n->image)
                                <div class="ph"><img src="{{ asset('storage/' . ltrim($n->image, '/')) }}" alt="{{ $n->title }}" loading="lazy"></div>
                            @endif
                            <div class="meta">
                                @if($n->category){{ \Illuminate\Support\Str::title(str_replace('_', ' ', $n->category)) }} · @endif
                                @if($n->date){{ \Illuminate\Support\Carbon::parse($n->date)->format('j M Y') }}@endif
                            </div>
                            <h3>{{ $n->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($n->content), 150) }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ===== EVENTS ===== --}}
        @if($showEvents)
        <section class="ne" id="events" style="background:var(--paper-deep); border-block:1px solid var(--rule);">
            <div class="container">
                <div class="ne-head">
                    <h2>What's coming up</h2>
                    <a class="all" href="#contact">Full calendar →</a>
                </div>
                <div class="ne-grid">
                    @foreach($upcomingEvents as $e)
                        @php $start = \Illuminate\Support\Carbon::parse($e->start_date); @endphp
                        <article class="ne-item">
                            <div class="when">
                                <span class="d">{{ $start->format('d') }}</span>
                                <span>{{ $start->format('M Y') }}</span>
                            </div>
                            <h3>{{ $e->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($e->description ?? ''), 130) }}</p>
                            <p style="margin-top:.6rem; font-size:.85rem; color:var(--muted);">
                                {{ $start->format('g:i A') }}
                                @if($e->location) · {{ $e->location }} @endif
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ===== CTA STRIP ===== --}}
        @if($showCtaBanner)
        <section class="cta">
            <div class="container cta-grid">
                <div>
                    <h2>{{ $ctaBannerTitle }}</h2>
                    <p>{{ $ctaBannerBody }}</p>
                </div>
                <div class="actions">
                    @if(filled($ctaBannerPriLabel))
                        <a class="btn btn-light" href="{{ $ctaBannerPriUrl }}">{{ $ctaBannerPriLabel }}</a>
                    @endif
                    @if(filled($ctaBannerSecLabel))
                        <a class="btn btn-paper" href="{{ $ctaBannerSecUrl }}">{{ $ctaBannerSecLabel }}</a>
                    @endif
                </div>
            </div>
        </section>
        @endif

        {{-- ===== CONTACT ===== --}}
        <section class="contact" id="contact">
            <div class="container contact-grid">
                <div class="contact-info">
                    <h2>Talk to us.</h2>
                    <p>Send a note and our admissions office will respond within one working day. Or call — we'd be glad to host you on campus.</p>

                    <dl class="contact-list">
                        <div><dt>Address</dt><dd>{{ $address }}</dd></div>
                        <div><dt>Phone</dt><dd>
                            <a href="tel:{{ preg_replace('/\s+/','',$phone) }}">{{ $phone }}</a>
                            @if($altPhone) <br><a href="tel:{{ preg_replace('/\s+/','',$altPhone) }}">{{ $altPhone }}</a>@endif
                        </dd></div>
                        <div><dt>Email</dt><dd><a href="mailto:{{ $email }}">{{ $email }}</a></dd></div>
                        <div><dt>Office hours</dt><dd>Monday — Friday · 07:30 – 16:30</dd></div>
                    </dl>
                </div>

                <form class="contact-form" method="post" action="#" onsubmit="return false;" aria-label="Contact form">
                    <div class="form-row">
                        <div class="field">
                            <label for="cname">Full name</label>
                            <input type="text" id="cname" name="name" required autocomplete="name">
                        </div>
                        <div class="field">
                            <label for="cphone">Phone</label>
                            <input type="tel" id="cphone" name="phone" autocomplete="tel">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="field">
                            <label for="cemail">Email</label>
                            <input type="email" id="cemail" name="email" required autocomplete="email">
                        </div>
                        <div class="field">
                            <label for="cinterest">Enquiry type</label>
                            <select id="cinterest" name="interest">
                                <option>Admissions enquiry</option>
                                <option>Campus tour</option>
                                <option>Fees &amp; financial info</option>
                                <option>Employment</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="field">
                            <label for="cmessage">Message</label>
                            <textarea id="cmessage" name="message" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="contact-submit">
                        <small>We respect your privacy. Your details are not shared.</small>
                        <button type="submit" class="btn">Send message →</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer class="site">
        <div class="container">
            <div class="grid">
                <div>
                    <a class="brand" href="/" style="color:#fff;">
                        <img src="{{ $logoPath }}" alt="" style="width:40px;height:40px;border-radius:50%;background:#fff;padding:2px;" onerror="this.style.display='none'">
                        <span class="brand-text">
                            <span class="brand-name" style="color:#fff; font-family:var(--serif);">{{ $shortName }}</span>
                            <span class="brand-tag" style="color:rgba(255,255,255,.55);">{{ $motto }}</span>
                        </span>
                    </a>
                    <p class="blurb">A Christ-centred school in Zambia. Faith, family and future — nurtured term by term.</p>
                    <div class="social-row">
                        <a href="{{ $facebook }}"  aria-label="Facebook"  target="_blank" rel="noopener"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9V14.9H8V12h2.4V9.8c0-2.4 1.4-3.7 3.6-3.7c1 0 2.1.2 2.1.2v2.3h-1.2c-1.2 0-1.5.7-1.5 1.5V12h2.6l-.4 2.9h-2.2v6.9A10 10 0 0 0 22 12Z"/></svg></a>
                        <a href="{{ $instagram }}" aria-label="Instagram" target="_blank" rel="noopener"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                        <a href="{{ $youtube }}"   aria-label="YouTube"   target="_blank" rel="noopener"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1C24 15.9 24 12 24 12s0-3.9-.5-5.8zM9.6 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg></a>
                    </div>
                </div>

                <div>
                    <h5>Visit</h5>
                    <ul>
                        <li><a href="#welcome">Welcome</a></li>
                        <li><a href="#programs">Programs</a></li>
                        <li><a href="#learning">Approach</a></li>
                        <li><a href="{{ route('gallery') }}">Gallery</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h5>Portal</h5>
                    <ul>
                        <li><a href="{{ $parentApp }}"  target="_blank" rel="noopener">Parent app</a></li>
                        <li><a href="{{ $teacherApp }}" target="_blank" rel="noopener">Teacher app</a></li>
                        <li><a href="{{ url('/admin/login') }}">Staff sign-in</a></li>
                        <li><a href="{{ url('/pay') }}">Pay fees</a></li>
                    </ul>
                </div>

                <div>
                    <h5>Newsletter</h5>
                    <p style="margin-bottom:1rem;">School news in your inbox, once a term.</p>
                    <form action="#" onsubmit="return false;" style="display:flex; gap:.5rem;">
                        <input type="email" placeholder="Email address" style="flex:1; padding:.6rem .8rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.15); color:#fff;">
                        <button type="submit" style="padding:.6rem 1rem; background:#fff; color:var(--ink); font-size:.85rem;">Subscribe</button>
                    </form>
                </div>
            </div>

            @if($accreditations->isNotEmpty())
            <div class="recognised">
                <span>{{ $accreditationHeading }}</span>
                @foreach($accreditations as $a)
                    <img src="{{ $a['logo'] }}" alt="{{ $a['label'] }}">
                @endforeach
            </div>
            @endif

            <div class="legal">
                <p>© {{ $year }} {{ $schoolName }}. All rights reserved.</p>
                <div style="display:flex; gap:1.25rem;">
                    <a href="#">Privacy</a>
                    <a href="#">Terms</a>
                    <a href="#">Code of Conduct</a>
                </div>
            </div>
        </div>
    </footer>

    <button class="to-top" id="to-top" aria-label="Back to top">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><polyline points="18 15 12 9 6 15"/></svg>
    </button>

    <script>
        // Sticky nav shadow + back-to-top
        (function () {
            const nav = document.getElementById('nav');
            const top = document.getElementById('to-top');
            const onScroll = () => {
                const y = window.scrollY;
                nav.classList.toggle('scrolled', y > 8);
                top.classList.toggle('show', y > 700);
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            top.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
            onScroll();
        })();

        // Mobile menu
        (function () {
            const toggle = document.getElementById('menu-toggle');
            const close  = document.getElementById('menu-close');
            const menu   = document.getElementById('mobile-menu');
            const open  = () => { menu.classList.add('open');    document.body.classList.add('menu-open');    toggle.setAttribute('aria-expanded','true');  };
            const shut  = () => { menu.classList.remove('open'); document.body.classList.remove('menu-open'); toggle.setAttribute('aria-expanded','false'); };
            toggle?.addEventListener('click', open);
            close ?.addEventListener('click', shut);
            menu  ?.querySelectorAll('a').forEach(a => a.addEventListener('click', shut));
            document.addEventListener('keydown', e => { if (e.key === 'Escape') shut(); });
        })();

        // Voice quote rotator (no auto-advance — user-controlled)
        (function () {
            const voices = document.querySelectorAll('[data-voice-index]');
            const dots   = document.querySelectorAll('[data-voice-dot]');
            if (voices.length < 2) return;
            let active = 0;
            const show = (i) => {
                voices.forEach((v, idx) => v.hidden = idx !== i);
                dots.forEach((d, idx) => d.classList.toggle('active', idx === i));
                active = i;
            };
            dots.forEach((d, i) => d.addEventListener('click', () => show(i)));
            // Soft auto-advance every 8 seconds
            setInterval(() => show((active + 1) % voices.length), 8000);
        })();
    </script>
</body>
</html>
