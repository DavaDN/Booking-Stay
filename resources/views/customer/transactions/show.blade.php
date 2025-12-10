@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Transaksi</h5>
            <a href="{{ route('customer.transactions.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Informasi Transaksi</h6>
                    <table class="table table-borderless">
                        <tr><th>ID</th><td>{{ $transaction->id }}</td></tr>
                        <tr><th>Metode</th><td>{{ ucfirst($transaction->payment_method) }}</td></tr>
                        <tr><th>Total</th><td>Rp {{ number_format($transaction->total,0,',','.') }}</td></tr>
                        <tr><th>Status</th><td>{{ ucfirst($transaction->status) }}</td></tr>
                        <tr><th>Dibuat</th><td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6>Informasi Booking</h6>
                    @if($transaction->booking)
                        <table class="table table-borderless">
                            <tr><th>Kode Booking</th><td>{{ $transaction->booking->booking_code }}</td></tr>
                            <tr><th>Tipe Kamar</th><td>{{ $transaction->booking->roomType->name ?? '-' }}</td></tr>
                            <tr><th>Check-in</th><td>{{ optional($transaction->booking->check_in)->format('d/m/Y') }}</td></tr>
                            <tr><th>Check-out</th><td>{{ optional($transaction->booking->check_out)->format('d/m/Y') }}</td></tr>
                        </table>
                    @else
                        <div class="alert alert-info">Transaksi ini tidak terkait booking.</div>
                    @endif
                </div>
            </div>


            <div class="d-flex gap-2">
                @if($transaction->status === 'pending')
                    <form method="POST" action="{{ route('customer.transactions.update', $transaction->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="cancel">
                        <button class="btn btn-danger" onclick="return confirm('Batalkan transaksi?')">Batalkan</button>
                    </form>
                @endif

                @if($transaction->status !== 'paid')
                    <button id="checkStatusBtn" class="btn btn-secondary">Periksa Status Pembayaran</button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        const btn = document.getElementById('checkStatusBtn');
        if (!btn) return;
        btn.addEventListener('click', async function(e){
            e.preventDefault();
            btn.disabled = true;
            const original = btn.innerText;
            btn.innerText = 'Memeriksa...';
            try {
                const resp = await fetch("{{ route('customer.midtrans.check_status', $transaction->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (!resp.ok) {
                    const body = await resp.json().catch(()=>({}));
                    alert(body.message || 'Gagal memeriksa status');
                    btn.disabled = false;
                    btn.innerText = original;
                    return;
                }
                // reload to reflect updated status
                window.location.reload();
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat memeriksa status');
                btn.disabled = false;
                btn.innerText = original;
            }
        });
    })();
</script>
@endpush
@endsection
@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #2365A2;
        --text: #586A80;
        --success: #27ae60;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }

    .detail-card {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .detail-card h3 {
        color: var(--primary);
        margin-bottom: 25px;
        font-size: 22px;
        font-weight: 700;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .detail-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .detail-item label {
        display: block;
        font-size: 12px;
        color: var(--text);
        margin-bottom: 8px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .detail-item value {
        display: block;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-paid { background: #d4edda; color: #155724; }
    .badge-failed { background: #f8d7da; color: #721c24; }
    .badge-cancelled { background: #e7e8ea; color: #383d41; }

    .section {
        border-top: 2px solid #f0f0f0;
        padding-top: 20px;
        margin-top: 20px;
    }

    .section h4 {
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 15px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #1a4d7a;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .action-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .action-group .btn {
        flex: 1;
        justify-content: center;
    }

    .alert {
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .info-box {
        background: #e8f4f8;
        border-left: 4px solid var(--primary);
        padding: 15px;
        border-radius: 6px;
        margin: 20px 0;
    }

    @media (max-width: 768px) {
        .action-group {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('customer.transactions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-card">
        <h3>
            <i class="fas fa-receipt"></i> Detail Transaksi
            <span class="badge badge-{{ $transaction->status }}" style="float: right;">
                {{ ucfirst($transaction->status) }}
            </span>
        </h3>

        <div class="detail-grid">
            <div class="detail-item">
                <label>ID Transaksi</label>
                <value>#{{ $transaction->id }}</value>
            </div>
            <div class="detail-item">
                <label>Kode Booking</label>
                <value>{{ $transaction->booking->booking_code }}</value>
            </div>
            <div class="detail-item">
                <label>Metode Pembayaran</label>
                <value>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</value>
            </div>
            <div class="detail-item">
                <label>Jumlah Pembayaran</label>
                <value style="color: var(--success); font-size: 18px;">Rp {{ number_format($transaction->total, 0, ',', '.') }}</value>
            </div>
            <div class="detail-item">
                <label>Tanggal Transaksi</label>
                <value>{{ $transaction->created_at->format('d/m/Y H:i') }}</value>
            </div>
            @if ($transaction->payment_date)
                <div class="detail-item">
                    <label>Tanggal Pembayaran</label>
                    <value>{{ $transaction->payment_date->format('d/m/Y H:i') }}</value>
                </div>
            @endif
        </div>

        <div class="section">
            <h4>Detail Booking</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Tipe Kamar</label>
                    <value>{{ $transaction->booking->roomType->name ?? 'N/A' }}</value>
                </div>
                <div class="detail-item">
                    <label>Check-In</label>
                    <value>{{ $transaction->booking->check_in->format('d/m/Y') }}</value>
                </div>
                <div class="detail-item">
                    <label>Check-Out</label>
                    <value>{{ $transaction->booking->check_out->format('d/m/Y') }}</value>
                </div>
                <div class="detail-item">
                    <label>Lama Menginap</label>
                    <value>{{ $transaction->booking->check_out->diffInDays($transaction->booking->check_in) }} Malam</value>
                </div>
                <div class="detail-item">
                    <label>Jumlah Kamar</label>
                    <value>{{ $transaction->booking->total_room }}</value>
                </div>
                <div class="detail-item">
                    <label>Total Tamu</label>
                    <value>{{ $transaction->booking->guests }} Orang</value>
                </div>
            </div>
        </div>

        @if ($transaction->status === 'pending')
            <div class="alert alert-success" style="background: #fff3cd; color: #856404; border-color: #ffeaa7;">
                <strong><i class="fas fa-info-circle"></i> Perhatian:</strong> Transaksi ini masih menunggu konfirmasi pembayaran. Mohon selesaikan pembayaran Anda.
            </div>
        @elseif ($transaction->status === 'paid')
            <div class="alert alert-success">
                <strong><i class="fas fa-check-circle"></i> Sukses:</strong> Pembayaran Anda telah dikonfirmasi. Terima kasih!
            </div>
        @elseif ($transaction->status === 'failed')
            <div class="alert" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                <strong><i class="fas fa-times-circle"></i> Gagal:</strong> Pembayaran tidak berhasil. Silakan coba lagi atau hubungi customer service.
            </div>
        @endif

        <div class="section">
            <h4>Aksi</h4>
            <div class="action-group">
                @if ($transaction->status === 'pending')
                    <form action="{{ route('customer.transactions.update', $transaction->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center;" onclick="return confirm('Batalkan transaksi ini?')">
                            <i class="fas fa-times"></i> Batalkan Transaksi
                        </button>
                    </form>
                @endif
                <a href="{{ route('customer.bookings.show', $transaction->booking->id) }}" class="btn btn-primary">
                    <i class="fas fa-calendar"></i> Lihat Booking
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
