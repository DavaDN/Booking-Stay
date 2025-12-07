<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the paths that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $guards = [];

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Tentukan redirect berdasarkan route
        if ($request->is('admin*') || $request->is('resepsionis*')) {
            return route('login');
        }

        if ($request->is('customer*')) {
            return route('customer.login.form');
        }

        return route('login');
    }
}
