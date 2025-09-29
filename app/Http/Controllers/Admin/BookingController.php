<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

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
        $bookings = $query->orderBy('id', 'desc')->paginate(5)->appends($request->query());
        return response()->json([
            'success' => true,
            'message' => 'List Bookings',
            'data' => $bookings
        ]);
        return view('admin.booking', compact('bookings'));
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

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated',
            'data' => $booking
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking status updated');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return response()->json([
            'success' => true,
            'message' => 'Booking deleted'
        ]);
        return redirect()->route('bookings.index')->with('success', 'Booking deleted');
    }
}
