<?php

namespace App\Http\Controllers\Customer;

use App\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerBookController extends Controller
{
    public function index()
    {
        return view('customer.booking.index');
    }

    public function create()
    {
        return view('customer.booking.create');
    }

    public function store(Request $request)
    {
        $request()->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'total_room' => 'required|integer|min:1'

        ]);

        $booking = Booking::create($request->all());

        return $booking
            ? redirect()->route('transaction.index')->with('success', 'Booking berhasil ditambahkan.')
            : back()->with('error', 'Gagal menambahkan Booking.');
    }
}
