@extends('layouts.sidebar')

@section('content')
<style>
    .details-container {
        max-width: 1000px;
    }

    .card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px; 
        border-bottom: 2px solid #f0f0f0;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 12px;
        color: #999;
        margin-bottom: 5px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .info-value {
        font-size: 16px;
        color: #2c3e50;
        font-weight: 500;
    }

    .badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        display: inline-block;
        width: fit-content;
    }

    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .badge-checked_in { background: #d1ecf1; color: #0c5460; }
    .badge-checked_out { background: #e7e8ea; color: #383d41; }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #3498db;
        color: white;
    }

    .btn-primary:hover {
        background: #2980b9;
    }

    .btn-success {
        background: #27ae60;
        color: white;
    }

    .btn-success:hover {
        background: #229954;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .action-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-back {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .header-back a {
        color: #3498db;
        text-decoration: none;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .header-back a:hover {
        color: #2980b9;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .price-summary {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .price-row.total {
        border-top: 2px solid #ddd;
        padding-top: 10px;
        font-weight: 600;
        font-size: 16px;
    }

    .transaction-status {
        padding: 15px;
        border-radius: 6px;
        background: #d1ecf1;
        color: #0c5460;
        margin-bottom: 20px;
    }

    .transaction-status.no-payment {
        background: #f8d7da;
        color: #721c24;
    }

    .transaction-status.paid {
        background: #d4edda;
        color: #155724;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }

    .form-group select,
    .form-group input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-group select:focus,
    .form-group input:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
</style>

<div class="details-container">
    <!-- Header dengan back button -->
    <div class="header-back">
        <a href="{{ route('admin.bookings.index') }}">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Booking
        </a>
    </div>

    <!-- Status dan Info Dasar -->
    <div class="card">
        <div class="action-header">
            <div>
                <h6 class="mb-0" style="color: #999; font-size: 12px; text-transform: uppercase;">Kode Booking</h6>
                <h2 class="mb-3">{{ $booking->booking_code }}</h2>
                <span class="badge badge-{{ $booking->status }}">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>
            <div style="text-align: right;">
                <h6 style="color: #999; font-size: 12px; text-transform: uppercase;">Total Pembayaran</h6>
                <h3 style="color: #27ae60;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Informasi Customer -->
    <div class="card">
        <div class="card-title">👤 Informasi Customer</div>
        <div class="info-row">
            <div class="info-item">
                <span class="info-label">Nama</span>
                <span class="info-value">{{ $booking->customer->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $booking->customer->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">No. Telepon</span>
                <span class="info-value">{{ $booking->customer->phone ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Username</span>
                <span class="info-value">{{ $booking->customer->username }}</span>
            </div>
        </div>
    </div>

    <!-- Informasi Kamar & Tanggal -->
    <div class="card">
        <div class="card-title">🏨 Informasi Reservasi</div>
        <div class="info-row">
            <div class="info-item">
                <span class="info-label">Tipe Kamar</span>
                <span class="info-value">{{ $booking->roomType->name ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jumlah Kamar</span>
                <span class="info-value">{{ $booking->total_room ?? 0 }} Kamar</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jumlah Tamu</span>
                <span class="info-value">{{ $booking->guests ?? 0 }} Orang</span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-item">
                <span class="info-label">Check-In</span>
                <span class="info-value">{{ $booking->check_in->format('d M Y') }} ({{ $booking->check_in->format('l') }})</span>
            </div>
            <div class="info-item">
                <span class="info-label">Check-Out</span>
                <span class="info-value">{{ $booking->check_out->format('d M Y') }} ({{ $booking->check_out->format('l') }})</span>
            </div>
            <div class="info-item">
                <span class="info-label">Lama Menginap</span>
                <span class="info-value">{{ $booking->check_out->diff($booking->check_in)->days }} Malam</span>
            </div>
        </div>

        @if($booking->special_requests)
        <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
            <strong>🗒️ Permintaan Khusus:</strong>
            <p class="mb-0" style="margin-top: 10px; color: #555;">{{ $booking->special_requests }}</p>
        </div>
        @endif
    </div>

    <!-- Informasi Pembayaran & Transaksi -->
    <div class="card">
        <div class="card-title">💳 Informasi Pembayaran</div>
        
        @if($booking->transaction)
            <div class="transaction-status {{ $booking->transaction->status === 'paid' ? 'paid' : 'no-payment' }}">
                <strong>Status Transaksi:</strong> {{ ucfirst($booking->transaction->status) }}
                <br>
                <small>ID Transaksi: {{ $booking->transaction->transaction_code }}</small>
            </div>

            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Metode Pembayaran</span>
                    <span class="info-value">{{ $booking->transaction->payment_method ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Pembayaran</span>
                    <span class="info-value">{{ $booking->transaction->paid_at ? $booking->transaction->paid_at->format('d M Y H:i') : 'Belum Dibayar' }}</span>
                </div>
            </div>

            <div class="price-summary">
                <div class="price-row">
                    <span>Harga Kamar ({{ $booking->total_room }} x {{ $booking->check_out->diff($booking->check_in)->days }} malam):</span>
                    <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                </div>
                <div class="price-row">
                    <span>Diskon:</span>
                    <strong>- Rp {{ number_format($booking->transaction->discount ?? 0, 0, ',', '.') }}</strong>
                </div>
                <div class="price-row total">
                    <span>Total Pembayaran:</span>
                    <strong style="color: #27ae60;">Rp {{ number_format(($booking->total_price - ($booking->transaction->discount ?? 0)), 0, ',', '.') }}</strong>
                </div>
            </div>
        @else
            <div class="transaction-status no-payment">
                ⚠️ Belum ada data transaksi/pembayaran untuk booking ini
            </div>
        @endif
    </div>

    <!-- Update Status -->
    @if($booking->status !== 'cancelled' && $booking->status !== 'checked_out')
    <div class="card">
        <div class="card-title">⚙️ Update Status Booking</div>
        <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="form-group">
                <label for="status">Pilih Status Baru:</label>
                <select name="status" id="status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Konfirmasi)</option>
                    <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed (Dikonfirmasi)</option>
                    <option value="checked_in" {{ $booking->status === 'checked_in' ? 'selected' : '' }}>Checked In (Sudah Check-In)</option>
                    <option value="checked_out" {{ $booking->status === 'checked_out' ? 'selected' : '' }}>Checked Out (Sudah Check-Out)</option>
                    <option value="cancelled">Cancelled (Dibatalkan)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Status
            </button>
        </form>
    </div>
    @endif

    <!-- Tombol Aksi -->
    <div class="card">
        <div class="btn-group">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            @if(in_array($booking->status, ['pending', 'confirmed']))
            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus booking ini? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus Booking
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

@endsection
