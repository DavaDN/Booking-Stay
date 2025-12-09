<?php

namespace App\Http\Controllers\Customer;

use App\Models\Booking;
use App\Models\RoomType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerBookController extends Controller
{
    /**
     * List booking customer
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Booking::where('customer_id', Auth::guard('customer')->id())
                        ->with(['roomType', 'transaction']);

        if ($search) {
            $query->where('booking_code', 'like', "%$search%");
        }

        $bookings = $query->orderBy('id', 'desc')->paginate(10)->appends($request->query());

        return view('customer.bookings.index', compact('bookings'));
    }

    /**
     * Show booking detail
     */
    public function show($id)
    {
        $booking = Booking::where('customer_id', Auth::guard('customer')->id())
                         ->with(['roomType', 'transaction'])
                         ->findOrFail($id);

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Create new booking
     */
    public function create()
    {
        $roomTypes = RoomType::with(['rooms', 'facilities'])->get();
        return view('customer.bookings.create', compact('roomTypes'));
    }

    /**
     * Store new booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'number_of_rooms' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        $customer = Auth::guard('customer')->user();
        $roomType = RoomType::findOrFail($request->room_type_id);

        // Generate booking code
        $booking_code = 'BK' . strtoupper(uniqid());

        // Calculate nights
        $checkIn = new \DateTime($request->check_in);
        $checkOut = new \DateTime($request->check_out);
        $nights = $checkOut->diff($checkIn)->days;
        $total_price = $roomType->price * $request->number_of_rooms * $nights;

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'room_type_id' => $request->room_type_id,
            'booking_code' => $booking_code,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'number_of_rooms' => $request->number_of_rooms,
            'total_price' => $total_price,
            'special_requests' => $request->special_requests,
            'status' => 'pending',
        ]);

        return redirect()->route('customer.bookings.index')
                       ->with('success', 'Booking berhasil dibuat! Kode: ' . $booking_code);
    }

    /**
     * Update booking status or cancel
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::where('customer_id', Auth::guard('customer')->id())
                         ->findOrFail($id);

        // Handle cancel action
        if ($request->action === 'cancel') {
            if ($booking->status !== 'pending') {
                return redirect()->back()->with('error', 'Hanya booking dengan status pending yang bisa dibatalkan');
            }
            $booking->update(['status' => 'cancelled']);
            return redirect()->route('customer.bookings.index')
                           ->with('success', 'Booking berhasil dibatalkan!');
        }

        // Handle update booking details
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'total_room' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        $booking->update($request->all());

        return redirect()->route('customer.bookings.show', $booking->id)
                       ->with('success', 'Booking berhasil diperbarui!');
    }

    /**
     * Delete booking
     */
    public function destroy($id)
    {
        $booking = Booking::where('customer_id', Auth::guard('customer')->id())
                         ->findOrFail($id);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya booking dengan status pending yang bisa dihapus');
        }

        $booking->delete();

        return redirect()->route('customer.bookings.index')
                       ->with('success', 'Booking berhasil dihapus!');
    }
}
