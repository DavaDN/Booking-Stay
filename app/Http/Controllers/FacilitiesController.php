<?php

namespace App\Http\Controllers;

use App\Models\Facilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilitiesController extends Controller
{
    /**
     * Menampilkan daftar fasilitas dengan fitur pencarian & pagination
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Facilities::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $facilities = $query->paginate(10)->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'List Facilities',
            'data' => $facilities
        ]);

        return view('admin.facilities', compact('facilities'));
    }

    /**
     * Menyimpan fasilitas baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Jika ada file gambar, simpan ke storage
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        $facility = Facilities::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Facility berhasil ditambahkan.',
            'data' => $facility
        ]);

        return $facility
            ? redirect()->route('facilities.index')->with('success', 'Facility berhasil ditambahkan.')
            : back()->with('error', 'Gagal menambahkan facility.');
    }

    /**
     * Menampilkan detail fasilitas
     */
    public function show($id)
    {
        return Facilities::findOrFail($id);
    }

    /**
     * Update fasilitas
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $facility = Facilities::findOrFail($id);
        $data = ['name' => $request->name];

        // Jika ada file baru, hapus file lama dan upload yang baru
        if ($request->hasFile('image')) {
            if ($facility->image) {
                Storage::disk('public')->delete($facility->image);
            }
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        $facility->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Facility berhasil diperbarui.',
            'data' => $facility
        ]);

        return redirect()->route('facilities.index')->with('success', 'Facility berhasil diperbarui.');
    }

    /**
     * Hapus fasilitas
     */
    public function destroy($id)
    {
        $facility = Facilities::findOrFail($id);

        // Hapus gambar jika ada
        if ($facility->image) {
            Storage::disk('public')->delete($facility->image);
        }

        $facility->delete();

        return response()->json([
            'success' => true,
            'message' => 'Facility berhasil dihapus.'
        ]);

        return redirect()->route('facilities.index')->with('success', 'Facility berhasil dihapus.');
    }
}
