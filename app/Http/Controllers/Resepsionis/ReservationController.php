<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $reservationsQuery = Reservation::with(['customer', 'room.roomType']);
        if ($hotelId) {
            $reservationsQuery->whereHas('room.roomType', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        $reservations = $reservationsQuery->latest()->get();

        $customers = Customer::all();
        $roomsQuery = Room::with('roomType');
        if ($hotelId) {
            $roomsQuery->whereHas('roomType', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }
        $rooms = $roomsQuery->get();

        return view('resepsionis.reservations.index', compact('reservations', 'customers', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required',
            'room_id'        => 'required',
            'check_in_date'  => 'required|date',
            'check_in_time'  => 'required',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'check_out_time' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::guard('resepsionis')->user();
                $hotelId = $user->hotel_id ?? null;

                $roomQuery = Room::where('id', $request->room_id)
                    ->where('status', 'available');

                if ($hotelId) {
                    $roomQuery->whereHas('roomType', function ($q) use ($hotelId) {
                        $q->where('hotel_id', $hotelId);
                    });
                }

                $room = $roomQuery->lockForUpdate()->firstOrFail();

                Reservation::create($request->all());

                $room->status = 'booked';
                $room->save();
            }, 5);

            return back()->with('success', 'Reservation created successfully');
        } catch (\Exception $e) {
            Log::error('Reservation create failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat reservation. Kamar mungkin sudah dibooking oleh orang lain.');
        }
    }

    /**
     * Create a booking (walk-in) from resepsionis and create a pending transaction.
     * Then call Midtrans snap API and return a checkout view with token.
     */
    public function createBooking(Request $request)
    {
        $request->validate([
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_email' => 'nullable|email|required_without:customer_id',
            'customer_id' => 'nullable|exists:customers,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'price_per_day' => 'required|numeric|min:0',
        ]);

        try {
            $user = auth()->guard('resepsionis')->user();

            // customer
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
            } else {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                ]);
            }

            // compute nights
            $checkIn = new \DateTime($request->check_in);
            $checkOut = new \DateTime($request->check_out);
            $nights = $checkOut->diff($checkIn)->days;
            $totalPrice = $request->price_per_day * max(1, $nights);

            // create booking (pending until payment)
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'room_type_id' => $request->room_type_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'number_of_rooms' => 1,
                'total_price' => $totalPrice,
                'booking_code' => 'BK' . strtoupper(uniqid()),
                'status' => 'pending',
            ]);

            // create transaction
            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'customer_id' => $customer->id,
                'payment_method' => 'midtrans',
                'total' => $totalPrice,
                'status' => 'pending',
            ]);

            // set order id
            $orderId = 'ORDER-' . $transaction->id . '-' . time();
            $transaction->midtrans_order_id = $orderId;
            $transaction->save();

            // Call Midtrans Snap API to create token
            $serverKey = config('midtrans.server_key');
            $isProduction = config('midtrans.is_production');

            $url = $isProduction
                ? 'https://api.midtrans.com/snap/v1/transactions'
                : 'https://api.sandbox.midtrans.com/snap/v1/transactions';

            $payload = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $transaction->total,
                ],
                'customer_details' => [
                    'first_name' => $customer->name ?? 'Guest',
                    'email' => $customer->email ?? null,
                ],
                'item_details' => [
                    [
                        'id' => $request->room_type_id,
                        'price' => (int) $transaction->total,
                        'quantity' => 1,
                        'name' => 'Booking ' . ($booking->booking_code ?? $booking->id),
                    ]
                ],
            ];

            $client = Http::withBasicAuth($serverKey, '')->asJson();
            if (!$isProduction) $client = $client->withOptions(['verify' => false]);
            $response = $client->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? null;
                $transaction->midtrans_response = json_encode($data);
                $transaction->save();

                // Redirect to checkout view with token
                return view('resepsionis.payments.snap', compact('token', 'transaction'));
            }

            Log::error('Midtrans create snap failed (resepsionis)', ['resp' => $response->body()]);
            return back()->with('error', 'Gagal membuat pembayaran Midtrans');

        } catch (\Exception $e) {
            Log::error('Resepsionis createBooking error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat booking: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $reservation = Reservation::with('room.roomType')->findOrFail($id);

        if ($hotelId) {
            $roomHotelId = $reservation->room->roomType->hotel_id ?? null;
            if ($roomHotelId != $hotelId) {
                abort(403, 'Unauthorized action.');
            }
        }

        $oldStatus = $reservation->status;
        $reservation->update($request->all());

        // If resepsionis marks reservation as checked-in/checked-out, sync related booking status
        try {
            $newStatus = $reservation->status;
            if ($oldStatus !== $newStatus) {
                // Map reservation status to booking status
                if (in_array($newStatus, ['check_in', 'checked_in', 'check-in'])) {
                    // Find booking for same customer, same roomType and date range
                    $roomTypeId = $reservation->room->roomType->id ?? null;
                    if ($roomTypeId) {
                        $booking = Booking::where('customer_id', $reservation->customer_id)
                            ->where('room_type_id', $roomTypeId)
                            ->whereDate('check_in', $reservation->check_in_date)
                            ->whereDate('check_out', $reservation->check_out_date)
                            ->first();

                        if ($booking && $booking->status !== 'check-in') {
                            $booking->update(['status' => 'check-in']);
                        }
                    }
                }

                if (in_array($newStatus, ['check_out', 'checked_out', 'check-out'])) {
                    $roomTypeId = $reservation->room->roomType->id ?? null;
                    if ($roomTypeId) {
                        $booking = Booking::where('customer_id', $reservation->customer_id)
                            ->where('room_type_id', $roomTypeId)
                            ->whereDate('check_in', $reservation->check_in_date)
                            ->whereDate('check_out', $reservation->check_out_date)
                            ->first();

                        if ($booking && $booking->status !== 'check-out') {
                            $booking->update(['status' => 'check-out']);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync booking status from reservation update: ' . $e->getMessage());
        }

        return back()->with('success', 'Reservation updated successfully');
    }

    public function destroy($id)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $reservation = Reservation::with('room.roomType')->findOrFail($id);

        if ($hotelId) {
            $roomHotelId = $reservation->room->roomType->hotel_id ?? null;
            if ($roomHotelId != $hotelId) {
                abort(403, 'Unauthorized action.');
            }
        }

        Room::where('id', $reservation->room_id)
            ->update(['status' => 'available']);

        $reservation->delete();

        return back()->with('success', 'Reservation deleted successfully');
    }
}
