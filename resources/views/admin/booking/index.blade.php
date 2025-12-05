@extends('layouts.sidebar')

@section('content')
<style>
    .header {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .header h5 {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 10px 0;
    }

    .search-box {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .search-box input {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        flex: 1;
    }

    .table-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .table {
        margin: 0;
    }

    .table thead {
        background: #f8f9fa;
    }

    .table th {
        border: none;
        font-weight: 600;
        color: #586A80;
        padding: 15px;
    }

    .table td {
        padding: 15px;
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .badge-checked_in { background: #d1ecf1; color: #0c5460; }
    .badge-checked_out { background: #e7e8ea; color: #383d41; }

    .btn {
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        font-size: 12px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-info {
        background: #3498db;
        color: white;
    }

    .btn-info:hover {
        background: #2980b9;
    }

    .btn-warning {
        background: #f39c12;
        color: white;
    }

    .btn-warning:hover {
        background: #d68910;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #999;
    }
</style>

<div class="header">
    <h5>Daftar Booking</h5>
    <p class="text-muted mb-0">Kelola semua reservasi hotel</p>
    
    <form method="GET" class="search-box mt-3">
        <input type="text" name="search" placeholder="Cari booking code atau customer..." value="{{ request('search') }}">
        <button type="submit" class="btn" style="background: #3498db; color: white;">Cari</button>
    </form>
</div>

<div class="table-container">
    @if ($bookings->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Booking</th>
                    <th>Customer</th>
                    <th>Tipe Kamar</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr>
                        <td><strong>{{ $booking->booking_code }}</strong></td>
                        <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                        <td>{{ $booking->roomType->name ?? 'N/A' }}</td>
                        <td>{{ $booking->check_in->format('d/m/Y') }}</td>
                        <td>{{ $booking->check_out->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $booking->status }}">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if ($booking->status === 'pending')
                                <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="btn btn-warning" title="Konfirmasi">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end mt-4">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-calendar-times" style="font-size: 3rem; color: #ddd; margin-bottom: 10px;"></i>
            <p>Tidak ada data booking</p>
        </div>
    @endif
</div>
@endsection
