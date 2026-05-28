<?php

use App\Http\Controllers\Api\TeacherAuthController;
use App\Http\Controllers\Api\TeacherApiController;
use App\Http\Controllers\Api\TeacherQuizController;
use App\Http\Controllers\Api\TeacherQuestionBankController;
use App\Http\Controllers\Api\TeacherAssessmentController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [TeacherAuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/forgot-password', [TeacherAuthController::class, 'forgotPassword']);
Route::get('/school-settings', [TeacherAuthController::class, 'schoolSettings']);

// Authenticated teacher routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [TeacherAuthController::class, 'logout']);
    Route::get('/user', [TeacherAuthController::class, 'user']);

    Route::get('/dashboard', [TeacherApiController::class, 'dashboard']);
    Route::get('/head-dashboard', [TeacherApiController::class, 'headDashboard']);

    // Quizzes
    Route::get('/quizzes', [TeacherQuizController::class, 'index']);
    Route::post('/quizzes', [TeacherQuizController::class, 'store']);
    Route::get('/quizzes/{quiz}', [TeacherQuizController::class, 'show']);
    Route::get('/quizzes/{quiz}/results', [TeacherQuizController::class, 'results']);
    Route::post('/quizzes/{quiz}/close', [TeacherQuizController::class, 'close']);
    Route::delete('/quizzes/{quiz}', [TeacherQuizController::class, 'destroy']);

    // Question bank
    Route::get('/question-bank/meta', [TeacherQuestionBankController::class, 'meta']);
    Route::get('/question-bank', [TeacherQuestionBankController::class, 'index']);
    Route::post('/question-bank', [TeacherQuestionBankController::class, 'store']);
    Route::get('/question-bank/{item}', [TeacherQuestionBankController::class, 'show']);
    Route::put('/question-bank/{item}', [TeacherQuestionBankController::class, 'update']);
    Route::delete('/question-bank/{item}', [TeacherQuestionBankController::class, 'destroy']);

    // CBC assessments (scenario / Theory), SBA gradebook, 70/30 weighting
    Route::get('/assessments', [TeacherAssessmentController::class, 'index']);
    Route::post('/assessments', [TeacherAssessmentController::class, 'store']);
    Route::get('/assessment-submissions/{submission}', [TeacherAssessmentController::class, 'submissionDetail']);
    Route::post('/assessment-submissions/{submission}/mark', [TeacherAssessmentController::class, 'markSubmission']);
    Route::get('/assessment-summary', [TeacherAssessmentController::class, 'subjectSummary']);
    Route::get('/assessments/{assessment}/submissions', [TeacherAssessmentController::class, 'submissions']);
    Route::get('/assessments/{assessment}', [TeacherAssessmentController::class, 'show']);
    Route::post('/assessments/{assessment}/close', [TeacherAssessmentController::class, 'close']);
    Route::delete('/assessments/{assessment}', [TeacherAssessmentController::class, 'destroy']);
    Route::get('/sba', [TeacherAssessmentController::class, 'sbaGradebook']);
    Route::post('/sba', [TeacherAssessmentController::class, 'saveSba']);
    Route::get('/ecz-settings', [TeacherAssessmentController::class, 'getSettings']);
    Route::post('/ecz-settings', [TeacherAssessmentController::class, 'saveSettings']);
    Route::get('/my-classes', [TeacherApiController::class, 'myClasses']);
    Route::get('/class/{classSectionId}/students', [TeacherApiController::class, 'classStudents']);

    // Attendance
    Route::post('/attendance/mark', [TeacherApiController::class, 'markAttendance']);
    Route::get('/attendance/{classSectionId}', [TeacherApiController::class, 'getAttendance']);

    // Homework
    Route::get('/homework', [TeacherApiController::class, 'myHomework']);
    Route::post('/homework/create', [TeacherApiController::class, 'createHomework']);
    Route::get('/homework/{homeworkId}/submissions', [TeacherApiController::class, 'homeworkSubmissions']);
    Route::post('/homework/submission/{submissionId}/grade', [TeacherApiController::class, 'gradeSubmission']);

    // Results
    Route::post('/results/enter', [TeacherApiController::class, 'enterResults']);
    Route::get('/results/{classSectionId}/{subjectId}', [TeacherApiController::class, 'getResults']);

    // Timetable
    Route::get('/timetable', [TeacherApiController::class, 'myTimetable']);

    // Notices
    Route::get('/notices', [TeacherApiController::class, 'notices']);

    // Profile & Employment
    Route::get('/profile', [TeacherApiController::class, 'profile']);
    Route::get('/payslips', [TeacherApiController::class, 'payslips']);
    Route::get('/my-students', [TeacherApiController::class, 'myStudents']);

    // Analytics
    Route::get('/attendance-analytics/{classSectionId}', [TeacherApiController::class, 'attendanceAnalytics']);
    Route::get('/homework-analytics', [TeacherApiController::class, 'homeworkAnalytics']);
    Route::get('/student-performance/{studentId}', [TeacherApiController::class, 'studentPerformance']);

    // Class Communication
    Route::post('/send-notice', [TeacherApiController::class, 'sendClassNotice']);

    // Leave
    Route::get('/leave-balances', [TeacherApiController::class, 'leaveBalances']);
    Route::get('/leave-applications', [TeacherApiController::class, 'leaveApplications']);
    Route::post('/leave/apply', [TeacherApiController::class, 'applyLeave']);

    // Report Card Comments
    Route::get('/report-card-students', [TeacherApiController::class, 'reportCardStudents']);
    Route::post('/report-card-comment', [TeacherApiController::class, 'saveReportCardComment']);

    // Directories
    Route::get('/parent-contacts', [TeacherApiController::class, 'parentContacts']);
    Route::get('/staff-directory', [TeacherApiController::class, 'staffDirectory']);

    // Calendar
    Route::get('/school-calendar', [TeacherApiController::class, 'schoolCalendar']);

    // CPD
    Route::get('/cpd/dashboard', [TeacherApiController::class, 'cpdDashboard']);
    Route::get('/cpd/activities', [TeacherApiController::class, 'cpdActivities']);
    Route::post('/cpd/activities', [TeacherApiController::class, 'createCpdActivity']);
    Route::get('/cpd/goals', [TeacherApiController::class, 'cpdGoals']);
    Route::post('/cpd/goals', [TeacherApiController::class, 'createCpdGoal']);
    Route::post('/cpd/goals/{goalId}/update', [TeacherApiController::class, 'updateCpdGoal']);
    Route::get('/cpd/resources', [TeacherApiController::class, 'cpdResources']);
    Route::post('/cpd/resources', [TeacherApiController::class, 'shareCpdResource']);
    Route::get('/cpd/observations', [TeacherApiController::class, 'cpdObservations']);
    Route::post('/cpd/observations/{observationId}/reflection', [TeacherApiController::class, 'saveObservationReflection']);
    Route::get('/cpd/term-breakdown', [TeacherApiController::class, 'cpdTermBreakdown']);
    Route::get('/cpd/templates', [TeacherApiController::class, 'cpdTemplates']);
    Route::post('/cpd/quick-log', [TeacherApiController::class, 'quickLogCpd']);
    Route::post('/cpd/activities/{activityId}/approve', [TeacherApiController::class, 'approveCpdActivity']);
    Route::get('/cpd/pending-approvals', [TeacherApiController::class, 'pendingApprovals']);
    Route::post('/cpd/activities/{activityId}/link-goal', [TeacherApiController::class, 'linkActivityToGoal']);
    Route::post('/cpd/activities/{activityId}/link-observation', [TeacherApiController::class, 'linkActivityToObservation']);
    Route::get('/cpd/certificates', [TeacherApiController::class, 'cpdCertificates']);
    Route::get('/cpd/school-report', [TeacherApiController::class, 'schoolWideCpdReport']);
    Route::get('/cpd/school-report/csv', [TeacherApiController::class, 'schoolCpdCsvExport']);
    Route::get('/cpd/export/download', [TeacherApiController::class, 'personalCpdCsvExport']);
    Route::post('/cpd/activities/{activityId}/update', [TeacherApiController::class, 'updateCpdActivity']);
    Route::delete('/cpd/activities/{activityId}', [TeacherApiController::class, 'deleteCpdActivity']);
    Route::delete('/cpd/goals/{goalId}', [TeacherApiController::class, 'deleteCpdGoal']);
    Route::post('/cpd/observations/create', [TeacherApiController::class, 'createObservation']);
    Route::get('/cpd/export', [TeacherApiController::class, 'cpdExport']);

    // Account
    Route::post('/change-password', [TeacherApiController::class, 'changePassword']);
    Route::post('/profile-photo', [TeacherApiController::class, 'updateProfilePhoto']);

    // Messaging
    Route::get('/messages', [TeacherApiController::class, 'conversations']);
    Route::get('/messages/{partnerId}', [TeacherApiController::class, 'chatMessages']);
    Route::post('/messages/send', [TeacherApiController::class, 'sendMessage']);
});

// Download routes — token via query param, outside auth:sanctum
Route::middleware([\App\Http\Middleware\TokenFromQuery::class])->group(function () {
    Route::get('/cpd/school-report/csv', [TeacherApiController::class, 'schoolCpdCsvExport']);
    Route::get('/cpd/export/download', [TeacherApiController::class, 'personalCpdCsvExport']);
});
