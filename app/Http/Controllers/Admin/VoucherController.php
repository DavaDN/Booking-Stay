<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $voucher = Voucher::when($search, function ($query) use ($search) {
            $query->where('code', 'like', '%' . $search . '%');
        })->orderBy('id', 'desc')->paginate(5);

        return view('voucher', compact('voucher', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:voucher,code',
            'discount' => 'required|numeric|min:0|max:100',
            'expired_at' => 'required|date|after_or_equal:today',
        ]);

        Voucher::create([
            'code' => $request->code,
            'discount' => $request->discount,
            'expired_at' => $request->expired_at,
        ]);


        return redirect()->route('voucher.index')->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|unique:voucher,code,' . $voucher->id,
            'discount' => 'required|numeric|min:0|max:100',
            'expired_at' => 'required|date|after_or_equal:today',
        ]);

        $voucher->update([
            'code' => $request->code,
            'discount' => $request->discount,
            'expired_at' => $request->expired_at,
        ]);


        return redirect()->route('voucher.index')->with('success', 'Voucher berhasil diupdate.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('voucher.index')->with('success', 'Voucher berhasil dihapus.');
    }
}
