<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Configuration Card --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 p-3 rounded-lg bg-primary-50 dark:bg-primary-900/20">
                    <x-heroicon-o-cog-6-tooth class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Auto-Generate Timetable</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Automatically creates a complete timetable based on subject-teacher assignments. The system will:
                    </p>
                    <ul class="mt-2 text-sm text-gray-500 dark:text-gray-400 list-disc list-inside space-y-1">
                        <li>Distribute subjects evenly across the week for each class</li>
                        <li>Ensure no teacher is scheduled in two classes at the same time</li>
                        <li>Avoid placing the same subject in consecutive periods</li>
                        <li>Use class teachers for Baby Class to Grade 7 (primary)</li>
                        <li>Use subject-specific teachers for secondary classes</li>
                    </ul>

                    <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                        <div class="text-sm text-amber-700 dark:text-amber-300">
                            <strong>Prerequisites:</strong>
                            <ul class="list-disc list-inside mt-1">
                                <li>Subjects must be assigned to grades (Grade Subjects)</li>
                                <li>Teachers must be assigned to subjects per class (Subject Teachings)</li>
                                <li>Class teachers must be set for primary classes</li>
                                <li>Timetable periods must be configured</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic Year</label>
                    <select
                        wire:model.live="selectedAcademicYear"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm text-sm"
                    >
                        @foreach($this->getAcademicYears() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($selectedAcademicYear)
                    <div>
                        @php $existingCount = $this->getExistingEntryCount(); @endphp
                        @if($existingCount > 0)
                            <div class="flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400">
                                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                                <span>{{ number_format($existingCount) }} existing entries will be replaced</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-information-circle class="w-5 h-5" />
                                <span>No existing timetable entries</span>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="ml-auto">
                    <button
                        onclick="if(confirm('This will DELETE all existing timetable entries and generate a new timetable. Continue?')) { @this.call('generate') }"
                        wire:loading.attr="disabled"
                        wire:target="generate"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors shadow-sm disabled:opacity-50"
                    >
                        <div wire:loading wire:target="generate">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                        <x-heroicon-o-sparkles wire:loading.remove wire:target="generate" class="w-4 h-4" />
                        <span wire:loading.remove wire:target="generate">Generate Timetable</span>
                        <span wire:loading wire:target="generate">Generating...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Results --}}
        @if($generated)
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 text-center">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $generationStats['total_classes'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Classes</div>
                </div>
                <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $generationStats['primary_classes'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Primary Classes</div>
                </div>
                <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 text-center">
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $generationStats['secondary_classes'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Secondary Classes</div>
                </div>
                <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 text-center">
                    <div class="text-2xl font-bold text-success-600 dark:text-success-400">{{ number_format($generationStats['entries_created'] ?? 0) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Entries Created</div>
                </div>
                <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 text-center">
                    <div class="text-2xl font-bold {{ empty($conflicts) ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">{{ count($conflicts) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Conflicts</div>
                </div>
            </div>

            {{-- Conflicts --}}
            @if(!empty($conflicts))
                <div class="rounded-xl bg-danger-50 dark:bg-danger-900/20 p-6 ring-1 ring-danger-200 dark:ring-danger-700">
                    <h4 class="text-base font-semibold text-danger-800 dark:text-danger-200 flex items-center gap-2">
                        <x-heroicon-o-exclamation-circle class="w-5 h-5" />
                        Teacher Conflicts Detected
                    </h4>
                    <p class="mt-1 text-sm text-danger-600 dark:text-danger-300">
                        The following conflicts need to be resolved manually in the Manage Timetable page:
                    </p>
                    <ul class="mt-3 space-y-1">
                        @foreach($conflicts as $conflict)
                            <li class="text-sm text-danger-700 dark:text-danger-300 flex items-start gap-2">
                                <x-heroicon-o-x-circle class="w-4 h-4 flex-shrink-0 mt-0.5" />
                                {{ $conflict }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="rounded-xl bg-success-50 dark:bg-success-900/20 p-4 ring-1 ring-success-200 dark:ring-success-700">
                    <div class="flex items-center gap-2 text-success-700 dark:text-success-300">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        <span class="font-semibold">No teacher conflicts detected! The timetable is valid.</span>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-wrap gap-3">
                <x-filament::button
                    tag="a"
                    href="{{ url('/admin/master-timetable') }}"
                    icon="heroicon-o-table-cells"
                    color="primary"
                >
                    View Master Timetable
                </x-filament::button>
                <x-filament::button
                    tag="a"
                    href="{{ url('/admin/manage-timetable') }}"
                    icon="heroicon-o-pencil-square"
                    color="gray"
                >
                    Edit Individual Classes
                </x-filament::button>
            </div>
        @endif

        {{-- Generation Log --}}
        @if(!empty($generationLogs))
            <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Generation Log</h4>
                </div>
                <div class="p-4 max-h-64 overflow-y-auto">
                    <div class="space-y-1 font-mono text-xs">
                        @foreach($generationLogs as $log)
                            <div class="flex items-start gap-2 {{ str_contains($log, 'WARNING') ? 'text-amber-600 dark:text-amber-400' : (str_contains($log, 'ERROR') ? 'text-danger-600 dark:text-danger-400' : 'text-gray-600 dark:text-gray-400') }}">
                                <span class="text-gray-400 dark:text-gray-600 select-none">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.</span>
                                {{ $log }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
