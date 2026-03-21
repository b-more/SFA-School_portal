<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    @if($selectedClassSection && $selectedAcademicYear)
        <div class="mt-6">
            {{-- Header with class name and print buttons --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Weekly Timetable: {{ $this->getSelectedClassName() }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Click on any cell to edit, or use "Set Day" to assign all periods for a day at once
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-filament::button
                        color="gray"
                        tag="a"
                        href="{{ route('timetable.print.master', ['academicYear' => $selectedAcademicYear]) }}"
                        target="_blank"
                        icon="heroicon-o-document-text"
                        size="sm"
                    >
                        Master Timetable
                    </x-filament::button>
                    <x-filament::button
                        color="success"
                        tag="a"
                        href="{{ route('timetable.print.class', ['classSection' => $selectedClassSection, 'academicYear' => $selectedAcademicYear]) }}"
                        target="_blank"
                        icon="heroicon-o-printer"
                        size="sm"
                    >
                        Print Class Timetable
                    </x-filament::button>
                </div>
            </div>

            {{-- Timetable Grid: Rows = Days, Columns = Periods --}}
            @php
                $allPeriods = $this->getPeriods();
            @endphp
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <table class="w-full border-collapse">
                    <thead>
                        {{-- Period names row --}}
                        <tr class="bg-primary-600 dark:bg-primary-700">
                            <th class="border-r border-primary-500 px-3 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider w-28 sticky left-0 z-10 bg-primary-600 dark:bg-primary-700">
                                Day
                            </th>
                            @foreach($allPeriods as $period)
                                <th class="border-r border-primary-500 last:border-r-0 px-1 py-2 text-center text-xs font-semibold text-white uppercase tracking-wider min-w-[90px]
                                    {{ $period->isBreak() ? 'bg-primary-700 dark:bg-primary-800 min-w-[50px]' : '' }}">
                                    <div>{{ $period->short_name ?? $period->name }}</div>
                                    <div class="text-[10px] font-normal opacity-75 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800">
                        @foreach($this->getDays() as $day)
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                {{-- Day name + Set Day button --}}
                                <td class="border-r border-gray-200 dark:border-gray-700 px-3 py-2 sticky left-0 z-10 bg-white dark:bg-gray-800">
                                    <div class="font-semibold text-gray-900 dark:text-white text-sm">
                                        {{ $day }}
                                    </div>
                                    <button
                                        wire:click="openDayModal('{{ $day }}')"
                                        type="button"
                                        class="mt-1 px-2 py-0.5 text-[10px] font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 hover:bg-primary-100 dark:hover:bg-primary-800/40 rounded transition-colors"
                                    >
                                        Set Day
                                    </button>
                                </td>

                                {{-- Period cells --}}
                                @foreach($allPeriods as $period)
                                    <td class="border-r border-gray-200 dark:border-gray-700 last:border-r-0 px-1 py-1 text-center">
                                        @if($period->isBreak())
                                            <div class="h-16 flex items-center justify-center bg-amber-50 dark:bg-amber-900/20 rounded">
                                                <span class="text-amber-600 dark:text-amber-400 text-[10px] font-medium">
                                                    @switch($period->type)
                                                        @case('assembly') ASM @break
                                                        @case('tea_break') BRK @break
                                                        @case('lunch_break') LCH @break
                                                        @default BRK
                                                    @endswitch
                                                </span>
                                            </div>
                                        @else
                                            @php
                                                $entry = $timetableData[$period->id]['days'][$day] ?? null;
                                            @endphp
                                            <button
                                                wire:click="openEntryModal({{ $period->id }}, '{{ $day }}')"
                                                type="button"
                                                class="w-full h-16 p-1 rounded-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1
                                                    {{ $entry
                                                        ? 'bg-primary-50 dark:bg-primary-900/30 hover:bg-primary-100 dark:hover:bg-primary-800/40 border border-primary-200 dark:border-primary-700'
                                                        : 'bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 border-2 border-dashed border-gray-300 dark:border-gray-600'
                                                    }}"
                                            >
                                                @if($entry)
                                                    <div class="h-full flex flex-col justify-center">
                                                        <div class="text-xs font-semibold text-primary-700 dark:text-primary-300 truncate">
                                                            {{ $entry->subject?->name ?? '-' }}
                                                        </div>
                                                        <div class="text-[10px] text-gray-600 dark:text-gray-400 truncate mt-0.5">
                                                            {{ $entry->teacher?->name ?? '-' }}
                                                        </div>
                                                        @if($entry->room)
                                                            <div class="text-[9px] text-gray-500 truncate">
                                                                {{ $entry->room }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="h-full flex items-center justify-center">
                                                        <span class="text-gray-400 dark:text-gray-500 text-xs">+</span>
                                                    </div>
                                                @endif
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-700"></div>
                    <span>Assigned</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-gray-50 dark:bg-gray-700/50 border-2 border-dashed border-gray-300 dark:border-gray-600"></div>
                    <span>Empty (Click to add)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-amber-50 dark:bg-amber-900/20"></div>
                    <span>Break</span>
                </div>
            </div>
        </div>

        {{-- Entry Modal --}}
        @if($showEntryModal)
            <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 z-50 flex items-center justify-center p-4" wire:click.self="closeModal">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
                    {{-- Modal Header --}}
                    <div class="bg-primary-600 dark:bg-primary-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            {{ $editingEntryId ? 'Edit' : 'Add' }} Timetable Entry
                        </h3>
                        <p class="text-primary-100 text-sm mt-1">
                            {{ $editingDay }} - {{ $this->getEditingPeriodName() }}
                        </p>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-4">
                        {{-- Subject --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Subject
                            </label>
                            <select
                                wire:model="entrySubjectId"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">-- Select Subject --</option>
                                @foreach($this->getAvailableSubjects() as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Teacher --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Teacher
                            </label>
                            @if($this->isPrimaryClass())
                                {{-- Primary class: Show class teacher (read-only) --}}
                                <div class="w-full px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">
                                    {{ $this->getClassTeacherName() }}
                                </div>
                                <p class="mt-1 text-xs text-primary-600 dark:text-primary-400">
                                    <x-heroicon-o-information-circle class="w-3 h-3 inline"/> Primary class - using class teacher
                                </p>
                            @else
                                {{-- Secondary class: Show teacher dropdown --}}
                                <select
                                    wire:model="entryTeacherId"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">-- Select Teacher --</option>
                                    @foreach($this->getAvailableTeachers() as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Conflicts will be checked when saving
                                </p>
                            @endif
                        </div>

                        {{-- Room --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Room (Optional)
                            </label>
                            <input
                                type="text"
                                wire:model="entryRoom"
                                placeholder="e.g., Room 101, Lab A"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes (Optional)
                            </label>
                            <textarea
                                wire:model="entryNotes"
                                rows="2"
                                placeholder="Any additional notes..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            ></textarea>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex justify-between">
                        <div>
                            @if($editingEntryId)
                                <button
                                    type="button"
                                    wire:click="deleteEntry"
                                    class="px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors"
                                >
                                    Delete Entry
                                </button>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                wire:click="saveEntry"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors"
                            >
                                {{ $editingEntryId ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Day Assignment Modal --}}
        @if($showDayModal)
            <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 z-50 flex items-center justify-center p-4" wire:click.self="closeDayModal">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                    {{-- Modal Header --}}
                    <div class="bg-success-600 dark:bg-success-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            Set All Periods for {{ $assigningDay }}
                        </h3>
                        <p class="text-success-100 text-sm mt-1">
                            {{ $this->getSelectedClassName() }}
                        </p>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 overflow-y-auto flex-1">
                        {{-- Primary class info banner --}}
                        @if($this->isPrimaryClass())
                            <div class="mb-4 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-700">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-information-circle class="w-5 h-5 text-primary-500 flex-shrink-0"/>
                                    <div class="text-sm text-primary-700 dark:text-primary-300">
                                        <strong>Primary Class:</strong> {{ $this->getClassTeacherName() }} (Class Teacher) will be assigned to all periods.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-3">
                            @foreach($this->getLessonPeriods() as $period)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div class="w-28 flex-shrink-0">
                                            <div class="font-medium text-gray-900 dark:text-white text-sm">
                                                {{ $period->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}
                                            </div>
                                        </div>
                                        @if($this->isPrimaryClass())
                                            {{-- Primary class: Only subject selection --}}
                                            <div class="flex-1">
                                                <select
                                                    wire:model="dayAssignments.{{ $period->id }}.subject_id"
                                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                >
                                                    <option value="">-- Select Subject --</option>
                                                    @foreach($this->getAvailableSubjects() as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @else
                                            {{-- Secondary class: Subject + Teacher selection --}}
                                            <div class="flex-1 grid grid-cols-2 gap-2">
                                                <select
                                                    wire:model="dayAssignments.{{ $period->id }}.subject_id"
                                                    class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                >
                                                    <option value="">-- Subject --</option>
                                                    @foreach($this->getAvailableSubjects() as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <select
                                                    wire:model="dayAssignments.{{ $period->id }}.teacher_id"
                                                    class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                >
                                                    <option value="">-- Teacher --</option>
                                                    @foreach($this->getAvailableTeachers() as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(!$this->isPrimaryClass())
                            <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                                <div class="flex items-start gap-2">
                                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5"/>
                                    <div class="text-sm text-amber-700 dark:text-amber-300">
                                        <strong>Conflict Detection:</strong> The system will check for teacher conflicts before saving. Teachers cannot be assigned to two classes during the same period.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeDayModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            wire:click="saveDayAssignments"
                            class="px-4 py-2 text-sm font-medium text-white bg-success-600 rounded-lg hover:bg-success-700 transition-colors"
                        >
                            Save All Periods
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="mt-6 text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
            <x-heroicon-o-calendar-days class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500"/>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                No Class Selected
            </h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                Select an academic year and class section above to view and manage its timetable.
            </p>
        </div>
    @endif
</x-filament-panels::page>
