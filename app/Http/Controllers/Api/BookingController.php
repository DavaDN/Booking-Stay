<?php

// app/Http/Controllers/Api/BookingController.php
namespace App\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        return Booking::with('room.roomType')->where('customer_id', $request->user()->id)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $booking = Booking::create([
            'customer_id' => $request->user()->id,
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => 'pending',
        ]);

        return $booking;
    }
}
