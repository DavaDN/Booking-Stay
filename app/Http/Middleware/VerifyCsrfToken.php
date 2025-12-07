<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle a request failure.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Session\TokenMismatchException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    protected function handleTokenMismatch($request, TokenMismatchException $exception)
    {
        // Regenerate session dan redirect ke referrer
        $request->session()->regenerateToken();
        
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'CSRF token mismatch. Silakan refresh page dan coba lagi.'
            ], 419);
        }

        // Redirect ke halaman sebelumnya dengan pesan error
        return redirect()->back()
            ->withInput($request->except(['password', 'password_confirmation']))
            ->with('error', 'Session expired. Silakan refresh page dan coba lagi.');
    }
}
