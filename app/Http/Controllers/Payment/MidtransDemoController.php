<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class MidtransDemoController extends Controller
{
    public function showDemo()
    {
        return view('midtrans.demo');
    }

    public function createDemoSnap(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'item_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $amount = (int) $request->amount;
        $itemName = $request->item_name ?: 'Demo Payment';

        // create transaction record (no booking)
        $transaction = new Transaction();
        $transaction->booking_id = null;
        $transaction->customer_id = $user->id;
        $transaction->payment_method = 'midtrans';
        $transaction->total = $amount;
        $transaction->status = 'pending';
        $transaction->save();

        $orderId = 'DEMO-' . $transaction->id . '-' . time();
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
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'Demo',
                'email' => $user->email ?? null,
            ],
            'item_details' => [
                [
                    'id' => 'demo-' . $transaction->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => $itemName,
                ]
            ],
        ];

        try {
            $client = Http::withBasicAuth($serverKey, '')->asJson();
            if (!$isProduction) {
                $client = $client->withOptions(['verify' => false]);
            }
            $response = $client->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? null;

                $transaction->midtrans_response = json_encode($data);
                $transaction->save();

                return response()->json([
                    'token' => $token,
                    'transaction_id' => $transaction->id,
                ]);
            }

            Log::error('Midtrans demo create snap failed', ['resp' => $response->body()]);
            return response()->json(['message' => 'Midtrans error'], 500);
        } catch (\Exception $e) {
            Log::error('Midtrans demo exception', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Midtrans exception'], 500);
        }
    }
}
