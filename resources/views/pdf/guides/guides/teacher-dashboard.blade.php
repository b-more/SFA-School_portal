@extends('pdf.guides._layout')

@section('content')
    <div class="section-title">Overview</div>
    <p>The Teacher Dashboard is your home page after logging in. It shows a summary of your class, quick actions, and important information at a glance.</p>

    <div class="section-title">What You'll See</div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">1</div></div>
            <div class="step-title-cell"><div class="step-title">Class Summary Cards</div></div>
        </div>
        <div class="step-body">
            <p>At the top, you'll see cards showing:</p>
            <ul>
                <li><strong>Total Students</strong> &mdash; Number of active students in your class</li>
                <li><strong>Attendance Today</strong> &mdash; How many students are present today</li>
                <li><strong>Pending Homework</strong> &mdash; Homework awaiting submissions</li>
                <li><strong>Upcoming Events</strong> &mdash; School events happening soon</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">2</div></div>
            <div class="step-title-cell"><div class="step-title">Quick Action Buttons</div></div>
        </div>
        <div class="step-body">
            <p>Use the quick action buttons to navigate to common tasks:</p>
            <ul>
                <li><strong>Mark Attendance</strong> &mdash; Go directly to daily attendance</li>
                <li><strong>Enter Results</strong> &mdash; Enter student exam results</li>
                <li><strong>Create Homework</strong> &mdash; Assign new homework to your class</li>
                <li><strong>View Students</strong> &mdash; See your class student list</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">3</div></div>
            <div class="step-title-cell"><div class="step-title">Navigation Menu</div></div>
        </div>
        <div class="step-body">
            <p>The left sidebar contains links to all your available pages. As a class teacher, you have access to:</p>
            <ul>
                <li><strong>Dashboard</strong> &mdash; This page (home)</li>
                <li><strong>Students</strong> &mdash; Your class students list</li>
                <li><strong>Daily Attendance</strong> &mdash; Mark and view attendance</li>
                <li><strong>Enter Results</strong> &mdash; Bulk enter exam results</li>
                <li><strong>Results</strong> &mdash; View all recorded results</li>
                <li><strong>Homework</strong> &mdash; Manage homework assignments</li>
            </ul>
        </div>
    </div>

    <div class="tip">
        <span class="tip-label">Tip:</span> You can collapse the sidebar by clicking the arrow icon at the top to give yourself more screen space.
    </div>
@endsection
