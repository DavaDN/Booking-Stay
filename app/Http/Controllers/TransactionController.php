<?php

// app/Http/Controllers/TransactionController.php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $transactions = Transaction::with(['booking.customer', 'booking.room'])
            ->when($search, function ($query, $search) {
                $query->whereHas('booking.customer', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('booking.room', function ($q) use ($search) {
                        $q->where('number', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions', 'search'));
    }
}
