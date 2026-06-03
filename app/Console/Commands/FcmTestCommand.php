<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FcmSender;
use Illuminate\Console\Command;

class FcmTestCommand extends Command
{
    protected $signature = 'fcm:test {user : User id or email} {title=SFA test notification} {body=This is a test FCM push from the portal.}';

    protected $description = 'Send a test FCM push notification to a user\'s registered devices.';

    public function handle(FcmSender $sender): int
    {
        $arg = $this->argument('user');
        $user = is_numeric($arg)
            ? User::find((int) $arg)
            : User::where('email', $arg)->first();
        if (!$user) {
            $this->error("User not found: {$arg}");
            return self::FAILURE;
        }

        $this->info("Sending to user #{$user->id} ({$user->name})");
        if (!$sender->isConfigured()) {
            $this->warn('FCM is not configured. Set FCM_SERVICE_ACCOUNT_JSON to the path of a Firebase service-account JSON.');
        }

        $result = $sender->sendToUser(
            $user->id,
            $this->argument('title'),
            $this->argument('body'),
        );

        $this->line(json_encode($result, JSON_PRETTY_PRINT));
        return self::SUCCESS;
    }
}
