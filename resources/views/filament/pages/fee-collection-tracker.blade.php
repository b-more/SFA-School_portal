<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}
    </x-filament-panels::form>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ $this->pdfUrl() }}" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 shadow">
            <x-heroicon-o-document-arrow-down class="w-5 h-5" />
            Generate PDF Report
        </a>
        <a href="{{ $this->xlsxUrl() }}" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 shadow">
            <x-heroicon-o-table-cells class="w-5 h-5" />
            Generate Excel Report
        </a>
    </div>

    <x-filament::section class="mt-6">
        <x-slot name="heading">About this report</x-slot>
        <div class="prose dark:prose-invert max-w-none text-sm">
            <p>
                The <strong>Termly Fee Collection Tracker</strong> is generated live from the
                database at the moment you click Generate. Every figure reflects the current
                state of the ledger.
            </p>
            <ul>
                <li><strong>Pupils</strong> — active students on the roll for each section (ECE / Primary / Secondary).</li>
                <li><strong>Fee per Pupil</strong> — the published <code>fee_structures.basic_fee</code> that the majority of the section's pupils are billed against for that term.</li>
                <li><strong>Expected</strong> — Pupils × Fee per Pupil (every pupil on the roll is counted, whether or not a StudentFee row exists yet).</li>
                <li><strong>Actual Collected</strong> — sum of completed <code>payment_transactions</code> tied to those pupils' StudentFee rows for the term (parent app · WhatsApp bot · public QR · office-recorded).</li>
                <li><strong>Salary Bill</strong> — sum of <code>payrolls.net_salary</code> whose month falls inside the term window.</li>
            </ul>
            <p>The Population sheet lists every active class, its class teacher, and headcount.</p>
        </div>
    </x-filament::section>
</x-filament-panels::page>
