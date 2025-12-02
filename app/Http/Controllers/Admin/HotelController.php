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

        return response()->json([
            'success' => true,
            'message' => 'List Hotel',
            'data' => $hotel
        ]);

        return view('admin.hotel', compact('hotel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('hotels', 'public');
        }

        $hotel = Hotel::create($request->all());

        if ($request->has('facilities')) {
            $hotel->facilities()->sync($request->facilities);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hotel berhasil ditambahkan.',
            'data' => $hotel
        ]);

        return $hotel
            ? redirect()->route('hotels.index')->with('success', 'Hotel berhasil ditambahkan.')
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
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('hotels', 'public');
        }

        $hotel->update($request->all());

        if ($request->has('facilities')) {
            $hotel->facilities()->sync($request->facilities);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hotel berhasil diperbarui.',
            'data' => $hotel
        ]);

        return $hotel
            ? redirect()->route('hotels.index')->with('success', 'Hotel berhasil diperbarui.')
            : redirect()->back()->with('error', 'Gagal memperbarui hotel. Silakan coba lagi.');
    }

    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hotel berhasil dihapus.'
        ]);

        return redirect()->route('hotels.index')->with('success', 'Hotel berhasil dihapus.');
    }
}
