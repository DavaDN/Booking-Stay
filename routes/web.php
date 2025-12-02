<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\{
    AdminAuthController,
    ResepsionisController,
    CustomerController,
    RoomTypeController,
    RoomController,
    FacilitiesController,
    FacilityHotelController,
    BookingController,
    TransactionController,
    HotelController
};

use App\Http\Controllers\Customer\{
    CustomerAuthController,
    ProfileController,
    CustomerBookController,
    CustomerTransactionController,
    CustomerListRoomTypeController
};
use App\Http\Controllers\{
    LandingPageController,
};

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
| Public Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');

/*
|--------------------------------------------------------------------------
| Customer Auth (login/register/logout)
|--------------------------------------------------------------------------
*/
Route::post('customer/login', [CustomerAuthController::class, 'login'])->name('customer.login');
Route::post('customer/register', [CustomerAuthController::class, 'register'])->name('customer.register');
Route::post('customer/verify-otp', [CustomerAuthController::class, 'verifyOtp'])->name('customer.verify-otp');
Route::post('customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

/*
|--------------------------------------------------------------------------
| Auth Login untuk Admin & Resepsionis
|--------------------------------------------------------------------------
| - Admin dan Resepsionis login di halaman yang sama
| - Register hanya untuk Admin
| - Logout berbeda sesuai guard
|--------------------------------------------------------------------------
*/
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);

// Register hanya untuk Admin
Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AdminAuthController::class, 'register']);

// Logout untuk admin dan resepsionis
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Area (Hanya Admin yang login)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Resepsionis hanya dikelola oleh admin
    Route::resource('resepsionis', ResepsionisController::class);

    // Customers
    Route::resource('customers', CustomerController::class)->only(['index', 'destroy']);

    // Hotels
    Route::resource('hotels', HotelController::class);

    // Rooms & Room Types
    Route::resource('room-types', RoomTypeController::class);
    Route::resource('rooms', RoomController::class);

    // Facilities
    Route::resource('facilities', FacilitiesController::class);
    Route::resource('facility-hotels', FacilityHotelController::class);

    // Bookings
    Route::resource('bookings', BookingController::class);
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('admin.bookings.updateStatus');

    // Transactions
    Route::resource('transactions', TransactionController::class);
    Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus'])->name('admin.transactions.updateStatus');
});

/*
|--------------------------------------------------------------------------
| Resepsionis Area
|--------------------------------------------------------------------------
| - Login menggunakan halaman yang sama dengan admin
| - Tidak bisa register, akun dibuat oleh admin
| - Tidak punya akses ke menu admin
|--------------------------------------------------------------------------
*/
Route::prefix('resepsionis')->middleware('auth:resepsionis')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('resepsionis.dashboard');
    })->name('resepsionis.dashboard');

    // Booking (index, show, update)
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update']);
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('resepsionis.bookings.updateStatus');

    // Transaction (index, show, update)
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'update']);
    Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus'])->name('resepsionis.transactions.updateStatus');
});

/*
|--------------------------------------------------------------------------
| Customer Area (Web)
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->middleware('auth:customer')->group(function () {
    // Profile
    Route::get('profile', [ProfileController::class, 'profile'])->name('customer.profile');
    Route::put('profile', [ProfileController::class, 'updateProfile'])->name('customer.profile.update');
    Route::delete('profile', [ProfileController::class, 'deleteAccount'])->name('customer.profile.delete');

    // List Hotels
    Route::get('home', [HotelController::class, 'index'])->name('customer.hotels.index');
    Route::get('home/{id}', [HotelController::class, 'show'])->name('customer.hotels.show');

    // List Room Types
    Route::get('list', [CustomerListRoomTypeController::class, 'index'])->name('customer.list');
    Route::get('list/{id}', [CustomerListRoomTypeController::class, 'show'])->name('customer.list.show');
    
    // Bookings
    Route::resource('bookings', CustomerBookController::class)->only(['index', 'show', 'store']);

    // Transactions
    Route::resource('transactions', CustomerTransactionController::class)->only(['index', 'show', 'store']);
});
