<?php

namespace App\Http\Controllers\Customer;

use App\Models\RoomType;
use App\Models\Room;
use App\Models\Facilities;
use App\Models\Hotel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerListRoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $city = $request->get('city');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $hotelId = $request->get('hotel_id');

        $query = RoomType::with(['rooms', 'facilities', 'hotel']);

        // Filter by search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by city (through hotel relationship)
        if ($city) {
            $query->whereHas('hotel', function($q) use ($city) {
                $q->where('city', 'like', "%{$city}%");
            });
        }

        // Filter by hotel
        if ($hotelId) {
            $query->where('hotel_id', $hotelId);
        }

        // Filter by price range
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        $query->orderBy('created_at', 'desc');

        $roomTypes = $query->paginate(12)->appends($request->query());

        // Get all hotels for filter dropdown
        $hotels = Hotel::orderBy('name')->get();

        // Get available cities
        $cities = Hotel::select('city')->distinct()->orderBy('city')->pluck('city');

        $totalRooms = Room::count();

        $facilities = Facilities::all();


        return view('customer.list', compact('roomTypes', 'hotels', 'cities', 'totalRooms', 'facilities'));
    }

    public function show($id)
    {
        $roomType = RoomType::with(['rooms', 'facilities', 'hotel.facilities'])
            ->findOrFail($id);

        $roomType->total_rooms = $roomType->rooms()->count();

        return view('customer.list-show', compact('roomType'));
    }
}
