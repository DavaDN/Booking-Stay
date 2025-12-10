<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resepsionis;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResepsionisController extends Controller
{
    public function index(Request $request)
    {
        $query = Resepsionis::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->has('sort')) {
            $query->orderBy('name', $request->query('sort'));
        }

        $resepsionis = $query->paginate(10)->appends($request->query());

        return view('admin.resepsionis.index', compact('resepsionis'));
    }

    public function create()
    {
        $hotels = Hotel::all();
        return view('admin.resepsionis.create', compact('hotels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:resepsionis,email',
            'password' => 'required|min:6',
            'hotel_id' => 'nullable|exists:hotels,id'
        ]);

        $resepsionis = Resepsionis::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'hotel_id' => $request->hotel_id,
        ]);


        return redirect()->route('admin.resepsionis.index')->with('success', 'Akun resepsionis berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $resepsionis = Resepsionis::findOrFail($id);
        $hotels = Hotel::all();
        return view('admin.resepsionis.edit', compact('resepsionis', 'hotels'));
    }

    public function update(Request $request, $id)
    {
        $resepsionis = Resepsionis::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:resepsionis,email,' . $resepsionis->id,
            'password' => 'nullable|min:6',
            'hotel_id' => 'nullable|exists:hotels,id'
        ]);

        $resepsionis->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? bcrypt($request->password) : $resepsionis->password,
            'hotel_id' => $request->hotel_id,
        ]);


        return redirect()->route('admin.resepsionis.index')->with('success', 'Akun resepsionis berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $resepsionis = Resepsionis::findOrFail($id);
        $resepsionis->delete();

        return redirect()->route('admin.resepsionis.index')->with('success', 'Akun resepsionis berhasil dihapus!');
    }
}
