<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Event;
use App\Models\HomeworkSubmission;
use App\Models\PaymentTransaction;
use App\Models\Student;
use App\Models\StudentFee;
use App\Observers\EmployeeObserver;
use App\Observers\EventObserver;
use App\Observers\HomeworkSubmissionObserver;
use App\Observers\PaymentTransactionObserver;
use App\Observers\StudentObserver;
use App\Observers\StudentFeeObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // The default password-reset notification (used by the mobile-app and
        // teacher-app /forgot-password endpoints) builds its link from the
        // route('password.reset') name, which this app doesn't define — only
        // Filament's reset routes exist. Point it at Filament's reset page so
        // the emailed link works. Filament's own reset flow uses a separate
        // notification and is unaffected.
        ResetPassword::createUrlUsing(
            fn ($notifiable, string $token) => Filament::getPanel('admin')->getResetPasswordUrl($token, $notifiable)
        );

        // Register model observers for admin notifications
        Student::observe(StudentObserver::class);
        StudentFee::observe(StudentFeeObserver::class);
        HomeworkSubmission::observe(HomeworkSubmissionObserver::class);
        Employee::observe(EmployeeObserver::class);
        Event::observe(EventObserver::class);

        // Register accounting integration observer
        PaymentTransaction::observe(PaymentTransactionObserver::class);
    }
}
