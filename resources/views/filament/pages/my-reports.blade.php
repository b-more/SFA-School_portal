<x-filament-panels::page>
    @php
        $teacher = $this->getTeacher();
        $academicYear = \App\Models\AcademicYear::current();
        $currentTerm = \App\Models\Term::current();
        $studentStats = $this->getStudentStats();
        $attendanceStats = $this->getAttendanceStats();
        $homeworkStats = $this->getHomeworkStats();
        $performanceBySubject = $this->getPerformanceBySubject();
        $performanceByClass = $this->getPerformanceByClass();
    @endphp

    @if(!$teacher)
        <div class="text-center py-16">
            <x-heroicon-o-exclamation-triangle class="w-16 h-16 mx-auto text-warning-500 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Teacher Profile Not Found</h3>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Your user account is not linked to a teacher profile.</p>
        </div>
    @else

    {{-- Context Banner --}}
    <div class="mb-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-600 text-white">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $teacher->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($teacher->is_class_teacher && $teacher->classSection)
                            Class Teacher — {{ $teacher->classSection->grade->name ?? '' }} {{ $teacher->classSection->name }}
                        @else
                            Subject Teacher
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/70 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                    <x-heroicon-m-calendar class="w-4 h-4 text-indigo-500" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $academicYear?->name ?? 'N/A' }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/70 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                    <x-heroicon-m-clock class="w-4 h-4 text-green-500" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $currentTerm?->name ?? 'N/A' }}</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Top Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {{-- Total Students --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    <x-heroicon-o-user-group class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $studentStats['total'] }}</p>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Students</p>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-3 text-xs">
                <span class="text-blue-600 dark:text-blue-400"><strong>{{ $studentStats['boys'] }}</strong> Boys</span>
                <span class="text-pink-600 dark:text-pink-400"><strong>{{ $studentStats['girls'] }}</strong> Girls</span>
            </div>
        </div>

        {{-- Attendance Rate --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center
                    {{ $attendanceStats['term']['rate'] >= 90 ? 'bg-green-100 dark:bg-green-900/50' : ($attendanceStats['term']['rate'] >= 75 ? 'bg-amber-100 dark:bg-amber-900/50' : 'bg-red-100 dark:bg-red-900/50') }}">
                    <x-heroicon-o-check-circle class="w-6 h-6
                        {{ $attendanceStats['term']['rate'] >= 90 ? 'text-green-600 dark:text-green-400' : ($attendanceStats['term']['rate'] >= 75 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $attendanceStats['term']['rate'] }}%</p>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Attendance</p>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                {{ $attendanceStats['term']['days_marked'] }} {{ Str::plural('day', $attendanceStats['term']['days_marked']) }} marked this term
            </div>
        </div>

        {{-- Homework --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center">
                    <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $homeworkStats['total'] }}</p>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Homework</p>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-3 text-xs">
                <span class="text-green-600 dark:text-green-400"><strong>{{ $homeworkStats['active'] }}</strong> Active</span>
                <span class="text-gray-500 dark:text-gray-400"><strong>{{ $homeworkStats['past_due'] }}</strong> Past Due</span>
            </div>
        </div>

        {{-- Results --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center">
                    <x-heroicon-o-academic-cap class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    @php
                        $avgMarks = $performanceByClass->isNotEmpty() ? round($performanceByClass->avg('avg_marks'), 1) : 0;
                    @endphp
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $performanceByClass->isNotEmpty() ? $avgMarks . '%' : '--' }}
                    </p>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg Score</p>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                @if($performanceByClass->isNotEmpty())
                    {{ $performanceByClass->sum('student_count') }} students assessed
                @else
                    No results recorded yet
                @endif
            </div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Today's Attendance --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-green-600" />
                    <span>Today's Attendance</span>
                    <span class="text-xs text-gray-400 font-normal ml-1">{{ now()->format('l, M d') }}</span>
                </div>
            </x-slot>

            @if($attendanceStats['today']['marked'])
                <div class="grid grid-cols-4 gap-3 mb-4">
                    <div class="text-center p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $attendanceStats['today']['present'] }}</p>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-green-700 dark:text-green-300">Present</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $attendanceStats['today']['absent'] }}</p>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-red-700 dark:text-red-300">Absent</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $attendanceStats['today']['late'] }}</p>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">Late</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $attendanceStats['today']['sick'] }}</p>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">Sick</p>
                    </div>
                </div>

                @php
                    $todayTotal = $attendanceStats['today']['present'] + $attendanceStats['today']['absent'] + $attendanceStats['today']['late'] + $attendanceStats['today']['sick'];
                    $todayPresentRate = $todayTotal > 0 ? round((($attendanceStats['today']['present'] + $attendanceStats['today']['late']) / $todayTotal) * 100, 1) : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="flex-1 h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all
                            {{ $todayPresentRate >= 90 ? 'bg-green-500' : ($todayPresentRate >= 75 ? 'bg-amber-500' : 'bg-red-500') }}"
                             style="width: {{ $todayPresentRate }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $todayPresentRate }}%</span>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ $attendanceStats['today']['present'] + $attendanceStats['today']['late'] }} of {{ $todayTotal }} students present
                </p>
            @else
                <div class="text-center py-8">
                    <x-heroicon-o-clipboard-document class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Attendance not marked today</p>
                    <a href="{{ url('/admin/mark-attendance') }}" class="inline-flex items-center gap-1 mt-2 text-sm text-primary-600 hover:text-primary-500">
                        <x-heroicon-m-pencil-square class="w-4 h-4" />
                        Mark Attendance
                    </a>
                </div>
            @endif
        </x-filament::section>

        {{-- Term Attendance Summary --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-chart-pie class="w-5 h-5 text-indigo-600" />
                    <span>Term Attendance Summary</span>
                </div>
            </x-slot>

            @if($attendanceStats['term']['total_records'] > 0)
                @php
                    $term = $attendanceStats['term'];
                    $totalRec = $term['total_records'];
                    $presentPct = round(($term['present'] / $totalRec) * 100, 1);
                    $absentPct = round(($term['absent'] / $totalRec) * 100, 1);
                    $latePct = round(($term['late'] / $totalRec) * 100, 1);
                    $sickPct = round(($term['sick'] / $totalRec) * 100, 1);
                @endphp

                {{-- Stacked bar --}}
                <div class="mb-4">
                    <div class="flex h-6 rounded-full overflow-hidden">
                        @if($presentPct > 0)
                            <div class="bg-green-500 flex items-center justify-center" style="width: {{ $presentPct }}%" title="Present {{ $presentPct }}%">
                                @if($presentPct > 15)<span class="text-[10px] font-bold text-white">{{ $presentPct }}%</span>@endif
                            </div>
                        @endif
                        @if($latePct > 0)
                            <div class="bg-amber-400 flex items-center justify-center" style="width: {{ $latePct }}%" title="Late {{ $latePct }}%">
                                @if($latePct > 10)<span class="text-[10px] font-bold text-white">{{ $latePct }}%</span>@endif
                            </div>
                        @endif
                        @if($sickPct > 0)
                            <div class="bg-blue-400 flex items-center justify-center" style="width: {{ $sickPct }}%" title="Sick {{ $sickPct }}%">
                                @if($sickPct > 10)<span class="text-[10px] font-bold text-white">{{ $sickPct }}%</span>@endif
                            </div>
                        @endif
                        @if($absentPct > 0)
                            <div class="bg-red-400 flex items-center justify-center" style="width: {{ $absentPct }}%" title="Absent {{ $absentPct }}%">
                                @if($absentPct > 10)<span class="text-[10px] font-bold text-white">{{ $absentPct }}%</span>@endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Legend --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0"></span>
                        <span class="text-gray-600 dark:text-gray-400">Present</span>
                        <span class="ml-auto font-bold text-gray-900 dark:text-gray-100">{{ $term['present'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-3 h-3 rounded-full bg-red-400 flex-shrink-0"></span>
                        <span class="text-gray-600 dark:text-gray-400">Absent</span>
                        <span class="ml-auto font-bold text-gray-900 dark:text-gray-100">{{ $term['absent'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-3 h-3 rounded-full bg-amber-400 flex-shrink-0"></span>
                        <span class="text-gray-600 dark:text-gray-400">Late</span>
                        <span class="ml-auto font-bold text-gray-900 dark:text-gray-100">{{ $term['late'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-3 h-3 rounded-full bg-blue-400 flex-shrink-0"></span>
                        <span class="text-gray-600 dark:text-gray-400">Sick</span>
                        <span class="ml-auto font-bold text-gray-900 dark:text-gray-100">{{ $term['sick'] }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ $term['days_marked'] }} school {{ Str::plural('day', $term['days_marked']) }} recorded</span>
                    <span>{{ $totalRec }} total records</span>
                </div>
            @else
                <div class="text-center py-8">
                    <x-heroicon-o-chart-pie class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">No attendance data for this term yet</p>
                </div>
            @endif
        </x-filament::section>
    </div>

    {{-- Students by Class --}}
    @if($studentStats['by_class']->isNotEmpty())
    <x-filament::section class="mb-6" collapsible>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-user-group class="w-5 h-5 text-blue-600" />
                <span>Students by Class</span>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($studentStats['by_class'] as $classData)
                @php
                    $boysPercent = $classData->total > 0 ? round(($classData->boys / $classData->total) * 100) : 0;
                @endphp
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100">
                            {{ $classData->grade_name }} {{ $classData->section_name }}
                        </h4>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $classData->total }}</span>
                    </div>
                    {{-- Gender bar --}}
                    <div class="flex h-2 rounded-full overflow-hidden mb-2">
                        <div class="bg-blue-500" style="width: {{ $boysPercent }}%"></div>
                        <div class="bg-pink-500" style="width: {{ 100 - $boysPercent }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span class="text-blue-600 dark:text-blue-400">{{ $classData->boys }} boys</span>
                        <span class="text-pink-600 dark:text-pink-400">{{ $classData->girls }} girls</span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
    @endif

    {{-- Attendance by Class --}}
    @if($attendanceStats['by_class']->isNotEmpty())
    <x-filament::section class="mb-6" collapsible>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-table-cells class="w-5 h-5 text-green-600" />
                <span>Attendance by Class</span>
                <span class="text-xs text-gray-400 font-normal ml-1">{{ $currentTerm?->name ?? 'This Term' }}</span>
            </div>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                        <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Class</th>
                        <th class="text-center px-3 py-3 font-semibold text-green-600 dark:text-green-400">Present</th>
                        <th class="text-center px-3 py-3 font-semibold text-red-600 dark:text-red-400">Absent</th>
                        <th class="text-center px-3 py-3 font-semibold text-amber-600 dark:text-amber-400">Late</th>
                        <th class="text-center px-3 py-3 font-semibold text-blue-600 dark:text-blue-400">Sick</th>
                        <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">Days</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendanceStats['by_class'] as $classAtt)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                {{ $classAtt->grade_name }} {{ $classAtt->section_name }}
                            </td>
                            <td class="text-center px-3 py-3 text-green-600 dark:text-green-400 font-semibold">{{ $classAtt->present_count }}</td>
                            <td class="text-center px-3 py-3 text-red-600 dark:text-red-400 font-semibold">{{ $classAtt->absent_count }}</td>
                            <td class="text-center px-3 py-3 text-amber-600 dark:text-amber-400 font-semibold">{{ $classAtt->late_count }}</td>
                            <td class="text-center px-3 py-3 text-blue-600 dark:text-blue-400 font-semibold">{{ $classAtt->sick_count }}</td>
                            <td class="text-center px-3 py-3 text-gray-600 dark:text-gray-400">{{ $classAtt->days_marked }}</td>
                            <td class="text-right px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $classAtt->rate >= 90 ? 'bg-green-500' : ($classAtt->rate >= 75 ? 'bg-amber-500' : 'bg-red-500') }}"
                                             style="width: {{ $classAtt->rate }}%"></div>
                                    </div>
                                    <span class="font-bold text-gray-900 dark:text-gray-100 min-w-[3rem] text-right">{{ $classAtt->rate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
    @endif

    {{-- Homework by Subject --}}
    @if($homeworkStats['by_subject']->isNotEmpty())
    <x-filament::section class="mb-6" collapsible>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-purple-600" />
                <span>Homework by Subject</span>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($homeworkStats['by_subject'] as $subjectName => $stats)
                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center">
                        <x-heroicon-o-book-open class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $subjectName }}</h4>
                        <div class="flex items-center gap-2 mt-0.5 text-xs">
                            <span class="text-gray-500 dark:text-gray-400">{{ $stats['total'] }} total</span>
                            @if($stats['active'] > 0)
                                <span class="text-green-600 dark:text-green-400 font-semibold">{{ $stats['active'] }} active</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $stats['total'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
    @endif

    {{-- Academic Performance --}}
    <x-filament::section collapsible>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-academic-cap class="w-5 h-5 text-amber-600" />
                <span>Academic Performance</span>
                <span class="text-xs text-gray-400 font-normal ml-1">{{ $academicYear?->name ?? '' }}</span>
            </div>
        </x-slot>

        @if($performanceBySubject->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Subject</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">Exam</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">Students</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">Avg</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">High</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">Low</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Pass Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($performanceBySubject as $perf)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $perf->subject_name }}</td>
                                <td class="text-center px-3 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                        {{ ucfirst(str_replace('_', ' ', $perf->exam_type ?? 'N/A')) }}
                                    </span>
                                </td>
                                <td class="text-center px-3 py-3 text-gray-600 dark:text-gray-400">{{ $perf->student_count }}</td>
                                <td class="text-center px-3 py-3">
                                    <span class="font-bold {{ $perf->avg_marks >= 50 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($perf->avg_marks, 1) }}%
                                    </span>
                                </td>
                                <td class="text-center px-3 py-3 text-green-600 dark:text-green-400 font-semibold">{{ number_format($perf->max_marks, 0) }}%</td>
                                <td class="text-center px-3 py-3 text-red-600 dark:text-red-400 font-semibold">{{ number_format($perf->min_marks, 0) }}%</td>
                                <td class="text-right px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $perf->pass_rate >= 75 ? 'bg-green-500' : ($perf->pass_rate >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                                                 style="width: {{ $perf->pass_rate }}%"></div>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-gray-100 min-w-[3rem] text-right">{{ $perf->pass_rate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($performanceByClass->isNotEmpty())
            {{-- Show class-level performance if no subject-level data --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Class</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">Students</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">Avg</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">High</th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 dark:text-gray-300">Low</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Pass Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($performanceByClass as $class)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $class->grade_name }} {{ $class->section_name }}
                                </td>
                                <td class="text-center px-3 py-3 text-gray-600 dark:text-gray-400">{{ $class->student_count }}</td>
                                <td class="text-center px-3 py-3">
                                    <span class="font-bold {{ $class->avg_marks >= 50 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($class->avg_marks, 1) }}%
                                    </span>
                                </td>
                                <td class="text-center px-3 py-3 text-green-600 dark:text-green-400 font-semibold">{{ number_format($class->max_marks, 0) }}%</td>
                                <td class="text-center px-3 py-3 text-red-600 dark:text-red-400 font-semibold">{{ number_format($class->min_marks, 0) }}%</td>
                                <td class="text-right px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $class->pass_rate >= 75 ? 'bg-green-500' : ($class->pass_rate >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                                                 style="width: {{ $class->pass_rate }}%"></div>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-gray-100 min-w-[3rem] text-right">{{ $class->pass_rate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                <x-heroicon-o-academic-cap class="w-12 h-12 mx-auto text-gray-400 mb-3" />
                <p class="text-gray-500 dark:text-gray-400 font-medium">No results recorded yet</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Results will appear here once exam marks are entered.</p>
                <a href="{{ url('/admin/enter-results') }}" class="inline-flex items-center gap-1 mt-3 text-sm text-primary-600 hover:text-primary-500">
                    <x-heroicon-m-pencil-square class="w-4 h-4" />
                    Enter Results
                </a>
            </div>
        @endif
    </x-filament::section>

    @endif
</x-filament-panels::page>
