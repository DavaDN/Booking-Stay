@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #2365A2;
        --secondary: #D3E7FF;
        --text: #586A80;
        --success: #27ae60;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-container {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .form-container h3 {
        color: var(--primary);
        margin-bottom: 30px;
        font-size: 22px;
        font-weight: 700;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(35, 101, 162, 0.1);
    }

    .booking-summary {
        background: var(--secondary);
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .booking-summary h6 {
        color: var(--primary);
        font-weight: 700;
        margin: 0 0 10px 0;
    }

    .booking-summary p {
        margin: 5px 0;
        font-size: 14px;
        color: var(--text);
    }

    .booking-summary strong {
        color: #333;
    }

    .price-box {
        background: #f8f9fa;
        border-left: 4px solid var(--success);
        padding: 15px;
        border-radius: 6px;
        margin: 15px 0;
    }

    .price-box p {
        margin: 8px 0;
        font-size: 14px;
        color: var(--text);
    }

    .price-box p strong {
        color: #333;
    }

    .total-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #ddd;
        margin-top: 10px;
        font-size: 16px;
        font-weight: 700;
        color: var(--success);
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        padding: 10px 25px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        justify-content: center;
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

    .error-message {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 5px;
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

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('customer.transactions.index') }}" class="btn btn-secondary" style="width: auto;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-container">
        <h3>
            <i class="fas fa-credit-card"></i> Buat Transaksi Pembayaran
        </h3>

        <div class="alert alert-info">
            <strong><i class="fas fa-info-circle"></i> Info:</strong> Pilih booking yang ingin dibayar dan metode pembayaran Anda.
        </div>

        <form action="{{ route('customer.transactions.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="booking_id">Pilih Booking *</label>
                <select id="booking_id" name="booking_id" required onchange="updateBookingDetails()">
                    <option value="">-- Pilih Booking --</option>
                    @foreach ($bookings as $booking)
                        <option 
                            value="{{ $booking->id }}" 
                            data-code="{{ $booking->booking_code }}"
                            data-total="{{ $booking->total_price }}"
                            data-dates="{{ $booking->check_in->format('d/m/Y') }} - {{ $booking->check_out->format('d/m/Y') }}"
                            data-room="{{ $booking->roomType->name }}"
                        >
                            {{ $booking->booking_code }} - {{ $booking->roomType->name }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('booking_id'))
                    <div class="error-message">{{ $errors->first('booking_id') }}</div>
                @endif
            </div>

            <div id="bookingDetails" style="display: none;">
                <div class="booking-summary">
                    <h6><i class="fas fa-info-circle"></i> Detail Booking</h6>
                    <p><strong>Kode:</strong> <span id="bookingCode">-</span></p>
                    <p><strong>Kamar:</strong> <span id="roomName">-</span></p>
                    <p><strong>Tanggal:</strong> <span id="bookingDates">-</span></p>
                </div>

                <div class="price-box">
                    <p><strong>Total yang Harus Dibayar:</strong></p>
                    <div class="total-price">
                        <span>Rp <span id="totalAmount">0</span></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="payment_method">Metode Pembayaran *</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="credit_card">Kartu Kredit</option>
                    <option value="debit_card">Kartu Debit</option>
                    <option value="bank_transfer">Transfer Bank</option>
                    <option value="e_wallet">Dompet Digital (OVO, GoPay, DANA)</option>
                    <option value="cash">Tunai di Tempat</option>
                </select>
                @if ($errors->has('payment_method'))
                    <div class="error-message">{{ $errors->first('payment_method') }}</div>
                @endif
            </div>

            <div class="alert alert-warning">
                <strong><i class="fas fa-clock"></i> Penting:</strong> Setelah membuat transaksi, Anda harus menyelesaikan pembayaran dalam waktu 24 jam. Jika tidak, booking akan otomatis dibatalkan.
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Lanjutkan Pembayaran
                </button>
                <a href="{{ route('customer.transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function updateBookingDetails() {
        const select = document.getElementById('booking_id');
        const option = select.options[select.selectedIndex];
        const details = document.getElementById('bookingDetails');

        if (option.value) {
            document.getElementById('bookingCode').textContent = option.getAttribute('data-code');
            document.getElementById('roomName').textContent = option.getAttribute('data-room');
            document.getElementById('bookingDates').textContent = option.getAttribute('data-dates');
            document.getElementById('totalAmount').textContent = parseInt(option.getAttribute('data-total')).toLocaleString('id-ID');
            details.style.display = 'block';
        } else {
            details.style.display = 'none';
        }
    }
</script>
@endsection
