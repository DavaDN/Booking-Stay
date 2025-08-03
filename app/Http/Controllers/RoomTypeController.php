<?php

// app/Http/Controllers/RoomTypeController.php
namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $roomTypes = RoomType::when($search, function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })->latest()->paginate(10);

        return view('room-types.index', compact('roomTypes', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);
        RoomType::create($request->only('name', 'price', 'description'));
        return back()->with('success', 'Tipe kamar berhasil ditambahkan.');
    }

    public function update(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);
        $roomType->update($request->only('name', 'price', 'description'));
        return back()->with('success', 'Tipe kamar berhasil diperbarui.');
    }

    public function destroy(RoomType $roomType)
    {
        $roomType->delete();
        return back()->with('success', 'Tipe kamar berhasil dihapus.');
    }
}
