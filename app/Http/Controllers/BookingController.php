<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $bookings = Booking::with(['room.roomType', 'customer'])
            ->when($search, function ($query, $search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('room', function ($q) use ($search) {
                        $q->where('number', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings', 'search'));
    }
}
