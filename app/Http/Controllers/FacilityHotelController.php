<?php

namespace App\Http\Controllers;

use App\Models\FacilityHotel;
use Illuminate\Http\Request;

class FacilityHotelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = FacilityHotel::query();

        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
        }

        $facility_hotel = $query->paginate(10)->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'List Facility_$facility_hotel',
            'data' => $facility_hotel
        ]);

        return view('admin.facility-hotel.index', compact('facility-hotels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string'
        ]);

        $facility_hotel = FacilityHotel::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Facility hotel Berhasil Ditambahkan',
            'data' => $facility_hotel
        ]);

        return redirect('admin.facility-hotel.index', compact('facility-hotels'));
    }

    public function show($id)
    {
        return FacilityHotel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $facility_hotel = FacilityHotel::findOrFail($id);
        $facility_hotel->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Facility hotel Berhasil Diperbarui',
            'data' => $facility_hotel
        ]);

        return redirect('admin.facility-hotel.index', compact('facility-hotels'));
    }

    public function destroy($id)
    {
        $facility_hotel = FacilityHotel::findOrFail($id);
        $facility_hotel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Facility hotel Berhasil Dihapus'
        ]);

        return redirect()->route('facility-hotels.index')->with('success', 'Facility hotel berhasil dihapus!');
    }
}
