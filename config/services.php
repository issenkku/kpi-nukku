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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // KKU APIs
    'kku' => [
        'api_base' => env('KKU_API_BASE', 'https://api.kku.ac.th/v3'),
        'client_id' => env('KKU_CLIENT_ID'),
        'secret_key' => env('KKU_SECRET_KEY'),
        // Token cache key and TTL in minutes (default ~23h)
        'token_cache_key' => 'kku_api_token',
        'token_ttl_minutes' => env('KKU_TOKEN_TTL_MINUTES', 23 * 60),
    ],

];
