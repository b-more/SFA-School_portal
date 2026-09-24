<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BotApiController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\Api\ParentQuizController;
use App\Http\Controllers\Api\ParentAssessmentController;
use App\Http\Controllers\Api\FcmController;
use App\Http\Controllers\Api\UssdController;
use Illuminate\Support\Facades\Route;

// Public USSD callback — hit by the Ontech gateway (ussd.ontech.co.zm) for
// every keypress in a session. Must respond within 5s. Its own throttle
// (300/min per Ontech source IP) lets peak load through without tripping
// the global 60/min api limiter that would otherwise trigger during a
// popular menu prompt.
Route::post('/ussd/callback', [UssdController::class, 'callback'])
    ->middleware('throttle:300,1');

// Public
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
// SMS-based reset for parents with dummy / no email — OTP step + verify step.
Route::post('/forgot-password-sms', [AuthController::class, 'forgotPasswordSms'])->middleware('throttle:5,15');
Route::post('/reset-password-sms', [AuthController::class, 'resetPasswordSms'])->middleware('throttle:10,15');
Route::get('/school-settings', [AuthController::class, 'schoolSettings']);

// Authenticated parent routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/profile-photo', [AuthController::class, 'uploadProfilePhoto']);
    Route::delete('/profile-photo', [AuthController::class, 'deleteProfilePhoto']);

    Route::get('/dashboard', [ParentApiController::class, 'dashboard']);
    Route::get('/children', [ParentApiController::class, 'children']);
    Route::get('/children/{student}/attendance', [ParentApiController::class, 'attendance']);
    Route::get('/children/{student}/fees', [ParentApiController::class, 'fees']);
    Route::get('/children/{student}/results', [ParentApiController::class, 'results']);
    Route::get('/children/{student}/homework', [ParentApiController::class, 'homework']);
    Route::get('/children/{student}/report-cards', [ParentApiController::class, 'reportCards']);
    Route::get('/children/{student}/timetable', [ParentApiController::class, 'timetable']);
    Route::get('/children/{student}/book-loans', [ParentApiController::class, 'bookLoans']);
    Route::get('/children/{student}/bus-payments', [ParentApiController::class, 'busPayments']);
    Route::get('/events', [ParentApiController::class, 'events']);
    Route::get('/payments', [ParentApiController::class, 'payments']);
    Route::get('/notices', [ParentApiController::class, 'notices']);
    Route::get('/news', [ParentApiController::class, 'news']);
    Route::get('/complaints', [ParentApiController::class, 'complaints']);
    Route::post('/children/{student}/complaints', [ParentApiController::class, 'createComplaint']);
    Route::get('/school-calendar', [ParentApiController::class, 'schoolCalendar']);
    Route::post('/children/{student}/homework/{homework}/submit', [ParentApiController::class, 'submitHomework']);

    // Quizzes
    Route::get('/children/{student}/quizzes', [ParentQuizController::class, 'index']);
    Route::get('/children/{student}/quizzes/{quiz}', [ParentQuizController::class, 'show']);
    Route::post('/children/{student}/quizzes/{quiz}/start', [ParentQuizController::class, 'start']);
    Route::post('/children/{student}/quizzes/{quiz}/submit', [ParentQuizController::class, 'submit']);

    // CBC assessments (scenario, teacher-marked)
    Route::get('/children/{student}/assessments', [ParentAssessmentController::class, 'index']);
    Route::get('/children/{student}/assessments/{assessment}', [ParentAssessmentController::class, 'show']);
    Route::post('/children/{student}/assessments/{assessment}/submit', [ParentAssessmentController::class, 'submit']);

    // Payments
    Route::get('/bus-routes', [ParentApiController::class, 'busRoutes']);
    Route::post('/children/{student}/pay', [ParentApiController::class, 'initiatePayment']);
    Route::post('/children/{student}/pay-bus', [ParentApiController::class, 'payBusFare']);
    Route::post('/payment-status', [ParentApiController::class, 'checkPaymentStatus']);

    // Push notifications
    Route::get('/push/vapid-key', [ParentApiController::class, 'vapidPublicKey']);
    Route::post('/push/subscribe', [ParentApiController::class, 'pushSubscribe']);
    Route::post('/push/unsubscribe', [ParentApiController::class, 'pushUnsubscribe']);
    // FCM (native Android push)
    Route::post('/push/fcm/register', [FcmController::class, 'registerParent']);
    Route::post('/push/fcm/unregister', [FcmController::class, 'unregister']);
});

// Internal API used by the WhatsApp bot. Shared bearer token + rate limit,
// no user session. Kept read-only + payment-initiation only.
Route::prefix('bot')->middleware(['bot.auth', 'throttle:30,1'])->group(function () {
    Route::post('identify',     [BotApiController::class, 'identify']);
    Route::post('balance',      [BotApiController::class, 'balance']);
    Route::post('pay',          [BotApiController::class, 'pay']);
    Route::post('receipt',      [BotApiController::class, 'receipt']);
    Route::get ('transactions', [BotApiController::class, 'transactions']);
    Route::get ('fee-schedule',      [BotApiController::class, 'feeSchedule']);
    Route::get ('fee-schedule-pdf',  [BotApiController::class, 'feeSchedulePdf']);
    Route::post('homework',          [BotApiController::class, 'homework']);
    Route::post('homework-detail',   [BotApiController::class, 'homeworkDetail']);
    Route::post('quizzes',           [BotApiController::class, 'quizzes']);
    Route::post('attendance',        [BotApiController::class, 'attendance']);
    Route::post('report-cards',      [BotApiController::class, 'reportCards']);
    Route::post('report-card-pdf',   [BotApiController::class, 'reportCardPdf']);
    Route::post('timetable',         [BotApiController::class, 'timetable']);
    Route::get ('gallery',           [BotApiController::class, 'gallery']);
    Route::post('album-photos',      [BotApiController::class, 'albumPhotos']);
});
// Conversation logging has its own throttle — the bot posts one per
// inbound/outbound message so we allow a higher rate for it.
Route::prefix('bot')->middleware(['bot.auth', 'throttle:120,1'])->group(function () {
    Route::post('log', [BotApiController::class, 'log']);
});
