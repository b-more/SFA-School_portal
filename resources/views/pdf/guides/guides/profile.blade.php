@extends('pdf.guides._layout')

@section('content')
    <div class="section-title">Overview</div>
    <p>The My Profile page allows you to view and update your personal information, change your password, and download your employee documents (Profile PDF, Business Card, Employee ID).</p>

    <div class="section-title">Managing Your Profile</div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">1</div></div>
            <div class="step-title-cell"><div class="step-title">Access Your Profile</div></div>
        </div>
        <div class="step-body">
            <p>Click your <strong>name or avatar</strong> in the top-right corner of any page, then select <strong>Profile</strong>.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">2</div></div>
            <div class="step-title-cell"><div class="step-title">Update Profile Photo</div></div>
        </div>
        <div class="step-body">
            <p>In the <strong>Profile Photo</strong> section, click to upload a new photo.</p>
            <ul>
                <li>The photo will be cropped to a square (1:1 ratio)</li>
                <li>Accepted formats: JPG, PNG</li>
                <li>This photo appears on your ID card and business card</li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">3</div></div>
            <div class="step-title-cell"><div class="step-title">Update Contact Information</div></div>
        </div>
        <div class="step-body">
            <p>Update your:</p>
            <ul>
                <li><strong>Email Address</strong> &mdash; This is also your login email</li>
                <li><strong>Phone Number</strong></li>
                <li><strong>Address, City, Province</strong></li>
            </ul>
            <p>Click <strong>Save Profile</strong> after making changes.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">4</div></div>
            <div class="step-title-cell"><div class="step-title">Emergency Contact &amp; Next of Kin</div></div>
        </div>
        <div class="step-body">
            <p>Keep your emergency contact and next of kin information up to date. This is important for school safety records.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">5</div></div>
            <div class="step-title-cell"><div class="step-title">Change Password</div></div>
        </div>
        <div class="step-body">
            <p>Click the <strong>Change Password</strong> button (yellow) in the top-right.</p>
            <ul>
                <li>Enter your <strong>Current Password</strong></li>
                <li>Enter and confirm your <strong>New Password</strong> (minimum 8 characters)</li>
                <li>Click <strong>Submit</strong></li>
            </ul>
        </div>
    </div>

    <div class="step">
        <div class="step-header">
            <div class="step-number-cell"><div class="step-number">6</div></div>
            <div class="step-title-cell"><div class="step-title">Download Documents</div></div>
        </div>
        <div class="step-body">
            <p>Use the buttons in the top-right to download:</p>
            <ul>
                <li><strong>Profile PDF</strong> (green) &mdash; Full employee profile document</li>
                <li><strong>Business Card</strong> (blue) &mdash; Printable business cards (6 per page)</li>
                <li><strong>Employee ID</strong> (info) &mdash; Printable ID card with QR code</li>
            </ul>
        </div>
    </div>

    <div class="warning">
        <span class="warning-label">Important:</span> Changing your email will also change your login email. Make sure you remember it!
    </div>
@endsection
