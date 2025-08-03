<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// Login & Register (Breeze)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth');

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// Dashboard + Management
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Master Data
    Route::resource('floors', FloorController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('room-types', RoomTypeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('rooms', RoomController::class)->only(['index', 'store', 'update', 'destroy']);

    // Booking & Transaction View
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});

require __DIR__ . '/auth.php';
