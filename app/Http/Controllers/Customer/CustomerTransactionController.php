<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerTransactionController extends Controller
{
    /**
     * List transaction customer
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $customerId = Auth::guard('customer')->id();
        
        $query = Transaction::with(['booking.customer'])
            ->where(function($q) use ($customerId) {
                $q->whereHas('booking', function ($q2) use ($customerId) {
                    $q2->where('customer_id', $customerId);
                })->orWhere('customer_id', $customerId);
            });

        if ($search) {
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('booking_code', 'like', "%$search%");
            });
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(10)->appends($request->query());

        return view('customer.transactions.index', compact('transactions'));
    }

    /**
     * Show transaction detail
     */
    public function show($id)
    {
        $customerId = Auth::guard('customer')->id();
        
        $transaction = Transaction::with(['booking.customer', 'booking.roomType'])
            ->where(function($q) use ($customerId) {
                $q->whereHas('booking', function ($q2) use ($customerId) {
                    $q2->where('customer_id', $customerId);
                })->orWhere('customer_id', $customerId);
            })->findOrFail($id);

        return view('customer.transactions.show', compact('transaction'));
    }

    /**
     * Update transaction (cancel only)
     */
    public function update(Request $request, $id)
    {
        $customerId = Auth::guard('customer')->id();
        
        $transaction = Transaction::whereHas('booking', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })->findOrFail($id);

        $request->validate([
            'action' => 'required|in:cancel',
        ]);

        if ($request->action === 'cancel' && $transaction->status === 'pending') {
            $transaction->update(['status' => 'cancelled']);

            // Also mark related booking as cancelled if exists and still pending
            if ($transaction->booking && $transaction->booking->status === 'pending') {
                $transaction->booking->update(['status' => 'cancelled']);
            }

            return redirect()->route('customer.transactions.index')
                           ->with('success', 'Transaksi berhasil dibatalkan. Booking terkait juga dibatalkan jika masih pending.');
        }

        return back()->with('error', 'Tidak dapat membatalkan transaksi dengan status ini.');
    }
}
