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
use App\Listeners\LogAuthEvent;
use Illuminate\Auth\Events\Failed as LoginFailed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Console\Command as ConsoleCommand;
use Illuminate\Support\Facades\Event as EventFacade;
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
        // Framework commands declared with #[AsCommand] (route:list, about,
        // schedule:run, package:discover, migrate, etc.) are lazy-loaded via
        // Symfony's container command loader, which instantiates them
        // WITHOUT calling setLaravel() — so their internal $laravel is null
        // and Command::run() dies with "Call to a member function make() on
        // null" as soon as the command is invoked.
        //
        // Custom commands using $signature go through Application::add()
        // and get wired correctly, which is why /this/ app's commands work
        // but every stock framework command breaks.
        //
        // Hook the container so every Illuminate\Console\Command resolved
        // out of it gets its laravel binding set as it comes off the line.
        $this->app->afterResolving(ConsoleCommand::class, function (ConsoleCommand $command) {
            $command->setLaravel($this->app);
        });
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

        // Login trail — write every auth event (login / logout / failed
        // login / lockout / password reset) into audit_logs so we always
        // have a "who signed in, from where, when" record.
        //
        // Guarded so it registers once per process — Filament's panel
        // provider triggers a second boot pass that would otherwise
        // duplicate the listener and double-write every event.
        static $authListenerBooted = false;
        if (! $authListenerBooted) {
            $authListenerBooted = true;
            EventFacade::listen(Login::class,         [LogAuthEvent::class, 'handleLogin']);
            EventFacade::listen(Logout::class,        [LogAuthEvent::class, 'handleLogout']);
            EventFacade::listen(LoginFailed::class,   [LogAuthEvent::class, 'handleFailed']);
            EventFacade::listen(Lockout::class,       [LogAuthEvent::class, 'handleLockout']);
            EventFacade::listen(PasswordReset::class, [LogAuthEvent::class, 'handlePasswordReset']);
        }
    }
}
