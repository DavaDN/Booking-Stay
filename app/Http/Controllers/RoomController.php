<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Room::with('roomType');

        if ($search) {
            $query->where('number', 'like', "%$search%")
                ->orWhereHas('roomType', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        $rooms = $query->paginate(12);

        return view('admin.room', compact('rooms'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'number' => 'required|string|max:50',
            'status' => 'required|in:available,booked,maintenance'
        ]);

        $room = Room::create($request->all());

        return response()->json($room, 201);
    }

    public function show($id)
    {
        return Room::with('roomType')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $room->update($request->all());

        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room berhasil dihapus');
    }
}
