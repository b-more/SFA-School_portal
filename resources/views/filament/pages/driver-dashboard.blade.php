<x-filament-panels::page>
    @php
        $routes = $this->driverRoutes();
        $term = $this->currentTerm();
        $rosterByRoute = $this->rosterByRoute();
        $total = $this->totalCount();
        $logs = $this->todaysLogs();
        [$markedCount, $progressTotal] = $this->boardingProgress();
        $stats = $this->boardingStats();
        $unmarkedIds = array_flip($this->unmarkedStudentIds());

        $tripLabel = $this->currentTrip === 'to_school' ? 'To School' : 'From School';
        $tripLabelShort = $this->currentTrip === 'to_school' ? 'Pickup from home' : 'Drop-off to home';
        $tripIcon = $this->currentTrip === 'to_school' ? '🌅' : '🌇';

        $sectionStyles = [
            'ECE' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
            'PRI' => ['bg' => '#dbeafe', 'fg' => '#1e40af'],
            'SEC' => ['bg' => '#dcfce7', 'fg' => '#166534'],
        ];

        $cleanPhone = fn ($p) => $p ? preg_replace('/[^0-9+]/', '', $p) : null;
    @endphp

    <style>
        /* Driver dashboard — mobile-first, big touch targets. */
        .dd-hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
            color: #fff;
            border-radius: 14px;
            padding: 22px 22px 18px;
            box-shadow: 0 10px 25px -10px rgba(30,58,95,.45);
        }
        .dd-hero .salutation { font-size: 12px; letter-spacing: .14em; text-transform: uppercase; opacity: .8; }
        .dd-hero .day { font-size: 15px; opacity: .9; margin-top: 4px; }
        .dd-hero .total { font-size: 56px; font-weight: 800; line-height: 1; margin-top: 2px; }
        .dd-hero .total-label { font-size: 12px; letter-spacing: .14em; text-transform: uppercase; opacity: .8; }
        .dd-hero-row { display: flex; flex-direction: column; gap: 16px; }
        @media (min-width: 640px) {
            .dd-hero-row { flex-direction: row; align-items: flex-end; justify-content: space-between; }
        }

        .dd-print-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; color: #1e3a5f; font-weight: 600;
            padding: 10px 16px; border-radius: 9999px; text-decoration: none;
            box-shadow: 0 4px 10px -4px rgba(0,0,0,.2);
            transition: transform .1s;
            font-size: 14px;
        }
        .dd-print-btn:hover { transform: translateY(-1px); color: #1e3a5f; text-decoration: none; }

        .dd-search {
            width: 100%; padding: 12px 16px 12px 42px; border-radius: 12px;
            border: 1px solid #e5e7eb; background: #fff;
            font-size: 15px; outline: none;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .dark .dd-search { background: #1f2937; border-color: #374151; color: #f3f4f6; }
        .dd-search:focus { border-color: #1e3a5f; box-shadow: 0 0 0 3px rgba(30,58,95,.15); }
        .dd-search-wrap { position: relative; }
        .dd-search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }

        .dd-route-hdr {
            display: flex; align-items: center; justify-content: space-between;
            margin: 24px 0 10px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }
        .dd-route-name { font-weight: 700; font-size: 15px; color: #1e3a5f; display: flex; align-items: center; gap: 8px; }
        .dark .dd-route-name { color: #93c5fd; }
        .dd-route-count {
            background: #1e3a5f; color: #fff; font-size: 12px; font-weight: 700;
            padding: 3px 10px; border-radius: 9999px;
        }

        .dd-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 10px;
            display: grid;
            gap: 10px;
            grid-template-columns: 56px 1fr;
            align-items: start;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .dark .dd-card { background: #1f2937; border-color: #374151; }

        .dd-avatar {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, #1e3a5f, #2c5282);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 18px; letter-spacing: .04em;
            flex-shrink: 0;
        }

        .dd-body { min-width: 0; }
        .dd-name-row { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .dd-name { font-size: 16px; font-weight: 700; color: #111827; }
        .dark .dd-name { color: #f3f4f6; }
        .dd-section-chip {
            display: inline-block; font-size: 11px; font-weight: 700;
            padding: 2px 8px; border-radius: 9999px;
            letter-spacing: .03em;
        }
        .dd-grade-text { font-size: 12px; color: #6b7280; }
        .dark .dd-grade-text { color: #9ca3af; }

        /* Expiry chip — color encodes urgency */
        .dd-expiry {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600;
            padding: 2px 8px; border-radius: 9999px;
            letter-spacing: .02em;
        }
        .dd-expiry-ok      { background: #f3f4f6; color: #6b7280; }
        .dd-expiry-heads   { background: #dbeafe; color: #1e40af; }
        .dd-expiry-warn    { background: #fef3c7; color: #92400e; }
        .dd-expiry-urgent  { background: #fee2e2; color: #991b1b; }
        .dd-expiry-expired { background: #1f2937; color: #fff; }
        .dark .dd-expiry-ok { background: #374151; color: #d1d5db; }

        .dd-info-row {
            display: flex; align-items: flex-start; gap: 6px;
            font-size: 13px; color: #4b5563; margin-top: 4px;
            line-height: 1.4;
        }
        .dark .dd-info-row { color: #d1d5db; }
        .dd-info-row .ico { color: #9ca3af; flex-shrink: 0; margin-top: 2px; }

        .dd-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .dd-call {
            display: inline-flex; align-items: center; gap: 7px;
            background: #16a34a; color: #fff; font-weight: 700;
            padding: 9px 14px; border-radius: 10px; text-decoration: none;
            font-size: 14px; min-height: 40px;
            box-shadow: 0 2px 6px -2px rgba(22,163,74,.4);
        }
        .dd-call:hover { background: #15803d; color: #fff; text-decoration: none; }
        .dd-call-alt {
            background: #f3f4f6; color: #1f2937;
            box-shadow: none; font-weight: 600;
        }
        .dark .dd-call-alt { background: #374151; color: #f3f4f6; }
        .dd-call-alt:hover { background: #e5e7eb; color: #111827; }
        .dd-no-phone {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 14px; border-radius: 10px;
            background: #fef2f2; color: #991b1b; font-weight: 600; font-size: 13px;
        }
        .dark .dd-no-phone { background: #7f1d1d; color: #fecaca; }

        .dd-empty {
            text-align: center; padding: 40px 20px;
            background: #f9fafb; border: 2px dashed #e5e7eb; border-radius: 14px;
            color: #6b7280;
        }
        .dark .dd-empty { background: #111827; border-color: #374151; color: #9ca3af; }
        .dd-empty-icon { font-size: 48px; opacity: .35; margin-bottom: 8px; }

        @media (min-width: 768px) {
            .dd-card { grid-template-columns: 64px 1fr auto; align-items: center; }
            .dd-actions { margin-top: 0; flex-direction: column; align-items: flex-end; }
            .dd-call { padding: 10px 18px; font-size: 15px; }
        }

        /* Boarding segmented control */
        .dd-board {
            grid-column: 1 / -1;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #e5e7eb;
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
            justify-content: space-between;
        }
        .dark .dd-board { border-top-color: #374151; }
        .dd-board-label { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
        .dd-board-group {
            display: inline-flex; border-radius: 10px; overflow: hidden;
            border: 1px solid #e5e7eb; background: #fff;
        }
        .dark .dd-board-group { background: #1f2937; border-color: #374151; }
        .dd-board-btn {
            padding: 8px 14px; font-size: 13px; font-weight: 600;
            background: transparent; color: #6b7280;
            border: none; cursor: pointer; min-height: 40px;
            display: inline-flex; align-items: center; gap: 6px;
            border-right: 1px solid #e5e7eb;
            transition: background .12s, color .12s;
        }
        .dark .dd-board-btn { color: #9ca3af; border-right-color: #374151; }
        .dd-board-btn:last-child { border-right: none; }
        .dd-board-btn:hover { background: #f9fafb; color: #1f2937; }
        .dark .dd-board-btn:hover { background: #111827; color: #f3f4f6; }
        .dd-board-btn.is-boarded { background: #16a34a; color: #fff; }
        .dd-board-btn.is-absent { background: #6b7280; color: #fff; }
        .dd-board-btn.is-noshow { background: #dc2626; color: #fff; }
        .dd-board-time { font-size: 11px; color: #9ca3af; }

        /* Progress bar in hero */
        .dd-progress {
            margin-top: 10px;
            background: rgba(255,255,255,.15);
            border-radius: 9999px; height: 8px; overflow: hidden;
        }
        .dd-progress-fill {
            height: 100%; background: #10b981;
            transition: width .3s ease;
        }
        .dd-progress-label {
            display: flex; justify-content: space-between;
            font-size: 11px; opacity: .85; margin-top: 6px;
            letter-spacing: .06em; text-transform: uppercase;
        }

        /* Trip selector */
        .dd-trip-tabs {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 16px;
            background: #fff; padding: 6px; border-radius: 14px;
            border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .dark .dd-trip-tabs { background: #1f2937; border-color: #374151; }
        .dd-trip-tab {
            display: flex; flex-direction: column; align-items: center; gap: 2px;
            padding: 12px 8px; border-radius: 10px; cursor: pointer;
            font-weight: 600; color: #6b7280; background: transparent;
            border: none; transition: background .12s;
            min-height: 60px; justify-content: center;
        }
        .dd-trip-tab:hover { background: #f9fafb; }
        .dark .dd-trip-tab { color: #9ca3af; }
        .dark .dd-trip-tab:hover { background: #111827; }
        .dd-trip-tab.is-active {
            background: #1e3a5f; color: #fff;
            box-shadow: 0 4px 12px -4px rgba(30,58,95,.4);
        }
        .dd-trip-tab.is-active:hover { background: #142638; }
        .dd-trip-tab .lbl { font-size: 14px; }
        .dd-trip-tab .sub { font-size: 11px; opacity: .8; font-weight: 500; }
        .dd-trip-tab .ico { font-size: 18px; }

        /* Stats bar */
        .dd-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;
            margin-top: 12px;
        }
        .dd-stat {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 12px 8px; text-align: center;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .dark .dd-stat { background: #1f2937; border-color: #374151; }
        .dd-stat .num { font-size: 24px; font-weight: 800; line-height: 1; }
        .dd-stat .lbl { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; margin-top: 4px; color: #6b7280; font-weight: 600; }
        .dark .dd-stat .lbl { color: #9ca3af; }
        .dd-stat-boarded .num { color: #16a34a; }
        .dd-stat-absent  .num { color: #6b7280; }
        .dd-stat-noshow  .num { color: #dc2626; }
        .dd-stat-unmarked .num { color: #d97706; }
        .dd-stat-unmarked.has-missing { border-color: #f59e0b; box-shadow: 0 0 0 2px rgba(245,158,11,.18); }

        /* Filter toggle */
        .dd-filter-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 12px; gap: 8px; flex-wrap: wrap;
        }
        .dd-toggle-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 14px; border-radius: 10px;
            background: #fff; color: #1f2937; font-weight: 600; font-size: 13px;
            border: 1px solid #e5e7eb; cursor: pointer;
            transition: all .12s;
        }
        .dark .dd-toggle-btn { background: #1f2937; color: #f3f4f6; border-color: #374151; }
        .dd-toggle-btn:hover { background: #f9fafb; }
        .dark .dd-toggle-btn:hover { background: #111827; }
        .dd-toggle-btn.is-on {
            background: #f59e0b; color: #fff; border-color: #f59e0b;
        }
        .dd-toggle-btn.is-on:hover { background: #d97706; }

        /* Unmarked card visual */
        .dd-card.is-unmarked {
            border-left: 4px solid #f59e0b;
            background: #fffbeb;
        }
        .dark .dd-card.is-unmarked { background: rgba(245,158,11,.08); }
        .dd-card.is-unmarked .dd-name { color: #92400e; }
        .dark .dd-card.is-unmarked .dd-name { color: #fbbf24; }

        @keyframes dd-pulse {
            0%, 100% { box-shadow: 0 1px 2px rgba(0,0,0,.04); }
            50% { box-shadow: 0 0 0 4px rgba(245,158,11,.18); }
        }
        .dd-card.is-unmarked.is-pulse {
            animation: dd-pulse 2s ease-in-out infinite;
        }

        .dd-missing-tag {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 700;
            padding: 2px 8px; border-radius: 9999px;
            background: #f59e0b; color: #fff;
            letter-spacing: .04em;
        }
    </style>

    {{-- HERO --}}
    <div class="dd-hero">
        <div class="dd-hero-row">
            <div>
                <div class="salutation">Bus driver</div>
                <div style="font-size: 26px; font-weight: 700; margin-top: 6px;">{{ auth()->user()->name }}</div>
                <div class="day">
                    {{ now()->format('l, j F Y') }}
                    @if ($term)
                        · {{ $term->name }}
                    @endif
                </div>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                <div style="text-align: right;">
                    <div class="total">{{ $total }}</div>
                    <div class="total-label">{{ \Illuminate\Support\Str::plural('student', $total) }} today</div>
                </div>
                @if ($total > 0)
                    <button wire:click="downloadPdf" class="dd-print-btn">
                        <x-heroicon-o-printer class="w-4 h-4" />
                        Print Roster
                    </button>
                @endif
            </div>
        </div>

        @if ($total > 0)
            <div class="dd-progress">
                <div class="dd-progress-fill" style="width: {{ $progressTotal > 0 ? round(($markedCount / $progressTotal) * 100) : 0 }}%"></div>
            </div>
            <div class="dd-progress-label">
                <span>{{ $tripIcon }} {{ $tripLabel }} marked</span>
                <span>{{ $markedCount }} / {{ $progressTotal }}</span>
            </div>
        @endif
    </div>

    {{-- TRIP SELECTOR --}}
    @if ($total > 0)
        <div class="dd-trip-tabs">
            <button type="button"
                    class="dd-trip-tab {{ $this->currentTrip === 'to_school' ? 'is-active' : '' }}"
                    wire:click="setTrip('to_school')">
                <span class="ico">🌅</span>
                <span class="lbl">To School</span>
                <span class="sub">Pickup from home</span>
            </button>
            <button type="button"
                    class="dd-trip-tab {{ $this->currentTrip === 'from_school' ? 'is-active' : '' }}"
                    wire:click="setTrip('from_school')">
                <span class="ico">🌇</span>
                <span class="lbl">From School</span>
                <span class="sub">Drop-off to home</span>
            </button>
        </div>

        {{-- STATS BAR for current trip --}}
        <div class="dd-stats">
            <div class="dd-stat dd-stat-boarded">
                <div class="num">{{ $stats['boarded'] }}</div>
                <div class="lbl">✓ Boarded</div>
            </div>
            <div class="dd-stat dd-stat-absent">
                <div class="num">{{ $stats['absent'] }}</div>
                <div class="lbl">⊖ Absent</div>
            </div>
            <div class="dd-stat dd-stat-noshow">
                <div class="num">{{ $stats['no_show'] }}</div>
                <div class="lbl">✗ No-show</div>
            </div>
            <div class="dd-stat dd-stat-unmarked {{ $stats['unmarked'] > 0 ? 'has-missing' : '' }}">
                <div class="num">{{ $stats['unmarked'] }}</div>
                <div class="lbl">⏰ Unmarked</div>
            </div>
        </div>

        {{-- FILTER TOGGLE --}}
        <div class="dd-filter-row">
            <div style="font-size: 12px; color: #6b7280;">
                {{ $tripIcon }} Marking <b style="color: #1f2937;">{{ $tripLabel }}</b> trip
            </div>
            <button type="button"
                    class="dd-toggle-btn {{ $this->showUnmarkedOnly ? 'is-on' : '' }}"
                    wire:click="toggleUnmarkedOnly">
                <x-heroicon-o-funnel class="w-4 h-4" />
                {{ $this->showUnmarkedOnly ? 'Showing only missing' : 'Show only missing' }}
                @if ($stats['unmarked'] > 0 && ! $this->showUnmarkedOnly)
                    <span style="background: #f59e0b; color: white; padding: 1px 7px; border-radius: 9999px; font-size: 11px;">{{ $stats['unmarked'] }}</span>
                @endif
            </button>
        </div>
    @endif

    {{-- No routes assigned --}}
    @if ($routes->isEmpty())
        <div class="dd-empty" style="margin-top: 16px;">
            <div class="dd-empty-icon">🚌</div>
            <div style="font-size: 16px; font-weight: 600; color: #111827;">No routes assigned</div>
            <div style="margin-top: 6px;">The school office hasn't assigned you to a bus route yet.</div>
        </div>
    @else
        {{-- Search --}}
        @if ($total > 0)
            <div class="dd-search-wrap" style="margin-top: 16px;">
                <x-heroicon-o-magnifying-glass class="dd-search-icon w-5 h-5" />
                <input
                    type="search"
                    class="dd-search"
                    placeholder="Search by student or parent name..."
                    wire:model.live.debounce.250ms="search"
                />
            </div>
        @endif

        {{-- Roster grouped by route --}}
        @forelse ($routes as $route)
            @php
                $rows = $rosterByRoute->get($route->id, collect());
                if ($this->showUnmarkedOnly) {
                    $rows = $rows->filter(fn ($p) => isset($unmarkedIds[$p->student_id]));
                }
                $routeUnmarked = $rows->filter(fn ($p) => isset($unmarkedIds[$p->student_id]))->count();
            @endphp
            <div class="dd-route-hdr">
                <div class="dd-route-name">
                    <x-heroicon-o-map-pin class="w-4 h-4" />
                    {{ $route->route_name }}
                </div>
                <div style="display: flex; gap: 6px; align-items: center;">
                    @if ($routeUnmarked > 0 && ! $this->showUnmarkedOnly)
                        <span class="dd-missing-tag">{{ $routeUnmarked }} missing</span>
                    @endif
                    <span class="dd-route-count">{{ $rows->count() }}</span>
                </div>
            </div>

            @forelse ($rows as $payment)
                @php
                    $student = $payment->student;
                    $isUnmarked = isset($unmarkedIds[$student?->id]);
                    $grade = $student?->grade;
                    $section = $grade?->schoolSection?->code ?? 'PRI';
                    $style = $sectionStyles[$section] ?? $sectionStyles['PRI'];
                    $parent = $student?->parentGuardian;
                    $phone = $cleanPhone($parent?->phone);
                    $altPhone = $cleanPhone($parent?->alternate_phone);
                    $initials = collect(explode(' ', (string) $student?->name))
                        ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
                @endphp
                @php
                    $expiresAt = $payment->expires_at;
                    $expiryClass = 'dd-expiry-ok';
                    $expiryText = null;
                    $expiryIcon = '⏱';
                    if ($expiresAt) {
                        $daysLeft = (int) round(now()->startOfDay()->diffInDays($expiresAt->startOfDay(), false));
                        if ($daysLeft < 0) {
                            $expiryClass = 'dd-expiry-expired';
                            $expiryText = 'Expired ' . $expiresAt->format('d M');
                            $expiryIcon = '⚠';
                        } elseif ($daysLeft === 0) {
                            $expiryClass = 'dd-expiry-urgent';
                            $expiryText = 'Expires today';
                            $expiryIcon = '⚠';
                        } elseif ($daysLeft <= 3) {
                            $expiryClass = 'dd-expiry-urgent';
                            $expiryText = "Expires in {$daysLeft} " . \Illuminate\Support\Str::plural('day', $daysLeft);
                            $expiryIcon = '⚠';
                        } elseif ($daysLeft <= 7) {
                            $expiryClass = 'dd-expiry-warn';
                            $expiryText = "Expires in {$daysLeft} days";
                        } elseif ($daysLeft <= 14) {
                            $expiryClass = 'dd-expiry-heads';
                            $expiryText = 'Expires ' . $expiresAt->format('d M');
                        } else {
                            $expiryText = 'Valid to ' . $expiresAt->format('d M');
                        }
                    }
                @endphp
                <div class="dd-card {{ $isUnmarked ? 'is-unmarked is-pulse' : '' }}">
                    <div class="dd-avatar">{{ $initials ?: '?' }}</div>

                    <div class="dd-body">
                        <div class="dd-name-row">
                            <span class="dd-name">{{ $student?->name ?? 'Unknown student' }}</span>
                            <span class="dd-section-chip" style="background: {{ $style['bg'] }}; color: {{ $style['fg'] }};">
                                {{ $section }}
                            </span>
                            <span class="dd-grade-text">{{ $grade?->name ?? '' }}</span>
                            @if ($expiryText)
                                <span class="dd-expiry {{ $expiryClass }}">{{ $expiryIcon }} {{ $expiryText }}</span>
                            @endif
                        </div>

                        @if ($student?->address)
                            <div class="dd-info-row">
                                <x-heroicon-o-map-pin class="ico w-4 h-4" />
                                <span>{{ $student->address }}</span>
                            </div>
                        @endif

                        @if ($parent)
                            <div class="dd-info-row">
                                <x-heroicon-o-user class="ico w-4 h-4" />
                                <span>{{ $parent->name ?? 'Guardian' }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="dd-actions">
                        @if ($phone)
                            <a class="dd-call" href="tel:{{ $phone }}">
                                <x-heroicon-s-phone class="w-4 h-4" />
                                Call {{ $parent?->phone }}
                            </a>
                        @endif

                        @if ($altPhone && $altPhone !== $phone)
                            <a class="dd-call dd-call-alt" href="tel:{{ $altPhone }}">
                                <x-heroicon-o-phone class="w-4 h-4" />
                                Alt {{ $parent?->alternate_phone }}
                            </a>
                        @endif

                        @if (! $phone && ! $altPhone)
                            <span class="dd-no-phone">
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                                No phone
                            </span>
                        @endif
                    </div>

                    @php $log = $logs->get($student?->id); @endphp
                    <div class="dd-board">
                        <div>
                            <div class="dd-board-label">{{ $tripLabel }}</div>
                            @if ($log)
                                <div class="dd-board-time">Marked {{ $log->updated_at?->format('H:i') }}</div>
                            @else
                                <div class="dd-board-time" style="color: #f59e0b; font-weight: 600;">⏰ Not marked yet</div>
                            @endif
                        </div>
                        <div class="dd-board-group" role="group" aria-label="Boarding status">
                            <button type="button"
                                    class="dd-board-btn {{ $log?->status === 'boarded' ? 'is-boarded' : '' }}"
                                    wire:click="markBoarding({{ $student?->id }}, {{ $route->id }}, 'boarded')"
                                    wire:loading.attr="disabled"
                                    wire:target="markBoarding({{ $student?->id }}, {{ $route->id }}, 'boarded')">
                                <x-heroicon-s-check class="w-4 h-4" />
                                Boarded
                            </button>
                            <button type="button"
                                    class="dd-board-btn {{ $log?->status === 'absent' ? 'is-absent' : '' }}"
                                    wire:click="markBoarding({{ $student?->id }}, {{ $route->id }}, 'absent')"
                                    wire:loading.attr="disabled"
                                    wire:target="markBoarding({{ $student?->id }}, {{ $route->id }}, 'absent')">
                                <x-heroicon-o-minus-circle class="w-4 h-4" />
                                Absent
                            </button>
                            <button type="button"
                                    class="dd-board-btn {{ $log?->status === 'no_show' ? 'is-noshow' : '' }}"
                                    wire:click="markBoarding({{ $student?->id }}, {{ $route->id }}, 'no_show')"
                                    wire:loading.attr="disabled"
                                    wire:target="markBoarding({{ $student?->id }}, {{ $route->id }}, 'no_show')">
                                <x-heroicon-o-x-circle class="w-4 h-4" />
                                No-show
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="dd-empty">
                    <div class="dd-empty-icon">📋</div>
                    <div style="font-size: 14px; font-weight: 600;">No paid-up students on this route today.</div>
                </div>
            @endforelse
        @empty
        @endforelse

        @if ($total > 0 && collect($rosterByRoute)->flatten()->isEmpty())
            <div class="dd-empty" style="margin-top: 12px;">
                <div class="dd-empty-icon">🔍</div>
                <div style="font-size: 14px;">No students match "{{ $search }}"</div>
                <button wire:click="clearSearch"
                        style="margin-top: 10px; padding: 6px 12px; border-radius: 8px; background: #1e3a5f; color: white; font-size: 13px;">
                    Clear search
                </button>
            </div>
        @endif
    @endif
</x-filament-panels::page>
