<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->has('sort')) {
            $query->orderBy('name', $request->query('sort'));
        }

        $customers = $query->paginate(10)->appends($request->query());


        return view ('admin.customers.index', compact('customers'));
    }

    public function destroy($id){
        $customer = Customer::FindOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Data customer berhasil dihapus!');
    }
}
