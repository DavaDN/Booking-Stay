<?php

// app/Http/Controllers/FloorController.php
namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $floors = Floor::when($search, function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })->latest()->paginate(10);

        return view('floors.index', compact('floors', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Floor::create($request->only('name'));
        return back()->with('success', 'Lantai berhasil ditambahkan.');
    }

    public function update(Request $request, Floor $floor)
    {
        $request->validate(['name' => 'required']);
        $floor->update($request->only('name'));
        return back()->with('success', 'Lantai berhasil diperbarui.');
    }

    public function destroy(Floor $floor)
    {
        $floor->delete();
        return back()->with('success', 'Lantai berhasil dihapus.');
    }
}
