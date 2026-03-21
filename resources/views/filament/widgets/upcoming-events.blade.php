<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Stats Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="flex items-center gap-3 rounded-xl bg-warning-50 dark:bg-warning-950/30 p-4 ring-1 ring-warning-200 dark:ring-warning-800">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-100 dark:bg-warning-900">
                    <x-heroicon-o-calendar-days class="h-6 w-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $this->getTotalUpcoming() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Upcoming Events</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-success-50 dark:bg-success-950/30 p-4 ring-1 ring-success-200 dark:ring-success-800">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success-100 dark:bg-success-900">
                    <x-heroicon-o-play-circle class="h-6 w-6 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $this->getTotalOngoing() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Ongoing Now</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-primary-50 dark:bg-primary-950/30 p-4 ring-1 ring-primary-200 dark:ring-primary-800">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900">
                    <x-heroicon-o-calendar class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $this->getTotalThisMonth() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">This Month</p>
                </div>
            </div>
        </div>

        {{-- Upcoming Events Cards --}}
        @php $events = $this->getEvents(); @endphp

        @if(count($events) > 0)
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-sparkles class="h-5 w-5 text-warning-500" />
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Coming Up Next</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($events as $event)
                    <div class="group relative flex gap-4 rounded-xl p-4 transition-all duration-200 hover:shadow-md
                        {{ $event['is_today'] ? 'bg-warning-50 dark:bg-warning-950/30 ring-2 ring-warning-400' : 'bg-gray-50 dark:bg-gray-800/50 ring-1 ring-gray-200 dark:ring-gray-700' }}
                        hover:ring-primary-300 dark:hover:ring-primary-700">

                        {{-- Date Box --}}
                        <div class="flex flex-col items-center justify-center min-w-[56px] rounded-lg py-2 px-1
                            {{ $event['is_today'] ? 'bg-warning-500 text-white' : ($event['status'] === 'ongoing' ? 'bg-success-500 text-white' : 'bg-primary-600 text-white') }}">
                            <span class="text-[10px] font-semibold uppercase leading-none">{{ $event['month'] }}</span>
                            <span class="text-2xl font-black leading-none mt-0.5">{{ $event['day'] }}</span>
                            <span class="text-[10px] font-medium leading-none mt-0.5">{{ $event['weekday'] }}</span>
                        </div>

                        {{-- Event Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $event['title'] }}</h4>
                                @if($event['is_today'])
                                    <span class="shrink-0 inline-flex items-center rounded-full bg-warning-100 px-2 py-0.5 text-[10px] font-bold text-warning-700 dark:bg-warning-900 dark:text-warning-300 uppercase">Today</span>
                                @elseif($event['status'] === 'ongoing')
                                    <span class="shrink-0 inline-flex items-center rounded-full bg-success-100 px-2 py-0.5 text-[10px] font-bold text-success-700 dark:bg-success-900 dark:text-success-300 uppercase">Live</span>
                                @elseif($event['days_away'] <= 7 && $event['days_away'] > 0)
                                    <span class="shrink-0 inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $event['days_away'] }}d
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1.5 space-y-1">
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <x-heroicon-m-clock class="h-3.5 w-3.5 shrink-0" />
                                    <span>{{ $event['time'] }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <x-heroicon-m-map-pin class="h-3.5 w-3.5 shrink-0" />
                                    <span class="truncate">{{ $event['location'] }}</span>
                                </div>
                            </div>

                            <div class="mt-2">
                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide
                                    {{ match($event['category']) {
                                        'academic' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                                        'sports' => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
                                        'cultural' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                                        'religious' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    } }}">
                                    {{ ucfirst($event['category']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-8 text-gray-400 dark:text-gray-500">
                <x-heroicon-o-calendar class="h-12 w-12 mb-2" />
                <p class="text-sm font-medium">No upcoming events scheduled</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
