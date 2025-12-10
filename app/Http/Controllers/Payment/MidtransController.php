<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Resepsionis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingPaidMail;

class MidtransController extends Controller
{
    public function createSnapToken(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);

        $booking = Booking::find($request->booking_id);

        // ensure the booking belongs to the logged in customer (for security)
        $user = Auth::guard('customer')->user();
        if (!$user || $booking->customer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // create or update transaction
        $transaction = Transaction::firstOrNew([
            'booking_id' => $booking->id,
        ]);

        $transaction->payment_method = 'midtrans';
        $transaction->total = $booking->total_price;
        $transaction->status = 'pending';
        $transaction->save();

        // Ensure booking reflects that a payment transaction was initiated
        try {
            $booking->status = 'pending';
            $booking->save();
        } catch (\Exception $e) {
            Log::warning('Failed to set booking status to pending on createSnapToken: ' . $e->getMessage(), ['booking_id' => $booking->id]);
        }

        // generate an order id used for Midtrans
        $orderId = 'ORDER-' . $transaction->id . '-' . time();
        $transaction->midtrans_order_id = $orderId;
        $transaction->save();

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
                'first_name' => $user->name ?? 'Customer',
                'email' => $user->email ?? null,
            ],
            'item_details' => [
                [
                    'id' => $booking->room_type_id ?? 'room',
                    'price' => (int) $transaction->total,
                    'quantity' => 1,
                    'name' => 'Booking ' . ($booking->booking_code ?? $booking->id),
                ]
            ],
        ];

        try {
            $client = Http::withBasicAuth($serverKey, '')->asJson();
            if (!$isProduction) {
                // disable SSL verification in non-production/local to avoid cURL CA issues
                $client = $client->withOptions(['verify' => false]);
            }
            $response = $client->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? null;

                // save Midtrans raw response for reference
                $transaction->midtrans_response = json_encode($data);
                $transaction->save();

                return response()->json([
                    'token' => $token,
                    'transaction_id' => $transaction->id,
                ]);
            }

            Log::error('Midtrans create snap failed', ['resp' => $response->body()]);
            return response()->json(['message' => 'Midtrans error'], 500);
        } catch (\Exception $e) {
            Log::error('Midtrans exception', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Midtrans exception'], 500);
        }
    }

    // Endpoint for Midtrans notifications (webhook)
    public function notification(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans notification', $payload);

        // try to find transaction by order id
        $orderId = $payload['order_id'] ?? ($payload['transaction_details']['order_id'] ?? null);
        if ($orderId) {
            $transaction = Transaction::where('midtrans_order_id', $orderId)->first();
            if ($transaction) {
                $transaction->midtrans_status = $payload['transaction_status'] ?? null;
                $transaction->midtrans_response = json_encode($payload);
                // map status
                $status = $payload['transaction_status'] ?? null;
                $previousStatus = $transaction->status;

                if ($status === 'capture' || $status === 'settlement' || $status === 'success') {
                    $transaction->status = 'paid';
                    $transaction->payment_date = now();
                } elseif ($status === 'pending') {
                    $transaction->status = 'pending';
                } else {
                    $transaction->status = 'failed';
                }

                $transaction->save();

                // Ensure booking status follows transaction status (map transaction -> booking)
                $booking = $transaction->booking;
                if ($booking) {
                    if ($transaction->status === 'paid') {
                        $booking->status = 'paid';
                    } elseif ($transaction->status === 'pending') {
                        $booking->status = 'pending';
                    } else {
                        $booking->status = 'cancelled';
                    }
                    $booking->save();
                }

                // If the transaction just changed to 'paid', allocate rooms and create reservations
                if ($transaction->status === 'paid' && $previousStatus !== 'paid') {
                    try {
                        if ($booking) {
                            // Prefer allocating specific rooms if booking->room_ids is provided
                            if (!empty($booking->room_ids) && is_array($booking->room_ids)) {
                                $roomIds = $booking->room_ids;
                                DB::transaction(function () use ($roomIds, $booking) {
                                    foreach ($roomIds as $rid) {
                                                $room = Room::lockForUpdate()->find($rid);
                                                if ($room) {
                                                    $overlap = Reservation::where('room_id', $room->id)
                                                        ->where('status', '!=', 'cancelled')
                                                        ->where(function ($q) use ($booking) {
                                                            $q->where('check_in_date', '<', $booking->check_out->format('Y-m-d'))
                                                              ->where('check_out_date', '>', $booking->check_in->format('Y-m-d'));
                                                        })->exists();

                                                    if (!$overlap) {
                                                        Reservation::create([
                                                            'customer_id' => $booking->customer_id,
                                                            'room_id' => $room->id,
                                                            'check_in_date' => $booking->check_in->format('Y-m-d'),
                                                            'check_in_time' => $booking->check_in->format('H:i:s'),
                                                            'check_out_date' => $booking->check_out->format('Y-m-d'),
                                                            'check_out_time' => $booking->check_out->format('H:i:s'),
                                                            'status' => 'paid',
                                                        ]);
                                                    } else {
                                                        Log::warning('Preferred room not available during payment allocation', ['booking_id' => $booking->id, 'room_id' => $rid]);
                                                    }
                                                } else {
                                                    Log::warning('Preferred room not found during payment allocation', ['booking_id' => $booking->id, 'room_id' => $rid]);
                                                }
                                    }
                                }, 5);
                            } else {
                                $roomTypeId = $booking->room_type_id ?? null;
                                $roomsNeeded = (int) ($booking->number_of_rooms ?? 1);

                                if ($roomTypeId && $roomsNeeded > 0) {
                                    DB::transaction(function () use ($roomTypeId, $roomsNeeded, $booking) {
                                        $candidates = Room::where('room_type_id', $roomTypeId)
                                            ->lockForUpdate()
                                            ->get();

                                        $allocated = 0;
                                        foreach ($candidates as $room) {
                                            if ($allocated >= $roomsNeeded) break;

                                            $overlap = Reservation::where('room_id', $room->id)
                                                ->where('status', '!=', 'cancelled')
                                                ->where(function ($q) use ($booking) {
                                                    $q->where('check_in_date', '<', $booking->check_out->format('Y-m-d'))
                                                      ->where('check_out_date', '>', $booking->check_in->format('Y-m-d'));
                                                })->exists();

                                            if (!$overlap) {
                                                Reservation::create([
                                                    'customer_id' => $booking->customer_id,
                                                    'room_id' => $room->id,
                                                    'check_in_date' => $booking->check_in->format('Y-m-d'),
                                                    'check_in_time' => $booking->check_in->format('H:i:s'),
                                                    'check_out_date' => $booking->check_out->format('Y-m-d'),
                                                    'check_out_time' => $booking->check_out->format('H:i:s'),
                                                    'status' => 'paid',
                                                ]);
                                                $allocated++;
                                            }
                                        }
                                    }, 5);
                                }
                            }
                        }
                        // If transaction has meta for reservation payload, create reservation now
                        $meta = $transaction->meta ?? null;
                        if (is_array($meta) && ($meta['type'] ?? null) === 'reservation') {
                            $payload = $meta['payload'] ?? [];
                            if (!empty($payload)) {
                                DB::transaction(function () use ($payload, $transaction) {
                                    $room = Room::lockForUpdate()->find($payload['room_id']);
                                        if ($room) {
                                            $overlap = Reservation::where('room_id', $room->id)
                                                ->where('status', '!=', 'cancelled')
                                                ->where(function ($q) use ($payload) {
                                                    $q->where('check_in_date', '<', $payload['check_out_date'] ?? null)
                                                      ->where('check_out_date', '>', $payload['check_in_date'] ?? null);
                                                })->exists();

                                            if (!$overlap) {
                                                $reservationData = [
                                                    'customer_id' => $payload['customer_id'] ?? null,
                                                    'customer_name' => $payload['customer_name'] ?? null,
                                                    'customer_email' => $payload['customer_email'] ?? null,
                                                    'room_id' => $room->id,
                                                    'check_in_date' => $payload['check_in_date'] ?? null,
                                                    'check_in_time' => $payload['check_in_time'] ?? null,
                                                    'check_out_date' => $payload['check_out_date'] ?? null,
                                                    'check_out_time' => $payload['check_out_time'] ?? null,
                                                    'status' => 'paid',
                                                ];

                                                $reservation = Reservation::create($reservationData);
                                            } else {
                                                Log::warning('Payload room not available during payment allocation', ['room_id' => $room->id]);
                                            }
                                        }
                                }, 5);
                            }
                        }
                        // If transaction meta references an existing reservation id
                        if (is_array($meta) && ($meta['type'] ?? null) === 'reservation_ref' && !empty($meta['reservation_id'])) {
                            $resId = $meta['reservation_id'];
                            DB::transaction(function () use ($resId) {
                                $reservation = Reservation::with('room')->find($resId);
                                    if ($reservation) {
                                        $reservation->update(['status' => 'paid']);
                                    }
                            }, 5);
                        }
                    } catch (\Exception $e) {
                        Log::error('Auto-reservation on payment failed: ' . $e->getMessage(), ['transaction_id' => $transaction->id]);
                    }
                    // notify resepsionis for the hotel of this booking
                    try {
                        $this->notifyResepsionis($booking);
                    } catch (\Exception $e) {
                        Log::warning('Failed to notify resepsionis: ' . $e->getMessage(), ['booking_id' => $booking->id ?? null]);
                    }
                }
            }
        }

        return response('OK', 200);
    }

    /**
     * Return parsed Midtrans info for a transaction (customer-only).
     */
    public function transactionDetails(Request $request, $id)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaction = Transaction::with('booking')->find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // ensure the booking belongs to the authenticated customer
        if (!$transaction->booking || $transaction->booking->customer_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $raw = null;
        if ($transaction->midtrans_response) {
            $raw = json_decode($transaction->midtrans_response, true);
        }

        // Parse common fields from Midtrans response
        $parsed = [];
        if (is_array($raw)) {
            // snap returns 'va_numbers' or 'permata_va_number' or 'payment_code' etc.
            if (!empty($raw['va_numbers'])) {
                $parsed['va_numbers'] = $raw['va_numbers'];
            }
            if (!empty($raw['permata_va_number'])) {
                $parsed['permata_va_number'] = $raw['permata_va_number'];
            }
            if (!empty($raw['payment_code'])) {
                $parsed['payment_code'] = $raw['payment_code'];
            }
            if (!empty($raw['transaction_status'])) {
                $parsed['transaction_status'] = $raw['transaction_status'];
            }
            if (!empty($raw['transaction_time'])) {
                $parsed['transaction_time'] = $raw['transaction_time'];
            }
            if (!empty($raw['gross_amount'])) {
                $parsed['gross_amount'] = $raw['gross_amount'];
            }
            // include full raw response for debugging
            $parsed['_raw'] = $raw;
        }

        return response()->json([
            'transaction' => $transaction->toArray(),
            'midtrans' => $parsed,
        ]);
    }

    /**
     * Check Midtrans status for a given transaction and apply the same handling
     * as the notification webhook (safe server-side verification).
     */
    public function checkStatus(Request $request, $id)
    {
        $transaction = Transaction::with('booking')->find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $orderId = $transaction->midtrans_order_id ?? null;
        if (!$orderId) {
            return response()->json(['message' => 'Order ID missing'], 400);
        }

        $serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production');
        $url = $isProduction
            ? 'https://api.midtrans.com/v2/' . $orderId . '/status'
            : 'https://api.sandbox.midtrans.com/v2/' . $orderId . '/status';

        try {
            $client = Http::withBasicAuth($serverKey, '');
            if (!$isProduction) $client = $client->withOptions(['verify' => false]);
            $resp = $client->get($url);
            if (!$resp->successful()) {
                return response()->json(['message' => 'Midtrans status request failed'], 500);
            }

            $payload = $resp->json();
            $status = $payload['transaction_status'] ?? ($payload['status_code'] ?? null);

            $previousStatus = $transaction->status;
            if (in_array($status, ['capture', 'settlement', 'success'])) {
                $transaction->status = 'paid';
                $transaction->payment_date = now();
            } elseif ($status === 'pending') {
                $transaction->status = 'pending';
            } else {
                $transaction->status = 'failed';
            }

            $transaction->midtrans_status = $status;
            $transaction->midtrans_response = json_encode($payload);
            $transaction->save();

            // Ensure booking status follows transaction status
            $booking = $transaction->booking;
                if ($booking) {
                    if ($transaction->status === 'paid') {
                        $booking->status = 'paid';
                    } elseif ($transaction->status === 'pending') {
                        $booking->status = 'pending';
                    } else {
                        $booking->status = 'cancelled';
                    }
                    $booking->save();
                }

            // reuse same post-payment logic as notification: allocate rooms/create reservation
            if ($transaction->status === 'paid' && $previousStatus !== 'paid') {
                // call notification handling by reusing same code path: duplicate of notification actions
                try {
                    if ($booking) {

                        // Prefer allocating specific rooms if booking->room_ids is provided
                        if (!empty($booking->room_ids) && is_array($booking->room_ids)) {
                            $roomIds = $booking->room_ids;
                            DB::transaction(function () use ($roomIds, $booking) {
                                foreach ($roomIds as $rid) {
                                    $room = Room::lockForUpdate()->find($rid);
                                    if ($room) {
                                        $overlap = Reservation::where('room_id', $room->id)
                                            ->where('status', '!=', 'cancelled')
                                            ->where(function ($q) use ($booking) {
                                                $q->where('check_in_date', '<', $booking->check_out->format('Y-m-d'))
                                                  ->where('check_out_date', '>', $booking->check_in->format('Y-m-d'));
                                            })->exists();

                                        if (!$overlap) {
                                            Reservation::create([
                                                'customer_id' => $booking->customer_id,
                                                'room_id' => $room->id,
                                                'check_in_date' => $booking->check_in->format('Y-m-d'),
                                                'check_in_time' => $booking->check_in->format('H:i:s'),
                                                'check_out_date' => $booking->check_out->format('Y-m-d'),
                                                'check_out_time' => $booking->check_out->format('H:i:s'),
                                                'status' => 'paid',
                                            ]);
                                        } else {
                                            Log::warning('Preferred room not available during payment allocation', ['booking_id' => $booking->id, 'room_id' => $rid]);
                                        }
                                    } else {
                                        Log::warning('Preferred room not found during payment allocation', ['booking_id' => $booking->id, 'room_id' => $rid]);
                                    }
                                }
                            }, 5);
                        } else {
                            $roomTypeId = $booking->room_type_id ?? null;
                            $roomsNeeded = (int) ($booking->number_of_rooms ?? 1);

                            if ($roomTypeId && $roomsNeeded > 0) {
                                DB::transaction(function () use ($roomTypeId, $roomsNeeded, $booking) {
                                    $candidates = Room::where('room_type_id', $roomTypeId)->lockForUpdate()->get();

                                    $allocated = 0;
                                    foreach ($candidates as $room) {
                                        if ($allocated >= $roomsNeeded) break;

                                        $overlap = Reservation::where('room_id', $room->id)
                                            ->where('status', '!=', 'cancelled')
                                            ->where(function ($q) use ($booking) {
                                                $q->where('check_in_date', '<', $booking->check_out->format('Y-m-d'))
                                                  ->where('check_out_date', '>', $booking->check_in->format('Y-m-d'));
                                            })->exists();

                                        if (!$overlap) {
                                            Reservation::create([
                                                'customer_id' => $booking->customer_id,
                                                'room_id' => $room->id,
                                                'check_in_date' => $booking->check_in->format('Y-m-d'),
                                                'check_in_time' => $booking->check_in->format('H:i:s'),
                                                'check_out_date' => $booking->check_out->format('Y-m-d'),
                                                'check_out_time' => $booking->check_out->format('H:i:s'),
                                                'status' => 'paid',
                                            ]);
                                            $allocated++;
                                        }
                                    }
                                }, 5);
                            }
                        }
                    }

                    $meta = $transaction->meta ?? null;
                    if (is_array($meta) && ($meta['type'] ?? null) === 'reservation') {
                        $payload = $meta['payload'] ?? [];
                        if (!empty($payload)) {
                            DB::transaction(function () use ($payload, $transaction) {
                                $room = Room::lockForUpdate()->find($payload['room_id']);
                                if ($room) {
                                    $overlap = Reservation::where('room_id', $room->id)
                                        ->where('status', '!=', 'cancelled')
                                        ->where(function ($q) use ($payload) {
                                            $q->where('check_in_date', '<', $payload['check_out_date'] ?? null)
                                              ->where('check_out_date', '>', $payload['check_in_date'] ?? null);
                                        })->exists();

                                    if (!$overlap) {
                                        $reservationData = [
                                            'customer_id' => $payload['customer_id'] ?? null,
                                            'customer_name' => $payload['customer_name'] ?? null,
                                            'customer_email' => $payload['customer_email'] ?? null,
                                            'room_id' => $room->id,
                                            'check_in_date' => $payload['check_in_date'] ?? null,
                                            'check_in_time' => $payload['check_in_time'] ?? null,
                                            'check_out_date' => $payload['check_out_date'] ?? null,
                                            'check_out_time' => $payload['check_out_time'] ?? null,
                                            'status' => 'paid',
                                        ];

                                        $reservation = Reservation::create($reservationData);
                                    } else {
                                        Log::warning('Payload room not available during payment allocation', ['room_id' => $room->id]);
                                    }
                                }
                            }, 5);
                        }
                    }

                    if (is_array($meta) && ($meta['type'] ?? null) === 'reservation_ref' && !empty($meta['reservation_id'])) {
                        $resId = $meta['reservation_id'];
                        DB::transaction(function () use ($resId) {
                            $reservation = Reservation::with('room')->find($resId);
                                if ($reservation) {
                                    $reservation->update(['status' => 'paid']);
                                }
                        }, 5);
                    }
                } catch (\Exception $e) {
                    Log::error('checkStatus post-payment processing failed: ' . $e->getMessage(), ['transaction_id' => $transaction->id]);
                }
                    // notify resepsionis for the hotel of this booking
                    try {
                        $this->notifyResepsionis($booking);
                    } catch (\Exception $e) {
                        Log::warning('Failed to notify resepsionis (checkStatus): ' . $e->getMessage(), ['booking_id' => $booking->id ?? null]);
                    }
            }

            return response()->json(['message' => 'Status checked', 'status' => $transaction->status]);

        } catch (\Exception $e) {
            Log::error('Midtrans checkStatus exception: ' . $e->getMessage());
            return response()->json(['message' => 'Exception while checking Midtrans status'], 500);
        }
    }

    /**
     * Create snap token for resepsionis checkout (server side) and return token JSON.
     */
    public function createSnapForResepsionis(Request $request)
    {
        $request->validate(['transaction_id' => 'required|exists:transactions,id']);
        $transaction = Transaction::with('booking')->findOrFail($request->transaction_id);

        $orderId = $transaction->midtrans_order_id ?? ('ORDER-' . $transaction->id . '-' . time());
        $transaction->midtrans_order_id = $orderId;
        $transaction->save();

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
                'first_name' => $transaction->booking->customer->name ?? ($transaction->booking->customer_name ?? 'Guest'),
                'email' => $transaction->booking->customer->email ?? ($transaction->booking->customer_email ?? null),
            ],
            'item_details' => [
                [
                    'id' => $transaction->booking->room_type_id ?? 'room',
                    'price' => (int) $transaction->total,
                    'quantity' => 1,
                    'name' => 'Booking ' . ($transaction->booking->booking_code ?? $transaction->id),
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
            return response()->json(['token' => $token, 'transaction_id' => $transaction->id]);
        }

        return response()->json(['message' => 'Midtrans error'], 500);
    }

    /**
     * Notify resepsionis (hotel) that a booking has been paid.
     */
    private function notifyResepsionis($booking)
    {
        if (!$booking) return;

        // determine rooms assigned: prefer booking.room_ids
        $rooms = collect();
        if (!empty($booking->room_ids) && is_array($booking->room_ids)) {
            $rooms = Room::whereIn('id', $booking->room_ids)->get();
        } else {
            // try to find paid reservations matching booking dates and room type
            $resRoomIds = Reservation::where('check_in_date', $booking->check_in->format('Y-m-d'))
                ->where('check_out_date', $booking->check_out->format('Y-m-d'))
                ->whereHas('room', function ($q) use ($booking) {
                    $q->where('room_type_id', $booking->room_type_id);
                })
                ->where('status', 'paid')
                ->pluck('room_id')
                ->toArray();

            if (!empty($resRoomIds)) {
                $rooms = Room::whereIn('id', $resRoomIds)->get();
            }
        }

        // find resepsionis for the hotel
        $hotelId = optional($booking->roomType)->hotel_id ?? null;
        if (!$hotelId) return;

        $reseps = Resepsionis::where('hotel_id', $hotelId)->get();
        if ($reseps->isEmpty()) return;

        foreach ($reseps as $r) {
            try {
                Mail::to($r->email)->send(new BookingPaidMail($booking, $rooms));
            } catch (\Exception $e) {
                Log::warning('Failed sending booking paid mail to resepsionis ' . $r->email . ': ' . $e->getMessage());
            }
        }
    }

    public function showSnapForResepsionis($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        // If token already stored in midtrans_response, try to extract token
        $token = null;
        if ($transaction->midtrans_response) {
            $raw = json_decode($transaction->midtrans_response, true);
            $token = $raw['token'] ?? null;
        }

        return view('resepsionis.payments.snap', compact('token', 'transaction'));
    }
}
