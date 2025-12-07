<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Http\RedirectResponse;

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

        // Kirim OTP via email ke user (jika konfigurasi mail sudah diatur)
        try {
            Mail::to($customer->email)->send(new OtpMail($otp, $customer->name));
        } catch (\Exception $e) {
        }
        session(['otp_email' => $customer->email]);

        return redirect()->route('customer.verify-otp.form')
                 ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk kode OTP.');
    }

    /**
     * Verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp'   => 'required|numeric',
        ]);

        // Prefer email from request (if hidden input present) or session set during registration/resend
        $email = $request->input('email') ?? session('otp_email');

        if (!$email) {
            return back()->withErrors(['otp' => 'Email tidak tersedia untuk verifikasi.'])->withInput();
        }

        $customer = Customer::where('email', $email)->first();

        // Cek validitas OTP
        if (!$customer || $customer->otp != $request->otp || Carbon::now()->gt($customer->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.'])->withInput();
        }

        // Reset OTP setelah berhasil
        $customer->otp = null;
        $customer->otp_expires_at = null;
        $customer->save();
        // Remove stored otp_email from session after successful verification
        session()->forget('otp_email');
        
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

        $credentials = $request->only('email', 'password');

        // Coba login dengan guard 'customer'
        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect langsung ke Dashboard
            return redirect()->route('customer.hotels.index')->with('success', 'Login berhasil! Selamat datang.');
        }
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

    /**
     * Resend OTP to customer's email
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        // Prefer email from session (set after registration), fallback to provided email
        $email = session('otp_email') ?? $request->input('email');

        if (!$email) {
            return back()->with('error', 'Email tidak ditemukan untuk dikirim OTP.');
        }

        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return back()->with('error', 'Akun tidak ditemukan.');
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $customer->otp = $otp;
        $customer->otp_expires_at = Carbon::now()->addMinutes(5);
        $customer->save();

        // Update session email so verify form can pre-fill
        session(['otp_email' => $customer->email]);

        try {
            Mail::to($customer->email)->send(new OtpMail($otp, $customer->name));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim ulang OTP. Silakan coba lagi.');
        }

        return back()->with('success', 'Kode OTP telah dikirim ulang ke email Anda.');
    }
}