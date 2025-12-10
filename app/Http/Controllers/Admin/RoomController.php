<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        $rooms = $query->paginate(10)->appends($request->query());


        return view('admin.room', compact('rooms'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1',
            'number' => 'required|string|max:50',
        ]);

        $room = Room::create($request->all());


        return redirect()->route('admin.rooms.index')->with('success', 'Room berhasil ditambahkan');
    }

    public function show($id)
    {
        return Room::with('roomType')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $room->update($request->all());


        return redirect()->route('admin.rooms.index')->with('success', 'Room berhasil diperbarui');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room berhasil dihapus');
    }
}
