<x-filament-panels::page>
    <div class="space-y-4">
        <div class="fi-section bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-6">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Bulk fee generation</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Use the buttons above to create fee rows for many students at once. Every action is <strong>idempotent</strong>:
                running it again will skip students who already have that fee for the same period.
                <strong>Preview only</strong> is on by default — turn it off when you're ready to commit.
            </p>
            <ul class="mt-4 list-disc pl-6 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                <li><strong>Generate Annual Fees</strong> — creates PTA, Computer, and Maintenance rows for every active student for the chosen academic year.</li>
                <li><strong>Generate Bus Charges</strong> — creates a monthly bus fee for every student with an active bus assignment (manage assignments under <em>Bus Assignments</em>).</li>
                <li><strong>Assign Uniform Item</strong> / <strong>Assign Educational Tour</strong> — pick a catalogue item and apply it to selected grades or individual students.</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>
