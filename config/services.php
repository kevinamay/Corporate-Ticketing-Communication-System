<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'ai' => [
        'provider' => env('AI_PROVIDER', 'gemini'),
        'gemini_key' => env('GEMINI_API_KEY'),
        'gemini_model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'openai_key' => env('OPENAI_API_KEY'),
        'openai_model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'groq_key' => env('GROQ_API_KEY'),
        'groq_model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
    ],

    'whatsapp' => [
        'provider' => env('WA_GATEWAY_PROVIDER', 'meta'),
        'url' => env('WA_API_URL', 'https://api.fonnte.com/send'),
        'token' => env('WA_API_TOKEN'),
        'admin_number' => env('WA_ADMIN_NUMBER'),
    ],

    'meta_whatsapp' => [
        'phone_number_id' => env('META_WA_PHONE_NUMBER_ID'),
        'access_token' => env('META_WA_ACCESS_TOKEN'),
        'admin_number' => env('ADMIN_WA_NUMBER', env('WA_ADMIN_NUMBER', '6285784694910')),
        'api_version' => env('META_WA_API_VERSION', 'v20.0'),
    ],

    'waha' => [
        'base_url' => env('WAHA_BASE_URL'),
        'api_key' => env('WAHA_API_KEY', 'e8928adf08ec4cfd8b30dea033ee38bc'),
        'session' => env('WAHA_SESSION', 'default'),
        'sender_number' => env('COMPANY_WA_NUMBER', '6282244109503'),
        'admin_number' => env('ADMIN_WA_NUMBER', env('WA_ADMIN_NUMBER', '6285784694910')),
    ],

];
