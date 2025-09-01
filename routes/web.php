<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\{
    LandingPageController,
    AdminController,
    ResepsionisController,
    CustomerController,
    RoomTypeController,
    RoomController,
    FacilitiesController,
    FacilityHotelController,
    BookingController,
    TransactionController
};

// Public landing page
Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');

// Auth Admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::get('/admin/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin Area (wajib login)
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('admins', AdminController::class);
    Route::resource('resepsionis', ResepsionisController::class);
    Route::resource('customers', CustomerController::class)->only(['index', 'destroy']);
    Route::resource('room-types', RoomTypeController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('facilities', FacilitiesController::class);
    Route::resource('facility-hotels', FacilityHotelController::class);
    Route::resource('bookings', BookingController::class);
    Route::resource('transactions', TransactionController::class);
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus']);
    Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus']);
});


// Resepsionis
Route::prefix('resepsionis')->middleware('auth:resepsionis')->group(function () {
    Route::get('/dashboard', function () {
        return view('resepsionis.dashboard');
    })->name('resepsionis.dashboard');

    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update']);
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus']);
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'update']);
    Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus']);
});

// Logout resepsionis -> redirect ke /admin/login
Route::get('/resepsionis/logout', function (Request $request) {
    Auth::guard('resepsionis')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/admin/login');
})->name('resepsionis.logout');

// Customer Web
Route::prefix('customer')->middleware('auth:customer_web')->group(function () {
    Route::get('profile', [CustomerController::class, 'profile']);
    Route::put('profile', [CustomerController::class, 'updateProfile']);
    Route::delete('profile', [CustomerController::class, 'deleteAccount']);
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'store']);
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'store']);
});

// Customer Auth
Route::post('customer/login', [CustomerController::class, 'login'])->name('customer.login');
Route::post('customer/register', [CustomerController::class, 'register'])->name('customer.register');
Route::post('customer/logout', [CustomerController::class, 'logout'])->name('customer.logout');
