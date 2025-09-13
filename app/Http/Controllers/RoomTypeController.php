<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Facilities;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
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

        return view('admin.room-type', compact('roomTypes', 'totalRooms', 'availableRooms', 'facilities'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'capacity' => 'required|integer',
            'description' => 'nullable|string',
            'facility_ids' => 'required|array|min:1',
            'facility_ids.*' => 'exists:facilities,id',
        ]);

        // Cek apakah ada fasilitas yang duplikat
        if (count($request->facility_ids) !== count(array_unique($request->facility_ids))) {
            return response()->json([
                'success' => false,
                'message' => 'Fasilitas Duplikat Tidak Diperbolehkan.'
            ], 422);
        }

        $roomType = RoomType::create($request->only(['name', 'price', 'capacity', 'description']));
        $roomType->facilities()->attach($request->facility_ids);

        return response()->json([
            'success' => true,
            'message' => 'Room type created',
            'data' => $roomType->load('facilities')
        ]);

        return redirect()->route('room-types.index')->with('success', 'Room type berhasil ditambahkan.');
    }



    public function show($id)
    {
        return RoomType::with('rooms', 'facilities')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'capacity' => 'required|integer',
            'description' => 'nullable|string',
            'facility_ids' => 'nullable|array',
            'facility_ids.*' => 'exists:facilities,id',
        ]);

        $roomType = RoomType::findOrFail($id);
        $roomType->update($request->only(['name', 'price', 'capacity', 'description']));

        if ($request->has('facility_ids')) {
            $roomType->facilities()->sync($request->facility_ids);
        }
        return response()->json([
            'success' => true,
            'message' => 'Room type updated',
            'data' => $roomType->load('facilities')
        ]);
        return redirect()->route('room-types.index')->with('success', 'Room type berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $roomType = RoomType::findOrFail($id);
        $roomType->delete();
        return response()->json([
            'success' => true,
            'message' => 'Room type deleted'
        ]);

        return redirect()->route('room-types.index')->with('success', 'Room type berhasil dihapus.');
    }
}
