<?php

namespace App\Http\Controllers\Customer;

use App\Models\RoomType;
use App\Models\Room;
use App\Models\Facilities;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerListRoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = RoomType::with(['rooms', 'facilities']);

        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
        }

        $roomTypes = $query->paginate(10)->appends($request->query());

        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();

        $facilities = Facilities::all();

        return response()->json([
            'success' => true,
            'message' => 'List Room Types',
            'data' => [
                'Room_Type' => $roomTypes,
                'total_rooms' => $totalRooms,
                'available_rooms' => $availableRooms,
            ]
        ]);

        return view('customer.list', compact('roomTypes', 'totalRooms', 'availableRooms', 'facilities'));
    }

    public function show($id)
    {
        $roomType = RoomType::with(['rooms', 'facilities'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Room Type',
            'data' => $roomType
        ]);

        return view('customer.list-show', compact('roomType'));
    }
}
