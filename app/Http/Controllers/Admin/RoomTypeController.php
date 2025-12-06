<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Facilities;
use App\Models\Room;
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

        return view('admin.room-type', compact('roomTypes', 'totalRooms', 'availableRooms'));
    }

    public function create()
    {
        $facilities = Facilities::all();
        return view('admin.room-type.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric',
            'capacity'      => 'required|integer',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'facility_ids'  => 'required|array|min:1',
            'facility_ids.*'=> 'exists:facilities,id',
        ]);

        $roomType = RoomType::create($request->only(['name', 'price', 'capacity', 'description']));

        // Upload gambar
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('room-types'), $filename);

            $roomType->image = $filename;
            $roomType->save();
        }

        // Attach fasilitas
        $roomType->facilities()->attach($request->facility_ids);

        return redirect()->route('room-types.index')->with('success', 'Room type berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $roomType = RoomType::findOrFail($id);
        $facilities = Facilities::all();
        $selected = $roomType->facilities->pluck('id')->toArray();

        return view('admin.room-type.edit', compact('roomType', 'facilities', 'selected'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric',
            'capacity'      => 'required|integer',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'facility_ids'  => 'nullable|array',
            'facility_ids.*'=> 'exists:facilities,id',
        ]);

        $roomType = RoomType::findOrFail($id);
        $roomType->update($request->only(['name', 'price', 'capacity', 'description']));

        // Update gambar
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('room-types'), $filename);

            $roomType->image = $filename;
            $roomType->save();
        }

        // Update fasilitas
        if ($request->has('facility_ids')) {
            $roomType->facilities()->sync($request->facility_ids);
        }

        return redirect()->route('room-types.index')->with('success', 'Room type berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $roomType = RoomType::findOrFail($id);
        $roomType->delete();

        return redirect()->route('room-types.index')->with('success', 'Room type berhasil dihapus.');
    }
}
