<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\{
    AuthController,
    BookingController,
    RoomTypeController,
    TransactionController
};
use App\Http\Controllers\{
    FacilitiesController,
    FacilityHotelController,
    LandingPageController
};


Route::get('v1/room-types', [LandingPageController::class, 'apiRoomTypes']);


// Auth API (Breeze API / Sanctum)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Protected routes (Auth login)
Route::middleware('auth:customer_api')->group(function () {

    Route::get('/customer/profile', [AuthController::class, 'profile']);
    Route::put('/customer/profile', [AuthController::class, 'updateProfile']);
    Route::delete('/customer/profile', [AuthController::class, 'deleteAccount']);

    Route::get('/room-types', [RoomTypeController::class, 'index']);
    Route::get('/room-types/{id}', [RoomTypeController::class, 'show']);
    Route::get('/facility-hotels', [FacilityHotelController::class, 'index']);
    Route::get('/facilities', [FacilitiesController::class, 'index']);

    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);

    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
});
