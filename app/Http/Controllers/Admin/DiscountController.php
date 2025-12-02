<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $discounts = Discount::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')->paginate(5);

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'min_purchase' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
        ]);
        Discount::create($request->all());
        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function show(Discount $discount)
    {
        return view('admin.discounts.show', compact('discount'));
    }

    public function edit(Discount $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'min_purchase' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
        ]);
        $discount->update($request->all());
        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil dihapus.');
    }
}
