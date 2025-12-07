<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomerAuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    /**
     * Show register form
     */
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Tampilkan Halaman Verify OTP (GET)
     */
    public function showVerifyOtpForm()
    {
        return view('customer.auth.verify-otp');
    }

    /**
     * Registrasi Customer Baru
     */
    public function register(Request $request)
    {
        // Validasi Manual agar bisa trigger modal register jika error
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:customers',
            'password'    => 'required|string|min:6|confirmed',
            'phone'       => 'required|string|max:20',
        ]);

        // Jika gagal, redirect balik ke landing page dan buka modal register
        if ($validator->fails()) {
            return redirect()->route('landing.index')
                        ->withErrors($validator)
                        ->withInput()
                        ->with('show_register', true); 
        }

        // Simpan Data Customer
        $customer = Customer::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'phone'       => $request->phone,
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);
        $customer->otp = $otp;
        $customer->otp_expires_at = Carbon::now()->addMinutes(5);
        $customer->save();

        // Simpan email di session agar form OTP otomatis terisi
        session(['otp_email' => $customer->email]);

        // Disini seharusnya kirim Email OTP (Mail::to(...))
        
        // Redirect ke Halaman Verifikasi OTP
        return redirect()->route('customer.verify-otp.form')
                         ->with('success', 'Registrasi berhasil! Kode OTP (Simulasi): ' . $otp);
    }

    /**
     * Verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        // Cek validitas OTP
        if (!$customer || $customer->otp != $request->otp || Carbon::now()->gt($customer->otp_expires_at)) {
             return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.'])->withInput();
        }

        // Reset OTP setelah berhasil
        $customer->otp = null;
        $customer->otp_expires_at = null;
        $customer->save();
        
        // PENTING: Login otomatis user tersebut menggunakan guard 'customer'
        Auth::guard('customer')->login($customer);

        // Redirect langsung ke Dashboard (Home Customer)
        return redirect()->route('customer.hotels.index')->with('success', 'Verifikasi berhasil! Selamat datang.');
    }

    /**
     * Login Customer
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Jika validasi input gagal
        if ($validator->fails()) {
            return redirect()->route('landing.index')
                        ->withErrors($validator)
                        ->withInput()
                        ->with('show_login', true); // Sinyal buka modal login
        }

        $credentials = $request->only('email', 'password');

        // Coba login dengan guard 'customer'
        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect langsung ke Dashboard
            return redirect()->intended(route('customer.hotels.index'));
        }

        // Jika password salah, kembali ke landing page dan buka modal login
        return redirect()->route('landing.index')
                    ->withErrors(['email' => 'Email atau password salah.'])
                    ->withInput()
                    ->with('show_login', true);
    }

    /**
     * Logout Customer
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('landing.index');
    }
}