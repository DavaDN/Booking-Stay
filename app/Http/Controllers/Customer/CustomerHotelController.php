<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class CustomerHotelController extends Controller
{
    /**
     * Menampilkan semua hotel
     */
    public function index()
    {
        $hotels = Hotel::with('rooms.roomType')->paginate(10);

        return view('customer.hotel.index', compact('hotels'));
    }

    /**
     * Menampilkan detail satu hotel
     */
    public function detail($id)
    {
        $hotel = Hotel::with('rooms.roomType')->findOrFail($id);

        return view('customer.hotel.detail', compact('hotel'));
    }
}
