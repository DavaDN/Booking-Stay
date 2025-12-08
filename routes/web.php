<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\{
    ProfileAdminController,
    AdminAuthController,
    ResepsionisController,
    CustomerController,
    RoomTypeController,
    RoomController,
    FacilitiesController,
    FacilityHotelController,
    BookingController,
    TransactionController,
    HotelController,
    ReportController,
    DashboardController as AdminDashboardController
};

use App\Http\Controllers\Customer\{
    CustomerAuthController,
    ProfileController,
    CustomerBookController,
    CustomerTransactionController,
    CustomerListRoomTypeController,
    CustomerListHotelController,
};

use App\Http\Controllers\Resepsionis\{
    ProfileResepsionisController,
    DashboardController,
    ReservationController
};

use App\Http\Controllers\LandingPageController;

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::get('/whoami', function () {
    if (Auth::guard('admin')->check()) {
        return response()->json([
            'Success' => true,
            'Message' => 'User is logged in',
            'Data' => [
                'guard' => 'admin',
                'user' => Auth::guard('admin')->user()
            ]
        ]);
    }

    if (Auth::guard('resepsionis')->check()) {
        return response()->json([
            'Success' => true,
            'Message' => 'User is logged in',
            'Data' => [
                'guard' => 'resepsionis',
                'user' => Auth::guard('resepsionis')->user()
            ]
        ]);
    }

    if (Auth::guard('customer')->check()) {
        return response()->json([
            'Success' => true,
            'Message' => 'User is logged in',
            'Data' => [
                'guard' => 'customer',
                'user' => Auth::guard('customer')->user()
            ]
        ]);
    }

    return response()->json([
        'Success' => false,
        'Message' => 'Belum login',
        'Data' => [
            'guard' => null,
        ]
    ]);
});

/*
|--------------------------------------------------------------------------
| Landing Page (Public)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');

/*
|--------------------------------------------------------------------------
| Customer Authentication
|--------------------------------------------------------------------------
*/
Route::get('customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login.form');
Route::get('customer/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register.form');

Route::get('customer/verify-otp', [CustomerAuthController::class, 'showVerifyOtpForm'])
    ->name('customer.verify-otp.form');

Route::post('customer/login', [CustomerAuthController::class, 'login'])->name('customer.login');
Route::post('customer/register', [CustomerAuthController::class, 'register'])->name('customer.register');
Route::post('customer/verify-otp', [CustomerAuthController::class, 'verifyOtp'])->name('customer.verify-otp');
Route::post('customer/resend-otp', [CustomerAuthController::class, 'resendOtp'])->name('customer.resend-otp');
Route::post('customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

/*
|--------------------------------------------------------------------------
| Admin & Resepsionis Auth
|--------------------------------------------------------------------------
*/
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);

Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AdminAuthController::class, 'register']);

Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth:admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('resepsionis', ResepsionisController::class);

    Route::resource('customers', CustomerController::class)->only(['index', 'destroy']);

    Route::resource('hotels', HotelController::class);

    Route::resource('room-types', RoomTypeController::class);
    Route::resource('rooms', RoomController::class);

    Route::resource('facilities', FacilitiesController::class);
    Route::resource('facility-hotels', FacilityHotelController::class);

    Route::resource('bookings', BookingController::class);
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->name('admin.bookings.updateStatus');

    Route::resource('transactions', TransactionController::class);
    Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus'])
        ->name('admin.transactions.updateStatus');

    Route::get('report', [ReportController::class, 'index'])->name('admin.report');

    //profile
    Route::middleware(['auth'])->prefix('admin')->group(function () {
        Route::get('profile', [ProfileAdminController::class, 'index'])
            ->name('admin.profile');
        Route::get('profile/edit', [ProfileAdminController::class, 'edit'])
            ->name('admin.profile.edit');
        Route::post('profile/update', [ProfileAdminController::class, 'update'])
            ->name('admin.profile.update');
    });
});

/*
|--------------------------------------------------------------------------
| Resepsionis Area
|--------------------------------------------------------------------------
*/
Route::prefix('resepsionis')->middleware('auth:resepsionis')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('resepsionis.dashboard');

    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update']);
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->name('resepsionis.bookings.updateStatus');

    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'update']);
    Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus'])
        ->name('resepsionis.transactions.updateStatus');

    Route::middleware(['auth'])->prefix('resepsionis')->group(function () {
        Route::get('profile', [ProfileResepsionisController::class, 'index'])
            ->name('resepsionis.profile');
        Route::get('profile/edit', [ProfileResepsionisController::class, 'edit'])
            ->name('resepsionis.profile.edit');
        Route::post('profile/update', [ProfileResepsionisController::class, 'update'])
            ->name('resepsionis.profile.update');

            Route::prefix('resepsionis')
    ->name('resepsionis.')
    ->group(function () {

    Route::get('/reservations', [ReservationController::class, 'index'])
        ->name('reservations.index');

    Route::post('/reservations', [ReservationController::class, 'store'])
        ->name('reservations.store');

    Route::put('/reservations/{id}', [ReservationController::class, 'update'])
        ->name('reservations.update');

    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])
        ->name('reservations.destroy');
});

    });
});

/*
|--------------------------------------------------------------------------
| Customer Area (Web)
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->middleware('auth:customer')->group(function () {

    Route::get('profile', [ProfileController::class, 'profile'])->name('customer.profile');
    Route::put('profile', [ProfileController::class, 'updateProfile'])->name('customer.profile.update');
    Route::delete('profile', [ProfileController::class, 'deleteAccount'])->name('customer.profile.delete');

    Route::get('home', [CustomerListHotelController::class, 'index'])->name('customer.hotels.index');
    Route::get('home/{id}', [CustomerListHotelController::class, 'show'])->name('customer.hotels.show');

    Route::get('list', [CustomerListRoomTypeController::class, 'index'])->name('customer.list');
    Route::get('list/{id}', [CustomerListRoomTypeController::class, 'show'])->name('customer.list.show');

    Route::get('bookings', [CustomerBookController::class, 'index'])->name('customer.bookings.index');
    Route::get('bookings/create', [CustomerBookController::class, 'create'])->name('customer.bookings.create');
    Route::post('bookings', [CustomerBookController::class, 'store'])->name('customer.bookings.store');
    Route::get('bookings/{id}', [CustomerBookController::class, 'show'])->name('customer.bookings.show');
    Route::put('bookings/{id}', [CustomerBookController::class, 'update'])->name('customer.bookings.update');
    Route::delete('bookings/{id}', [CustomerBookController::class, 'destroy'])->name('customer.bookings.destroy');

    Route::resource('transactions', CustomerTransactionController::class)->only(['index', 'show', 'create', 'store']);



});
