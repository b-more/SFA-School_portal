<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:' . (env('MAIL_FROM_ADDRESS', 'admin@stfrancisofassisi.tech')),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $this->webPush = new WebPush($auth);
    }

    /**
     * Send push notification to a specific user
     */
    public function sendToUser(int $userId, string $title, string $body, ?string $url = null, ?string $tag = null): array
    {
        $subscriptions = PushSubscription::where('user_id', $userId)->get();
        return $this->sendToSubscriptions($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Send push notification to all subscribed users
     */
    public function sendToAll(string $title, string $body, ?string $url = null, ?string $tag = null): array
    {
        $subscriptions = PushSubscription::all();
        return $this->sendToSubscriptions($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Send push notification to multiple user IDs
     */
    public function sendToUsers(array $userIds, string $title, string $body, ?string $url = null, ?string $tag = null): array
    {
        $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();
        return $this->sendToSubscriptions($subscriptions, $title, $body, $url, $tag);
    }

    private function sendToSubscriptions($subscriptions, string $title, string $body, ?string $url, ?string $tag): array
    {
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url ?? '/',
            'tag' => $tag ?? 'sfa-notification',
        ]);

        $sent = 0;
        $failed = 0;
        $staleEndpoints = [];

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->p256dh_key,
                'authToken' => $sub->auth_token,
            ]);

            $this->webPush->queueNotification($subscription, $payload);
        }

        foreach ($this->webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                // Remove stale/expired subscriptions (410 Gone or 404)
                $statusCode = $report->getResponse()?->getStatusCode();
                if (in_array($statusCode, [404, 410])) {
                    $staleEndpoints[] = $report->getEndpoint();
                }
            }
        }

        if (!empty($staleEndpoints)) {
            PushSubscription::whereIn('endpoint', $staleEndpoints)->delete();
        }

        return ['sent' => $sent, 'failed' => $failed];
    }
}
