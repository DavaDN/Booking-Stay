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
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }

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

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    @media (max-width: 768px) {
        .action-group {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-card">
        <h3>
            <i class="fas fa-calendar-alt"></i> Detail Booking
            <span class="badge badge-{{ $booking->status }}" style="float: right;">
                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
            </span>
        </h3>

        <div class="detail-grid">
            <div class="detail-item">
                <label>Kode Booking</label>
                <value>{{ $booking->booking_code }}</value>
            </div>
            <div class="detail-item">
                <label>Tipe Kamar</label>
                <value>{{ $booking->roomType->name ?? 'N/A' }}</value>
            </div>
            <div class="detail-item">
                <label>Harga Per Malam</label>
                <value>Rp {{ number_format($booking->roomType->price ?? 0, 0, ',', '.') }}</value>
            </div>
            <div class="detail-item">
                <label>Jumlah Kamar</label>
                <value>{{ $booking->number_of_rooms }} Kamar</value>
            </div>
            <div class="detail-item">
                <label>Check-In</label>
                <value>{{ $booking->check_in->format('d/m/Y') }}</value>
            </div>
            <div class="detail-item">
                <label>Check-Out</label>
                <value>{{ $booking->check_out->format('d/m/Y') }}</value>
            </div>
            <div class="detail-item">
                <label>Lama Menginap</label>
                <value>{{ $booking->check_out->diffInDays($booking->check_in) }} Malam</value>
            </div>
            <div class="detail-item">
                <label>Total Harga</label>
                <value style="color: var(--success); font-size: 18px;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</value>
            </div>
            <div class="detail-item">
                <label>Tanggal Pesan</label>
                <value>{{ $booking->created_at->format('d/m/Y H:i') }}</value>
            </div>
        </div>

        @if ($booking->special_requests)
            <div class="section">
                <h4>Permintaan Khusus</h4>
                <p style="color: #666; line-height: 1.6;">{{ $booking->special_requests }}</p>
            </div>
        @endif

        @if ($booking->transaction)
            <div class="section">
                <h4>Status Transaksi</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Metode Pembayaran</label>
                        <value>{{ ucfirst(str_replace('_', ' ', $booking->transaction->payment_method)) }}</value>
                    </div>
                    <div class="detail-item">
                        <label>Status Pembayaran</label>
                        <value>
                            <span class="badge badge-{{ $booking->transaction->status }}">
                                {{ ucfirst($booking->transaction->status) }}
                            </span>
                        </value>
                    </div>
                    <div class="detail-item">
                        <label>Tanggal Transaksi</label>
                        <value>{{ $booking->transaction->created_at->format('d/m/Y H:i') }}</value>
                    </div>
                </div>
            </div>
        @else
            @if ($booking->status === 'confirmed' || $booking->status === 'checked_in')
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Info:</strong> Anda belum membuat transaksi untuk booking ini. Silakan lakukan pembayaran untuk menyelesaikan reservasi.
                </div>
            @endif
        @endif

        <div class="section">
            <h4>Aksi</h4>
            <div class="action-group">
                @if ($booking->status === 'pending')
                    <form action="{{ route('customer.bookings.update', $booking->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center;" onclick="return confirm('Batalkan booking ini?')">
                            <i class="fas fa-times"></i> Batalkan Booking
                        </button>
                    </form>
                @endif
                @if ((!$booking->transaction) && ($booking->status === 'confirmed' || $booking->status === 'checked_in'))
                    <a href="{{ route('customer.transactions.create') }}" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Lakukan Pembayaran
                    </a>
                @endif
                @if ($booking->transaction)
                    <a href="{{ route('customer.transactions.show', $booking->transaction->id) }}" class="btn btn-primary">
                        <i class="fas fa-receipt"></i> Lihat Transaksi
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
