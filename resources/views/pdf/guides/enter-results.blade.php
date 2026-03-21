@extends('pdf.guides._layout')

@section('content')
    <div class="section-title">Overview</div>
    <p>The Enter Results page allows you to enter exam marks for all students in your class at once. Your class, current term, and year are automatically set.</p>

    <div class="section-title">How to Enter Results</div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">1</div></div>
            <div class="step-title-cell"><div class="step-title">Verify Pre-filled Fields</div></div>
        </div>
        <div class="step-body">
            <p>When you open the page, the following fields are already set:</p>
            <ul>
                <li><strong>Class</strong> &mdash; Your assigned class (locked)</li>
                <li><strong>Term</strong> &mdash; Current term (locked)</li>
                <li><strong>Year</strong> &mdash; Current academic year (locked)</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">2</div></div>
            <div class="step-title-cell"><div class="step-title">Select Subject</div></div>
        </div>
        <div class="step-body">
            <p>Click the <strong>Subject</strong> dropdown and select the subject you want to enter results for.</p>
            <p>Only subjects assigned to your grade will appear in the list.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">3</div></div>
            <div class="step-title-cell"><div class="step-title">Select Exam Type</div></div>
        </div>
        <div class="step-body">
            <p>Choose the type of exam from the dropdown:</p>
            <ul>
                <li><strong>Mid-Term Exam</strong> &mdash; Mid-term assessment</li>
                <li><strong>Final Exam</strong> &mdash; End of term exam</li>
                <li><strong>Quiz</strong> &mdash; Short quiz or class test</li>
                <li><strong>Assignment</strong> &mdash; Homework or project</li>
                <li><strong>Test</strong> &mdash; Regular test</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">4</div></div>
            <div class="step-title-cell"><div class="step-title">Enter Marks</div></div>
        </div>
        <div class="step-body">
            <p>After selecting the subject and exam type, a table of all students will appear.</p>
            <p>For each student, enter their mark in the <strong>Marks</strong> column (0 to 100).</p>
            <p>The <strong>Grade</strong> column will be calculated automatically based on the school's grading scale.</p>
            <p>You can optionally add a <strong>Comment</strong> for each student.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">5</div></div>
            <div class="step-title-cell"><div class="step-title">Submit Results</div></div>
        </div>
        <div class="step-body">
            <p>Once you have entered marks for all students, click <strong>Save Results</strong>.</p>
            <p>Students without marks will be skipped (not saved).</p>
            <p>A success message will show how many results were saved.</p>
        </div>
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> If results were previously entered for the same subject, term, and exam type, they will be loaded automatically. You can update the marks and save again.
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> Use the <strong>Clear All</strong> button to reset all marks if you need to start over.
    </div>

    <div class="warning">
        <span class="warning-label">Important:</span> Marks must be between 0 and 100. Double-check your entries before submitting &mdash; results are visible to parents on the portal.
    </div>
@endsection
