@extends('pdf.guides._layout')

@section('content')
    <div class="section-title">Overview</div>
    <p>The Students page shows the list of students in your class. You can view student details, search for students, and access their fee and result records.</p>

    <div class="section-title">Viewing Your Students</div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">1</div></div>
            <div class="step-title-cell"><div class="step-title">Student List Table</div></div>
        </div>
        <div class="step-body">
            <p>The table shows all active students in your assigned class section. Each row displays:</p>
            <ul>
                <li><strong>Student Name</strong> &mdash; Full name of the student</li>
                <li><strong>Registration Number</strong> &mdash; Student's unique ID</li>
                <li><strong>Gender</strong> &mdash; Male or Female</li>
                <li><strong>Grade &amp; Section</strong> &mdash; Class assignment</li>
                <li><strong>Parent/Guardian</strong> &mdash; Name and phone number</li>
                <li><strong>Enrollment Status</strong> &mdash; Active, Transferred, etc.</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">2</div></div>
            <div class="step-title-cell"><div class="step-title">Search and Filter</div></div>
        </div>
        <div class="step-body">
            <p>Use the <strong>Search</strong> bar at the top of the table to find a specific student by name.</p>
            <p>Click the <strong>Filter</strong> icon to filter students by grade, section, gender, or enrollment status.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">3</div></div>
            <div class="step-title-cell"><div class="step-title">View Student Details</div></div>
        </div>
        <div class="step-body">
            <p>Click the <strong>eye icon</strong> (View) on any student row to see their full profile, including:</p>
            <ul>
                <li>Personal information and photo</li>
                <li>Parent/Guardian contact details</li>
                <li>Fee payment history</li>
                <li>Academic results</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">4</div></div>
            <div class="step-title-cell"><div class="step-title">Download Student List</div></div>
        </div>
        <div class="step-body">
            <p>Click the <strong>Download PDF</strong> button in the top-right to download a printable list of your students.</p>
        </div>
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> As a class teacher, you can only see students assigned to your class. This keeps the list focused and relevant.
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> Click on the column headers to sort students by name, registration number, or other fields.
    </div>
@endsection
