<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Jika login sebagai admin
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();


            if ($request->wantsJson()) {
                return response()->json([
                    'succes' => true,
                    'message' => 'Login berhasil sebagai admin',
                    'guard'   => 'admin',
                    'user'    => Auth::guard('admin')->user()
                ]);
            }

            return redirect()->intended('/admin/dashboard');
        }

        // Jika login sebagai resepsionis
        if (Auth::guard('resepsionis')->attempt($credentials)) {
            $request->session()->regenerate();

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Login berhasil sebagai resepsionis',
                    'guard'   => 'resepsionis',
                    'user'    => Auth::guard('resepsionis')->user()
                ]);
            }

            return redirect()->intended('/resepsionis/dashboard');
        }

        // Jika gagal login
        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }
    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('admin')->login($admin);

        return response()->json([
            'succes' => true,
            'message' => 'Registrasi Berhasil',
            'Data' => $admin
        ]);

        return redirect('/admin/dashboard')->with('success', 'Registrasi berhasil!');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('resepsionis')->check()) {
            Auth::guard('resepsionis')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
