<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $search = $request->get('search');

        // Only bookings for this resepsionis's hotel and with a paid transaction
        $query = Booking::with(['customer', 'roomType', 'transaction'])
            ->when($hotelId, function ($q) use ($hotelId) {
                $q->whereHas('roomType', function ($q2) use ($hotelId) {
                    $q2->where('hotel_id', $hotelId);
                });
            })
            ->whereHas('transaction', function ($qt) {
                $qt->where('status', 'paid');
            });

        if ($search) {
            $query->where('booking_code', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        return view('resepsionis.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $booking = Booking::with(['customer', 'roomType', 'transaction'])->findOrFail($id);

        // ensure booking belongs to resepsionis hotel
        if ($hotelId && ($booking->roomType->hotel_id ?? null) != $hotelId) {
            abort(403, 'Unauthorized');
        }

        // ensure transaction is paid
        if (! $booking->transaction || ($booking->transaction->status ?? null) !== 'paid') {
            abort(403, 'Booking not available for resepsionis (payment not completed)');
        }

        return view('resepsionis.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Resepsionis may only change booking status to check-in, check-out or cancelled
        $request->validate(['status' => 'required|in:check-in,check-out,cancelled']);

        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $booking = Booking::with('roomType')->findOrFail($id);
        if ($hotelId && ($booking->roomType->hotel_id ?? null) != $hotelId) {
            abort(403, 'Unauthorized');
        }

        $old = $booking->status;
        $booking->update(['status' => $request->status]);

        return redirect()->route('resepsionis.bookings.show', $booking->id)
            ->with('success', "Status updated from {$old} to {$request->status}");
    }
}
