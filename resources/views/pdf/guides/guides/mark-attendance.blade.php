@extends('pdf.guides._layout')

@section('content')
    <div class="section-title">Overview</div>
    <p>The Daily Attendance page allows you to mark attendance for your class students. Your class is automatically selected and locked.</p>

    <div class="section-title">How to Mark Attendance</div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">1</div></div>
            <div class="step-title-cell"><div class="step-title">Verify Class and Date</div></div>
        </div>
        <div class="step-body">
            <p>When you open the page, your assigned class is already selected and cannot be changed.</p>
            <p>The <strong>Date</strong> defaults to today. You can change it to mark attendance for a previous date if needed (you cannot select future dates).</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">2</div></div>
            <div class="step-title-cell"><div class="step-title">View Student List</div></div>
        </div>
        <div class="step-body">
            <p>Below the class and date fields, you'll see all your students listed in alphabetical order. Each student shows their name and current attendance status.</p>
            <p>By default, all students are marked as <strong>Present</strong> (shown in green).</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">3</div></div>
            <div class="step-title-cell"><div class="step-title">Change Student Status</div></div>
        </div>
        <div class="step-body">
            <p>To change a student's status, click on their status button. Each click cycles through:</p>
            <ul>
                <li><strong>Present</strong> (Green) &mdash; Student is in school</li>
                <li><strong>Absent</strong> (Red) &mdash; Student did not come</li>
                <li><strong>Sick</strong> (Blue) &mdash; Student is absent due to illness</li>
                <li><strong>Late</strong> (Yellow) &mdash; Student arrived late</li>
                <li><strong>Excused</strong> (Purple) &mdash; Student has permission to be absent</li>
            </ul>
            <p>You can also click on the specific status buttons to set a status directly.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">4</div></div>
            <div class="step-title-cell"><div class="step-title">Use Quick Actions</div></div>
        </div>
        <div class="step-body">
            <p><strong>Mark All Present</strong> &mdash; Click this button to quickly set all students as present. Useful when most students are present and you only need to change a few.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">5</div></div>
            <div class="step-title-cell"><div class="step-title">Submit Attendance</div></div>
        </div>
        <div class="step-body">
            <p>After marking all students, click the <strong>Submit Attendance</strong> button at the bottom.</p>
            <p>You'll see a success message with a summary (e.g., Present: 28 | Absent: 2 | Late: 1).</p>
        </div>
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> If attendance was already marked for a date, the existing records will be loaded. You can update them and re-submit.
    </div>

    <div class="warning">
        <span class="warning-label">Important:</span> Please mark attendance every school day before 9:00 AM. Attendance records are used for reports and parent notifications.
    </div>
@endsection
