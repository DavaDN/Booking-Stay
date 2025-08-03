<?php

// app/Http/Controllers/RoomController.php
namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $rooms = Room::with(['floor', 'roomType'])
            ->when($search, function ($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('rooms.index', [
            'rooms' => $rooms,
            'search' => $search,
            'floors' => Floor::all(),
            'roomTypes' => RoomType::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'floor_id' => 'required|exists:floors,id',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,booked,maintenance'
        ]);
        Room::create($request->only('number', 'floor_id', 'room_type_id', 'status'));
        return back()->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'number' => 'required',
            'floor_id' => 'required|exists:floors,id',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,booked,maintenance'
        ]);
        $room->update($request->only('number', 'floor_id', 'room_type_id', 'status'));
        return back()->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('success', 'Kamar berhasil dihapus.');
    }
}
