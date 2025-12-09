<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of all bookings from customers
     * 
     * Fitur Admin Booking Management:
     * - Melihat semua booking yang dibuat customer
     * - Search booking berdasarkan kode atau nama customer
     * - Update status booking (pending → confirmed → checked_in → checked_out)
     * - Lihat detail lengkap booking dengan info customer & transaction
     * - Kelola pembayaran dan cek status transaksi
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        
        $query = Booking::with(['customer', 'roomType', 'transaction']);

        if ($search) {
            $query->where('booking_code', 'like', "%$search%")
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('username', 'like', "%$search%")
                      ->orWhere('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
        $statusOptions = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];

        return view('admin.booking.index', compact('bookings', 'statusOptions'));
    }

    /**
     * Show detail booking dengan informasi customer dan pembayaran
     */
    public function show($id)
    {
        $booking = Booking::with(['customer', 'roomType', 'transaction'])->findOrFail($id);
        
        return view('admin.booking.show', compact('booking'));
    }

    /**
     * Update booking status
     * 
     * Status workflow:
     * pending → confirmed → checked_in → checked_out
     * atau langsung → cancelled
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,checked_in,checked_out'
        ]);

        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        return redirect()->route('admin.bookings.show', $booking->id)
                       ->with('success', "Booking status berhasil diupdate dari {$oldStatus} menjadi {$request->status}");
    }

    /**
     * Delete booking (hanya jika status masih pending)
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya booking dengan status pending yang bisa dihapus');
        }
        
        $booking->delete();
        
        return redirect()->route('admin.bookings.index')->with('success', 'Booking berhasil dihapus');
    }
}
