<x-filament-panels::page>
    <style>
        .coverage-wrap { font-family: 'DejaVu Sans', 'Inter', Arial, sans-serif; }
        .coverage-summary {
            display: flex; gap: 14px; margin-bottom: 14px; flex-wrap: wrap;
        }
        .summary-tile {
            background: white; border: 1px solid #e5e7eb; border-radius: 8px;
            padding: 10px 16px; min-width: 130px; flex: 0 0 auto;
        }
        .dark .summary-tile { background: #1f2937; border-color: #374151; }
        .summary-tile .label {
            font-size: 10px; text-transform: uppercase; color: #6b7280;
            letter-spacing: 0.5px; margin: 0 0 3px;
        }
        .summary-tile .value {
            font-size: 20px; font-weight: 700; color: #0F2A44; margin: 0;
        }
        .dark .summary-tile .value { color: #e5e7eb; }

        .coverage-table {
            width: 100%; border-collapse: collapse;
            background: white; border-radius: 8px; overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .dark .coverage-table { background: #1f2937; }
        .coverage-table thead th {
            background: #0F2A44; color: white;
            padding: 10px 12px; text-align: left;
            font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px;
        }
        .coverage-table tbody td {
            padding: 12px; font-size: 13px;
            border-bottom: 1px solid #e5e7eb; color: #111827;
        }
        .dark .coverage-table tbody td { border-color: #374151; color: #e5e7eb; }
        .coverage-table tbody tr:last-child td { border-bottom: none; }
        .coverage-table tbody tr:hover td { background: #fbf7ec; }
        .dark .coverage-table tbody tr:hover td { background: #374151; }

        .subj-name { font-weight: 700; color: #0F2A44; }
        .dark .subj-name { color: #93c5fd; }
        .teacher-cell { color: #4b5563; }
        .dark .teacher-cell { color: #9ca3af; }
        .teacher-unassigned { color: #b91c1c; font-style: italic; }
        .teacher-source-hint {
            display: inline-block; font-size: 10px; color: #92400e;
            background: #fef3c7; padding: 1px 6px; border-radius: 8px;
            margin-left: 6px; font-weight: 600;
        }

        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 10px; border-radius: 12px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.3px;
        }
        .badge-full    { background: #dcfce7; color: #14532d; }
        .badge-partial { background: #fef3c7; color: #78350f; }
        .badge-missing { background: #fee2e2; color: #7f1d1d; }
        .badge-none    { background: #f3f4f6; color: #6b7280; }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        .dot-full    { background: #16a34a; }
        .dot-partial { background: #d97706; }
        .dot-missing { background: #dc2626; }
        .dot-none    { background: #9ca3af; }

        .empty-state {
            padding: 40px 20px; text-align: center; color: #6b7280;
            background: white; border-radius: 8px; border: 1px dashed #cbd5e1;
        }
        .dark .empty-state { background: #1f2937; border-color: #374151; }

        .legend {
            display: flex; gap: 18px; margin-top: 12px;
            font-size: 11px; color: #6b7280; align-items: center;
            flex-wrap: wrap;
        }
        .legend .item { display: inline-flex; align-items: center; gap: 6px; }
    </style>

    <div class="coverage-wrap">
        @if($classLabel && $studentCount > 0)
            <div class="coverage-summary">
                <div class="summary-tile">
                    <p class="label">Class</p>
                    <p class="value">{{ $classLabel }}</p>
                </div>
                <div class="summary-tile">
                    <p class="label">Active pupils</p>
                    <p class="value">{{ $studentCount }}</p>
                </div>
                <div class="summary-tile">
                    <p class="label">Subjects</p>
                    <p class="value">{{ count($rows) }}</p>
                </div>
                @php
                    $midFull = collect($rows)->filter(fn($r) => $r['mid_count'] >= $r['total'])->count();
                    $eotFull = collect($rows)->filter(fn($r) => $r['eot_count'] >= $r['total'])->count();
                @endphp
                <div class="summary-tile">
                    <p class="label">Mid-Term done</p>
                    <p class="value">{{ $midFull }} / {{ count($rows) }}</p>
                </div>
                <div class="summary-tile">
                    <p class="label">End-of-Term done</p>
                    <p class="value">{{ $eotFull }} / {{ count($rows) }}</p>
                </div>
            </div>
        @endif

        @if(empty($rows))
            <div class="empty-state">
                @if(! $classSectionId)
                    Select a class above to see the marks-entry coverage.
                @elseif($studentCount === 0)
                    This class has no active pupils enrolled.
                @else
                    No subjects are being taught or marked in this class yet.
                @endif
            </div>
        @else
            <table class="coverage-table">
                <thead>
                    <tr>
                        <th style="width:30%">Subject</th>
                        <th style="width:30%">Teacher</th>
                        <th style="width:20%">Mid-Term</th>
                        <th style="width:20%">End-of-Term</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $r)
                        <tr>
                            <td><span class="subj-name">{{ $r['subject_name'] }}</span></td>
                            <td class="teacher-cell">
                                @if($r['teacher_name'])
                                    {{ $r['teacher_name'] }}
                                    @if($r['assignment_source'] === 'recorded_by')
                                        <span class="teacher-source-hint" title="This teacher recorded marks, but no formal SubjectTeaching assignment exists">informal</span>
                                    @endif
                                @else
                                    <span class="teacher-unassigned">— not assigned —</span>
                                @endif
                            </td>
                            @php
                                $renderBadge = function($count, $total) {
                                    if ($total === 0) {
                                        return ['none', '—', 'none'];
                                    }
                                    if ($count === 0) {
                                        return ['missing', 'Not entered', 'missing'];
                                    }
                                    if ($count >= $total) {
                                        return ['full', "Complete · {$count}/{$total}", 'full'];
                                    }
                                    return ['partial', "Partial · {$count}/{$total}", 'partial'];
                                };
                                [$mCls, $mLabel, $mDot] = $renderBadge($r['mid_count'], $r['total']);
                                [$eCls, $eLabel, $eDot] = $renderBadge($r['eot_count'], $r['total']);
                            @endphp
                            <td>
                                <span class="badge badge-{{ $mCls }}">
                                    <span class="badge-dot dot-{{ $mDot }}"></span>
                                    {{ $mLabel }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $eCls }}">
                                    <span class="badge-dot dot-{{ $eDot }}"></span>
                                    {{ $eLabel }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="legend">
                <span class="item"><span class="badge-dot dot-full"></span> Complete — every pupil has a mark</span>
                <span class="item"><span class="badge-dot dot-partial"></span> Partial — some pupils are still missing</span>
                <span class="item"><span class="badge-dot dot-missing"></span> Not entered — no marks for this exam yet</span>
                <span class="item"><em>informal</em> — a teacher recorded marks without a formal assignment</span>
            </div>
        @endif
    </div>
</x-filament-panels::page>
