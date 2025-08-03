<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Data Master
    Route::resource('floors', FloorController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('room-types', RoomTypeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('rooms', RoomController::class)->only(['index', 'store', 'update', 'destroy']);

    // Booking & Transaksi
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});

require __DIR__ . '/auth.php';
