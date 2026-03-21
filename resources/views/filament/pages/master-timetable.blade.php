<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Controls --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <select wire:model.live="selectedAcademicYear" class="fi-select-input rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    @foreach($this->getAcademicYears() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <button
                wire:click="downloadPdf"
                class="fi-btn fi-btn-size-md flex items-center gap-1.5 rounded-lg bg-success-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-success-500"
            >
                <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                Download PDF
            </button>
        </div>

        @php
            $allPeriods = $this->getPeriods();
            $classSections = $this->getClassSections();
            $days = $this->getDays();
        @endphp

        @if($allPeriods->isEmpty())
            <div class="rounded-xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No periods configured</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Run <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">php artisan timetable:seed-periods</code> to create periods.
                </p>
            </div>
        @elseif($classSections->isEmpty())
            <div class="rounded-xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <x-heroicon-o-academic-cap class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No class sections found</h3>
            </div>
        @else
            {{-- One timetable per class --}}
            @foreach($classSections as $cs)
                @php
                    $timetableData = $this->getTimetableForClass($cs->id);
                @endphp
                <div class="rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
                    {{-- Class header --}}
                    <div class="bg-primary-600 dark:bg-primary-700 px-4 py-2.5">
                        <h3 class="text-sm font-bold text-white">
                            {{ $cs->grade?->name ?? 'Unknown' }}{{ $cs->name ? ' ' . $cs->name : '' }}
                        </h3>
                    </div>

                    {{-- Grid: Rows = Days, Columns = Periods --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-800 text-white dark:bg-gray-950">
                                    <th class="sticky left-0 z-10 bg-gray-800 dark:bg-gray-950 px-2 py-2 text-left font-semibold whitespace-nowrap w-24">
                                        Day
                                    </th>
                                    @foreach($allPeriods as $period)
                                        <th class="px-1 py-2 text-center font-semibold whitespace-nowrap min-w-[75px]
                                            {{ $period->isBreak() ? 'bg-gray-600 dark:bg-gray-700 min-w-[40px]' : '' }}">
                                            <div>{{ $period->short_name ?? $period->name }}</div>
                                            <div class="text-[10px] font-normal opacity-75">
                                                {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $day)
                                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-blue-50/50 dark:hover:bg-blue-900/10">
                                        <td class="sticky left-0 z-10 bg-white dark:bg-gray-900 px-2 py-1.5 font-semibold text-gray-900 dark:text-white whitespace-nowrap border-r border-gray-200 dark:border-gray-700">
                                            {{ $day }}
                                        </td>
                                        @foreach($allPeriods as $period)
                                            @if($period->isBreak())
                                                <td class="bg-gray-100 dark:bg-gray-800 px-1 py-1.5 text-center text-[10px] text-gray-400 dark:text-gray-500 italic">
                                                    @if($loop->parent->first)
                                                        {{ $period->short_name }}
                                                    @endif
                                                </td>
                                            @else
                                                @php
                                                    $entry = $timetableData[$period->id]['days'][$day] ?? null;
                                                @endphp
                                                <td class="px-1 py-1 text-center border-r border-gray-100 dark:border-gray-800
                                                    {{ $entry ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-900/50' }}">
                                                    @if($entry)
                                                        <div class="font-semibold text-gray-900 dark:text-white leading-tight truncate" title="{{ $entry->subject?->name }}">
                                                            {{ $entry->subject?->code ?? Str::limit($entry->subject?->name, 10) ?? '-' }}
                                                        </div>
                                                        <div class="text-[10px] text-gray-500 dark:text-gray-400 truncate" title="{{ $entry->teacher?->name }}">
                                                            {{ Str::limit($entry->teacher?->name, 12) ?? '' }}
                                                        </div>
                                                    @else
                                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            {{-- Summary --}}
            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200"></span>
                    Break
                </span>
                <span>Total Classes: {{ $classSections->count() }}</span>
                <span>Lesson Periods/Day: {{ $allPeriods->filter(fn($p) => $p->isLesson())->count() }}</span>
            </div>
        @endif
    </div>
</x-filament-panels::page>
