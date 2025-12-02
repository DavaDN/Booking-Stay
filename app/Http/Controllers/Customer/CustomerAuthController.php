<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{
    /**
     * Registrasi Customer Baru
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:customers',
            'password'    => 'required|string|min:6',
            'phone'       => 'required|string|max:20',
        ]);

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

        // Kirim OTP via email
        Mail::raw("Jangan diberikan kepada siapapun! 
        Kode OTP Anda adalah: $otp", function ($msg) use ($customer) {
            $msg->to($customer->email)->subject('OTP Register BookingStay');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP dikirim ke email untuk verifikasi'
        ]);

        return redirect()->route('customer.auth.verify-otp')->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk OTP verifikasi.');
    }

    /**
     * Verifikasi OTP Setelah Registrasi
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || $customer->otp != $request->otp || Carbon::now()->gt($customer->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP tidak valid atau sudah kadaluarsa'
            ], 401);
        }

        // Reset OTP
        $customer->otp = null;
        $customer->otp_expires_at = null;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi OTP berhasil, silakan login'
        ]);
        return redirect()->route('customer.home')->with('success', 'Verifikasi OTP berhasil, silakan login.');
    }

    /**
     * Login Customer menggunakan session Laravel
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Gunakan guard customer
        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user'    => Auth::guard('customer')->user()
            ]);

            return redirect()->route('customer.home')->with('success', 'Login berhasil');
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Logout Customer
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
        return redirect()->route('landing.index')->with('success', 'Logout berhasil');
    }
}
