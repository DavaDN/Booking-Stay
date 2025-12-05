<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FacilityHotel;
use App\Models\Hotel;
use Illuminate\Http\Request;

class CustomerListHotelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Hotel::with(['rooms', 'facilities']);

        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
        }

        $hotels = $query->paginate(10)->appends($request->query());

        $facilityhotel = FacilityHotel::all();


        return view('customer.home', compact('hotels', 'facilityhotel'));
    }

    public function show($id)
    {
        $hotel = Hotel::with(['rooms', 'facilities'])->findOrFail($id);
        $facilityhotel = FacilityHotel::all();

        return view('customer.hotels-show', compact('hotel', 'facilityhotel'));
    }
}
