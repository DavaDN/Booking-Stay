<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Hotel::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $hotel = $query->paginate(10)->appends($request->query());


        return view('admin.hotels.index', compact('hotel'));
    }

    public function create()
    {
        // Load any data needed for the create form (e.g., available hotel facilities)
        $facilities = \App\Models\FacilityHotel::all();

        return view('admin.hotels.create', compact('facilities'));
    }

    public function edit($id)
    {
        $hotel = Hotel::with('facilities')->findOrFail($id);
        $facilities = \App\Models\FacilityHotel::all();

        return view('admin.hotels.create', compact('hotel', 'facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'facilities'  => 'nullable|array',
            'facilities.*'=> 'exists:facility_hotels,id',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('hotels', 'public');
        }

        $hotel = Hotel::create($data);

        if ($request->has('facilities')) {
            $hotel->facilities()->sync($request->facilities);
        }


        return $hotel
            ? redirect()->route('admin.hotels.index')->with('success', 'Hotel berhasil ditambahkan.')
            : redirect()->back()->with('error', 'Gagal menambahkan hotel. Silakan coba lagi.');
    }

    public function show($id)
    {
        return Hotel::findOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'facilities'  => 'nullable|array',
            'facilities.*'=> 'exists:facility_hotels,id',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('hotels', 'public');
        }

        $hotel->update($data);

        if ($request->has('facilities')) {
            $hotel->facilities()->sync($request->facilities);
        } else {
            // If no facilities submitted, detach any existing ones when editing
            $hotel->facilities()->detach();
        }

        return $hotel
            ? redirect()->route('admin.hotels.index')->with('success', 'Hotel berhasil diperbarui.')
            : redirect()->back()->with('error', 'Gagal memperbarui hotel. Silakan coba lagi.');
    }

    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel berhasil dihapus.');
    }
}
