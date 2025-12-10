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
    DashboardController
};

use App\Http\Controllers\Resepsionis\ReservationController;

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Payment\MidtransDemoController;
use App\Http\Controllers\Payment\MidtransController;

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
Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('resepsionis', ResepsionisController::class);

    Route::resource('customers', CustomerController::class)->only(['index', 'destroy']);

    Route::resource('hotels', HotelController::class);

    Route::resource('room-types', RoomTypeController::class);
    Route::resource('rooms', RoomController::class);

    Route::resource('facilities', FacilitiesController::class);
    Route::resource('facility-hotels', FacilityHotelController::class);

    Route::resource('bookings', BookingController::class);
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->name('bookings.updateStatus');

    Route::resource('transactions', TransactionController::class);
    Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus'])
        ->name('transactions.updateStatus');

    // Exports: PDF and Excel
    Route::get('transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.pdf');
    Route::get('transactions/export/excel', [TransactionController::class, 'exportExcel'])->name('transactions.excel');

    Route::get('report', [ReportController::class, 'index'])->name('report');

    //profile
    Route::middleware(['auth'])->group(function () {
        Route::get('profile', [ProfileAdminController::class, 'index'])
            ->name('profile');
        Route::get('profile/edit', [ProfileAdminController::class, 'edit'])
            ->name('profile.edit');
        Route::post('profile/update', [ProfileAdminController::class, 'update'])
            ->name('profile.update');
    });
});

/*
|--------------------------------------------------------------------------
| Resepsionis Area
|--------------------------------------------------------------------------
*/
Route::prefix('resepsionis')->middleware('auth:resepsionis')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('resepsionis.dashboard');

    // Reservations (resepsionis)
    Route::resource('reservations', ReservationController::class);

    // Map legacy 'bookings' path to resepsionis reservations controller (use reservations resource)
    Route::resource('bookings', ReservationController::class)->only(['index', 'show', 'update', 'destroy']);

    // Use Resepsionis-specific TransactionController so data is scoped by hotel
    Route::resource('transactions', \App\Http\Controllers\Resepsionis\TransactionController::class)->only(['index', 'show', 'update']);
    Route::patch('transactions/{id}/status', [\App\Http\Controllers\Resepsionis\TransactionController::class, 'updateStatus'])
        ->name('resepsionis.transactions.updateStatus');

    // Resepsionis: create bookings (walk-in) and midtrans checkout
    Route::post('bookings/create', [\App\Http\Controllers\Resepsionis\ReservationController::class, 'createBooking'])
        ->name('resepsionis.bookings.create');

    // Resepsionis bookings management
    Route::get('bookings', [\App\Http\Controllers\Resepsionis\BookingController::class, 'index'])->name('resepsionis.bookings.index');
    Route::get('bookings/{id}', [\App\Http\Controllers\Resepsionis\BookingController::class, 'show'])->name('resepsionis.bookings.show');
    Route::patch('bookings/{id}/status', [\App\Http\Controllers\Resepsionis\BookingController::class, 'updateStatus'])->name('resepsionis.bookings.updateStatus');

    Route::post('midtrans/create-snap', [\App\Http\Controllers\Payment\MidtransController::class, 'createSnapForResepsionis'])
        ->name('resepsionis.midtrans.create');

    Route::get('midtrans/checkout/{transaction}', [\App\Http\Controllers\Payment\MidtransController::class, 'showSnapForResepsionis'])
        ->name('resepsionis.midtrans.checkout');

    Route::middleware(['auth'])->prefix('resepsionis')->group(function () {
        Route::get('profile', [ProfileResepsionisController::class, 'index'])
            ->name('resepsionis.profile');
        Route::get('profile/edit', [ProfileResepsionisController::class, 'edit'])
            ->name('resepsionis.profile.edit');
        Route::post('profile/update', [ProfileResepsionisController::class, 'update'])
            ->name('resepsionis.profile.update');
    });
});

/*
|--------------------------------------------------------------------------
| Customer Area (Web)
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->middleware('auth:customer')->name('customer.')->group(function () {

    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
    Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'deleteAccount'])->name('profile.delete');

    Route::get('home', [CustomerListHotelController::class, 'index'])->name('home');
    Route::get('home/{id}', [CustomerListHotelController::class, 'show'])->name('hotels.show');

    Route::get('list', [CustomerListRoomTypeController::class, 'index'])->name('list');
    Route::get('list/{id}', [CustomerListRoomTypeController::class, 'show'])->name('list.show');

    Route::get('bookings', [CustomerBookController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create', [CustomerBookController::class, 'create'])->name('bookings.create');
    Route::post('bookings', [CustomerBookController::class, 'store'])->name('bookings.store');
    Route::get('bookings/{id}', [CustomerBookController::class, 'show'])->name('bookings.show');
    Route::put('bookings/{id}', [CustomerBookController::class, 'update'])->name('bookings.update');
    Route::delete('bookings/{id}', [CustomerBookController::class, 'destroy'])->name('bookings.destroy');

    Route::resource('transactions', CustomerTransactionController::class)->only(['index', 'show', 'create', 'store', 'update']);



});

// Midtrans notification (public endpoint used by Midtrans)

Route::post('midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');

// Create Snap token (customer must be authenticated)
Route::prefix('customer')->middleware('auth:customer')->name('customer.')->group(function () {
    Route::post('midtrans/create-snap', [MidtransController::class, 'createSnapToken'])->name('midtrans.create_snap');
        Route::get('transactions/{id}/midtrans', [MidtransController::class, 'transactionDetails'])->name('midtrans.transaction_details');
        
        // Midtrans demo routes (sandbox test page)
        Route::get('midtrans/demo', [MidtransDemoController::class, 'showDemo'])->name('midtrans.demo');
        Route::post('midtrans/demo/create', [MidtransDemoController::class, 'createDemoSnap'])->name('midtrans.demo_create');
});
