<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sms' => [
        'api_url' => env('SMS_API_URL', 'https://bulksms.ontech.co.zm/smsservice/httpapi'),
        'api_key' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID', ''),
        // Legacy — no longer used by Ontech but kept so old resource forms
        // that read config('services.sms.username') don't crash.
        'username' => env('SMS_USERNAME'),
        'password' => env('SMS_PASSWORD'),
        'shortcode' => env('SMS_SHORTCODE'),
    ],

    'whatsapp_bot' => [
        'token' => env('BOT_API_TOKEN'),
        'callback_url' => env('WHATSAPP_BOT_CALLBACK_URL', 'http://sfa-whatsapp-bot:3100/internal/payment-completed'),
        'timeout' => (int) env('WHATSAPP_BOT_TIMEOUT', 5),
        'allowed_ips' => env('BOT_API_ALLOWED_IPS', ''),
    ],

    'scheduler_alerts' => [
        'phone'                => env('SCHEDULER_ALERT_PHONE'),
        'stale_after_minutes'  => (int) env('SCHEDULER_STALE_MINUTES', 10),
        'cooldown_minutes'     => (int) env('SCHEDULER_ALERT_COOLDOWN_MINUTES', 30),
    ],

];
