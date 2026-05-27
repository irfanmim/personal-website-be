<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | FRONTEND_URL should be set to your Vue SPA's deployed origin, e.g.:
    |   https://yourdomain.com
    |
    | For local development both origins are always allowed below.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    'allowed_origins' => array_filter([
        env('FRONTEND_URL'),
    ]),

    'allowed_origins_patterns' => [
        'http://localhost(:\d+)?$',   // any localhost port (dev)
    ],

    'allowed_headers' => ['Content-Type', 'Authorization', 'Accept'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
