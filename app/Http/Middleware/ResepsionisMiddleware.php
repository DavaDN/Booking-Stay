<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResepsionisMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('resepsionis')->check()) {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Akses ditolak. Resepsionis harus login.');
    }
}
