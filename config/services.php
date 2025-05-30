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

    'statcounter' => [
        'project_id' => env('STATCOUNTER_PROJECT_ID'),
        'security' => env('STATCOUNTER_SECURITY'),
        'invisible' => env('STATCOUNTER_INVISIBLE', true),
        'remove_link' => env('STATCOUNTER_REMOVE_LINK', true),
    ],

    'openai' => [
        'key' => env('OPENAI_KEY'),
    ],

    'anthropic' => [
        'key' => env('ANTHROPIC_KEY'),
    ],

    'groq' => [
        'key' => env('GROQ_KEY'),
    ],

    'redpill' => [
        'key' => env('REDPILL_KEY'),
    ],

    'gemini' => [
        'key' => env('GEMINI_KEY'),
    ],
];
