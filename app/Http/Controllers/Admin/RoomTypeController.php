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

        return view('admin.room-type', compact('roomTypes', 'totalRooms'));
    }

    public function create()
    {
        $facilities = Facilities::all();
        $hotels = \App\Models\Hotel::all();
        return view('admin.room-type.create', compact('facilities', 'hotels'));
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
            'hotel_ids'     => 'required|array|min:1',
            'hotel_ids.*'   => 'exists:hotels,id',
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

        // Attach hotels
        $roomType->hotels()->attach($request->hotel_ids);

        return redirect()->route('admin.room-types.index')->with('success', 'Room type berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $roomType = RoomType::findOrFail($id);
        $facilities = Facilities::all();
        $selected = $roomType->facilities->pluck('id')->toArray();
        $hotels = \App\Models\Hotel::all();
        $selectedHotels = $roomType->hotels->pluck('id')->toArray();

        return view('admin.room-type.edit', compact('roomType', 'facilities', 'selected', 'hotels', 'selectedHotels'));
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
            'hotel_ids'     => 'nullable|array',
            'hotel_ids.*'   => 'exists:hotels,id',
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

        // Update hotels
        if ($request->has('hotel_ids')) {
            $roomType->hotels()->sync($request->hotel_ids);
        } else {
            $roomType->hotels()->detach();
        }

        return redirect()->route('admin.room-types.index')->with('success', 'Room type berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $roomType = RoomType::findOrFail($id);
        $roomType->delete();

        return redirect()->route('admin.room-types.index')->with('success', 'Room type berhasil dihapus.');
    }
}
