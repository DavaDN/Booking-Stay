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
        flex-wrap: wrap;
    }

    .search-box input,
    .search-box select {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        flex: 1;
        min-width: 200px;
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

    .btn-search {
        background: #3498db;
        color: white;
    }

    .btn-search:hover {
        background: #2980b9;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .stat-card h6 {
        margin: 0 0 5px 0;
        font-size: 12px;
        color: #999;
    }

    .stat-card .number {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
    }
</style>

<div class="header">
    <h5>🏨 Manajemen Booking Hotel</h5>
    <p class="text-muted mb-0">Kelola semua reservasi dari customer</p>
    
    <form method="GET" class="search-box mt-1">
        <input type="text" name="search" placeholder="Cari kode booking atau nama customer..." value="{{ request('search') }}">
        <select name="status" class="form-control">
            <option value="">-- Semua Status --</option>
            @foreach($statusOptions as $option)
                <option value="{{ $option }}" {{ request('status') === $option ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $option)) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-search">🔍 Cari</button>
        <a href="{{ route('admin.bookings.index') }}" class="btn" style="background: #95a5a6; color: white;">🔄 Reset</a>
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
                        <td>
                            <div>{{ $booking->customer->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $booking->customer->email ?? '' }}</small>
                        </td>
                        <td>{{ $booking->roomType->name ?? 'N/A' }}</td>
                        <td>{{ $booking->check_in->format('d M Y') }}</td>
                        <td>{{ $booking->check_out->format('d M Y') }}</td>
                        <td><strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="badge badge-{{ $booking->status }}">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-info" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- Admin no longer confirms bookings; handled by payment webhook and resepsionis -->
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus booking ini?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end mt-4">
            {{ $bookings->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-calendar-times" style="font-size: 3rem; color: #ddd; margin-bottom: 10px;"></i>
            <p>Tidak ada data booking</p>
        </div>
    @endif
</div>
@endsection
