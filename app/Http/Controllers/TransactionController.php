<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Booking;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Transaction::with('booking.customer');

        if ($search) {
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('booking_code', 'like', "%$search%");
            });
        }

        return $query->paginate(5);
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string',
            'total' => 'required|numeric'
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'payment_method' => $request->payment_method,
            'total' => $request->total,
            'status' => 'pending'
        ]);

        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        return Transaction::with('booking.customer')->findOrFail($id);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed'
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'status' => $request->status,
            'payment_date' => $request->status === 'paid' ? now() : null
        ]);

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        Transaction::destroy($id);
        return response()->json(['message' => 'Transaction deleted']);
    }
}
