@extends('pdf.guides._layout')

@section('content')
    <div class="section-title">Overview</div>
    <p>The Results page shows all recorded exam results. You can view individual results, search by student or subject, and filter by exam type, term, or year.</p>

    <div class="section-title">Managing Results</div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">1</div></div>
            <div class="step-title-cell"><div class="step-title">View Results Table</div></div>
        </div>
        <div class="step-body">
            <p>The table displays all results you've entered, showing:</p>
            <ul>
                <li><strong>Student Name</strong> &mdash; The student who took the exam</li>
                <li><strong>Subject</strong> &mdash; The subject tested</li>
                <li><strong>Exam Type</strong> &mdash; Mid-Term, Final, Quiz, Assignment, or Test</li>
                <li><strong>Marks</strong> &mdash; The score (out of 100)</li>
                <li><strong>Grade</strong> &mdash; Letter grade (e.g., A, B, C)</li>
                <li><strong>Term</strong> &mdash; Which term the result belongs to</li>
                <li><strong>Year</strong> &mdash; Academic year</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">2</div></div>
            <div class="step-title-cell"><div class="step-title">Add Individual Result</div></div>
        </div>
        <div class="step-body">
            <p>Click <strong>New Result</strong> to add a single student result.</p>
            <ul>
                <li>Select the <strong>Student</strong> from the dropdown</li>
                <li>The <strong>Subject</strong> dropdown shows only subjects assigned to that student's grade</li>
                <li><strong>Term</strong> and <strong>Year</strong> are automatically set to the current period</li>
                <li><strong>Recorded By</strong> is automatically set to your name</li>
                <li>Enter the marks, grade, and optional comment</li>
                <li>Toggle <strong>SMS notification</strong> to alert the parent</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">3</div></div>
            <div class="step-title-cell"><div class="step-title">Filter and Search</div></div>
        </div>
        <div class="step-body">
            <p>Use filters to find specific results:</p>
            <ul>
                <li><strong>Subject</strong> &mdash; Filter by a specific subject</li>
                <li><strong>Exam Type</strong> &mdash; Show only Mid-Term, Final, etc.</li>
                <li><strong>Term</strong> &mdash; Filter by term</li>
                <li><strong>Year</strong> &mdash; Filter by year</li>
            </ul>
            <p>Use the search bar to find results by student name or subject.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">4</div></div>
            <div class="step-title-cell"><div class="step-title">Edit or Delete Results</div></div>
        </div>
        <div class="step-body">
            <p>Click the <strong>pencil icon</strong> to edit a result, or the <strong>trash icon</strong> to delete it.</p>
            <p>You can only edit results that belong to your students.</p>
        </div>
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> For entering results for the whole class at once, use the <strong>Enter Results</strong> page from the sidebar &mdash; it's much faster than adding results one by one.
    </div>

    <div class="warning">
        <span class="warning-label">Important:</span> When SMS notification is enabled, the parent will receive an SMS with the student's result. Make sure marks are correct before saving.
    </div>
@endsection
