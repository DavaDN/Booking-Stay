<?php

// app/Http/Controllers/Api/TransactionController.php
namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:cash,card,transfer',
        ]);

        $transaction = Transaction::create([
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'paid',
        ]);

        $booking = Booking::find($request->booking_id);
        $booking->status = 'confirmed';
        $booking->save();

        return $transaction;
    }

    public function index(Request $request)
    {
        return Transaction::whereHas('booking', function ($q) use ($request) {
            $q->where('customer_id', $request->user()->id);
        })->get();
    }
}
