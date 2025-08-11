<?php

namespace App\Http\Controllers;

use App\Models\Resepsionis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResepsionisController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Resepsionis::query();

        if ($search) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:resepsionis,email',
            'password' => 'required|min:6',
        ]);

        $resepsionis = Resepsionis::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($resepsionis, 201);
    }

    public function show($id)
    {
        return Resepsionis::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $resepsionis = Resepsionis::findOrFail($id);

        $resepsionis->update([
            'name' => $request->name ?? $resepsionis->name,
            'email' => $request->email ?? $resepsionis->email,
            'password' => $request->password ? Hash::make($request->password) : $resepsionis->password,
        ]);

        return response()->json($resepsionis);
    }

    public function destroy($id)
    {
        Resepsionis::destroy($id);
        return response()->json(['message' => 'Resepsionis deleted']);
    }
}
