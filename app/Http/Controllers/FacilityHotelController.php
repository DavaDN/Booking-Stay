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

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string'
        ]);

        $facility = FacilityHotel::create($request->all());

        return response()->json($facility, 201);
    }

    public function show($id)
    {
        return FacilityHotel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $facility = FacilityHotel::findOrFail($id);
        $facility->update($request->all());

        return response()->json($facility);
    }

    public function destroy($id)
    {
        FacilityHotel::destroy($id);
        return response()->json(['message' => 'Facility hotel deleted']);
    }
}
