<?php

namespace App\Channels;

use App\Services\FcmSender;
use Illuminate\Notifications\Notification;

/**
 * Laravel notification channel that pushes via FCM.
 *
 * To opt a Notification into FCM:
 *   public function via($notifiable): array { return ['database', \App\Channels\FcmChannel::class]; }
 *   public function toFcm($notifiable): array {
 *       return [
 *           'title' => 'Homework submitted',
 *           'body'  => $studentName.' submitted '.$homeworkTitle,
 *           'data'  => ['homework_id' => (string) $this->submission->homework_id], // optional, string values only
 *           'app'   => 'parent', // optional: 'parent' | 'teacher' to limit to that app's tokens
 *       ];
 *   }
 *
 * The notifiable must have an integer `id` property (the User model does).
 * If the user has no FCM tokens, or FCM isn't configured yet, the call returns
 * gracefully — nothing breaks.
 */
class FcmChannel
{
    public function __construct(private readonly FcmSender $sender)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }
        $payload = $notification->toFcm($notifiable);
        if (!is_array($payload) || empty($payload['title'])) {
            return;
        }
        if (!isset($notifiable->id)) {
            return;
        }

        $app = $payload['app'] ?? null;
        $this->sender->sendToUser(
            userId: (int) $notifiable->id,
            title: (string) $payload['title'],
            body: (string) ($payload['body'] ?? ''),
            data: isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : null,
            app: in_array($app, ['parent', 'teacher'], true) ? $app : null,
        );
    }
}
