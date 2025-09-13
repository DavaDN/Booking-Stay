<?php

namespace App\Http\Controllers;

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

        $customer = $query->paginate(10)->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'List Customers',
            'data' => $customer
        ]);

        return view ('admin.customer.index', compact('customers'));
    }

    public function destroy($id){
        $customer = Customer::FindOrFail($id);
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Customer Berhasil Dihapus'
        ]);
    }
}
