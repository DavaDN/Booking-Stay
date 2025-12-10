<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $reservationsQuery = Reservation::with(['room.roomType']);
        if ($hotelId) {
            $reservationsQuery->whereHas('room.roomType', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        // Search query (searches customer name/email, room number, room type name)
        $search = $request->get('q');
        if ($search) {
            $reservationsQuery->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                                        ->orWhereHas('customer', function ($c) use ($search) {
                                                $c->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%");
                                        })
                    ->orWhereHas('room', function ($r) use ($search) {
                        $r->where('number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('room.roomType', function ($rt) use ($search) {
                        $rt->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Optional status filter
        if ($request->filled('status')) {
            $reservationsQuery->where('status', $request->get('status'));
        }

        // Pagination: allow only a small set of page sizes, default to 10
        $allowed = [10, 15, 25, 50];
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        // preserve all query string parameters except `page` when generating pagination links
        $reservations = $reservationsQuery->latest()->paginate($perPage)->appends($request->query());

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

    /**
     * Show booking details (mapped from bookings resource).
     */
    public function show($id)
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $booking = Booking::with(['customer', 'roomType', 'transaction'])->findOrFail($id);

        // ensure booking belongs to resepsionis hotel when applicable
        if ($hotelId) {
            $roomTypeHotelId = $booking->roomType->hotel_id ?? null;
            if ($roomTypeHotelId != $hotelId) {
                abort(403, 'Booking not available for resepsionis (different hotel)');
            }
        }

        return view('resepsionis.bookings.show', compact('booking'));
    }

    /**
     * Show form to create a new reservation (separated view)
     */
    public function create()
    {
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $customers = Customer::all();
        $roomsQuery = Room::with('roomType');
        if ($hotelId) {
            $roomsQuery->whereHas('roomType', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }
        $rooms = $roomsQuery->get();

        // Load room types directly so the select always has options (scoped to hotel)
        $roomTypesQuery = RoomType::query();
        if ($hotelId) {
            $roomTypesQuery->where('hotel_id', $hotelId);
        }
        $roomTypes = $roomTypesQuery->get();

        return view('resepsionis.reservations.create', compact('customers', 'rooms', 'roomTypes'));
    }

    /**
     * Return rooms for a given room_type_id (JSON) scoped to resepsionis hotel.
     * Used by AJAX to populate room dropdown when a room type is selected.
     */
    public function roomsByType(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
        ]);

        $roomTypeId = $request->get('room_type_id');

        $checkIn = $request->get('check_in_date');
        $checkOut = $request->get('check_out_date');

        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        $roomsQuery = Room::with('roomType')
            ->where('room_type_id', $roomTypeId);

        if ($hotelId) {
            $roomsQuery->whereHas('roomType', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        // If client passed check-in and check-out, filter out rooms that already have
        // non-cancelled reservations overlapping the requested date range.
        if ($checkIn && $checkOut) {
            $roomsQuery->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', '!=', 'cancelled')
                  ->where(function ($qq) use ($checkIn, $checkOut) {
                      $qq->where('check_in_date', '<', $checkOut)
                         ->where('check_out_date', '>', $checkIn);
                  });
            });
        }

        $rooms = $roomsQuery->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'number' => $r->number,
                'room_type_id' => $r->room_type_id,
                'room_type_name' => $r->roomType->name ?? null,
            ];
        });

        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_email' => 'nullable|email|required_without:customer_id',
            'room_id'        => 'required|exists:rooms,id',
            'check_in_date'  => 'required|date',
            'check_in_time'  => 'required',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'check_out_time' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::guard('resepsionis')->user();
                $hotelId = $user->hotel_id ?? null;

                // find/validate room availability
                $roomQuery = Room::where('id', $request->room_id);

                if ($hotelId) {
                    $roomQuery->whereHas('roomType', function ($q) use ($hotelId) {
                        $q->where('hotel_id', $hotelId);
                    });
                }

                $room = $roomQuery->lockForUpdate()->firstOrFail();

                // Determine customer_id (if resepsionis selected an existing customer)
                $customerId = $request->filled('customer_id') ? $request->customer_id : null;

                // create reservation storing guest name/email (do NOT create a Customer account here)
                $resData = [
                    'customer_id' => $customerId,
                    'customer_name' => $request->customer_name ?? null,
                    'customer_email' => $request->customer_email ?? null,
                    'room_id' => $room->id,
                    'check_in_date' => $request->check_in_date,
                    'check_in_time' => $request->check_in_time,
                    'check_out_date' => $request->check_out_date,
                    'check_out_time' => $request->check_out_time,
                    'status' => 'booked',
                ];

                Reservation::create($resData);
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
        ]);

        try {
            $user = auth()->guard('resepsionis')->user();

            // For walk-in bookings, do NOT create Customer accounts — store guest info on booking
            $customer = null;
            $guestName = $request->customer_name ?? null;
            $guestEmail = $request->customer_email ?? null;

            // compute nights
            $checkIn = new \DateTime($request->check_in);
            $checkOut = new \DateTime($request->check_out);
            $nights = $checkOut->diff($checkIn)->days;

            // price comes from room type, not user input
            $roomType = RoomType::findOrFail($request->room_type_id);
            // ensure room type belongs to the resepsionis' hotel (multi-tenant scope)
            $userHotelId = $user->hotel_id ?? null;
            if ($userHotelId && ($roomType->hotel_id ?? null) != $userHotelId) {
                abort(403, 'Unauthorized action.');
            }
            $pricePerDay = (float) $roomType->price;
            $totalPrice = $pricePerDay * max(1, $nights);
            // create reservation (pending until payment) instead of booking
            // Ensure the room belongs to the resepsionis' hotel
            $room = Room::where('id', $request->room_id)->with('roomType')->firstOrFail();
            if ($userHotelId && (($room->roomType->hotel_id ?? null) != $userHotelId)) {
                abort(403, 'Unauthorized action.');
            }

            $reservation = Reservation::create([
                'customer_id' => null,
                'customer_name' => $guestName,
                'customer_email' => $guestEmail,
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in,
                'check_in_time' => '00:00:00',
                'check_out_date' => $request->check_out,
                'check_out_time' => '00:00:00',
                'status' => 'pending',
            ]);

            // create transaction referencing reservation (no customer account)
            $transaction = Transaction::create([
                'booking_id' => null,
                'customer_id' => null,
                'payment_method' => 'midtrans',
                'total' => $totalPrice,
                'status' => 'pending',
                'meta' => [
                    'type' => 'reservation_ref',
                    'reservation_id' => $reservation->id,
                    'guest_name' => $guestName,
                    'guest_email' => $guestEmail,
                ],
            ]);

            // set order id for this transaction
            $orderId = 'ORDER-' . $transaction->id . '-' . time();
            $transaction->midtrans_order_id = $orderId;
            $transaction->save();

            // prepare Midtrans payload using guest info
            $payload = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $transaction->total,
                ],
                'customer_details' => [
                    'first_name' => $guestName ?? 'Guest',
                    'email' => $guestEmail ?? null,
                ],
                'item_details' => [
                    [
                        'id' => $request->room_type_id,
                        'price' => (int) $transaction->total,
                        'quantity' => 1,
                        'name' => 'Reservation ' . ($reservation->id),
                    ]
                ],
            ];

            // load Midtrans configuration and build endpoint URL
            $serverKey = config('midtrans.server_key');
            $isProduction = config('midtrans.is_production');
            $url = $isProduction
                ? 'https://api.midtrans.com/snap/v1/transactions'
                : 'https://api.sandbox.midtrans.com/snap/v1/transactions';

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

    /**
     * Initiate payment for a reservation: create a Transaction with reservation payload
     * and redirect to Midtrans checkout (token view).
     */
    public function pay(Request $request)
    {
        $request->validate([
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_email' => 'nullable|email|required_without:customer_id',
            'customer_id' => 'nullable|exists:customers,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_in_time' => 'required',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'check_out_time' => 'required',
        ]);

        try {
            $user = auth()->guard('resepsionis')->user();

            // customer: if resepsionis selected an existing customer, use it; otherwise do not create an account
            $customer = null;
            if ($request->filled('customer_id')) {
                $customer = Customer::find($request->customer_id);
            }

            // compute nights
            $checkIn = new \DateTime($request->check_in_date);
            $checkOut = new \DateTime($request->check_out_date);
            $nights = $checkOut->diff($checkIn)->days;

            // price from selected room's room type
            $room = Room::with('roomType')->findOrFail($request->room_id);
            $pricePerDay = (float) ($room->roomType->price ?? 0);
            $totalPrice = $pricePerDay * max(1, $nights);

            // Reserve the room temporarily by creating a Reservation in 'pending' state
            // then create a Transaction referencing that reservation (reservation_ref)
            DB::beginTransaction();
            try {
                $lockedRoom = Room::where('id', $request->room_id)->lockForUpdate()->with('roomType')->firstOrFail();

                // ensure room belongs to resepsionis hotel
                $userHotelId = $user->hotel_id ?? null;
                if ($userHotelId && (($lockedRoom->roomType->hotel_id ?? null) != $userHotelId)) {
                    DB::rollBack();
                    abort(403, 'Unauthorized action.');
                }

                // Room availability is enforced by lockForUpdate and reservation checks.

                $reservation = Reservation::create([
                    'customer_id' => $customer->id ?? null,
                    'customer_name' => $request->customer_name ?? null,
                    'customer_email' => $request->customer_email ?? null,
                    'room_id' => $lockedRoom->id,
                    'check_in_date' => $request->check_in_date,
                    'check_in_time' => $request->check_in_time ?? '00:00:00',
                    'check_out_date' => $request->check_out_date,
                    'check_out_time' => $request->check_out_time ?? '00:00:00',
                    'status' => 'pending',
                ]);

                $transaction = Transaction::create([
                    'booking_id' => null,
                    'customer_id' => $customer->id ?? null,
                    'payment_method' => 'midtrans',
                    'total' => $totalPrice,
                    'status' => 'pending',
                    'meta' => [
                        'type' => 'reservation_ref',
                        'reservation_id' => $reservation->id,
                    ],
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create pending reservation before payment: ' . $e->getMessage());
                return back()->with('error', 'Gagal membuat reservation sementara.');
            }

            // set order id
            $orderId = 'ORDER-' . $transaction->id . '-' . time();
            $transaction->midtrans_order_id = $orderId;
            $transaction->save();

            // prepare midtrans payload
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
                    'first_name' => $customer->name ?? ($request->customer_name ?? 'Guest'),
                    'email' => $customer->email ?? ($request->customer_email ?? null),
                ],
                'item_details' => [
                    [
                        'id' => $room->roomType->id ?? 'room',
                        'price' => (int) $transaction->total,
                        'quantity' => 1,
                        'name' => 'Reservation for Room ' . ($room->number ?? $room->id),
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

                return view('resepsionis.payments.snap', compact('token', 'transaction'));
            }

            Log::error('Midtrans create snap failed (resepsionis reservation)', ['resp' => $response->body()]);
            return back()->with('error', 'Gagal membuat pembayaran Midtrans');

        } catch (\Exception $e) {
            Log::error('Resepsionis reservation pay error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
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

        // No longer update room status column (column removed)

        $reservation->delete();

        return back()->with('success', 'Reservation deleted successfully');
    }

    /**
     * Create a transaction for an existing reservation and start Midtrans checkout.
     */
    public function payExisting($id)
    {
        $reservation = Reservation::with('room.roomType', 'customer')->findOrFail($id);

        // ensure reservation belongs to resepsionis hotel
        $user = auth()->guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;
        if ($hotelId) {
            $roomHotelId = $reservation->room->roomType->hotel_id ?? null;
            if ($roomHotelId != $hotelId) {
                abort(403, 'Unauthorized action.');
            }
        }

        // do not allow payment if already paid
        if ($reservation->status === 'paid') {
            return back()->with('error', 'Reservation already paid.');
        }

        try {
            $customer = $reservation->customer;
            // reservation may have guest name/email without a Customer account

            $checkIn = new \DateTime($reservation->check_in_date);
            $checkOut = new \DateTime($reservation->check_out_date);
            $nights = $checkOut->diff($checkIn)->days;

            $pricePerDay = (float) ($reservation->room->roomType->price ?? 0);
            $totalPrice = $pricePerDay * max(1, $nights);

            $transaction = Transaction::create([
                'booking_id' => null,
                'customer_id' => $customer->id ?? null,
                'payment_method' => 'midtrans',
                'total' => $totalPrice,
                'status' => 'pending',
                'meta' => [
                    'type' => 'reservation_ref',
                    'reservation_id' => $reservation->id,
                ],
            ]);

            // mark the existing reservation as pending while awaiting payment
            try {
                $reservation->update(['status' => 'pending']);
            } catch (\Exception $e) {
                Log::warning('Failed to mark reservation pending: ' . $e->getMessage());
            }

            $orderId = 'ORDER-' . $transaction->id . '-' . time();
            $transaction->midtrans_order_id = $orderId;
            $transaction->save();

            // create snap token
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
                    'first_name' => $customer->name ?? ($reservation->customer_name ?? 'Guest'),
                    'email' => $customer->email ?? ($reservation->customer_email ?? null),
                ],
                'item_details' => [
                    [
                        'id' => $reservation->room->roomType->id ?? 'room',
                        'price' => (int) $transaction->total,
                        'quantity' => 1,
                        'name' => 'Reservation ' . ($reservation->id),
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

                return view('resepsionis.payments.snap', compact('token', 'transaction'));
            }

            Log::error('Midtrans create snap failed (resepsionis reservation existing)', ['resp' => $response->body()]);
            return back()->with('error', 'Gagal membuat pembayaran Midtrans');
        } catch (\Exception $e) {
            Log::error('Resepsionis payExisting error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Mark reservation as checked-in. Only allowed when reservation is paid.
     */
    public function checkIn($id)
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

        if ($reservation->status !== 'paid') {
            return back()->with('error', 'Hanya reservation yang sudah dibayar yang dapat check-in.');
        }

        try {
            DB::transaction(function () use ($reservation) {
                $reservation->update(['status' => 'check_in']);

                // No room status field to update; room availability is managed by reservations

                // Sync booking if exists
                try {
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
                } catch (\Exception $e) {
                    Log::warning('Failed to sync booking status on checkIn: ' . $e->getMessage());
                }
            });

            return back()->with('success', 'Reservation berhasil di-check in');
        } catch (\Exception $e) {
            Log::error('CheckIn failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal melakukan check-in');
        }
    }

    /**
     * Mark reservation as checked-out. Only allowed when reservation is currently checked-in.
     */
    public function checkOut($id)
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

        if ($reservation->status !== 'check_in') {
            return back()->with('error', 'Hanya reservation yang sedang check-in yang dapat check-out.');
        }

        try {
            DB::transaction(function () use ($reservation) {
                $reservation->update(['status' => 'check_out']);

                // No room status field to update; room availability is managed by reservations

                // Sync booking if exists
                try {
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
                } catch (\Exception $e) {
                    Log::warning('Failed to sync booking status on checkOut: ' . $e->getMessage());
                }
            });

            return back()->with('success', 'Reservation berhasil di-check out');
        } catch (\Exception $e) {
            Log::error('CheckOut failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal melakukan check-out');
        }
    }
}
