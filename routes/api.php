<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\Api\ParentQuizController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/school-settings', [AuthController::class, 'schoolSettings']);

// Authenticated parent routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

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

    // Payments
    Route::post('/children/{student}/pay', [ParentApiController::class, 'initiatePayment']);
    Route::post('/payment-status', [ParentApiController::class, 'checkPaymentStatus']);

    // Push notifications
    Route::get('/push/vapid-key', [ParentApiController::class, 'vapidPublicKey']);
    Route::post('/push/subscribe', [ParentApiController::class, 'pushSubscribe']);
    Route::post('/push/unsubscribe', [ParentApiController::class, 'pushUnsubscribe']);
});
