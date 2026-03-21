@extends('pdf.guides._layout')

@section('content')
    <div class="section-title">Overview</div>
    <p>The Attendance Records page shows all recorded attendance for your class. You can view, search, and filter attendance records by date, student, and status.</p>

    <div class="section-title">Navigating Attendance Records</div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">1</div></div>
            <div class="step-title-cell"><div class="step-title">View Attendance Table</div></div>
        </div>
        <div class="step-body">
            <p>The table shows all attendance records for your class, with the most recent date first. Each row shows:</p>
            <ul>
                <li><strong>Date</strong> &mdash; The attendance date</li>
                <li><strong>Student</strong> &mdash; Student name</li>
                <li><strong>Class</strong> &mdash; Grade and section</li>
                <li><strong>Status</strong> &mdash; Present, Absent, Sick, Late, or Excused</li>
                <li><strong>Check In / Out</strong> &mdash; Time records (if applicable)</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">2</div></div>
            <div class="step-title-cell"><div class="step-title">Filter Records</div></div>
        </div>
        <div class="step-body">
            <p>Click the <strong>Filter</strong> icon to narrow down records:</p>
            <ul>
                <li><strong>Class Section</strong> &mdash; Filter by specific section</li>
                <li><strong>Status</strong> &mdash; Show only Present, Absent, Sick, Late, or Excused</li>
                <li><strong>Date Range</strong> &mdash; Set a From and To date to view a specific period</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">3</div></div>
            <div class="step-title-cell"><div class="step-title">Edit Attendance</div></div>
        </div>
        <div class="step-body">
            <p>To correct an attendance record, click the <strong>pencil icon</strong> (Edit) on that row.</p>
            <p>Update the status and click <strong>Save</strong>.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">4</div></div>
            <div class="step-title-cell"><div class="step-title">Bulk Actions</div></div>
        </div>
        <div class="step-body">
            <p>Select multiple records using the checkboxes, then use bulk actions to:</p>
            <ul>
                <li><strong>Mark as Present</strong> &mdash; Set all selected to Present</li>
                <li><strong>Mark as Absent</strong> &mdash; Set all selected to Absent</li>
                <li><strong>Mark as Late</strong> &mdash; Set all selected to Late</li>
            </ul>
        </div>
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> Use the <strong>Daily Attendance</strong> page (from the sidebar) to mark attendance for the whole class at once. This page is better for reviewing and correcting individual records.
    </div>
@endsection
