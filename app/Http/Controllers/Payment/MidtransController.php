<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Transaction;

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
                if ($status === 'capture' || $status === 'settlement' || $status === 'success') {
                    $transaction->status = 'paid';
                } elseif ($status === 'pending') {
                    $transaction->status = 'pending';
                } else {
                    $transaction->status = 'failed';
                }

                $transaction->save();
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
}
