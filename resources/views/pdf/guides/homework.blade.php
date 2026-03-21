@extends('pdf.guides._layout')

@section('content')
    <div class="section-title">Overview</div>
    <p>The Homework page allows you to create, manage, and track homework assignments for your class. You can set due dates, attach files, and monitor student submissions.</p>

    <div class="section-title">Creating Homework</div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">1</div></div>
            <div class="step-title-cell"><div class="step-title">Create New Homework</div></div>
        </div>
        <div class="step-body">
            <p>Click the <strong>New Homework</strong> button in the top-right corner.</p>
            <p>Fill in the required fields:</p>
            <ul>
                <li><strong>Title</strong> &mdash; A clear name for the assignment</li>
                <li><strong>Subject</strong> &mdash; Select the subject</li>
                <li><strong>Grade</strong> &mdash; Select the grade</li>
                <li><strong>Description</strong> &mdash; Detailed instructions for students</li>
                <li><strong>Due Date</strong> &mdash; When the homework must be submitted</li>
                <li><strong>Attachments</strong> &mdash; Upload any reference files (optional)</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">2</div></div>
            <div class="step-title-cell"><div class="step-title">View Homework List</div></div>
        </div>
        <div class="step-body">
            <p>The homework table shows all assignments you've created, with columns for:</p>
            <ul>
                <li><strong>Title</strong> &mdash; Homework name</li>
                <li><strong>Subject</strong> &mdash; Which subject</li>
                <li><strong>Grade</strong> &mdash; Which grade</li>
                <li><strong>Due Date</strong> &mdash; Submission deadline</li>
                <li><strong>Status</strong> &mdash; Active, Expired, etc.</li>
                <li><strong>Submissions</strong> &mdash; Number of students who submitted</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">3</div></div>
            <div class="step-title-cell"><div class="step-title">Review Submissions</div></div>
        </div>
        <div class="step-body">
            <p>Click <strong>View</strong> on a homework to see all student submissions.</p>
            <p>For each submission you can:</p>
            <ul>
                <li>View the student's submitted work/files</li>
                <li>Add feedback or comments</li>
                <li>Grade the submission</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">4</div></div>
            <div class="step-title-cell"><div class="step-title">Edit or Delete Homework</div></div>
        </div>
        <div class="step-body">
            <p>Use the <strong>pencil icon</strong> to edit a homework assignment (e.g., extend the due date).</p>
            <p>Use the <strong>trash icon</strong> to delete a homework (this will also remove all submissions).</p>
        </div>
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> When creating homework linked to results, use the "Assignment" exam type in the Enter Results page to connect the homework submission to the grade.
    </div>
@endsection
