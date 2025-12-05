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
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }

    .header {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .header h3 {
        margin: 0 0 10px 0;
        color: var(--primary);
        font-size: 24px;
        font-weight: 700;
    }

    .header p {
        margin: 0;
        color: var(--text);
    }

    .search-box {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .search-box input {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        flex: 1;
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

    .booking-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border-left: 4px solid var(--primary);
    }

    .booking-card h5 {
        margin: 0 0 10px 0;
        color: var(--primary);
        font-weight: 600;
    }

    .booking-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin: 15px 0;
    }

    .info-item {
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .info-item label {
        display: block;
        font-size: 12px;
        color: var(--text);
        margin-bottom: 5px;
    }

    .info-item value {
        display: block;
        font-weight: 600;
        color: #333;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .badge-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-checked_in {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-checked_out {
        background: #e7e8ea;
        color: #383d41;
    }

    .no-data {
        background: white;
        border-radius: 8px;
        padding: 60px 20px;
        text-align: center;
        color: #999;
    }

    .no-data i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .action-buttons .btn {
        flex: 1;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .booking-info {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <div class="header">
        <h3><i class="fas fa-calendar-alt"></i> Booking Saya</h3>
        <p>Lihat dan kelola semua reservasi hotel Anda</p>
        
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari booking code..." onkeyup="filterBookings()">
            <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Pesan Kamar Baru
            </a>
        </div>
    </div>

    @if ($bookings->count() > 0)
        <div id="bookingsList">
            @foreach ($bookings as $booking)
                <div class="booking-card" data-booking="{{ $booking->booking_code }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h5>{{ $booking->booking_code }}</h5>
                        <span class="badge badge-{{ $booking->status }}">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>

                    <div class="booking-info">
                        <div class="info-item">
                            <label>Tipe Kamar</label>
                            <value>{{ $booking->roomType->name ?? 'N/A' }}</value>
                        </div>
                        <div class="info-item">
                            <label>Check-In</label>
                            <value>{{ $booking->check_in->format('d/m/Y') }}</value>
                        </div>
                        <div class="info-item">
                            <label>Check-Out</label>
                            <value>{{ $booking->check_out->format('d/m/Y') }}</value>
                        </div>
                        <div class="info-item">
                            <label>Jumlah Kamar</label>
                            <value>{{ $booking->total_room }}</value>
                        </div>
                        <div class="info-item">
                            <label>Total Tamu</label>
                            <value>{{ $booking->guests }} orang</value>
                        </div>
                        <div class="info-item">
                            <label>Total Harga</label>
                            <value style="color: var(--success);">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</value>
                        </div>
                    </div>

                    @if ($booking->special_requests)
                        <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; margin: 10px 0;">
                            <strong style="font-size: 12px; color: var(--text);">Permintaan Khusus:</strong>
                            <p style="margin: 5px 0 0 0; font-size: 14px;">{{ $booking->special_requests }}</p>
                        </div>
                    @endif

                    <div class="action-buttons">
                        <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        @if ($booking->status === 'pending' || $booking->status === 'confirmed')
                            <a href="{{ route('customer.bookings.edit', $booking->id) }}" class="btn" style="background: #f39c12; color: white;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        @if ($booking->status === 'pending')
                            <form action="{{ route('customer.bookings.update', $booking->id) }}" method="POST" style="flex: 1;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" class="btn" style="background: #e74c3c; color: white; width: 100%; justify-content: center;" onclick="return confirm('Batalkan booking ini?')">
                                    <i class="fas fa-times"></i> Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-calendar-times"></i>
            <p>Anda belum memiliki booking.</p>
            <a href="{{ route('customer.list') }}" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-search"></i> Cari Kamar Sekarang
            </a>
        </div>
    @endif
</div>

<script>
    function filterBookings() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const bookings = document.querySelectorAll('.booking-card');

        bookings.forEach(booking => {
            const bookingCode = booking.getAttribute('data-booking');
            if (bookingCode.toUpperCase().indexOf(filter) > -1) {
                booking.style.display = '';
            } else {
                booking.style.display = 'none';
            }
        });
    }
</script>
@endsection
