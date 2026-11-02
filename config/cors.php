<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configured to permit requests from mobile apps, Flutter Web (localhost),
    | and external enterprise clients across all environments.
    |
    */

    'paths' => [
        'api/*',
        'auth/*',
        'employee/*',
        'hrd/*',
        'tickets/*',
        'departments/*',
        'categories/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];
