<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:customers',
            'password'    => 'required|string|min:6',
            'phone'       => 'required|integer|max:20',
        ]);

        $customer = Customer::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'phone'       => $request->phone,
        ]);

        $otp = rand(100000, 999999);
        $customer->otp = $otp;
        $customer->otp_expires_at = Carbon::now()->addMinutes(5);
        $customer->save();

        // kirim OTP via email
        Mail::raw("Jangan diberikan kepada siapapun! 
        Kode OTP Anda adalah: $otp", function ($msg) use ($customer) {
            $msg->to($customer->email)->subject('OTP Register BookingStay');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP dikirim ke email'
        ]);

        return redirect()->route('customer.verify-otp')->with('success', 'Registerasi Berhasil. Silahkan Verifikasi Otp');
    }

    public function verifyOtp(Request $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (!$customer || $customer->otp != $request->otp || Carbon::now()->gt($customer->otp_expires_at)) {
            return response()->json([
                'success' => true,
                'message' => 'OTP tidak valid atau kadaluarsa'
            ], 401);
        }
        $token = $customer->createToken('auth_token')->plainTextToken;
        $customer->otp = null;
        $customer->otp_expires_at = null;
        $customer->save();
        return response()->json([
            'success' => true,
            'message' => 'Verifikasi Otp Berhasil',
            'data' => ['token' => $token]
        ]);

        return redirect()->route('customer.list')->with('success', 'Verifikasi Otp Berhasil');
    }

    public function login(Request $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }
        $token = $customer->createToken('auth_token')->plainTextToken;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => ['token' => $token]
        ]);

        return redirect()->route('customer.list')->with('success', 'Login Berhasil');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);

        return redirect()->route('customer.login')->with('Succes', 'Logout Berhasil');
    }
}
