<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        return Booking::with('room.roomType')
            ->where('customer_id', $request->user()->id)
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:cash,card,transfer',
        ]);

        // 1. Simpan booking
        $booking = Booking::create([
            'code' => strtoupper(Str::random(10)),
            'customer_id' => $request->user()->id,
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => 'confirmed', // langsung confirmed
        ]);

        // 2. Update status room
        $booking->room->update(['status' => 'booked']);

        // 3. Simpan transaksi
        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'paid',
            'date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Booking dan transaksi berhasil',
            'booking' => $booking,
            'transaction' => $transaction,
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with('room.roomType')->findOrFail($id);
        return response()->json($booking);
    }
}
