<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Booking::with(['customer', 'roomType']);

        if ($search) {
            $query->where('booking_code', 'like', "%$search%")
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('username', 'like', "%$search%");
                });
        }

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'total_room' => 'required|integer|min:1'
        ]);

        $booking = Booking::create([
            'customer_id' => $request->user()->id,
            'room_type_id' => $request->room_type_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'total_room' => $request->total_room,
            'booking_code' => strtoupper(Str::random(8)),
            'status' => 'pending'
        ]);

        return response()->json($booking, 201);
    }

    public function show($id)
    {
        return Booking::with(['customer', 'roomType'])->findOrFail($id);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,checked_in,checked_out'
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return response()->json($booking);
    }

    public function destroy($id)
    {
        Booking::destroy($id);
        return response()->json(['message' => 'Booking deleted']);
    }
}
