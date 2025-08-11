<?php

namespace App\Http\Controllers;

use App\Models\Facilities;
use Illuminate\Http\Request;

class FacilitiesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Facilities::with('roomType');

        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhereHas('roomType', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|string'
        ]);

        $facilities = Facilities::create($request->all());

        return response()->json($facilities, 201);
    }

    public function show($id)
    {
        return Facilities::with('roomType')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $facilities = Facilities::findOrFail($id);
        $facilities->update($request->all());

        return response()->json($facilities);
    }

    public function destroy($id)
    {
        Facilities::destroy($id);
        return response()->json(['message' => 'Facilities room deleted']);
    }
}
