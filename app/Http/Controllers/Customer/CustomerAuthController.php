<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    // Tampilkan form register (opsional: view)
    public function showRegister()
    {
        return view('customer.register');
    }

    // proses register web
    public function register(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:6|confirmed'
        ]);

        Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone ?? null,
        ]);

        return redirect()->route('landing.index')->with('success', 'Registrasi berhasil, silakan login.');
    }

    // tampilkan form login
    public function showLogin()
    {
        return view('customer.login');
    }

    // proses login web (session)
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer_web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('landing.index'));
        }

        return back()->withErrors(['email' => 'Credensial salah'])->withInput();
    }

    // dashboard contoh
    public function dashboard()
    {
        $customer = Auth::guard('customer_web')->user();
        return view('landing.index', compact('customer'));
    }

    // logout
    public function logout(Request $request)
    {
        Auth::guard('customer_web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing.index');
    }

    // tampilkan form profile edit
    public function showProfile()
    {
        $customer = Auth::guard('customer_web')->user();
        return view('customer.profile', compact('customer'));
    }

    // update profile (web)
    public function updateProfile(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer_web')->user();

        $request->validate([
            'name' => 'required|string|max:255|unique:customers,name,' . $customer->id,
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'password' => 'nullable|min:6|confirmed'
        ]);

        $data = $request->only(['name', 'email', 'phone']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // delete account (web)
    public function deleteAccount(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer_web')->user();
        Auth::guard('customer_web')->logout();
        $customer->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.register.form')->with('success', 'Akun dihapus.');
    }
}
