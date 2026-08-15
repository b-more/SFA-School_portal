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

    {{-- ===== ANALYTICAL REPORT CATALOG (C1–L1) ===== --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Analytical reports</x-slot>
        <x-slot name="description">
            Seven purpose-built PDFs — clinical, governance, operational and class-level cuts.
            Each opens in a new tab with the school letterhead and signature blocks.
        </x-slot>

        @php
            $inputCls  = 'w-full rounded-md border-gray-300 dark:border-gray-600 text-sm py-1.5';
            $btnCls    = 'inline-flex items-center px-3 py-1.5 rounded-md bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium';
            $cardCls   = 'rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900';
            $labelCls  = 'block text-xs uppercase tracking-wide text-gray-500 mb-1';
            $students  = \App\Models\Student::where('enrollment_status','active')->orderBy('name')->pluck('name','id');
            $complaints= \App\Models\ClinicComplaint::orderBy('name')->pluck('name','id');
            $classes   = \App\Models\ClassSection::with('grade')->get()
                            ->sortBy(fn ($c) => ($c->grade?->name ?? '') . '-' . $c->name)
                            ->mapWithKeys(fn ($c) => [$c->id => ($c->grade?->name ?? '—') . ' - ' . $c->name]);
            $todayIso  = now()->toDateString();
            $monthAgo  = now()->subMonth()->toDateString();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- C1 · Per-pupil medical history --}}
            <form method="GET" action="{{ route('reports.clinic-pupil-history.pdf') }}" target="_blank" class="{{ $cardCls }}">
                <div class="font-semibold text-primary-700 dark:text-primary-400">C1 · Per-pupil medical history</div>
                <div class="text-xs text-gray-500 mb-3">Full visit ledger for one pupil, ready for parent, GP or referral.</div>
                <label class="{{ $labelCls }}">Pupil</label>
                <select name="student_id" required class="{{ $inputCls }}">
                    <option value="">— Choose pupil —</option>
                    @foreach($students as $id => $n)<option value="{{ $id }}">{{ $n }}</option>@endforeach
                </select>
                <div class="mt-3 text-right"><button class="{{ $btnCls }}">Download PDF</button></div>
            </form>

            {{-- C2 · Complaint trend --}}
            <form method="GET" action="{{ route('reports.clinic-complaint-trend.pdf') }}" target="_blank" class="{{ $cardCls }}">
                <div class="font-semibold text-primary-700 dark:text-primary-400">C2 · Complaint trend</div>
                <div class="text-xs text-gray-500 mb-3">See if one complaint (or all) is rising month-on-month.</div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $labelCls }}">Complaint</label>
                        <select name="complaint_id" class="{{ $inputCls }}">
                            <option value="">All complaints</option>
                            @foreach($complaints as $id => $n)<option value="{{ $id }}">{{ $n }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Window</label>
                        <select name="months" class="{{ $inputCls }}">
                            <option value="6">Last 6 months</option>
                            <option value="12" selected>Last 12 months</option>
                            <option value="18">Last 18 months</option>
                            <option value="24">Last 24 months</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 text-right"><button class="{{ $btnCls }}">Download PDF</button></div>
            </form>

            {{-- G1 · Sick notes register --}}
            <form method="GET" action="{{ route('reports.clinic-sick-notes.pdf') }}" target="_blank" class="{{ $cardCls }}">
                <div class="font-semibold text-primary-700 dark:text-primary-400">G1 · Sick-notes register</div>
                <div class="text-xs text-gray-500 mb-3">Signed collection register — guardians sign when collecting pupils.</div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $labelCls }}">From</label>
                        <input type="date" name="from" value="{{ $monthAgo }}" required class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">To</label>
                        <input type="date" name="to" value="{{ $todayIso }}" required class="{{ $inputCls }}">
                    </div>
                </div>
                <input type="hidden" name="period" value="custom">
                <div class="mt-3 text-right"><button class="{{ $btnCls }}">Download PDF</button></div>
            </form>

            {{-- G4 · Attendance-loss impact --}}
            <form method="GET" action="{{ route('reports.clinic-attendance-loss.pdf') }}" target="_blank" class="{{ $cardCls }}">
                <div class="font-semibold text-primary-700 dark:text-primary-400">G4 · Attendance-loss impact</div>
                <div class="text-xs text-gray-500 mb-3">School-days lost after sent-home / referred visits.</div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $labelCls }}">From</label>
                        <input type="date" name="from" value="{{ $monthAgo }}" required class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">To</label>
                        <input type="date" name="to" value="{{ $todayIso }}" required class="{{ $inputCls }}">
                    </div>
                </div>
                <input type="hidden" name="period" value="custom">
                <div class="mt-3 text-right"><button class="{{ $btnCls }}">Download PDF</button></div>
            </form>

            {{-- O1 · Cost per visit / pupil --}}
            <form method="GET" action="{{ route('reports.clinic-cost-metrics.pdf') }}" target="_blank" class="{{ $cardCls }}">
                <div class="font-semibold text-primary-700 dark:text-primary-400">O1 · Cost per visit &amp; per pupil</div>
                <div class="text-xs text-gray-500 mb-3">Bursar-ready cost analysis with a per-section breakdown.</div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $labelCls }}">From</label>
                        <input type="date" name="from" value="{{ $monthAgo }}" required class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">To</label>
                        <input type="date" name="to" value="{{ $todayIso }}" required class="{{ $inputCls }}">
                    </div>
                </div>
                <input type="hidden" name="period" value="custom">
                <div class="mt-3 text-right"><button class="{{ $btnCls }}">Download PDF</button></div>
            </form>

            {{-- O2 · Burn rate --}}
            <form method="GET" action="{{ route('reports.clinic-burn-rate.pdf') }}" target="_blank" class="{{ $cardCls }}">
                <div class="font-semibold text-primary-700 dark:text-primary-400">O2 · Stock burn-rate</div>
                <div class="text-xs text-gray-500 mb-3">Days-of-supply ranking so you never run out unnoticed.</div>
                <label class="{{ $labelCls }}">Averaging window</label>
                <select name="window" class="{{ $inputCls }}">
                    <option value="14">Last 14 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="60">Last 60 days</option>
                    <option value="90">Last 90 days</option>
                </select>
                <div class="mt-3 text-right"><button class="{{ $btnCls }}">Download PDF</button></div>
            </form>

            {{-- L1 · Class health snapshot --}}
            <form method="GET" action="{{ route('reports.clinic-class-snapshot.pdf') }}" target="_blank" class="{{ $cardCls }} lg:col-span-2">
                <div class="font-semibold text-primary-700 dark:text-primary-400">L1 · Class health snapshot</div>
                <div class="text-xs text-gray-500 mb-3">One-page snapshot for the class teacher — top complaints, repeat visitors, missed days.</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="{{ $labelCls }}">Class</label>
                        <select name="class_section_id" required class="{{ $inputCls }}">
                            <option value="">— Choose class —</option>
                            @foreach($classes as $id => $n)<option value="{{ $id }}">{{ $n }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">From</label>
                        <input type="date" name="from" value="{{ $monthAgo }}" required class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">To</label>
                        <input type="date" name="to" value="{{ $todayIso }}" required class="{{ $inputCls }}">
                    </div>
                </div>
                <input type="hidden" name="period" value="custom">
                <div class="mt-3 text-right"><button class="{{ $btnCls }}">Download PDF</button></div>
            </form>
        </div>
    </x-filament::section>

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
