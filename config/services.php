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

    'flussonic' => [
        'base_url' => env('FLUSSONIC_BASE_URL'),
        'secret_key' => env('FLUSSONIC_SECRET_KEY'),
        'token_lifetime' => env('FLUSSONIC_TOKEN_LIFETIME', 10800), // 3 hours in seconds
        'desync' => env('FLUSSONIC_DESYNC', 300), // 5 minutes desync tolerance
        'dev_ip' => env('FLUSSONIC_DEV_IP'), // For local development testing (use your real IP)
        'protocols' => [
            'hls' => '/index.m3u8',
            'dash' => '/manifest.mpd',
            'rtmp' => '',
        ],
    ],

    'epg' => [
        'url' => env('EPG_URL'),
        'format' => env('EPG_FORMAT', 'xmltv'), // xmltv or json
        'sync_interval' => env('EPG_SYNC_INTERVAL', 'daily'), // hourly, daily, weekly

        // Map EPG channel IDs to our database channel IDs
        // Example: 'bein-sports-1-ar' => 1
        'channel_mapping' => [
            // Add your channel mappings here
            // 'external_channel_id' => internal_channel_id,
        ],
    ],

];
