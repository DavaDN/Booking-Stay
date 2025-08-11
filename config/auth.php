<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Default guard dan password reset bawaan Laravel.
    | Diset ke 'web' untuk mencegah error di fitur bawaan.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | - web           : Default session untuk admin
    | - admin         : Session khusus admin
    | - resepsionis   : Session khusus resepsionis
    | - customer_web  : Session khusus customer via web
    | - customer_api  : API token via Sanctum untuk customer (mobile)
    |
    */

    'guards' => [
        // Default web guard (admin)
        'web' => [
            'driver' => 'session',
            'provider' => 'admin',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admin',
        ],

        'resepsionis' => [
            'driver' => 'session',
            'provider' => 'resepsionis',
        ],

        'customer_web' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],

        // Guard API untuk customer mobile
        'customer_api' => [
            'driver' => 'sanctum',
            'provider' => 'customers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Model yang digunakan untuk tiap tipe user.
    |
    */

    'providers' => [
        'admin' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'resepsionis' => [
            'driver' => 'eloquent',
            'model' => App\Models\Resepsionis::class,
        ],

        'customers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Customer::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Reset password untuk tiap provider user.
    |
    */

    'passwords' => [
        'admin' => [
            'provider' => 'admin',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'resepsionis' => [
            'provider' => 'resepsionis',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'customers' => [
            'provider' => 'customers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout konfirmasi password (detik).
    |
    */

    'password_timeout' => 10800,

];
