@extends('layouts.sb-resepsionis')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-body text-center">
            <h5>Proceed to Payment</h5>
            <p>Payment for transaction #{{ $transaction->id }} - Amount: Rp {{ number_format($transaction->total,0,',','.') }}</p>
            <button id="payButton" class="btn btn-primary">Pay Now</button>
        </div>
    </div>
</div>

@php
    $midtransScript = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<script src="{{ $midtransScript }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('payButton').addEventListener('click', function() {
        const token = '{{ $token }}';
        const transactionId = '{{ $transaction->id }}';

        window.snap.pay(token, {
                onSuccess: async function(result){
                    // After successful payment popup, verify with server-side Midtrans API
                    try {
                        await fetch('{{ route('resepsionis.reservations.check_status', ['transaction' => $transaction->id]) }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
                        });
                    } catch (err) {
                        console.error('Check status failed', err);
                    }
                    window.location.href = '/resepsionis/reservations';
                },
                onPending: async function(result){
                    try {
                        await fetch('{{ route('resepsionis.reservations.check_status', ['transaction' => $transaction->id]) }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
                        });
                    } catch (err) {
                        console.error('Check status failed', err);
                    }
                    window.location.href = '/resepsionis/reservations';
                },
            onError: function(result){
                alert('Payment error or cancelled');
            }
        });
    });
</script>

@endsection
