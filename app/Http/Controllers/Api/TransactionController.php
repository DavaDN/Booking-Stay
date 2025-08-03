<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return Transaction::with('booking.room.roomType')
            ->whereHas('booking', function ($q) use ($request) {
                $q->where('customer_id', $request->user()->id);
            })->get();
    }

    public function show($id)
    {
        $transaction = Transaction::with('booking.room.roomType')->findOrFail($id);
        return response()->json($transaction);
    }
}
