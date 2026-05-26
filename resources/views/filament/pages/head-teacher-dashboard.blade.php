<x-filament-panels::page>
    <div>
    @php $hd = $this->getHeadDashboardData(); @endphp

    <div class="relative overflow-hidden rounded-2xl shadow-2xl mb-8" style="background:linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%)">
        <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,%3Csvg width=%2240%22 height=%2240%22 viewBox=%220 0 40 40%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22%23ffffff%22%3E%3Cpath d=%22M20 20.5V18H0v-2h20V0h2v16h18v2H22v4.5a1.5 1.5 0 01-2 0z%22/%3E%3C/g%3E%3C/svg%3E')"></div>
        <div class="relative p-8">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                <div class="flex items-center gap-5">
                    @php $logoPath = \App\Models\SchoolSettings::first()?->school_logo; @endphp
                    @if($logoPath)
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-16 h-16 rounded-xl object-cover border-2 border-white/20 shadow-lg">
                    @else
                        <div class="w-16 h-16 rounded-xl bg-white/10 border-2 border-white/20 flex items-center justify-center text-white text-2xl font-bold shadow-lg">SF</div>
                    @endif
                    <div>
                        <p class="text-blue-200/60 text-xs font-semibold uppercase tracking-[0.15em]">{{ $hd['schoolName'] }}</p>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-white mt-1 tracking-tight">{{ $hd['sectionLabel'] }}</h1>
                        <p class="text-blue-300/50 text-sm mt-1">{{ $hd['term'] }} &middot; {{ $hd['year'] }}</p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-white/80 border border-white/10">{{ now()->format('l, d M Y') }}</span>
                    <span class="text-[10px] text-blue-300/40 uppercase tracking-wider">Executive Dashboard</span>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-8">
                @foreach([
                    ['val' => $hd['totalStudents'], 'label' => 'Students', 'sub' => '♂ '.$hd['maleStudents'].' &middot; ♀ '.$hd['femaleStudents'], 'color' => 'text-white', 'border' => 'border-white/10'],
                    ['val' => $hd['totalTeachers'], 'label' => 'Teachers', 'sub' => $hd['teachersOnLeave'].' on leave', 'color' => 'text-emerald-400', 'border' => 'border-emerald-500/20'],
                    ['val' => $hd['totalClasses'], 'label' => 'Classes', 'sub' => $hd['classesWithTeacher'].' with CT', 'color' => 'text-sky-400', 'border' => 'border-sky-500/20'],
                    ['val' => $hd['attRate'].'%', 'label' => 'Attendance', 'sub' => $hd['attPresent'].' present', 'color' => $hd['attRate'] >= 80 ? 'text-emerald-400' : ($hd['attRate'] >= 60 ? 'text-amber-400' : 'text-red-400'), 'border' => $hd['attRate'] >= 80 ? 'border-emerald-500/20' : 'border-red-500/20'],
                    ['val' => $hd['collectionRate'].'%', 'label' => 'Fees Collected', 'sub' => (100-$hd['collectionRate']).'% outstanding', 'color' => 'text-sky-400', 'border' => 'border-sky-500/20'],
                ] as $kpi)
                    <div class="bg-white/[0.06] backdrop-blur-sm rounded-xl p-5 text-center border {{ $kpi['border'] }} hover:bg-white/[0.1] transition-colors">
                        <div class="text-3xl md:text-4xl font-extrabold {{ $kpi['color'] }} tracking-tight">{{ $kpi['val'] }}</div>
                        <div class="text-[10px] text-blue-200/50 uppercase tracking-[0.12em] font-bold mt-2">{{ $kpi['label'] }}</div>
                        <div class="text-[10px] text-blue-300/30 mt-0.5">{!! $kpi['sub'] !!}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @php
        $alerts = collect();
        if ($hd['unmarkedClasses'] > 0) $alerts->push(['icon' => '⚠️', 'text' => $hd['unmarkedClasses'].' classes have not marked attendance', 'color' => 'warning']);
        if ($hd['pendingGrading'] > 0) $alerts->push(['icon' => '📝', 'text' => $hd['pendingGrading'].' submissions awaiting grading', 'color' => 'warning']);
        if ($hd['pendingLeave'] > 0) $alerts->push(['icon' => '📋', 'text' => $hd['pendingLeave'].' leave applications pending', 'color' => 'info']);
        if ($hd['openComplaints'] > 0) $alerts->push(['icon' => '🔴', 'text' => $hd['openComplaints'].' complaints require attention', 'color' => 'danger']);
    @endphp
    @if($alerts->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach($alerts as $alert)
            <div class="flex items-center gap-2 px-3 py-2 rounded-lg border text-sm font-medium {{ $alert['color'] === 'danger' ? 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:text-red-200' : ($alert['color'] === 'warning' ? 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:text-amber-200' : 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200') }}">
                <span>{{ $alert['icon'] }}</span> {{ $alert['text'] }}
            </div>
        @endforeach
    </div>
    @endif

    <x-filament::section class="mb-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-3"><div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center"><x-heroicon-m-clipboard-document-check class="w-3.5 h-3.5 text-emerald-600"/></div><span class="text-sm font-bold">Attendance Today</span></div>
                <div class="flex gap-3 mb-4">
                    @foreach([['val'=>$hd['attPresent'],'l'=>'Present','bg'=>'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200/50','t'=>'text-emerald-700 dark:text-emerald-400'],['val'=>$hd['attAbsent'],'l'=>'Absent','bg'=>'bg-red-50 dark:bg-red-900/20 border-red-200/50','t'=>'text-red-700 dark:text-red-400'],['val'=>$hd['attLate'],'l'=>'Late','bg'=>'bg-amber-50 dark:bg-amber-900/20 border-amber-200/50','t'=>'text-amber-700 dark:text-amber-400'],['val'=>$hd['attSick'],'l'=>'Sick','bg'=>'bg-blue-50 dark:bg-blue-900/20 border-blue-200/50','t'=>'text-blue-700 dark:text-blue-400'],['val'=>$hd['unmarkedClasses'],'l'=>'Unmarked','bg'=>'bg-gray-50 dark:bg-gray-800 border-gray-200/50','t'=>'text-gray-700 dark:text-gray-400']] as $ac)
                        <div class="flex-1 text-center p-3 rounded-xl border {{ $ac['bg'] }}"><div class="text-2xl font-extrabold {{ $ac['t'] }}">{{ $ac['val'] }}</div><div class="text-[10px] {{ $ac['t'] }} uppercase font-bold tracking-wider mt-1">{{ $ac['l'] }}</div></div>
                    @endforeach
                </div>
                @if(!empty($hd['weeklyAtt']))
                <div class="text-xs text-gray-500 uppercase font-semibold mb-2">Weekly Trend</div>
                <div class="flex items-end gap-2 h-16">
                    @foreach($hd['weeklyAtt'] as $day)
                        @php $barH = max(6, ($day['rate'] / 100) * 56); @endphp
                        <div class="flex-1 flex flex-col items-center gap-0.5">
                            <span class="text-[10px] font-bold {{ $day['rate'] >= 80 ? 'text-emerald-600' : ($day['rate'] >= 60 ? 'text-amber-600' : 'text-red-600') }}">{{ $day['rate'] }}%</span>
                            <div class="w-full rounded-t {{ $day['rate'] >= 80 ? 'bg-emerald-500' : ($day['rate'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}" style="height:{{ $barH }}px"></div>
                            <span class="text-[9px] text-gray-400">{{ $day['day'] }}</span>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="lg:w-48 flex flex-col items-center justify-center lg:border-l lg:pl-6 border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 uppercase font-semibold mb-2">Fee Collection</div>
                <div class="relative w-24 h-24">
                    <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 120 120"><circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="8" class="text-gray-200 dark:text-gray-700"/><circle cx="60" cy="60" r="52" fill="none" stroke-width="8" stroke-linecap="round" class="{{ $hd['collectionRate'] >= 80 ? 'text-emerald-500' : ($hd['collectionRate'] >= 50 ? 'text-sky-500' : 'text-red-500') }}" stroke-dasharray="{{ 2 * 3.14159 * 52 }}" stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $hd['collectionRate'] / 100) }}"/></svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center"><span class="text-xl font-extrabold {{ $hd['collectionRate'] >= 80 ? 'text-emerald-600' : ($hd['collectionRate'] >= 50 ? 'text-sky-600' : 'text-red-600') }}">{{ $hd['collectionRate'] }}%</span></div>
                </div>
                <div class="flex gap-3 mt-3 text-xs">
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $hd['feesPaid'] }}</span>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $hd['feesPartial'] }}</span>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ $hd['feesUnpaid'] }}</span>
                </div>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section class="mb-6">
        <div class="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">
            <div class="flex-1 p-4">
                <div class="flex items-center gap-2 mb-3"><div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center"><x-heroicon-m-clipboard-document-list class="w-3.5 h-3.5 text-amber-600"/></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">Homework</span></div>
                <div class="flex gap-4">
                    <div class="text-center"><div class="text-2xl font-extrabold text-primary-600">{{ $hd['totalHomework'] }}</div><div class="text-[10px] text-gray-400 uppercase">Active</div></div>
                    <div class="text-center"><div class="text-2xl font-extrabold {{ $hd['hwOverdue'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $hd['hwOverdue'] }}</div><div class="text-[10px] text-gray-400 uppercase">Overdue</div></div>
                    <div class="text-center"><div class="text-2xl font-extrabold {{ $hd['pendingGrading'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $hd['pendingGrading'] }}</div><div class="text-[10px] text-gray-400 uppercase">To Grade</div></div>
                </div>
            </div>
            <div class="flex-1 p-4">
                <div class="flex items-center gap-2 mb-3"><div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center"><x-heroicon-m-user-group class="w-3.5 h-3.5 text-indigo-600"/></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">Staff & Leave</span></div>
                <div class="flex gap-4">
                    <div class="text-center"><div class="text-2xl font-extrabold text-primary-600">{{ $hd['totalTeachers'] }}</div><div class="text-[10px] text-gray-400 uppercase">Teachers</div></div>
                    <div class="text-center"><div class="text-2xl font-extrabold {{ $hd['teachersOnLeave'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $hd['teachersOnLeave'] }}</div><div class="text-[10px] text-gray-400 uppercase">On Leave</div></div>
                    <div class="text-center"><div class="text-2xl font-extrabold {{ $hd['pendingLeave'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $hd['pendingLeave'] }}</div><div class="text-[10px] text-gray-400 uppercase">Pending</div></div>
                </div>
            </div>
            <div class="flex-1 p-4">
                <div class="flex items-center gap-2 mb-3"><div class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center"><x-heroicon-m-academic-cap class="w-3.5 h-3.5 text-purple-600"/></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">CPD</span></div>
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 flex-shrink-0"><svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 120 120"><circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="10" class="text-gray-200 dark:text-gray-700"/><circle cx="60" cy="60" r="52" fill="none" stroke-width="10" stroke-linecap="round" class="text-purple-500" stroke-dasharray="{{ 2 * 3.14159 * 52 }}" stroke-dashoffset="{{ 2 * 3.14159 * 52 * (1 - $hd['cpdRate'] / 100) }}"/></svg><div class="absolute inset-0 flex items-center justify-center"><span class="text-sm font-extrabold text-purple-600">{{ $hd['cpdRate'] }}%</span></div></div>
                    <div><div class="text-sm"><span class="font-bold text-purple-600">{{ $hd['cpdCompliant'] }}</span><span class="text-gray-400">/{{ $hd['cpdTotal'] }}</span></div><div class="text-[10px] text-gray-400">at 40h target</div></div>
                </div>
            </div>
        </div>
    </x-filament::section>

    @if(!empty($hd['gradeAttendance']))
    <x-filament::section heading="Attendance by Grade" icon="heroicon-o-chart-bar" collapsible class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
            @foreach($hd['gradeAttendance'] as $ga)
                <div>
                    <div class="flex justify-between items-center mb-1"><span class="text-sm font-medium">{{ $ga['grade'] }} <span class="text-gray-400 text-xs">({{ $ga['students'] }})</span></span><span class="text-sm font-bold {{ $ga['rate'] >= 80 ? 'text-emerald-600' : ($ga['rate'] >= 60 ? 'text-amber-600' : 'text-red-600') }}">{{ $ga['rate'] }}%</span></div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2"><div class="h-2 rounded-full {{ $ga['rate'] >= 80 ? 'bg-emerald-500' : ($ga['rate'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ $ga['rate'] }}%"></div></div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        @if($hd['recentNotices'] && $hd['recentNotices']->count() > 0)
        <x-filament::section heading="Recent Notices" icon="heroicon-o-megaphone" collapsible>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($hd['recentNotices'] as $n)
                    <div class="flex items-center justify-between py-2.5">
                        <div class="flex-1 min-w-0 pr-3"><div class="text-sm font-medium truncate">{{ $n->title }}</div><div class="text-xs text-gray-500">{{ $n->published_at?->diffForHumans() }}</div></div>
                        @if($n->priority === 'urgent')<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">URGENT</span>@elseif($n->priority === 'important')<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">IMPORTANT</span>@endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>
        @endif

        <x-filament::section heading="My Teachers ({{ $hd['teachers']->count() }})" icon="heroicon-o-user-group" collapsible collapsed>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($hd['teachers']->sortBy('name') as $t)
                    <div class="flex items-center justify-between py-2">
                        <div><div class="text-sm font-medium">{{ $t->name }}@if($t->is_class_teacher)<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 ml-1">CT</span>@endif</div><div class="text-xs text-gray-500">{{ $t->grade?->name }} {{ $t->classSection?->name }}</div></div>
                        @if($t->phone)<a href="tel:{{ $t->phone }}" class="text-xs text-primary-600 hover:underline">{{ $t->phone }}</a>@endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
    </div>
</x-filament-panels::page>
