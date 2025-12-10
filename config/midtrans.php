<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans configuration
    |--------------------------------------------------------------------------
    |
    | `server_key` is used for server-side API calls (Snap creation, notification
    | verification). `client_key` is used by the frontend Snap JS (only in views).
    | `is_production` toggles between sandbox and production endpoints.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    // optional sanitize and 3ds flags
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
];
