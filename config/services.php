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

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Services (Phase 4)
    |--------------------------------------------------------------------------
    */

    'paylink' => [
        'api_url' => env('PAYLINK_API_URL', 'https://api.paylink.sa/api'),
        'sandbox_url' => env('PAYLINK_SANDBOX_URL', 'https://sandbox.paylink.sa/api'),
        'api_key' => env('PAYLINK_API_KEY'),
        'secret_key' => env('PAYLINK_SECRET_KEY'),
    ],

    'telr' => [
        'api_url' => env('TELR_API_URL', 'https://secure.telr.com/gateway/order.json'),
        'sandbox_url' => env('TELR_SANDBOX_URL', 'https://secure.telr.com/gateway/order.json'),
        'store_id' => env('TELR_STORE_ID'),
        'auth_key' => env('TELR_AUTH_KEY'),
    ],

];
