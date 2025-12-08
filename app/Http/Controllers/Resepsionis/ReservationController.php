<?php

namespace App\Http\Controllers\resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Customer;
use App\Models\Room;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['customer', 'room.roomType'])
            ->latest()
            ->get();

        $customers = Customer::all();
        $rooms = Room::with('roomType')->get();

        return view('resepsionis.reservations.index', compact('reservations', 'customers', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required',
            'room_id'        => 'required',
            'check_in_date'  => 'required|date',
            'check_in_time'  => 'required',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'check_out_time' => 'required',
        ]);

        Reservation::create($request->all());

        Room::where('id', $request->room_id)->update(['status' => 'booked']);

        return back()->with('success', 'Reservation created successfully');
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update($request->all());

        return back()->with('success', 'Reservation updated successfully');
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        Room::where('id', $reservation->room_id)
            ->update(['status' => 'available']);

        $reservation->delete();

        return back()->with('success', 'Reservation deleted successfully');
    }
}
