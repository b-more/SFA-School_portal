<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Academic Year Selector --}}
        <x-filament::section>
            <x-slot name="heading">
                Select Academic Year
            </x-slot>

            <div class="grid grid-cols-1 gap-4">
                @foreach($this->getAcademicYears() as $year)
                    <div class="flex items-center justify-between p-4 border rounded-lg @if($year->is_active) border-primary-600 bg-primary-50 dark:bg-primary-900/20 @else border-gray-200 dark:border-gray-700 @endif">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-semibold">{{ $year->name }}</h3>
                                @if($year->is_active)
                                    <span class="px-2 py-1 text-xs font-medium text-white bg-primary-600 rounded-full">Active</span>
                                @endif
                                @if($selectedYearId === $year->id)
                                    <span class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded-full">Viewing</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $year->start_date->format('M d, Y') }} - {{ $year->end_date->format('M d, Y') }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            @if($selectedYearId !== $year->id)
                                <x-filament::button
                                    wire:click="switchYear({{ $year->id }})"
                                    color="gray"
                                    size="sm"
                                >
                                    View
                                </x-filament::button>
                            @endif

                            @if(!$year->is_active)
                                <x-filament::button
                                    wire:click="activateYear({{ $year->id }})"
                                    color="success"
                                    size="sm"
                                >
                                    Activate
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Statistics --}}
        @if($selectedYearId)
            <x-filament::section>
                <x-slot name="heading">
                    Year Statistics
                </x-slot>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Students</div>
                        <div class="text-3xl font-bold text-blue-700 dark:text-blue-300 mt-2">{{ number_format($yearStats['total_students'] ?? 0) }}</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                            {{ number_format($yearStats['active_students'] ?? 0) }} active
                        </div>
                    </div>

                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="text-sm font-medium text-green-600 dark:text-green-400">Results Recorded</div>
                        <div class="text-3xl font-bold text-green-700 dark:text-green-300 mt-2">{{ number_format($yearStats['total_results'] ?? 0) }}</div>
                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                            {{ number_format($yearStats['total_homework'] ?? 0) }} homework assignments
                        </div>
                    </div>

                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                        <div class="text-sm font-medium text-purple-600 dark:text-purple-400">Payments Received</div>
                        <div class="text-3xl font-bold text-purple-700 dark:text-purple-300 mt-2">
                            K{{ number_format($yearStats['total_payments'] ?? 0, 2) }}
                        </div>
                        <div class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                            {{ number_format($yearStats['payment_count'] ?? 0) }} transactions
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Important Notes --}}
        <x-filament::section>
            <x-slot name="heading">
                Important Notes
            </x-slot>

            <div class="prose dark:prose-invert max-w-none">
                <ul class="text-sm space-y-2">
                    <li><strong>Active Year:</strong> The active academic year is used for all new records by default.</li>
                    <li><strong>Viewing:</strong> You can switch between years to view historical data without changing the active year.</li>
                    <li><strong>Rollover:</strong> Use the "Rollover to Next Year" button to automatically promote students, copy teacher assignments, and generate fees.</li>
                    <li><strong>Data Isolation:</strong> All records (students, homework, results, payments) are isolated per academic year.</li>
                    <li><strong>Historical Access:</strong> Past academic year data is preserved and accessible in read-only mode.</li>
                </ul>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
