<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = RoomType::with('rooms'); 

        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
        }

        $roomTypes = $query->paginate(10);

        // hitung total kamar dan kamar tersedia
        $totalRooms = \App\Models\Room::count();
        $availableRooms = \App\Models\Room::where('status', 'available')->count();

        return view('admin.room-type', compact('roomTypes', 'totalRooms', 'availableRooms'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'capacity' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        $roomType = RoomType::create($request->all());

        return response()->json($roomType, 201);
    }

    public function show($id)
    {
        return RoomType::with('rooms', 'facilities')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $roomType = RoomType::findOrFail($id);
        $roomType->update($request->all());

        return response()->json($roomType);
    }

    public function destroy($id)
    {
        RoomType::destroy($id);
        return response()->json(['message' => 'Room type deleted']);
    }
}
