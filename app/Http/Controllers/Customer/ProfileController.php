<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule; // Import Rule untuk validasi unique email

class ProfileController extends Controller
{
    /**
     * Show customer profile (for customer routes)
     */
    public function profile(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.profile', compact('customer'));
    }

    /**
     * Update customer profile information
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Perbaikan validasi email agar mengabaikan email user saat ini
            'email' => ['required', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'phone' => ['required', 'string', 'max:20'],
            // Validasi password opsional
            'current_password' => ['nullable', 'required_with:password', 'current_password:customer'], 
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // Update informasi dasar
        $customer->fill($request->only(['name', 'email', 'phone']));

        // Reset verifikasi email jika email berubah (opsional, jika pakai fitur verifikasi)
        if ($customer->isDirty('email')) {
            $customer->email_verified_at = null;
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $customer->password = Hash::make($request->password);
        }

        $customer->save();

        return redirect()->route('customer.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Delete customer account
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:customer'], // Gunakan guard customer untuk validasi password
        ]);

        $customer = Auth::guard('customer')->user();

        Auth::guard('customer')->logout();

        $customer->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing.index')->with('success', 'Akun berhasil dihapus.');
    }
}