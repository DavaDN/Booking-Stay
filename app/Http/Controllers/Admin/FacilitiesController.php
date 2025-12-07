<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilitiesController extends Controller
{
    public function index()
    {
        $facilities = Facilities::all();
        return view('admin.facilities', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only('name');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        Facilities::create($data);

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $facilities = Facilities::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only('name');

        if ($request->hasFile('image')) {
            if ($facilities->image && Storage::disk('public')->exists($facilities->image)) {
                Storage::disk('public')->delete($facilities->image);
            }
            $data['image'] = $request->file('image')->store('facilities', 'public');
        }

        $facilities->update($data);

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $facility = Facilities::findOrFail($id);

        if ($facility->image && Storage::disk('public')->exists($facility->image)) {
            Storage::disk('public')->delete($facility->image);
        }

        $facility->delete();

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil dihapus!');
    }
}
