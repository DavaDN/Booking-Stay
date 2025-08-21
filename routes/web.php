<?php

use Illuminate\Support\Facades\Route;
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

// Admin
Route::prefix('admin')->middleware('auth:admin')->group(function () {
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
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update']);
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus']);
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'update']);
    Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus']);
});

// Customer Web
Route::prefix('customer')->middleware('auth:customer_web')->group(function () {
    Route::get('profile', [CustomerController::class, 'profile']);
    Route::put('profile', [CustomerController::class, 'updateProfile']);
    Route::delete('profile', [CustomerController::class, 'deleteAccount']);
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'store']);
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'store']);
});


//tambahan
// // Customer Auth
// Route::get('customer/login', [CustomerController::class, 'showLogin'])->name('customer.login.form');
// Route::post('customer/login', [CustomerController::class, 'login'])->name('customer.login');
// Route::get('customer/register', [CustomerController::class, 'showRegister'])->name('customer.register.form');
// Route::post('customer/register', [CustomerController::class, 'register'])->name('customer.register');
// Route::post('customer/logout', [CustomerController::class, 'logout'])->name('customer.logout');

// Customer Auth
Route::post('customer/login', [CustomerController::class, 'login'])->name('customer.login');
Route::post('customer/register', [CustomerController::class, 'register'])->name('customer.register');
Route::post('customer/logout', [CustomerController::class, 'logout'])->name('customer.logout');


