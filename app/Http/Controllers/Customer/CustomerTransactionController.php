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
        
        $query = Transaction::whereHas('booking', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })->with(['booking.customer']);

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
        
        $transaction = Transaction::whereHas('booking', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })->with(['booking.customer', 'booking.roomType'])->findOrFail($id);

        return view('customer.transactions.show', compact('transaction'));
    }

    /**
     * Create new transaction for booking
     */
    public function create()
    {
        $customerId = Auth::guard('customer')->id();
        $bookings = Booking::where('customer_id', $customerId)
                          ->where('status', 'confirmed')
                          ->doesntHave('transaction')
                          ->get();

        return view('customer.transactions.create', compact('bookings'));
    }

    /**
     * Store new transaction
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,cash,e_wallet',
        ]);

        $customerId = Auth::guard('customer')->id();
        $booking = Booking::where('customer_id', $customerId)
                         ->findOrFail($request->booking_id);

        // Check if transaction already exists
        if ($booking->transaction) {
            return back()->with('error', 'Booking ini sudah memiliki transaksi.');
        }

        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'payment_method' => $request->payment_method,
            'total' => $booking->total_price,
            'status' => 'pending',
        ]);

        return redirect()->route('customer.transactions.show', $transaction->id)
                       ->with('success', 'Transaksi berhasil dibuat!');
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
            
            return redirect()->route('customer.transactions.index')
                           ->with('success', 'Transaksi berhasil dibatalkan.');
        }

        return back()->with('error', 'Tidak dapat membatalkan transaksi dengan status ini.');
    }
}
