<x-filament-panels::page>
    @php
        $teacher = $this->getTeacher();
        $isPrimary = $this->isPrimaryTeacher();
        $assignments = $this->getTeachingAssignments();
        $totalStudents = $this->getStudentCount();
        $summary = $this->getGradingSummary();
        $totalClasses = $assignments->count();
        $totalSubjects = $assignments->flatMap(fn($a) => $a['subjects'])->unique('id')->count();
        $myClassData = $isPrimary ? $this->getMyClassData() : null;
    @endphp

    @if($isPrimary && $myClassData)
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- PRIMARY TEACHER DASHBOARD - Single class, all subjects        --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        @php
            $cs = $myClassData['classSection'];
            $gradeName = $cs->grade->name ?? 'Unknown';
            $sectionName = $cs->name;
            $att = $myClassData['attendance'];
            $classResults = $this->getMyClassRecentResults();
        @endphp

        {{-- Class Identity Banner --}}
        <div class="sfa-td-primary-banner">
            <div class="sfa-td-primary-banner-left">
                <div class="sfa-td-primary-banner-icon">
                    <x-heroicon-s-home class="w-7 h-7" />
                </div>
                <div>
                    <h2 class="sfa-td-primary-banner-title">{{ $gradeName }} - {{ $sectionName }}</h2>
                    <div class="sfa-td-primary-banner-sub">
                        <span class="sfa-td-badge sfa-td-badge--green">Class Teacher</span>
                        <span style="color: #6b7280; font-size: 0.78rem;">{{ $myClassData['subjects']->count() }} subjects</span>
                    </div>
                </div>
            </div>
            <div class="sfa-td-primary-banner-actions">
                <a href="/admin/mark-attendance" class="sfa-td-primary-action">
                    <x-heroicon-o-clipboard-document-check class="w-4 h-4" />
                    Mark Attendance
                </a>
                <a href="/admin/enter-results" class="sfa-td-primary-action">
                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                    Enter Results
                </a>
                <a href="/admin/homework/create" class="sfa-td-primary-action">
                    <x-heroicon-o-document-plus class="w-4 h-4" />
                    Assign Homework
                </a>
            </div>
        </div>

        {{-- Quick Stats - Primary Context --}}
        <div class="sfa-td-stats sfa-td-stats--5col">
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(124,58,237,0.08);">
                    <x-heroicon-o-user-group class="w-5 h-5" style="color: #7c3aed;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $myClassData['totalStudents'] }}</span>
                    <span class="sfa-td-stat-label">Students</span>
                </div>
            </div>
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(37,99,235,0.08);">
                    <x-heroicon-o-user class="w-5 h-5" style="color: #2563eb;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $myClassData['maleCount'] }}</span>
                    <span class="sfa-td-stat-label">Boys</span>
                </div>
            </div>
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(219,39,119,0.08);">
                    <x-heroicon-o-user class="w-5 h-5" style="color: #db2777;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $myClassData['femaleCount'] }}</span>
                    <span class="sfa-td-stat-label">Girls</span>
                </div>
            </div>
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(5,150,105,0.08);">
                    <x-heroicon-o-book-open class="w-5 h-5" style="color: #059669;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $myClassData['subjects']->count() }}</span>
                    <span class="sfa-td-stat-label">Subjects</span>
                </div>
            </div>
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(217,119,6,0.08);">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5" style="color: #d97706;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $summary['ungraded'] }}</span>
                    <span class="sfa-td-stat-label">To Grade</span>
                </div>
            </div>
        </div>

        {{-- Two Column: Attendance Today + Subjects --}}
        <div class="sfa-td-grid-2">
            {{-- Today's Attendance --}}
            <div class="sfa-td-section">
                <div class="sfa-td-section-header">
                    <h2 class="sfa-td-section-title">
                        <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                        Today's Attendance
                    </h2>
                    @if(!$att['recorded'])
                        <a href="/admin/mark-attendance" class="sfa-td-badge sfa-td-badge--amber" style="text-decoration:none;">
                            Not Recorded - Mark Now
                        </a>
                    @else
                        <span class="sfa-td-badge sfa-td-badge--green">Recorded</span>
                    @endif
                </div>
                @if($att['recorded'])
                    <div class="sfa-td-attendance-grid">
                        <div class="sfa-td-att-item sfa-td-att--green">
                            <span class="sfa-td-att-value">{{ $att['present'] }}</span>
                            <span class="sfa-td-att-label">Present</span>
                        </div>
                        <div class="sfa-td-att-item sfa-td-att--red">
                            <span class="sfa-td-att-value">{{ $att['absent'] }}</span>
                            <span class="sfa-td-att-label">Absent</span>
                        </div>
                        <div class="sfa-td-att-item sfa-td-att--amber">
                            <span class="sfa-td-att-value">{{ $att['late'] }}</span>
                            <span class="sfa-td-att-label">Late</span>
                        </div>
                        <div class="sfa-td-att-item sfa-td-att--blue">
                            <span class="sfa-td-att-value">{{ $att['total'] }}</span>
                            <span class="sfa-td-att-label">Total</span>
                        </div>
                    </div>
                    @php
                        $attendanceRate = $att['total'] > 0 ? round(($att['present'] / $att['total']) * 100) : 0;
                    @endphp
                    <div style="padding: 10px 18px;">
                        <div class="sfa-td-progress-bar">
                            <div class="sfa-td-progress-fill" style="width: {{ $attendanceRate }}%"></div>
                        </div>
                        <span style="font-size:0.7rem; color:#6b7280;">{{ $attendanceRate }}% attendance rate</span>
                    </div>
                @else
                    <div class="sfa-td-empty" style="padding: 24px 16px;">
                        <x-heroicon-o-clipboard-document-check class="w-8 h-8 opacity-30" />
                        <p>Attendance not yet recorded for today</p>
                        <a href="/admin/mark-attendance" class="sfa-td-primary-action" style="margin-top:4px;">Mark Attendance</a>
                    </div>
                @endif
            </div>

            {{-- My Subjects --}}
            <div class="sfa-td-section">
                <div class="sfa-td-section-header">
                    <h2 class="sfa-td-section-title">
                        <x-heroicon-o-book-open class="w-5 h-5" />
                        My Subjects
                    </h2>
                    <span class="sfa-td-section-badge">{{ $myClassData['subjects']->count() }} subjects</span>
                </div>
                @if($myClassData['subjects']->isNotEmpty())
                    <div style="padding: 12px 14px;">
                        <div class="sfa-td-subject-list">
                            @foreach($myClassData['subjects'] as $subject)
                                <div class="sfa-td-subject-row">
                                    <span class="sfa-td-subject-name">{{ $subject->name }}</span>
                                    @if($subject->code)
                                        <span class="sfa-td-badge sfa-td-badge--blue" style="font-size:0.6rem;">{{ $subject->code }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="sfa-td-empty-sm">
                        <p>No subjects assigned yet</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Student Roster --}}
        <div class="sfa-td-section">
            <div class="sfa-td-section-header">
                <h2 class="sfa-td-section-title">
                    <x-heroicon-o-user-group class="w-5 h-5" />
                    My Students
                </h2>
                <span class="sfa-td-section-badge">{{ $myClassData['totalStudents'] }} students</span>
            </div>
            @if($myClassData['students']->isNotEmpty())
                <div class="sfa-td-roster">
                    <table class="sfa-td-roster-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Student ID</th>
                                <th>Parent/Guardian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myClassData['students'] as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span style="font-weight:600; color:#111827;" class="sfa-td-student-name">{{ $student->name }}</span>
                                    </td>
                                    <td>
                                        <span class="sfa-td-badge {{ $student->gender === 'male' ? 'sfa-td-badge--blue' : 'sfa-td-badge--pink' }}" style="font-size:0.6rem;">
                                            {{ ucfirst($student->gender ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td style="color:#6b7280; font-size:0.78rem;">{{ $student->student_id ?? $student->admission_number ?? '-' }}</td>
                                    <td style="color:#6b7280; font-size:0.78rem;">{{ $student->parentGuardian->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="sfa-td-empty">
                    <x-heroicon-o-user-group class="w-10 h-10 opacity-30" />
                    <p>No students in this class</p>
                </div>
            @endif
        </div>

        {{-- Performance Overview (if results exist) --}}
        @if(!empty($classResults))
            <div class="sfa-td-section">
                <div class="sfa-td-section-header">
                    <h2 class="sfa-td-section-title">
                        <x-heroicon-o-chart-bar class="w-5 h-5" />
                        Class Performance by Subject
                    </h2>
                </div>
                <div style="padding: 12px 14px;">
                    @foreach($classResults as $result)
                        <div class="sfa-td-perf-row">
                            <span class="sfa-td-perf-subject">{{ $result['subject'] }}</span>
                            <div class="sfa-td-perf-bar-wrap">
                                <div class="sfa-td-perf-bar">
                                    <div class="sfa-td-perf-bar-fill {{ $result['percentage'] >= 70 ? 'sfa-td-perf--good' : ($result['percentage'] >= 50 ? 'sfa-td-perf--mid' : 'sfa-td-perf--low') }}" style="width: {{ min($result['percentage'], 100) }}%"></div>
                                </div>
                                <span class="sfa-td-perf-pct">{{ $result['percentage'] }}%</span>
                            </div>
                            <span class="sfa-td-perf-meta">{{ $result['entries'] }} entries · avg {{ $result['avg_marks'] }}/{{ $result['total_marks'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @else
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- SECONDARY TEACHER DASHBOARD - Multiple classes, specific subj  --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}

        {{-- Quick Stats Bar --}}
        <div class="sfa-td-stats">
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(30,58,95,0.08);">
                    <x-heroicon-o-rectangle-stack class="w-5 h-5" style="color: #1e3a5f;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $totalClasses }}</span>
                    <span class="sfa-td-stat-label">{{ $totalClasses === 1 ? 'Class' : 'Classes' }}</span>
                </div>
            </div>
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(5,150,105,0.08);">
                    <x-heroicon-o-book-open class="w-5 h-5" style="color: #059669;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $totalSubjects }}</span>
                    <span class="sfa-td-stat-label">{{ $totalSubjects === 1 ? 'Subject' : 'Subjects' }}</span>
                </div>
            </div>
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(124,58,237,0.08);">
                    <x-heroicon-o-user-group class="w-5 h-5" style="color: #7c3aed;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $totalStudents }}</span>
                    <span class="sfa-td-stat-label">Students</span>
                </div>
            </div>
            <div class="sfa-td-stat">
                <div class="sfa-td-stat-icon" style="background: rgba(217,119,6,0.08);">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5" style="color: #d97706;" />
                </div>
                <div class="sfa-td-stat-info">
                    <span class="sfa-td-stat-value">{{ $summary['ungraded'] }}</span>
                    <span class="sfa-td-stat-label">To Grade</span>
                </div>
            </div>
        </div>

        {{-- My Teaching Assignments --}}
        <div class="sfa-td-section">
            <div class="sfa-td-section-header">
                <h2 class="sfa-td-section-title">
                    <x-heroicon-o-academic-cap class="w-5 h-5" />
                    My Teaching Assignments
                </h2>
                <span class="sfa-td-section-badge">{{ $totalClasses }} {{ $totalClasses === 1 ? 'class' : 'classes' }}</span>
            </div>

            @if($assignments->isEmpty())
                <div class="sfa-td-empty">
                    <x-heroicon-o-academic-cap class="w-10 h-10 opacity-30" />
                    <p>No classes assigned yet</p>
                </div>
            @else
                <div class="sfa-td-classes">
                    @foreach($assignments as $assignment)
                        @php
                            $cs = $assignment['classSection'];
                            $subjects = $assignment['subjects'];
                            $isClassTeacher = $assignment['isClassTeacher'];
                            $studentCount = $assignment['studentCount'];
                            $gradeName = $cs->grade->name ?? 'Unknown';
                            $sectionName = $cs->name;
                        @endphp
                        <div class="sfa-td-class-card {{ $isClassTeacher ? 'sfa-td-class-card--primary' : '' }}">
                            <div class="sfa-td-class-header">
                                <div class="sfa-td-class-name-wrap">
                                    <h3 class="sfa-td-class-name">{{ $gradeName }} - {{ $sectionName }}</h3>
                                    @if($isClassTeacher)
                                        <span class="sfa-td-badge sfa-td-badge--green">Class Teacher</span>
                                    @else
                                        <span class="sfa-td-badge sfa-td-badge--blue">Subject Teacher</span>
                                    @endif
                                </div>
                                <div class="sfa-td-class-students">
                                    <span class="sfa-td-class-students-count">{{ $studentCount }}</span>
                                    <span class="sfa-td-class-students-label">students</span>
                                </div>
                            </div>

                            @if($subjects->isNotEmpty())
                                <div class="sfa-td-subjects">
                                    @foreach($subjects as $subject)
                                        <span class="sfa-td-subject-tag">{{ $subject->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="sfa-td-class-actions">
                                <a href="/admin/mark-attendance" class="sfa-td-action-btn">
                                    <x-heroicon-o-clipboard-document-check class="w-3.5 h-3.5" />
                                    Attendance
                                </a>
                                <a href="/admin/enter-results" class="sfa-td-action-btn">
                                    <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                                    Results
                                </a>
                                <a href="/admin/homework/create" class="sfa-td-action-btn">
                                    <x-heroicon-o-document-plus class="w-3.5 h-3.5" />
                                    Homework
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- SHARED SECTIONS (both primary and secondary)                       --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}

    {{-- Two Column Layout: Grading + Homework --}}
    <div class="sfa-td-grid-2">
        {{-- Grading Overview --}}
        <div class="sfa-td-section">
            <div class="sfa-td-section-header">
                <h2 class="sfa-td-section-title">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                    Grading Overview
                </h2>
            </div>
            <div class="sfa-td-grading-grid">
                <div class="sfa-td-grading-item sfa-td-grading--blue">
                    <span class="sfa-td-grading-value">{{ $summary['total_submitted'] }}</span>
                    <span class="sfa-td-grading-label">Total</span>
                </div>
                <div class="sfa-td-grading-item sfa-td-grading--amber">
                    <span class="sfa-td-grading-value">{{ $summary['ungraded'] }}</span>
                    <span class="sfa-td-grading-label">Pending</span>
                </div>
                <div class="sfa-td-grading-item sfa-td-grading--green">
                    <span class="sfa-td-grading-value">{{ $summary['graded'] }}</span>
                    <span class="sfa-td-grading-label">Graded</span>
                </div>
                <div class="sfa-td-grading-item sfa-td-grading--red">
                    <span class="sfa-td-grading-value">{{ $summary['late'] }}</span>
                    <span class="sfa-td-grading-label">Late</span>
                </div>
            </div>
            @if($summary['ungraded'] > 0)
                <a href="/admin/homework-submissions" class="sfa-td-link">
                    Grade {{ $summary['ungraded'] }} pending {{ $summary['ungraded'] === 1 ? 'submission' : 'submissions' }} →
                </a>
            @endif
        </div>

        {{-- Active Homework --}}
        <div class="sfa-td-section">
            <div class="sfa-td-section-header">
                <h2 class="sfa-td-section-title">
                    <x-heroicon-o-document-text class="w-5 h-5" />
                    Active Homework
                </h2>
            </div>
            @forelse($this->getAssignedHomework()->take(5) as $homework)
                <div class="sfa-td-hw-item">
                    <div class="sfa-td-hw-date">
                        <span class="sfa-td-hw-day">{{ $homework->due_date->format('d') }}</span>
                        <span class="sfa-td-hw-month">{{ $homework->due_date->format('M') }}</span>
                    </div>
                    <div class="sfa-td-hw-info">
                        <span class="sfa-td-hw-title">{{ $homework->title }}</span>
                        <span class="sfa-td-hw-meta">
                            {{ $homework->subject->name }} · {{ $homework->grade->name }} · {{ $homework->submissions()->count() }} submitted
                        </span>
                    </div>
                    @if($homework->due_date->isPast())
                        <span class="sfa-td-badge sfa-td-badge--red" style="font-size:0.65rem;">Overdue</span>
                    @elseif($homework->due_date->isToday())
                        <span class="sfa-td-badge sfa-td-badge--amber" style="font-size:0.65rem;">Due Today</span>
                    @endif
                </div>
            @empty
                <div class="sfa-td-empty-sm">
                    <p>No active homework</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Submissions --}}
    <div class="sfa-td-section">
        <div class="sfa-td-section-header">
            <h2 class="sfa-td-section-title">
                <x-heroicon-o-inbox-arrow-down class="w-5 h-5" />
                Recent Submissions
            </h2>
        </div>
        @forelse($this->getRecentSubmissions() as $submission)
            <div class="sfa-td-sub-item">
                <div class="sfa-td-sub-avatar">
                    {{ strtoupper(substr($submission->student->name ?? '?', 0, 1)) }}
                </div>
                <div class="sfa-td-sub-info">
                    <span class="sfa-td-sub-name">{{ $submission->student->name }}</span>
                    <span class="sfa-td-sub-meta">
                        {{ $submission->homework->title }} · {{ $submission->homework->subject->name }}
                        · {{ $submission->submitted_at->diffForHumans() }}
                    </span>
                </div>
                <div class="sfa-td-sub-right">
                    @if($submission->is_late)
                        <span class="sfa-td-badge sfa-td-badge--red" style="font-size:0.6rem;">Late</span>
                    @endif
                    <span class="sfa-td-badge sfa-td-badge--amber">{{ ucfirst($submission->status) }}</span>
                    <a href="/admin/homework-submissions/{{ $submission->id }}/edit" class="sfa-td-action-btn" style="font-size:0.7rem;">
                        Grade →
                    </a>
                </div>
            </div>
        @empty
            <div class="sfa-td-empty-sm">
                <p>No recent submissions</p>
            </div>
        @endforelse
    </div>

    {{-- Upcoming Events --}}
    <div class="sfa-td-section">
        <div class="sfa-td-section-header">
            <h2 class="sfa-td-section-title">
                <x-heroicon-o-calendar-days class="w-5 h-5" />
                Upcoming Events
            </h2>
        </div>
        @forelse($this->getUpcomingEvents() as $event)
            <div class="sfa-td-event-item">
                <div class="sfa-td-event-date">
                    <span class="sfa-td-event-day">{{ $event->start_date->format('d') }}</span>
                    <span class="sfa-td-event-month">{{ $event->start_date->format('M') }}</span>
                </div>
                <div class="sfa-td-event-info">
                    <span class="sfa-td-event-title">{{ $event->title }}</span>
                    @if($event->description)
                        <span class="sfa-td-event-desc">{{ Str::limit($event->description, 80) }}</span>
                    @endif
                </div>
                <span class="sfa-td-badge sfa-td-badge--blue" style="font-size:0.65rem;">
                    {{ $event->is_all_day ? 'All Day' : $event->start_date->format('h:i A') }}
                </span>
            </div>
        @empty
            <div class="sfa-td-empty-sm">
                <p>No upcoming events</p>
            </div>
        @endforelse
    </div>

    @push('styles')
    <style>
        .sfa-td-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .sfa-td-stats { grid-template-columns: repeat(2, 1fr); }
        }
        .sfa-td-stat {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }
        .dark .sfa-td-stat {
            background: #1f2937;
            border-color: #374151;
        }
        .sfa-td-stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .sfa-td-stat-info {
            display: flex;
            flex-direction: column;
        }
        .sfa-td-stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #111827;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .dark .sfa-td-stat-value { color: #f9fafb; }
        .sfa-td-stat-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-top: 2px;
        }

        /* Sections */
        .sfa-td-section {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .dark .sfa-td-section {
            background: #1f2937;
            border-color: #374151;
        }
        .sfa-td-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .dark .sfa-td-section-header {
            background: #111827;
            border-color: #374151;
        }
        .sfa-td-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e3a5f;
            margin: 0;
        }
        .dark .sfa-td-section-title { color: #93c5fd; }
        .sfa-td-section-badge {
            font-size: 0.7rem;
            font-weight: 600;
            color: #6b7280;
            background: #e5e7eb;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .dark .sfa-td-section-badge {
            background: #374151;
            color: #9ca3af;
        }

        /* Class Cards */
        .sfa-td-classes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 12px;
            padding: 14px;
        }
        @media (max-width: 640px) {
            .sfa-td-classes { grid-template-columns: 1fr; }
        }
        .sfa-td-class-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow 0.15s;
        }
        .sfa-td-class-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .dark .sfa-td-class-card {
            border-color: #374151;
        }
        .sfa-td-class-card--primary {
            border-left: 3px solid #1e3a5f;
        }
        .dark .sfa-td-class-card--primary {
            border-left-color: #93c5fd;
        }
        .sfa-td-class-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 12px 14px 8px;
        }
        .sfa-td-class-name-wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .sfa-td-class-name {
            font-size: 0.92rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        .dark .sfa-td-class-name { color: #f9fafb; }
        .sfa-td-class-students {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 4px 10px;
            background: #f3f4f6;
            border-radius: 6px;
            flex-shrink: 0;
        }
        .dark .sfa-td-class-students { background: #374151; }
        .sfa-td-class-students-count {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e3a5f;
            line-height: 1;
        }
        .dark .sfa-td-class-students-count { color: #93c5fd; }
        .sfa-td-class-students-label {
            font-size: 0.6rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        /* Subject Tags */
        .sfa-td-subjects {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            padding: 0 14px 10px;
        }
        .sfa-td-subject-tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.68rem;
            font-weight: 600;
            background: #eff6ff;
            color: #1e40af;
            white-space: nowrap;
        }
        .dark .sfa-td-subject-tag {
            background: rgba(37,99,235,0.15);
            color: #93c5fd;
        }

        /* Class Actions */
        .sfa-td-class-actions {
            display: flex;
            gap: 0;
            border-top: 1px solid #e5e7eb;
        }
        .dark .sfa-td-class-actions { border-color: #374151; }
        .sfa-td-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            font-size: 0.72rem;
            font-weight: 600;
            color: #1e3a5f;
            text-decoration: none;
            flex: 1;
            justify-content: center;
            border-right: 1px solid #e5e7eb;
            transition: background 0.1s;
        }
        .sfa-td-action-btn:last-child { border-right: none; }
        .sfa-td-action-btn:hover {
            background: #f0f4ff;
            color: #1e3a5f;
        }
        .dark .sfa-td-action-btn {
            color: #93c5fd;
            border-color: #374151;
        }
        .dark .sfa-td-action-btn:hover {
            background: rgba(147,197,253,0.08);
        }

        /* Badges */
        .sfa-td-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .sfa-td-badge--green { background: #ecfdf5; color: #065f46; }
        .sfa-td-badge--blue { background: #eff6ff; color: #1e40af; }
        .sfa-td-badge--amber { background: #fffbeb; color: #92400e; }
        .sfa-td-badge--red { background: #fef2f2; color: #991b1b; }
        .dark .sfa-td-badge--green { background: rgba(5,150,105,0.15); color: #6ee7b7; }
        .dark .sfa-td-badge--blue { background: rgba(37,99,235,0.15); color: #93c5fd; }
        .dark .sfa-td-badge--amber { background: rgba(217,119,6,0.15); color: #fbbf24; }
        .dark .sfa-td-badge--red { background: rgba(220,38,38,0.15); color: #fca5a5; }

        /* Two Column Grid */
        .sfa-td-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 768px) {
            .sfa-td-grid-2 { grid-template-columns: 1fr; }
        }

        /* Grading Grid */
        .sfa-td-grading-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .dark .sfa-td-grading-grid { border-color: #374151; }
        .sfa-td-grading-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 14px 8px;
            border-right: 1px solid #e5e7eb;
        }
        .dark .sfa-td-grading-item { border-color: #374151; }
        .sfa-td-grading-item:last-child { border-right: none; }
        .sfa-td-grading-value {
            font-size: 1.3rem;
            font-weight: 700;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .sfa-td-grading-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-top: 3px;
            color: #6b7280;
        }
        .sfa-td-grading--blue .sfa-td-grading-value { color: #1e40af; }
        .sfa-td-grading--amber .sfa-td-grading-value { color: #d97706; }
        .sfa-td-grading--green .sfa-td-grading-value { color: #059669; }
        .sfa-td-grading--red .sfa-td-grading-value { color: #dc2626; }
        .dark .sfa-td-grading--blue .sfa-td-grading-value { color: #93c5fd; }
        .dark .sfa-td-grading--amber .sfa-td-grading-value { color: #fbbf24; }
        .dark .sfa-td-grading--green .sfa-td-grading-value { color: #6ee7b7; }
        .dark .sfa-td-grading--red .sfa-td-grading-value { color: #fca5a5; }

        .sfa-td-link {
            display: block;
            padding: 10px 18px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #1e3a5f;
            text-decoration: none;
        }
        .sfa-td-link:hover { text-decoration: underline; }
        .dark .sfa-td-link { color: #93c5fd; }

        /* Homework Items */
        .sfa-td-hw-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .sfa-td-hw-item { border-color: #374151; }
        .sfa-td-hw-item:last-child { border-bottom: none; }
        .sfa-td-hw-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 36px;
        }
        .sfa-td-hw-day {
            font-size: 1rem;
            font-weight: 700;
            color: #1e3a5f;
            line-height: 1;
        }
        .dark .sfa-td-hw-day { color: #93c5fd; }
        .sfa-td-hw-month {
            font-size: 0.6rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
        }
        .sfa-td-hw-info {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }
        .sfa-td-hw-title {
            font-size: 0.82rem;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark .sfa-td-hw-title { color: #f9fafb; }
        .sfa-td-hw-meta {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 1px;
        }

        /* Submission Items */
        .sfa-td-sub-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .sfa-td-sub-item { border-color: #374151; }
        .sfa-td-sub-item:last-child { border-bottom: none; }
        .sfa-td-sub-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #1e3a5f;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .sfa-td-sub-info {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }
        .sfa-td-sub-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #111827;
        }
        .dark .sfa-td-sub-name { color: #f9fafb; }
        .sfa-td-sub-meta {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 1px;
        }
        .sfa-td-sub-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        /* Event Items */
        .sfa-td-event-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .sfa-td-event-item { border-color: #374151; }
        .sfa-td-event-item:last-child { border-bottom: none; }
        .sfa-td-event-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 36px;
        }
        .sfa-td-event-day {
            font-size: 1rem;
            font-weight: 700;
            color: #1e3a5f;
            line-height: 1;
        }
        .dark .sfa-td-event-day { color: #93c5fd; }
        .sfa-td-event-month {
            font-size: 0.6rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
        }
        .sfa-td-event-info {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }
        .sfa-td-event-title {
            font-size: 0.82rem;
            font-weight: 600;
            color: #111827;
        }
        .dark .sfa-td-event-title { color: #f9fafb; }
        .sfa-td-event-desc {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 1px;
        }

        /* Empty States */
        .sfa-td-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 32px 16px;
            color: #9ca3af;
            font-size: 0.82rem;
        }
        .sfa-td-empty-sm {
            padding: 20px 18px;
            text-align: center;
            color: #9ca3af;
            font-size: 0.8rem;
        }

        /* ==========================================
           PRIMARY TEACHER STYLES
           ========================================== */

        /* 5-column stats */
        .sfa-td-stats--5col {
            grid-template-columns: repeat(5, 1fr);
        }
        @media (max-width: 768px) {
            .sfa-td-stats--5col { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 480px) {
            .sfa-td-stats--5col { grid-template-columns: repeat(2, 1fr); }
        }

        /* Primary Banner */
        .sfa-td-primary-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%);
            border-radius: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .sfa-td-primary-banner-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .sfa-td-primary-banner-icon {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }
        .sfa-td-primary-banner-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            margin: 0;
            line-height: 1.2;
        }
        .sfa-td-primary-banner-sub {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 4px;
        }
        .sfa-td-primary-banner-sub .sfa-td-badge--green {
            background: rgba(5,150,105,0.25);
            color: #6ee7b7;
        }
        .sfa-td-primary-banner-sub span {
            color: rgba(255,255,255,0.7) !important;
        }
        .sfa-td-primary-banner-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .sfa-td-primary-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .sfa-td-primary-action:hover {
            background: rgba(255,255,255,0.22);
            color: #fff;
        }
        @media (max-width: 640px) {
            .sfa-td-primary-banner { flex-direction: column; align-items: flex-start; }
            .sfa-td-primary-banner-actions { width: 100%; }
        }

        /* Attendance Grid */
        .sfa-td-attendance-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .dark .sfa-td-attendance-grid { border-color: #374151; }
        .sfa-td-att-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 14px 8px;
            border-right: 1px solid #e5e7eb;
        }
        .dark .sfa-td-att-item { border-color: #374151; }
        .sfa-td-att-item:last-child { border-right: none; }
        .sfa-td-att-value {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .sfa-td-att-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-top: 3px;
            color: #6b7280;
        }
        .sfa-td-att--green .sfa-td-att-value { color: #059669; }
        .sfa-td-att--red .sfa-td-att-value { color: #dc2626; }
        .sfa-td-att--amber .sfa-td-att-value { color: #d97706; }
        .sfa-td-att--blue .sfa-td-att-value { color: #1e40af; }
        .dark .sfa-td-att--green .sfa-td-att-value { color: #6ee7b7; }
        .dark .sfa-td-att--red .sfa-td-att-value { color: #fca5a5; }
        .dark .sfa-td-att--amber .sfa-td-att-value { color: #fbbf24; }
        .dark .sfa-td-att--blue .sfa-td-att-value { color: #93c5fd; }

        /* Progress Bar */
        .sfa-td-progress-bar {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 4px;
        }
        .dark .sfa-td-progress-bar { background: #374151; }
        .sfa-td-progress-fill {
            height: 100%;
            background: #059669;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Subject List */
        .sfa-td-subject-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .sfa-td-subject-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            background: #f9fafb;
            border-radius: 6px;
        }
        .dark .sfa-td-subject-row { background: #111827; }
        .sfa-td-subject-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #111827;
        }
        .dark .sfa-td-subject-name { color: #f9fafb; }

        /* Student Roster */
        .sfa-td-roster {
            overflow-x: auto;
        }
        .sfa-td-roster-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }
        .sfa-td-roster-table th {
            padding: 10px 14px;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .dark .sfa-td-roster-table th {
            background: #111827;
            color: #9ca3af;
            border-color: #374151;
        }
        .sfa-td-roster-table td {
            padding: 8px 14px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .dark .sfa-td-roster-table td {
            border-color: #374151;
            color: #d1d5db;
        }
        .dark .sfa-td-student-name { color: #f9fafb !important; }
        .sfa-td-roster-table tr:hover td {
            background: #f0f4ff;
        }
        .dark .sfa-td-roster-table tr:hover td {
            background: rgba(147,197,253,0.05);
        }

        /* Pink badge for girls */
        .sfa-td-badge--pink { background: #fdf2f8; color: #9d174d; }
        .dark .sfa-td-badge--pink { background: rgba(219,39,119,0.15); color: #f9a8d4; }

        /* Performance Bars */
        .sfa-td-perf-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .sfa-td-perf-row { border-color: #374151; }
        .sfa-td-perf-row:last-child { border-bottom: none; }
        .sfa-td-perf-subject {
            font-size: 0.82rem;
            font-weight: 600;
            color: #111827;
        }
        .dark .sfa-td-perf-subject { color: #f9fafb; }
        .sfa-td-perf-bar-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sfa-td-perf-bar {
            flex: 1;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        .dark .sfa-td-perf-bar { background: #374151; }
        .sfa-td-perf-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        .sfa-td-perf--good { background: #059669; }
        .sfa-td-perf--mid { background: #d97706; }
        .sfa-td-perf--low { background: #dc2626; }
        .sfa-td-perf-pct {
            font-size: 0.78rem;
            font-weight: 700;
            color: #111827;
            min-width: 36px;
            text-align: right;
        }
        .dark .sfa-td-perf-pct { color: #f9fafb; }
        .sfa-td-perf-meta {
            font-size: 0.68rem;
            color: #6b7280;
        }
    </style>
    @endpush
</x-filament-panels::page>
