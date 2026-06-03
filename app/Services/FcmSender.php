<?php

namespace App\Services;

use App\Models\FcmToken;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Throwable;

/**
 * Sends FCM (Firebase Cloud Messaging) push notifications to registered Android devices.
 *
 * Configured via env var FCM_SERVICE_ACCOUNT_JSON (absolute path to the Firebase
 * service-account JSON file). If unset/unreadable, all send calls return a
 * "not configured" result without raising — keeps the rest of the app working.
 */
class FcmSender
{
    private ?Messaging $messaging = null;
    private bool $resolved = false;

    private function messaging(): ?Messaging
    {
        if ($this->resolved) {
            return $this->messaging;
        }
        $this->resolved = true;

        $path = env('FCM_SERVICE_ACCOUNT_JSON');
        if (!$path || !is_readable($path)) {
            return null;
        }
        try {
            $this->messaging = (new Factory())->withServiceAccount($path)->createMessaging();
        } catch (Throwable $e) {
            $this->messaging = null;
        }
        return $this->messaging;
    }

    public function isConfigured(): bool
    {
        return $this->messaging() !== null;
    }

    /**
     * Send a notification to every FCM token registered for a user.
     *
     * @param  array<string,string>|null  $data  Custom data payload (string values only per FCM).
     * @param  'parent'|'teacher'|null    $app   Restrict to one app's tokens; null = both.
     * @return array{sent:int,invalid_pruned:int,reason?:string,failed?:array}
     */
    public function sendToUser(int $userId, string $title, string $body, ?array $data = null, ?string $app = null): array
    {
        $messaging = $this->messaging();
        if (!$messaging) {
            return ['sent' => 0, 'invalid_pruned' => 0, 'reason' => 'FCM not configured (set FCM_SERVICE_ACCOUNT_JSON to a Firebase service-account JSON path).'];
        }

        $query = FcmToken::where('user_id', $userId);
        if ($app !== null) {
            $query->where('app', $app);
        }
        $tokens = $query->pluck('token')->all();
        if (empty($tokens)) {
            return ['sent' => 0, 'invalid_pruned' => 0, 'reason' => 'No FCM tokens registered for this user.'];
        }

        $sent = 0;
        $invalid = [];
        $failed = [];

        foreach ($tokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body));
                if ($data) {
                    $message = $message->withData(array_map('strval', $data));
                }
                $messaging->send($message);
                FcmToken::where('token', $token)->update(['last_active_at' => now()]);
                $sent++;
            } catch (NotFound $e) {
                // Token no longer valid (uninstalled, key rotated) — prune it.
                $invalid[] = $token;
            } catch (Throwable $e) {
                $failed[] = [
                    'token_prefix' => substr($token, 0, 16) . '…',
                    'error' => $e->getMessage(),
                ];
            }
        }

        if (!empty($invalid)) {
            FcmToken::whereIn('token', $invalid)->delete();
        }

        $result = ['sent' => $sent, 'invalid_pruned' => count($invalid)];
        if (!empty($failed)) {
            $result['failed'] = $failed;
        }
        return $result;
    }
}
