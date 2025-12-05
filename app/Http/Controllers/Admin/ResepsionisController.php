<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resepsionis;
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
        return view('admin.resepsionis.create');
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


        return redirect()->route('resepsionis.index')->with('success', 'Akun resepsionis berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $resepsionis = Resepsionis::findOrFail($id);
        return view('admin.resepsionis.edit', compact('resepsionis'));
    }

    public function update(Request $request, $id)
    {
        $resepsionis = Resepsionis::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:resepsionis,email,' . $resepsionis->id,
            'password' => 'nullable|min:6',
        ]);

        $resepsionis->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? bcrypt($request->password) : $resepsionis->password,
        ]);


        return redirect()->route('resepsionis.index')->with('success', 'Akun resepsionis berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $resepsionis = Resepsionis::findOrFail($id);
        $resepsionis->delete();

        return redirect()->route('resepsionis.index')->with('success', 'Akun resepsionis berhasil dihapus!');
    }
}
