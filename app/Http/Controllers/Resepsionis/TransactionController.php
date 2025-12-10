<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $query = Transaction::with('booking.customer');

        if ($hotelId) {
            $query->whereHas('booking.roomType', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        if ($search = $request->get('search')) {
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('booking_code', 'like', "%$search%");
            });
        }

        $transactions = $query->paginate(10)->appends($request->query());

        // Return resepsionis-specific view (uses $transactions variable)
        return view('resepsionis.transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $query = Transaction::with('booking.customer')->where('id', $id);

        if ($hotelId) {
            $query->whereHas('booking.roomType', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        $transaction = $query->firstOrFail();
        return view('resepsionis.transactions.show', compact('transaction'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed'
        ]);

        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $transaction = Transaction::with('booking.roomType')->findOrFail($id);

        if ($hotelId) {
            $roomHotelId = $transaction->booking->roomType->hotel_id ?? null;
            if ($roomHotelId != $hotelId) {
                abort(403, 'Unauthorized action.');
            }
        }

        $transaction->update([
            'status' => $request->status,
            'payment_date' => $request->status === 'paid' ? now() : null
        ]);

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $transaction = Transaction::with('booking.roomType')->findOrFail($id);

        if ($hotelId) {
            $roomHotelId = $transaction->booking->roomType->hotel_id ?? null;
            if ($roomHotelId != $hotelId) {
                abort(403, 'Unauthorized action.');
            }
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted']);
    }
}
