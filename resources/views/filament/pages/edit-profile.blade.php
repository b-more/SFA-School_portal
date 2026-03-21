<x-filament-panels::page>
{{-- ============================================================
     St. Francis of Assisi — Staff Profile
     Corporate Design System · DM Sans · Navy/Red
     ============================================================ --}}

<div class="sfa-dash">

{{-- ── Scoped Styles ── --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=JetBrains+Mono:wght@500;600;700&display=swap');

    .sfa-dash {
        --navy: 30,58,95;
        --red: 220,38,38;
        --green: 5,150,105;
        --amber: 217,119,6;
        --blue: 59,130,246;
        --purple: 124,58,237;
        --card-bg: 255,255,255;
        --card-border: 229,231,235;
        --text-primary: 17,24,39;
        --text-secondary: 107,114,128;
        --text-tertiary: 156,163,175;
        --surface: 249,250,251;
        font-family: 'DM Sans', sans-serif;
    }
    .dark .sfa-dash {
        --card-bg: 31,41,55;
        --card-border: 55,65,81;
        --text-primary: 243,244,246;
        --text-secondary: 156,163,175;
        --text-tertiary: 107,114,128;
        --surface: 17,24,39;
    }

    .sfa-profile-header {
        background: linear-gradient(135deg, rgb(var(--navy)) 0%, #2c5282 100%);
        border-radius: 16px;
        padding: 28px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .sfa-profile-header::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .sfa-profile-top {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }
    .sfa-avatar {
        width: 96px;
        height: 96px;
        border-radius: 16px;
        background: rgba(255,255,255,0.15);
        border: 3px solid rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }
    .sfa-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .sfa-profile-name { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
    .sfa-profile-role { font-size: 0.88rem; opacity: 0.8; margin-top: 4px; }
    .sfa-profile-badges { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
    .sfa-header-badge {
        background: rgba(255,255,255,0.15);
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }
    .sfa-badge-green-glow { background: rgba(52,211,153,0.2); border: 1px solid rgba(52,211,153,0.4); }

    .sfa-quick-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin-top: 20px;
        position: relative;
        z-index: 1;
    }
    .sfa-quick-stat {
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 14px 16px;
        backdrop-filter: blur(4px);
    }
    .sfa-quick-stat-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.04em; opacity: 0.7; font-weight: 500; }
    .sfa-quick-stat-value { font-size: 1rem; font-weight: 700; margin-top: 2px; }

    /* Tabs */
    .sfa-tabs {
        display: flex;
        gap: 4px;
        border-bottom: 2px solid rgb(var(--card-border));
        margin-top: 24px;
        margin-bottom: 20px;
        overflow-x: auto;
    }
    .sfa-tab {
        padding: 10px 20px;
        font-size: 0.85rem;
        font-weight: 600;
        color: rgb(var(--text-secondary));
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        white-space: nowrap;
        transition: all 0.15s;
        background: none;
        border-top: none; border-left: none; border-right: none;
    }
    .sfa-tab:hover { color: rgb(var(--text-primary)); }
    .sfa-tab.active { color: rgb(var(--navy)); border-bottom-color: rgb(var(--navy)); }
    .dark .sfa-tab.active { color: rgb(var(--blue)); border-bottom-color: rgb(var(--blue)); }
    [x-cloak] { display: none !important; }

    /* Cards */
    .sfa-card {
        background: rgb(var(--card-bg));
        border: 1px solid rgb(var(--card-border));
        border-radius: 14px;
        overflow: hidden;
    }
    .sfa-card + .sfa-card { margin-top: 16px; }
    .sfa-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid rgb(var(--card-border));
    }
    .sfa-card-title {
        font-size: 0.9rem; font-weight: 600; color: rgb(var(--text-primary));
        display: flex; align-items: center; gap: 8px;
    }
    .sfa-card-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* Info Grid */
    .sfa-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0;
    }
    .sfa-info-item {
        padding: 14px 20px;
        border-bottom: 1px solid rgba(var(--card-border), 0.5);
        border-right: 1px solid rgba(var(--card-border), 0.3);
    }
    .sfa-info-label {
        font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;
        color: rgb(var(--text-tertiary)); font-weight: 600; margin-bottom: 3px;
    }
    .sfa-info-value { font-size: 0.88rem; font-weight: 600; color: rgb(var(--text-primary)); }

    /* Document row */
    .sfa-doc-row {
        display: flex; align-items: center; padding: 14px 20px;
        border-bottom: 1px solid rgba(var(--card-border), 0.5);
        gap: 14px; transition: background 0.15s;
    }
    .sfa-doc-row:last-child { border-bottom: none; }
    .sfa-doc-row:hover { background: rgba(var(--surface), 0.5); }
    .sfa-doc-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .sfa-doc-icon-blue { background: rgba(var(--blue), 0.1); color: rgb(var(--blue)); }
    .dark .sfa-doc-icon-blue { background: rgba(var(--blue), 0.2); }
    .sfa-doc-info { flex: 1; min-width: 0; }
    .sfa-doc-name { font-size: 0.88rem; font-weight: 600; color: rgb(var(--text-primary)); }
    .sfa-doc-meta { font-size: 0.75rem; color: rgb(var(--text-secondary)); margin-top: 2px; }

    /* Badges */
    .sfa-badge {
        display: inline-flex; align-items: center; padding: 3px 10px;
        border-radius: 20px; font-size: 0.7rem; font-weight: 600; white-space: nowrap;
    }
    .sfa-badge-green { background: rgba(5,150,105,0.1); color: #059669; }
    .sfa-badge-red { background: rgba(220,38,38,0.1); color: #dc2626; }
    .sfa-badge-yellow { background: rgba(217,119,6,0.1); color: #d97706; }
    .sfa-badge-blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .dark .sfa-badge-green { background: rgba(5,150,105,0.2); color: #34d399; }
    .dark .sfa-badge-red { background: rgba(220,38,38,0.2); color: #f87171; }
    .dark .sfa-badge-yellow { background: rgba(217,119,6,0.2); color: #fbbf24; }
    .dark .sfa-badge-blue { background: rgba(59,130,246,0.2); color: #93c5fd; }

    /* Action buttons */
    .sfa-action-btn {
        display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px;
        border-radius: 8px; font-size: 0.75rem; font-weight: 600;
        text-decoration: none; transition: all 0.2s; border: none; cursor: pointer;
    }
    .sfa-action-btn svg { width: 14px; height: 14px; }
    .sfa-action-btn-view { background: rgba(var(--navy), 0.08); color: rgb(var(--navy)); }
    .sfa-action-btn-view:hover { background: rgba(var(--navy), 0.15); }
    .dark .sfa-action-btn-view { background: rgba(var(--blue), 0.15); color: rgb(var(--blue)); }
    .sfa-action-btn-download { background: rgba(var(--green), 0.1); color: rgb(var(--green)); }
    .sfa-action-btn-download:hover { background: rgba(var(--green), 0.18); }
    .dark .sfa-action-btn-download { background: rgba(var(--green), 0.2); color: #34d399; }

    /* Payslip Row */
    .sfa-payslip-row {
        display: flex; align-items: center; padding: 14px 20px;
        border-bottom: 1px solid rgba(var(--card-border), 0.5);
        gap: 14px; transition: background 0.15s;
    }
    .sfa-payslip-row:last-child { border-bottom: none; }
    .sfa-payslip-row:hover { background: rgba(var(--surface), 0.5); }
    .sfa-month-badge {
        min-width: 52px; text-align: center; background: rgba(var(--navy), 0.08);
        border-radius: 10px; padding: 8px 6px; flex-shrink: 0;
    }
    .dark .sfa-month-badge { background: rgba(var(--navy), 0.3); }
    .sfa-month-badge-month { font-size: 0.85rem; font-weight: 700; color: rgb(var(--navy)); }
    .dark .sfa-month-badge-month { color: rgb(var(--blue)); }
    .sfa-month-badge-year { font-size: 0.65rem; font-weight: 500; color: rgb(var(--text-secondary)); }
    .sfa-payslip-info { flex: 1; min-width: 0; }
    .sfa-payslip-title { font-size: 0.88rem; font-weight: 600; color: rgb(var(--text-primary)); }
    .sfa-payslip-sub { font-size: 0.75rem; color: rgb(var(--text-secondary)); margin-top: 2px; }
    .sfa-payslip-amount {
        font-family: 'JetBrains Mono', monospace; font-size: 1rem; font-weight: 700;
        color: rgb(var(--green)); text-align: right; min-width: 110px; flex-shrink: 0;
    }
    .sfa-payslip-amount-label { font-size: 0.65rem; color: rgb(var(--text-tertiary)); font-family: 'DM Sans', sans-serif; font-weight: 500; }

    /* Empty */
    .sfa-empty { padding: 48px 24px; text-align: center; color: rgb(var(--text-tertiary)); }
    .sfa-empty svg { width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.4; }
    .sfa-empty-title { font-size: 1rem; font-weight: 600; color: rgb(var(--text-secondary)); margin-bottom: 4px; }
    .sfa-empty-sub { font-size: 0.82rem; }

    .sfa-fade { opacity: 0; transform: translateY(12px); animation: sfaFadeUp 0.4s ease forwards; }
    @keyframes sfaFadeUp { to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 768px) {
        .sfa-profile-header { padding: 20px 16px; }
        .sfa-profile-name { font-size: 1.2rem; }
        .sfa-tabs { gap: 0; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .sfa-tab { padding: 8px 14px; font-size: 0.78rem; }
        .sfa-card-head { padding: 12px 16px; }
        .sfa-info-item { padding: 10px 16px; }
        .sfa-payslip-row, .sfa-doc-row { padding: 10px 14px; }
        .sfa-payslip-amount { font-size: 0.88rem; min-width: auto; }
    }
    @media (max-width: 640px) {
        .sfa-profile-top { flex-direction: column; text-align: center; }
        .sfa-profile-badges { justify-content: center; flex-wrap: wrap; }
        .sfa-header-badge { font-size: 0.68rem; padding: 3px 10px; }
        .sfa-quick-stats { grid-template-columns: 1fr 1fr; }
        .sfa-info-grid { grid-template-columns: 1fr; }
        .sfa-payslip-row, .sfa-doc-row { flex-wrap: wrap; gap: 8px; }
        .sfa-avatar { width: 72px; height: 72px; font-size: 2rem; border-radius: 14px; }
    }
    @media (max-width: 400px) {
        .sfa-quick-stats { grid-template-columns: 1fr; }
        .sfa-profile-badges { gap: 4px; }
    }
</style>

@php
    $user = auth()->user();
    $isParent = $this->isParent();
    $employee = $isParent ? null : $this->getEmployee();
    $teacher = $isParent ? null : $this->getTeacher();
    $isTeaching = $isParent ? false : $this->isTeachingStaff();
    $documents = $isParent ? collect() : $this->getDocuments();
    $payslips = $isParent ? collect() : $this->getPayslips();
    $parentGuardian = $isParent ? $this->getParentGuardian() : null;
    $children = $isParent ? $this->getChildren() : collect();
@endphp

{{-- ── Profile Header ── --}}
<div class="sfa-profile-header sfa-fade" style="animation-delay:0.05s">
    <div class="sfa-profile-top">
        <div class="sfa-avatar">
            @if($isParent)
                {{ strtoupper(substr($parentGuardian->name ?? $user->name, 0, 1)) }}
            @elseif($employee && $employee->profile_photo)
                <img src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->name }}">
            @elseif($teacher && $teacher->profile_photo)
                <img src="{{ Storage::url($teacher->profile_photo) }}" alt="{{ $teacher->name }}">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div style="flex:1;min-width:0">
            @if($isParent)
                <div class="sfa-profile-name">{{ $parentGuardian->name ?? $user->name }}</div>
                <div class="sfa-profile-role">
                    Parent / Guardian
                    @if($parentGuardian && $parentGuardian->relationship)
                        &middot; {{ ucfirst($parentGuardian->relationship) }}
                    @endif
                </div>
                <div class="sfa-profile-badges">
                    @if($parentGuardian && $parentGuardian->phone)
                        <span class="sfa-header-badge">{{ $parentGuardian->phone }}</span>
                    @endif
                    @if($parentGuardian && $parentGuardian->email)
                        <span class="sfa-header-badge">{{ $parentGuardian->email }}</span>
                    @endif
                    <span class="sfa-header-badge sfa-badge-green-glow">{{ $children->count() }} {{ $children->count() === 1 ? 'Child' : 'Children' }} Enrolled</span>
                </div>
            @else
                <div class="sfa-profile-name">{{ $employee->name ?? $teacher->name ?? $user->name }}</div>
                <div class="sfa-profile-role">
                    {{ $employee->position ?? ($isTeaching ? 'Teacher' : 'Staff') }}
                    @if($employee && $employee->department)
                        &middot; {{ ucfirst(str_replace('_', ' ', $employee->department)) }}
                    @elseif($teacher && $teacher->department)
                        &middot; {{ ucfirst(str_replace('_', ' ', $teacher->department)) }}
                    @endif
                </div>
                <div class="sfa-profile-badges">
                    @if($employee && $employee->employee_number)
                        <span class="sfa-header-badge">{{ $employee->employee_number }}</span>
                    @elseif($teacher && $teacher->employee_id)
                        <span class="sfa-header-badge">{{ $teacher->employee_id }}</span>
                    @endif
                    @if($employee && $employee->status === 'active')
                        <span class="sfa-header-badge sfa-badge-green-glow">Active</span>
                    @elseif($teacher && $teacher->is_active)
                        <span class="sfa-header-badge sfa-badge-green-glow">Active</span>
                    @endif
                    @if($employee && $employee->employment_type)
                        <span class="sfa-header-badge">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</span>
                    @endif
                    @if($isTeaching && $teacher)
                        @if($teacher->is_class_teacher)
                            <span class="sfa-header-badge sfa-badge-green-glow">Class Teacher</span>
                        @endif
                        @if($teacher->is_grade_teacher)
                            <span class="sfa-header-badge" style="background:rgba(124,58,237,0.2);border:1px solid rgba(124,58,237,0.4)">Grade Teacher</span>
                        @endif
                    @endif
                    <span class="sfa-header-badge">{{ $user->email }}</span>
                </div>
            @endif
        </div>
    </div>

    @if($isParent && $children->count())
    <div class="sfa-quick-stats">
        @foreach($children as $child)
            <div class="sfa-quick-stat">
                <div class="sfa-quick-stat-label">{{ $child->grade?->name ?? 'N/A' }}</div>
                <div class="sfa-quick-stat-value">{{ $child->name }}</div>
            </div>
        @endforeach
    </div>
    @elseif($employee)
    <div class="sfa-quick-stats">
        <div class="sfa-quick-stat">
            <div class="sfa-quick-stat-label">Joined</div>
            <div class="sfa-quick-stat-value">{{ $employee->joining_date ? $employee->joining_date->format('d M Y') : 'N/A' }}</div>
        </div>
        <div class="sfa-quick-stat">
            <div class="sfa-quick-stat-label">Years of Service</div>
            <div class="sfa-quick-stat-value">{{ $employee->years_of_service }} yrs</div>
        </div>
        <div class="sfa-quick-stat">
            <div class="sfa-quick-stat-label">Documents</div>
            <div class="sfa-quick-stat-value">{{ $documents->count() }}</div>
        </div>
        <div class="sfa-quick-stat">
            <div class="sfa-quick-stat-label">Payslips</div>
            <div class="sfa-quick-stat-value">{{ $payslips->count() }}</div>
        </div>
    </div>
    @elseif($isTeaching && $teacher)
    <div class="sfa-quick-stats">
        <div class="sfa-quick-stat">
            <div class="sfa-quick-stat-label">Joined</div>
            <div class="sfa-quick-stat-value">{{ $teacher->join_date ? $teacher->join_date->format('d M Y') : 'N/A' }}</div>
        </div>
        <div class="sfa-quick-stat">
            <div class="sfa-quick-stat-label">Qualification</div>
            <div class="sfa-quick-stat-value">{{ $teacher->qualification ?: 'N/A' }}</div>
        </div>
        @if($teacher->specialization)
        <div class="sfa-quick-stat">
            <div class="sfa-quick-stat-label">Specialization</div>
            <div class="sfa-quick-stat-value">{{ $teacher->specialization }}</div>
        </div>
        @endif
        <div class="sfa-quick-stat">
            <div class="sfa-quick-stat-label">Payslips</div>
            <div class="sfa-quick-stat-value">{{ $payslips->count() }}</div>
        </div>
    </div>
    @endif
</div>

{{-- ── Tabs ── --}}
<div class="sfa-fade" style="animation-delay:0.1s" x-data="{ tab: 'edit' }">
    @if($isParent)
    <div class="sfa-tabs">
        <button type="button" class="sfa-tab" :class="{ active: tab === 'edit' }" @click="tab = 'edit'">Edit Profile</button>
        <button type="button" class="sfa-tab" :class="{ active: tab === 'children' }" @click="tab = 'children'">
            My Children
            <span class="sfa-badge sfa-badge-blue" style="margin-left:6px">{{ $children->count() }}</span>
        </button>
    </div>
    @else
    <div class="sfa-tabs">
        <button type="button" class="sfa-tab" :class="{ active: tab === 'edit' }" @click="tab = 'edit'">Edit Profile</button>
        <button type="button" class="sfa-tab" :class="{ active: tab === 'personal' }" @click="tab = 'personal'">Personal Info</button>
        <button type="button" class="sfa-tab" :class="{ active: tab === 'employment' }" @click="tab = 'employment'">Employment</button>
        <button type="button" class="sfa-tab" :class="{ active: tab === 'documents' }" @click="tab = 'documents'">
            Documents
            @if($documents->count())
                <span class="sfa-badge sfa-badge-blue" style="margin-left:6px">{{ $documents->count() }}</span>
            @endif
        </button>
        <button type="button" class="sfa-tab" :class="{ active: tab === 'payslips' }" @click="tab = 'payslips'">
            Payslips
            @if($payslips->count())
                <span class="sfa-badge sfa-badge-green" style="margin-left:6px">{{ $payslips->count() }}</span>
            @endif
        </button>
    </div>
    @endif

    {{-- ── Edit Profile Tab ── --}}
    <div x-show="tab === 'edit'">
        @if($isParent && $parentGuardian)
            <form wire:submit.prevent="saveProfile">
                {{ $this->editForm }}
                <div class="mt-4 flex justify-end">
                    <x-filament::button type="submit" size="lg">
                        Save Changes
                    </x-filament::button>
                </div>
            </form>
        @elseif($employee || $teacher)
            <form wire:submit.prevent="saveProfile">
                {{ $this->editForm }}
                <div class="mt-4 flex justify-end">
                    <x-filament::button type="submit" size="lg">
                        Save Changes
                    </x-filament::button>
                </div>
            </form>
        @else
            <div class="sfa-card">
                <div class="sfa-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    <div class="sfa-empty-title">No Profile Record</div>
                    <div class="sfa-empty-sub">Your profile has not been set up yet. Please contact administration.</div>
                </div>
            </div>
        @endif
    </div>

    {{-- ── My Children Tab (Parent only) ── --}}
    @if($isParent)
    <div x-show="tab === 'children'" x-cloak>
        @foreach($children as $child)
            <div class="sfa-card">
                <div class="sfa-card-head">
                    <div class="sfa-card-title">
                        <span class="sfa-card-dot" style="background:rgb(var(--blue))"></span>
                        {{ $child->name }}
                    </div>
                    <span class="sfa-badge sfa-badge-green">{{ ucfirst($child->enrollment_status) }}</span>
                </div>
                <div class="sfa-info-grid">
                    <div class="sfa-info-item">
                        <div class="sfa-info-label">Grade</div>
                        <div class="sfa-info-value">{{ $child->grade?->name ?? 'N/A' }}</div>
                    </div>
                    <div class="sfa-info-item">
                        <div class="sfa-info-label">Class</div>
                        <div class="sfa-info-value">{{ $child->classSection?->name ?? 'N/A' }}</div>
                    </div>
                    <div class="sfa-info-item">
                        <div class="sfa-info-label">Date of Birth</div>
                        <div class="sfa-info-value">{{ $child->date_of_birth ? $child->date_of_birth->format('d M Y') : 'N/A' }}</div>
                    </div>
                    <div class="sfa-info-item">
                        <div class="sfa-info-label">Gender</div>
                        <div class="sfa-info-value">{{ $child->gender ? ucfirst($child->gender) : 'N/A' }}</div>
                    </div>
                    @if($child->medical_conditions)
                    <div class="sfa-info-item">
                        <div class="sfa-info-label">Medical Conditions</div>
                        <div class="sfa-info-value">{{ $child->medical_conditions }}</div>
                    </div>
                    @endif
                    @if($child->allergies)
                    <div class="sfa-info-item">
                        <div class="sfa-info-label">Allergies</div>
                        <div class="sfa-info-value">{{ $child->allergies }}</div>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($children->isEmpty())
            <div class="sfa-card">
                <div class="sfa-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    <div class="sfa-empty-title">No Children Enrolled</div>
                    <div class="sfa-empty-sub">Contact the school administration to enroll your children.</div>
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- ── Personal Info Tab (Read-only) ── --}}
    <div x-show="tab === 'personal'" x-cloak>
        @if($employee)
        <div class="sfa-card">
            <div class="sfa-card-head">
                <div class="sfa-card-title">
                    <span class="sfa-card-dot" style="background:rgb(var(--blue))"></span>
                    Personal Information
                </div>
            </div>
            <div class="sfa-info-grid">
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Full Name</div>
                    <div class="sfa-info-value">{{ $employee->name }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Email</div>
                    <div class="sfa-info-value">{{ $employee->email ?? $user->email }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Phone</div>
                    <div class="sfa-info-value">{{ $employee->phone ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Gender</div>
                    <div class="sfa-info-value">{{ $employee->gender ? ucfirst($employee->gender) : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Date of Birth</div>
                    <div class="sfa-info-value">{{ $employee->date_of_birth ? $employee->date_of_birth->format('d M Y') : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Marital Status</div>
                    <div class="sfa-info-value">{{ $employee->marital_status ? ucfirst($employee->marital_status) : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Nationality</div>
                    <div class="sfa-info-value">{{ $employee->nationality ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Address</div>
                    <div class="sfa-info-value">{{ $employee->full_address ?: 'Not set' }}</div>
                </div>
            </div>
        </div>

        <div class="sfa-card">
            <div class="sfa-card-head">
                <div class="sfa-card-title">
                    <span class="sfa-card-dot" style="background:rgb(var(--red))"></span>
                    Statutory Information
                </div>
            </div>
            <div class="sfa-info-grid">
                <div class="sfa-info-item">
                    <div class="sfa-info-label">NRC Number</div>
                    <div class="sfa-info-value">{{ $employee->nrc_number ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">NAPSA Number</div>
                    <div class="sfa-info-value">{{ $employee->napsa_number ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">TPIN Number</div>
                    <div class="sfa-info-value">{{ $employee->tpin_number ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">NHIMA Number</div>
                    <div class="sfa-info-value">{{ $employee->nhima_number ?: 'Not set' }}</div>
                </div>
            </div>
        </div>

        <div class="sfa-card">
            <div class="sfa-card-head">
                <div class="sfa-card-title">
                    <span class="sfa-card-dot" style="background:rgb(var(--amber))"></span>
                    Emergency Contact & Next of Kin
                </div>
            </div>
            <div class="sfa-info-grid">
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Emergency Contact</div>
                    <div class="sfa-info-value">{{ $employee->emergency_contact_name ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Emergency Phone</div>
                    <div class="sfa-info-value">{{ $employee->emergency_contact_phone ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Relationship</div>
                    <div class="sfa-info-value">{{ $employee->emergency_contact_relationship ? ucfirst($employee->emergency_contact_relationship) : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Next of Kin</div>
                    <div class="sfa-info-value">{{ $employee->next_of_kin_name ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Next of Kin Phone</div>
                    <div class="sfa-info-value">{{ $employee->next_of_kin_phone ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Next of Kin Address</div>
                    <div class="sfa-info-value">{{ $employee->next_of_kin_address ?: 'Not set' }}</div>
                </div>
            </div>
        </div>
        @else
        <div class="sfa-card">
            <div class="sfa-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                <div class="sfa-empty-title">No Employee Record</div>
                <div class="sfa-empty-sub">Your employee profile has not been set up yet. Please contact administration.</div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Employment Tab (Read-only) ── --}}
    <div x-show="tab === 'employment'" x-cloak>
        @if($employee)
        <div class="sfa-card">
            <div class="sfa-card-head">
                <div class="sfa-card-title">
                    <span class="sfa-card-dot" style="background:rgb(var(--navy))"></span>
                    Employment Details
                </div>
            </div>
            <div class="sfa-info-grid">
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Employee ID</div>
                    <div class="sfa-info-value">{{ $employee->employee_id ?: ($employee->employee_number ?: 'N/A') }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Department</div>
                    <div class="sfa-info-value">{{ $employee->department ? ucfirst(str_replace('_', ' ', $employee->department)) : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Position</div>
                    <div class="sfa-info-value">{{ $employee->position ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Employment Type</div>
                    <div class="sfa-info-value">{{ $employee->employment_type ? ucfirst(str_replace('_', ' ', $employee->employment_type)) : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Joining Date</div>
                    <div class="sfa-info-value">{{ $employee->joining_date ? $employee->joining_date->format('d M Y') : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Contract Start</div>
                    <div class="sfa-info-value">{{ $employee->contract_start_date ? $employee->contract_start_date->format('d M Y') : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Contract End</div>
                    <div class="sfa-info-value">{{ $employee->contract_end_date ? $employee->contract_end_date->format('d M Y') : 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Status</div>
                    <div class="sfa-info-value">
                        <span class="sfa-badge {{ $employee->status === 'active' ? 'sfa-badge-green' : 'sfa-badge-red' }}">{{ ucfirst($employee->status ?? 'unknown') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="sfa-card">
            <div class="sfa-card-head">
                <div class="sfa-card-title">
                    <span class="sfa-card-dot" style="background:rgb(var(--green))"></span>
                    Banking Details
                </div>
            </div>
            <div class="sfa-info-grid">
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Bank Name</div>
                    <div class="sfa-info-value">{{ $employee->bank_name ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Branch</div>
                    <div class="sfa-info-value">{{ $employee->bank_branch ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Account Name</div>
                    <div class="sfa-info-value">{{ $employee->bank_account_name ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Account Number</div>
                    <div class="sfa-info-value">{{ $employee->bank_account_number ?: 'Not set' }}</div>
                </div>
            </div>
        </div>

        <div class="sfa-card">
            <div class="sfa-card-head">
                <div class="sfa-card-title">
                    <span class="sfa-card-dot" style="background:rgb(var(--purple))"></span>
                    Qualifications
                </div>
            </div>
            <div class="sfa-info-grid">
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Highest Qualification</div>
                    <div class="sfa-info-value">{{ $employee->highest_qualification ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Institution</div>
                    <div class="sfa-info-value">{{ $employee->qualification_institution ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Year</div>
                    <div class="sfa-info-value">{{ $employee->qualification_year ?: 'Not set' }}</div>
                </div>
                <div class="sfa-info-item">
                    <div class="sfa-info-label">Professional Certifications</div>
                    <div class="sfa-info-value">{{ $employee->professional_certifications ?: 'Not set' }}</div>
                </div>
            </div>
        </div>
        @else
        <div class="sfa-card">
            <div class="sfa-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg>
                <div class="sfa-empty-title">No Employment Record</div>
                <div class="sfa-empty-sub">Your employment details have not been set up yet.</div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Documents Tab ── --}}
    <div x-show="tab === 'documents'" x-cloak>
        <div class="sfa-card">
            <div class="sfa-card-head">
                <div class="sfa-card-title">
                    <span class="sfa-card-dot" style="background:rgb(var(--blue))"></span>
                    My Documents
                </div>
                @if($documents->count())
                    <span class="sfa-badge sfa-badge-blue">{{ $documents->count() }} file{{ $documents->count() !== 1 ? 's' : '' }}</span>
                @endif
            </div>
            <div>
                @forelse($documents as $doc)
                    <div class="sfa-doc-row">
                        <div class="sfa-doc-icon sfa-doc-icon-blue">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div class="sfa-doc-info">
                            <div class="sfa-doc-name">{{ $doc->document_name ?: $doc->document_type_label }}</div>
                            <div class="sfa-doc-meta">
                                {{ $doc->document_type_label }}
                                @if($doc->formatted_file_size !== 'N/A') &middot; {{ $doc->formatted_file_size }} @endif
                                &middot; Uploaded {{ $doc->created_at->format('d M Y') }}
                            </div>
                        </div>
                        @if($doc->is_verified)
                            <span class="sfa-badge sfa-badge-green">Verified</span>
                        @else
                            <span class="sfa-badge sfa-badge-yellow">Pending</span>
                        @endif
                        @if($doc->expiry_date)
                            @if($doc->isExpired())
                                <span class="sfa-badge sfa-badge-red">Expired</span>
                            @elseif($doc->isExpiringSoon())
                                <span class="sfa-badge sfa-badge-yellow">Expiring Soon</span>
                            @endif
                        @endif
                        <div style="display:flex;gap:6px;">
                            @if($doc->file_path)
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="sfa-action-btn sfa-action-btn-view">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    View
                                </a>
                                <a href="{{ Storage::url($doc->file_path) }}" download class="sfa-action-btn sfa-action-btn-download">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    Download
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="sfa-empty">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9.75m3 0h3m-3 0v3m0-3v-3m-9.75 3h.008v.008H3.75V15zM4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v12.75A2.25 2.25 0 004.5 19.5z"/></svg>
                        <div class="sfa-empty-title">No Documents</div>
                        <div class="sfa-empty-sub">Your documents will appear here once uploaded by administration.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Payslips Tab ── --}}
    <div x-show="tab === 'payslips'" x-cloak>
        <div class="sfa-card">
            <div class="sfa-card-head">
                <div class="sfa-card-title">
                    <span class="sfa-card-dot" style="background:rgb(var(--green))"></span>
                    Salary History
                </div>
                @if($payslips->count())
                    <span class="sfa-badge sfa-badge-green">{{ $payslips->count() }} payslip{{ $payslips->count() !== 1 ? 's' : '' }}</span>
                @endif
            </div>
            <div>
                @forelse($payslips as $payslip)
                    <div class="sfa-payslip-row">
                        <div class="sfa-month-badge">
                            <div class="sfa-month-badge-month">{{ substr($payslip->month, 0, 3) }}</div>
                            <div class="sfa-month-badge-year">{{ $payslip->year }}</div>
                        </div>
                        <div class="sfa-payslip-info">
                            <div class="sfa-payslip-title">{{ $payslip->month }} {{ $payslip->year }}</div>
                            <div class="sfa-payslip-sub">
                                Gross: ZMW {{ number_format($payslip->gross_salary, 2) }}
                                &middot; Deductions: ZMW {{ number_format(collect($payslip->deductions ?? [])->sum('amount'), 2) }}
                            </div>
                        </div>
                        <div class="sfa-payslip-amount">
                            <div class="sfa-payslip-amount-label">Net Pay</div>
                            ZMW {{ number_format($payslip->net_salary, 2) }}
                        </div>
                        <span class="sfa-badge {{ $payslip->payment_status === 'paid' ? 'sfa-badge-green' : 'sfa-badge-yellow' }}">
                            {{ ucfirst($payslip->payment_status) }}
                        </span>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('payslips.view', $payslip) }}" target="_blank" class="sfa-action-btn sfa-action-btn-view">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                View
                            </a>
                            <a href="{{ route('payslips.download', $payslip) }}" class="sfa-action-btn sfa-action-btn-download">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                PDF
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="sfa-empty">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                        <div class="sfa-empty-title">No Payslips Yet</div>
                        <div class="sfa-empty-sub">Your payslips will appear here once generated by administration.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

</div>{{-- /.sfa-dash --}}
</x-filament-panels::page>
