<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'current_password' => 'nullable|required_if:password,*',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Update basic info
        $customer->update($request->only(['name', 'email', 'phone']));

        // Update password if provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $customer->password)) {
                return back()->with('error', 'Password saat ini tidak sesuai.');
            }
            $customer->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('customer.profile')->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Delete customer account
     */
    public function deleteAccount(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'password' => 'required|current_password',
        ]);

        Auth::guard('customer')->logout();
        $customer->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing.index')->with('success', 'Akun berhasil dihapus.');
    }
}
