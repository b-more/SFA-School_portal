@php
    $stats = [
        ['label' => 'Active testimonials', 'value' => $activeTestimonials . ' / ' . $testimonialCount, 'href' => $testimonialsUrl, 'icon' => 'heroicon-o-chat-bubble-left-right'],
        ['label' => 'Published news',      'value' => $publishedNews,                                  'href' => $newsUrl,         'icon' => 'heroicon-o-newspaper'],
        ['label' => 'Upcoming events',     'value' => $upcomingEvents,                                  'href' => $eventsUrl,       'icon' => 'heroicon-o-calendar-days'],
        ['label' => 'Announcement bar',    'value' => $announcementActive ? 'On' : 'Off',               'href' => $manageUrl,       'icon' => 'heroicon-o-megaphone'],
    ];
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-window" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    Public Landing Page
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    What visitors see at <span class="font-mono">{{ url('/') }}</span>
                    @if($lastUpdated)
                        · last updated {{ \Illuminate\Support\Carbon::parse($lastUpdated)->diffForHumans() }}
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-filament::button tag="a" href="{{ $previewUrl }}" target="_blank" rel="noopener" color="gray" icon="heroicon-o-arrow-top-right-on-square" size="sm">
                    Preview
                </x-filament::button>
                <x-filament::button tag="a" href="{{ $manageUrl }}" color="primary" icon="heroicon-o-pencil-square" size="sm">
                    Edit Landing
                </x-filament::button>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($stats as $s)
                <a href="{{ $s['href'] }}"
                   class="group flex items-center gap-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 px-4 py-3 hover:border-primary-500/60 hover:shadow-sm transition">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400 group-hover:bg-primary-100">
                        <x-filament::icon :icon="$s['icon']" class="w-5 h-5" />
                    </span>
                    <div class="min-w-0">
                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $s['label'] }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $s['value'] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
