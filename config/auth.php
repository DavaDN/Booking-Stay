<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Guard default yang akan digunakan untuk autentikasi.
    | Di sini kita set ke 'web' agar tetap kompatibel dengan fitur Laravel bawaan.
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
    | - web         : Guard default untuk admin
    | - admin       : Guard khusus admin (opsional, mirip web)
    | - resepsionis : Guard khusus resepsionis
    | - customer    : Guard khusus customer via web
    |
    */

    'guards' => [
        // Default web guard, diarahkan ke admin
        'web' => [
            'driver' => 'session',
            'provider' => 'admin',
        ],

        // Guard admin
        'admin' => [
            'driver' => 'session',
            'provider' => 'admin',
        ],

        // Guard resepsionis
        'resepsionis' => [
            'driver' => 'session',
            'provider' => 'resepsionis',
        ],

        // Guard customer via web
        'customer' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],

        // Guard customer_web (legacy/alias)
        'customer_web' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Provider menentukan dari mana data user diambil, di sini kita pakai Eloquent.
    | Setiap role memiliki modelnya sendiri.
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
    | Reset password untuk masing-masing provider user.
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
    | Timeout untuk konfirmasi password dalam detik.
    |
    */

    'password_timeout' => 10800,

];
