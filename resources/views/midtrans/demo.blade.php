@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Midtrans Demo - Sandbox</div>

                <div class="card-body">
                    <p>Gunakan halaman ini untuk menguji Midtrans Snap (sandbox). Pilih jumlah, nama item, lalu klik <strong>Bayar Demo</strong>.</p>

                    <form id="demoForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Item</label>
                            <input type="text" name="item_name" class="form-control" value="Demo Booking" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah (IDR)</label>
                            <input type="number" name="amount" class="form-control" value="10000" min="1000" />
                        </div>

                        <div class="mb-3">
                            <button id="demoPay" class="btn btn-primary">Bayar Demo</button>
                        </div>
                    </form>

                    <div class="mt-3">
                        <h6>Petunjuk sandbox</h6>
                        <ul>
                            <li>Snap popup akan menampilkan metode pembayaran sandbox (kartu kredit, VA, e-wallet, dll.).</li>
                            <li>Untuk kartu kredit sandbox, gunakan nomor kartu contoh dari dokumentasi Midtrans.</li>
                            <li>Untuk VA, catat nomor VA yang ditampilkan dan lakukan simulasi pembayaran di Midtrans sandbox dashboard jika perlu.</li>
                        </ul>
                    </div>
                </div>
            </div>
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
document.getElementById('demoPay').addEventListener('click', async function (e) {
    e.preventDefault();
    const btn = this;
    btn.disabled = true;
    btn.innerText = 'Mempersiapkan...';

    const form = document.getElementById('demoForm');
    const formData = new FormData(form);
    const payload = {
        item_name: formData.get('item_name'),
        amount: formData.get('amount'),
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    try {
        const res = await fetch('{{ route('customer.midtrans.demo_create') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body: JSON.stringify({ item_name: payload.item_name, amount: payload.amount })
        });

        if (!res.ok) {
            const err = await res.json().catch(()=>({}));
            alert(err.message || 'Gagal membuat token demo');
            btn.disabled = false;
            btn.innerText = 'Bayar Demo';
            return;
        }

        const data = await res.json();
        const token = data.token;
        const transactionId = data.transaction_id;

        if (!token) {
            alert('Token pembayaran tidak diterima');
            btn.disabled = false;
            btn.innerText = 'Bayar Demo';
            return;
        }

        window.snap.pay(token, {
            onSuccess: function(result){
                window.location.href = '/customer/transactions/' + transactionId;
            },
            onPending: function(result){
                window.location.href = '/customer/transactions/' + transactionId;
            },
            onError: function(result){
                alert('Pembayaran gagal atau dibatalkan');
                btn.disabled = false;
                btn.innerText = 'Bayar Demo';
            }
        });

    } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan.');
        btn.disabled = false;
        btn.innerText = 'Bayar Demo';
    }
});
</script>

@endsection
