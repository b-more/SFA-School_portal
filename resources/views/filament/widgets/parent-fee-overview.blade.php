<x-filament-widgets::widget>
    @php
        $data = $this->getData();
        $children = $data['children'] ?? collect();
        $year = $data['year'];
    @endphp

    @foreach($children as $child)
        @php
            $student = $child['student'];
            $terms = $child['terms'];
        @endphp
        <div class="mb-4">
            {{-- Child Header --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-t-xl px-4 sm:px-5 py-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-white">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $student->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->grade?->name ?? '' }} &bull; {{ $year?->name ?? '' }}</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    @if($child['total_balance'] > 0)
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Outstanding</p>
                        <p class="text-lg font-bold text-red-600">K {{ number_format($child['total_balance'], 2) }}</p>
                    @else
                        <p class="text-sm font-semibold text-emerald-600 flex items-center gap-1">
                            <x-heroicon-s-check-circle class="w-4 h-4" /> All Fees Paid
                        </p>
                    @endif
                </div>
            </div>

            {{-- Term Cards Row --}}
            <div class="grid grid-cols-1 md:grid-cols-{{ $terms->count() }} border-x border-b border-gray-200 dark:border-gray-700 rounded-b-xl overflow-hidden divide-y md:divide-y-0 md:divide-x divide-gray-200 dark:divide-gray-700">
                @foreach($terms as $term)
                    @php
                        $statusBadgeClass = match($term['status']) {
                            'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                            'partial' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                            'unpaid' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            'overpaid' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                            default => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                        };
                        $statusLabel = match($term['status']) {
                            'paid' => 'PAID',
                            'partial' => 'PARTIAL',
                            'unpaid' => 'NOT PAID',
                            'overpaid' => 'OVERPAID',
                            default => 'NO FEE',
                        };
                        $progressColor = match($term['status']) {
                            'paid' => 'text-emerald-600',
                            'partial' => 'text-amber-600',
                            'overpaid' => 'text-blue-600',
                            default => 'text-red-600',
                        };
                        $hasFee = $term['total_fee'] > 0;
                    @endphp
                    <div class="bg-white dark:bg-gray-800 p-4 flex flex-col">
                        {{-- Term Header --}}
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $term['term_name'] }}
                            </h4>
                            @if($hasFee)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ $statusBadgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500">
                                    N/A
                                </span>
                            @endif
                        </div>

                        @if($hasFee)
                            {{-- Tuition Fee --}}
                            <div class="mb-3">
                                <p class="text-xs text-gray-400 dark:text-gray-500">Tuition Fee</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">K {{ number_format($term['total_fee'], 2) }}</p>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="mb-3">
                                <div class="flex justify-between text-[11px] mb-1">
                                    <span class="text-gray-500 dark:text-gray-400">Paid: K {{ number_format($term['amount_paid'], 2) }}</span>
                                    <span class="font-semibold {{ $progressColor }}">{{ $term['progress'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-500
                                        {{ match($term['status']) {
                                            'paid' => 'bg-emerald-500',
                                            'partial' => 'bg-amber-500',
                                            'overpaid' => 'bg-blue-500',
                                            default => 'bg-red-400',
                                        } }}"
                                        style="width: {{ min($term['progress'], 100) }}%">
                                    </div>
                                </div>
                            </div>

                            {{-- Balance --}}
                            <div class="mt-auto pt-2 border-t border-gray-100 dark:border-gray-700">
                                @if($term['balance'] > 0)
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Balance Due</span>
                                        <span class="text-sm font-bold text-red-600">K {{ number_format($term['balance'], 2) }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center gap-1 text-xs text-emerald-600 font-semibold">
                                        <x-heroicon-s-check-circle class="w-3.5 h-3.5" />
                                        Fully Paid
                                    </div>
                                @endif
                            </div>

                            {{-- Download receipt if paid --}}
                            @if($term['fee_id'] && in_array($term['status'], ['paid', 'partial', 'overpaid']))
                                <div class="mt-2">
                                    <a href="{{ route('student-fees.receipt', $term['fee_id']) }}"
                                       target="_blank"
                                       class="flex items-center justify-center gap-1.5 w-full px-3 py-1.5 text-xs font-medium rounded-md
                                              bg-blue-50 text-blue-700 hover:bg-blue-100
                                              dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50
                                              transition-colors">
                                        <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                                        Download Receipt
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="flex-1 flex items-center justify-center py-4">
                                <p class="text-xs text-gray-400 dark:text-gray-500">No fee assigned</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Overall Summary Bar --}}
            @if($child['total_owed'] > 0)
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 bg-gray-50 dark:bg-gray-800/50 border border-t-0 border-gray-200 dark:border-gray-700 rounded-b-xl px-4 sm:px-5 py-2 -mt-px">
                    <div class="flex flex-wrap items-center gap-3 sm:gap-6 text-xs">
                        <span class="text-gray-500 dark:text-gray-400">
                            Annual Tuition: <strong class="text-gray-900 dark:text-white">K {{ number_format($child['total_owed'], 2) }}</strong>
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">
                            Paid: <strong class="text-emerald-600">K {{ number_format($child['total_paid'], 2) }}</strong>
                        </span>
                        @if($child['total_balance'] > 0)
                            <span class="text-gray-500 dark:text-gray-400">
                                Remaining: <strong class="text-red-600">K {{ number_format($child['total_balance'], 2) }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $child['overall_progress'] >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}"
                                 style="width: {{ min($child['overall_progress'], 100) }}%"></div>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $child['overall_progress'] }}%</span>
                    </div>
                </div>
            @endif
        </div>
    @endforeach

    @if($children->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
            <x-heroicon-o-banknotes class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
            <p class="text-sm text-gray-500 dark:text-gray-400">No fee records found for the current academic year.</p>
        </div>
    @endif
</x-filament-widgets::widget>
