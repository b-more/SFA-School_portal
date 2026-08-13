<x-filament-panels::page>
    <x-filament-panels::form wire:submit="refresh">
        {{ $this->form }}
    </x-filament-panels::form>

    <div class="flex flex-wrap gap-2 mt-4">
        <a href="{{ $this->pdfUrl() }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Download PDF
        </a>
        <a href="{{ $this->xlsxUrl() }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Download Excel
        </a>
    </div>

    @php $d = $data; @endphp

    {{-- Per-term tables --}}
    @foreach($d['terms'] as $term)
        @php $rows = $d['by_term'][$term->id]; $t = $d['term_totals'][$term->id]; @endphp
        <x-filament::section class="mt-6">
            <x-slot name="heading">{{ strtoupper($term->name) }} — {{ $d['year_label'] }}</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="text-left px-3 py-2">Section</th>
                            <th class="text-right px-3 py-2">Pupils</th>
                            <th class="text-right px-3 py-2">Fee / Pupil</th>
                            <th class="text-right px-3 py-2">Expected (ZMW)</th>
                            <th class="text-right px-3 py-2">Actual (ZMW)</th>
                            <th class="text-right px-3 py-2">Shortfall (ZMW)</th>
                            <th class="text-right px-3 py-2">% Collected</th>
                            <th class="text-right px-3 py-2">% Loss</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($d['sections'] as $section)
                            @php $r = $rows[$section]; @endphp
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-3 py-2 font-medium">
                                    {{ $section }}
                                    @if(($r['zero_fee_rows'] ?? 0) > 0)
                                        <span class="text-red-600 text-xs">⚠ {{ $r['zero_fee_rows'] }} K0 fee row(s)</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right">{{ number_format($r['pupils']) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($r['fee_per'], 0) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($r['expected'], 0) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($r['actual'], 0) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($r['shortfall'], 0) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($r['pct_collected'], 1) }}%</td>
                                <td class="px-3 py-2 text-right">{{ number_format($r['pct_loss'], 1) }}%</td>
                            </tr>
                        @endforeach
                        <tr class="border-t border-gray-300 bg-amber-50 dark:bg-amber-900/20 font-semibold">
                            <td class="px-3 py-2">TOTAL</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['pupils']) }}</td>
                            <td></td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['expected'], 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['actual'], 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['shortfall'], 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['pct_collected'], 1) }}%</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['pct_loss'], 1) }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endforeach

    {{-- Annual roll-up --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Annual Summary — All Three Terms</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="text-left px-3 py-2">Term</th>
                        <th class="text-right px-3 py-2">Expected</th>
                        <th class="text-right px-3 py-2">Actual</th>
                        <th class="text-right px-3 py-2">Shortfall</th>
                        <th class="text-right px-3 py-2">% Collected</th>
                        <th class="text-right px-3 py-2">% Loss</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($d['terms'] as $term)
                        @php $t = $d['term_totals'][$term->id]; @endphp
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2 font-medium">{{ strtoupper($term->name) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['expected'], 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['actual'], 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['shortfall'], 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['pct_collected'], 1) }}%</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['pct_loss'], 1) }}%</td>
                        </tr>
                    @endforeach
                    <tr class="border-t border-gray-300 bg-blue-50 dark:bg-blue-900/30 font-semibold">
                        <td class="px-3 py-2">YEAR TOTAL</td>
                        <td class="px-3 py-2 text-right">{{ number_format($d['annual']['expected'], 0) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($d['annual']['actual'], 0) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($d['annual']['shortfall'], 0) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($d['annual']['pct_collected'], 1) }}%</td>
                        <td class="px-3 py-2 text-right">{{ number_format($d['annual']['pct_loss'], 1) }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- Salaries --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Can Collections Cover Salaries?</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="text-left px-3 py-2">Term</th>
                        <th class="text-right px-3 py-2">Salary Bill</th>
                        <th class="text-left px-3 py-2">Payroll Months</th>
                        <th class="text-right px-3 py-2">Expected</th>
                        <th class="text-right px-3 py-2">Actual</th>
                        <th class="text-right px-3 py-2">Surplus after Salaries</th>
                        <th class="text-right px-3 py-2">Sal / Expected</th>
                        <th class="text-right px-3 py-2">Sal / Actual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($d['terms'] as $term)
                        @php
                            $t  = $d['term_totals'][$term->id];
                            $sb = $d['salary_bill'][$term->id];
                            $sm = $d['salary_meta'][$term->id];
                        @endphp
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2 font-medium">{{ strtoupper($term->name) }}</td>
                            <td class="px-3 py-2 text-right">{{ $sb === null ? '—' : number_format($sb, 0) }}</td>
                            <td class="px-3 py-2 text-xs text-gray-500">{{ $sm ?? '—' }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['expected'], 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($t['actual'], 0) }}</td>
                            <td class="px-3 py-2 text-right {{ ($sb !== null && ($t['actual'] - $sb) < 0) ? 'text-red-600 font-semibold' : '' }}">
                                {{ $sb === null ? '—' : number_format($t['actual'] - $sb, 0) }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                {{ ($sb !== null && $t['expected'] > 0) ? number_format(($sb / $t['expected']) * 100, 1) . '%' : '—' }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                {{ ($sb !== null && $t['actual'] > 0) ? number_format(($sb / $t['actual']) * 100, 1) . '%' : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- Population --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">School Population by Class — {{ $d['year_label'] }}</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="text-left px-3 py-2">Section</th>
                        <th class="text-left px-3 py-2">Class</th>
                        <th class="text-left px-3 py-2">Class Teacher</th>
                        <th class="text-right px-3 py-2">Enrolment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($d['sections'] as $section)
                        @foreach($d['population'][$section] ?? [] as $entry)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-3 py-2">{{ $section }}</td>
                                <td class="px-3 py-2">{{ $entry['class'] }}</td>
                                <td class="px-3 py-2">{{ $entry['teacher'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($entry['enrolment']) }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-t border-gray-300 bg-amber-50 dark:bg-amber-900/20 font-semibold">
                            <td></td>
                            <td colspan="2" class="px-3 py-2">{{ strtoupper($section) }} TOTAL</td>
                            <td class="px-3 py-2 text-right">{{ number_format($d['section_totals'][$section] ?? 0) }}</td>
                        </tr>
                    @endforeach
                    <tr class="border-t border-gray-300 bg-blue-50 dark:bg-blue-900/30 font-semibold">
                        <td></td>
                        <td colspan="2" class="px-3 py-2">SCHOOL TOTAL</td>
                        <td class="px-3 py-2 text-right">{{ number_format($d['school_total']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
