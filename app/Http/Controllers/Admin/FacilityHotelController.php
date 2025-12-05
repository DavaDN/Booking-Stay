<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityHotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilityHotelController extends Controller
{
    /**
     * Display list of facility hotels
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = FacilityHotel::query();

        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
        }

        $facilityHotels = $query->paginate(10)->appends($request->query());

        return view('admin.facility-hotel.index', compact('facilityHotels'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.facility-hotel.create');
    }

    /**
     * Store new facility hotel
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('facility-hotels', 'public');
        }

        FacilityHotel::create($data);

        return redirect()->route('facility-hotels.index')->with('success', 'Facility hotel berhasil ditambahkan!');
    }

    /**
     * Show facility hotel detail
     */
    public function show($id)
    {
        $facilityHotel = FacilityHotel::findOrFail($id);
        return view('admin.facility-hotel.show', compact('facilityHotel'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $facilityHotel = FacilityHotel::findOrFail($id);
        return view('admin.facility-hotel.edit', compact('facilityHotel'));
    }

    /**
     * Update facility hotel
     */
    public function update(Request $request, $id)
    {
        $facilityHotel = FacilityHotel::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($facilityHotel->image) {
                Storage::disk('public')->delete($facilityHotel->image);
            }
            $data['image'] = $request->file('image')->store('facility-hotels', 'public');
        }

        $facilityHotel->update($data);

        return redirect()->route('facility-hotels.index')->with('success', 'Facility hotel berhasil diperbarui!');
    }

    /**
     * Delete facility hotel
     */
    public function destroy($id)
    {
        $facilityHotel = FacilityHotel::findOrFail($id);

        // Delete image if exists
        if ($facilityHotel->image) {
            Storage::disk('public')->delete($facilityHotel->image);
        }

        $facilityHotel->delete();

        return redirect()->route('facility-hotels.index')->with('success', 'Facility hotel berhasil dihapus!');
    }
}
