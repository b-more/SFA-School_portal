<x-filament-panels::page>
    <x-filament-panels::form>{{ $this->form }}</x-filament-panels::form>

    @php
        $d = $data;
        $v = $d['visits'];
        $s = $d['stock'];
        $moneyFmt = fn ($n) => 'K' . number_format((float) $n, 2);
    @endphp

    <div class="mt-4 flex flex-wrap items-center gap-3">
        @if($this->pdfUrl())
            <x-filament::button tag="a" :href="$this->pdfUrl()" target="_blank" color="primary" size="lg" icon="heroicon-o-document-arrow-down">
                Download PDF report
            </x-filament::button>
        @else
            <x-filament::button color="gray" size="lg" icon="heroicon-o-document-arrow-down" :disabled="true">
                Download PDF report
            </x-filament::button>
            <span class="text-xs text-gray-500 italic">
                @if($this->period === 'termly')  Pick a term to enable the download.
                @elseif($this->period === 'custom')  Set both From and To dates to enable the download.
                @endif
            </span>
        @endif
        <x-filament::badge color="gray">
            {{ $d['from']->format('d M Y') }} → {{ $d['to']->format('d M Y') }}
        </x-filament::badge>
    </div>

    {{-- ===== VISIT METRICS ===== --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Visit metrics</x-slot>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center"><div class="text-3xl font-bold text-primary-600">{{ $v['total_visits'] }}</div><div class="text-xs text-gray-500 uppercase">Total visits</div></div>
            <div class="text-center"><div class="text-3xl font-bold text-primary-600">{{ $v['unique_students'] }}</div><div class="text-xs text-gray-500 uppercase">Unique students</div></div>
            <div class="text-center"><div class="text-3xl font-bold text-primary-600">{{ $v['sick_notes'] }}</div><div class="text-xs text-gray-500 uppercase">Sick notes ({{ $v['sick_notes_pct'] }}%)</div></div>
            <div class="text-center"><div class="text-3xl font-bold text-{{ $v['repeat_visitors']->count() ? 'danger' : 'primary' }}-600">{{ $v['repeat_visitors']->count() }}</div><div class="text-xs text-gray-500 uppercase">Repeat visitors (≥3)</div></div>
        </div>
    </x-filament::section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">
        <x-filament::section>
            <x-slot name="heading">Top complaints</x-slot>
            @if($v['by_complaint']->isEmpty())
                <div class="text-sm text-gray-500 italic">No visits in this period.</div>
            @else
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($v['by_complaint']->take(10) as $name => $count)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-2 py-1">{{ $name }}</td>
                                <td class="px-2 py-1 text-right font-semibold">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Visits by grade</x-slot>
            @if($v['by_grade']->isEmpty())
                <div class="text-sm text-gray-500 italic">No data.</div>
            @else
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($v['by_grade'] as $g => $c)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-2 py-1">Grade / Form-eq {{ $g }}</td>
                                <td class="px-2 py-1 text-right font-semibold">{{ $c }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Visits by day</x-slot>
            @if($v['by_day']->isEmpty())
                <div class="text-sm text-gray-500 italic">No data.</div>
            @else
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($v['by_day'] as $day => $c)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-2 py-1">{{ \Carbon\Carbon::parse($day)->format('D d M') }}</td>
                                <td class="px-2 py-1 text-right font-semibold">{{ $c }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Outcomes</x-slot>
            @if($v['by_outcome']->isEmpty())
                <div class="text-sm text-gray-500 italic">No data.</div>
            @else
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($v['by_outcome'] as $o => $c)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-2 py-1">{{ ucwords(str_replace('_',' ',$o)) }}</td>
                                <td class="px-2 py-1 text-right font-semibold">{{ $c }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::section>
    </div>

    @if($v['repeat_visitors']->isNotEmpty())
        <x-filament::section class="mt-4">
            <x-slot name="heading">Repeat visitors (≥3 visits — welfare flag)</x-slot>
            <table class="w-full text-sm">
                <tbody>
                    @foreach($v['repeat_visitors'] as $rv)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-2 py-1">{{ $rv->student_name }}</td>
                            <td class="px-2 py-1 text-right font-semibold text-danger-600">{{ $rv->c }} visits</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>
    @endif

    {{-- ===== STOCK METRICS ===== --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Stock — spend & movement</x-slot>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
            <div class="text-center"><div class="text-2xl font-bold text-primary-600">{{ $moneyFmt($s['total_spend']) }}</div><div class="text-xs text-gray-500 uppercase">Total spend</div></div>
            <div class="text-center"><div class="text-2xl font-bold text-primary-600">{{ $s['usage_by_item']->sum('qty') }}</div><div class="text-xs text-gray-500 uppercase">Items dispensed</div></div>
            <div class="text-center"><div class="text-2xl font-bold text-{{ $s['low_stock']->count() ? 'danger' : 'success' }}-600">{{ $s['low_stock']->count() }}</div><div class="text-xs text-gray-500 uppercase">Low-stock items</div></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="text-left px-2 py-2">Item</th>
                        <th class="text-right px-2 py-2">Opening</th>
                        <th class="text-right px-2 py-2">In</th>
                        <th class="text-right px-2 py-2">Out</th>
                        <th class="text-right px-2 py-2">Adjust</th>
                        <th class="text-right px-2 py-2">Closing</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($s['movement'] as $m)
                        <tr class="border-t border-gray-200 dark:border-gray-700 {{ $m['low'] ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                            <td class="px-2 py-1">
                                {{ $m['item'] }}
                                @if($m['low']) <span class="text-red-600 text-xs">⚠ low</span> @endif
                            </td>
                            <td class="px-2 py-1 text-right">{{ $m['opening'] }}</td>
                            <td class="px-2 py-1 text-right text-green-600">{{ $m['in'] }}</td>
                            <td class="px-2 py-1 text-right text-red-600">{{ $m['out'] }}</td>
                            <td class="px-2 py-1 text-right">{{ $m['adjust'] }}</td>
                            <td class="px-2 py-1 text-right font-semibold">{{ $m['closing'] }} {{ $m['unit'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
