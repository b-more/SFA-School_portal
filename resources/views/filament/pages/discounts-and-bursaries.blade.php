<x-filament-panels::page>
    <x-filament-panels::form>{{ $this->form }}</x-filament-panels::form>

    @php
        $ss           = $this->settings;
        $sib          = $this->siblingPreview;
        $early        = $this->earlyPreview;
        $current      = $this->currentTotals;
        $moneyFmt     = fn ($n) => number_format((float) $n, 2);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- ===== SIBLING DISCOUNT ===== --}}
        <x-filament::section>
            <x-slot name="heading">Sibling Discount</x-slot>
            <x-slot name="description">Flat percentage on each pupil's tuition once the family reaches the threshold. Configure the policy on the School Settings page.</x-slot>

            <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1 mb-4">
                <div><strong>Enabled:</strong> {{ $ss->sibling_discount_enabled ? 'Yes' : 'No' }}</div>
                <div><strong>Threshold:</strong> {{ (int) $ss->sibling_discount_min_pupils }}+ pupils per family</div>
                <div><strong>Discount:</strong> {{ number_format((float) $ss->sibling_discount_percentage, 2) }}% off each pupil's tuition</div>
            </div>

            @if(! $sib['enabled'])
                <div class="rounded-md bg-amber-50 dark:bg-amber-900/30 p-3 text-sm text-amber-800 dark:text-amber-200">
                    Sibling discount is disabled. Toggle it on in School Settings before applying.
                </div>
            @else
                <div class="rounded-md bg-gray-50 dark:bg-gray-800 p-3 mb-4 grid grid-cols-3 gap-4 text-center">
                    <div><div class="text-2xl font-bold text-primary-600">{{ $sib['summary']['families'] }}</div><div class="text-xs text-gray-500 uppercase">Families</div></div>
                    <div><div class="text-2xl font-bold text-primary-600">{{ $sib['summary']['pupils'] }}</div><div class="text-xs text-gray-500 uppercase">Pupils</div></div>
                    <div><div class="text-2xl font-bold text-primary-600">K{{ $moneyFmt($sib['summary']['discount_total']) }}</div><div class="text-xs text-gray-500 uppercase">Total Discount</div></div>
                </div>

                @if($sib['families']->isNotEmpty())
                    <div class="max-h-72 overflow-y-auto border rounded-md dark:border-gray-700">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-100 dark:bg-gray-800 sticky top-0">
                                <tr>
                                    <th class="text-left px-2 py-1">Family</th>
                                    <th class="text-left px-2 py-1">Pupils</th>
                                    <th class="text-right px-2 py-1">Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sib['families'] as $f)
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="px-2 py-1">
                                            <div class="font-semibold">{{ $f['guardian_name'] }}</div>
                                            <div class="text-gray-500">{{ $f['family_pupil_count'] }} pupils</div>
                                        </td>
                                        <td class="px-2 py-1 text-gray-600">
                                            @foreach($f['pupils'] as $p)
                                                <div>{{ $p['name'] }} <span class="text-gray-400">— {{ $p['class'] }}</span></div>
                                            @endforeach
                                        </td>
                                        <td class="px-2 py-1 text-right font-semibold">K{{ $moneyFmt($f['total_new_discount']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-sm text-gray-500 italic">No families meet the {{ (int) $ss->sibling_discount_min_pupils }}-pupil threshold this term.</div>
                @endif
            @endif

            <div class="mt-4 flex flex-wrap gap-2">
                <x-filament::button wire:click="applySibling" color="success" icon="heroicon-o-check-circle" :disabled="! $sib['enabled'] || $sib['summary']['pupils'] === 0">
                    Apply for this term
                </x-filament::button>
                <x-filament::button wire:click="reverseSibling" color="danger" outlined icon="heroicon-o-arrow-uturn-left"
                    wire:confirm="This clears every sibling discount stamped on this term's fees. Continue?">
                    Reverse
                </x-filament::button>
            </div>

            @if($current['sibling'])
                <div class="mt-3 text-xs text-gray-500">
                    Currently stamped on this term: {{ $current['sibling']->pupils }} pupil(s) · K{{ $moneyFmt($current['sibling']->total) }} total
                </div>
            @endif
        </x-filament::section>

        {{-- ===== EARLY PAYMENT DISCOUNT ===== --}}
        <x-filament::section>
            <x-slot name="heading">Early Payment Discount</x-slot>
            <x-slot name="description">Retroactive credit for pupils whose full term fee landed on or before term start date.</x-slot>

            <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1 mb-4">
                <div><strong>Enabled:</strong> {{ $ss->early_payment_discount_enabled ? 'Yes' : 'No' }}</div>
                <div><strong>Discount:</strong> {{ number_format((float) $ss->early_payment_discount_percentage, 2) }}% off tuition</div>
                <div><strong>Cutoff:</strong> {{ $early['cutoff_date']?->format('d M Y') ?? '—' }} (term start)</div>
            </div>

            @if(! $early['enabled'])
                <div class="rounded-md bg-amber-50 dark:bg-amber-900/30 p-3 text-sm text-amber-800 dark:text-amber-200">
                    Early payment discount is disabled. Toggle it on in School Settings before applying.
                </div>
            @else
                <div class="rounded-md bg-gray-50 dark:bg-gray-800 p-3 mb-4 grid grid-cols-2 gap-4 text-center">
                    <div><div class="text-2xl font-bold text-primary-600">{{ $early['summary']['pupils'] }}</div><div class="text-xs text-gray-500 uppercase">Eligible Pupils</div></div>
                    <div><div class="text-2xl font-bold text-primary-600">K{{ $moneyFmt($early['summary']['discount_total']) }}</div><div class="text-xs text-gray-500 uppercase">Total Discount</div></div>
                </div>

                @if($early['pupils']->isNotEmpty())
                    <div class="max-h-72 overflow-y-auto border rounded-md dark:border-gray-700">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-100 dark:bg-gray-800 sticky top-0">
                                <tr>
                                    <th class="text-left px-2 py-1">Pupil</th>
                                    <th class="text-left px-2 py-1">Class</th>
                                    <th class="text-left px-2 py-1">Paid on</th>
                                    <th class="text-right px-2 py-1">Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($early['pupils'] as $p)
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="px-2 py-1">{{ $p['name'] }}</td>
                                        <td class="px-2 py-1 text-gray-600">{{ $p['class'] }}</td>
                                        <td class="px-2 py-1 text-gray-600">{{ \Carbon\Carbon::parse($p['payment_date'])->format('d M Y') }}</td>
                                        <td class="px-2 py-1 text-right font-semibold">K{{ $moneyFmt($p['discount']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-sm text-gray-500 italic">No pupils have a fully-settled fee dated on or before {{ $early['cutoff_date']?->format('d M Y') }}.</div>
                @endif
            @endif

            <div class="mt-4 flex flex-wrap gap-2">
                <x-filament::button wire:click="applyEarlyPayment" color="success" icon="heroicon-o-check-circle" :disabled="! $early['enabled'] || $early['summary']['pupils'] === 0">
                    Apply for this term
                </x-filament::button>
                <x-filament::button wire:click="reverseEarlyPayment" color="danger" outlined icon="heroicon-o-arrow-uturn-left"
                    wire:confirm="This clears every early-payment discount stamped on this term's fees. Continue?">
                    Reverse
                </x-filament::button>
            </div>

            @if($current['early_payment'])
                <div class="mt-3 text-xs text-gray-500">
                    Currently stamped on this term: {{ $current['early_payment']->pupils }} pupil(s) · K{{ $moneyFmt($current['early_payment']->total) }} total
                </div>
            @endif
        </x-filament::section>
    </div>

    <x-filament::section class="mt-6">
        <x-slot name="heading">How it works</x-slot>
        <div class="prose dark:prose-invert max-w-none text-sm">
            <ul>
                <li>Every applied discount stamps <code>discount_type</code>, <code>discount_amount</code>, <code>discount_percentage</code>, <code>discount_reason</code>, and <code>approved_by</code> onto each affected StudentFee row.</li>
                <li>The StudentFee balance observer subtracts <code>discount_amount</code> automatically, so parent statements and the Fee Collection Tracker update immediately.</li>
                <li>Both actions are idempotent — re-running only touches rows whose computed discount would change (e.g. a family grew from 4 pupils to 5).</li>
                <li>Manual/bursary discounts already on a row are respected — the automatic apply skips those and logs it.</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-panels::page>
