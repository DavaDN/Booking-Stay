<?php

namespace App\Http\Controllers;

use App\Models\Facilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilitiesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Facilities::query();

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $facilities = $query->paginate(10);

        return view('admin.facilities', compact('facilities'));
    }

   public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        $facility = Facilities::create($data);

        if ($facility) {
            return redirect()->route('facilities.index')->with('success', 'Facility berhasil ditambahkan.');
        } else {
            return back()->with('error', 'Gagal menambahkan facility.');
        }
    }


    public function show($id)
    {
        return Facilities::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $facilities = Facilities::findOrFail($id);
        $data = $request->only('name');

        if ($request->hasFile('image')) {
            // hapus gambar lama jika ada
            if ($facilities->image) {
                Storage::disk('public')->delete($facilities->image);
            }
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        $facilities->update($data);

        return redirect()->route('facilities.index')->with('success', 'Facility updated successfully!');
    }

    public function destroy($id)
    {
        $facilities = Facilities::findOrFail($id);

        if ($facilities->image) {
            Storage::disk('public')->delete($facilities->image);
        }

        $facilities->delete();

        return redirect()->route('facilities.index')->with('success', 'Facility deleted successfully!');
    }
}
