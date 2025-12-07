@extends('layouts.sidebar')

@section('title', 'Daftar Booking - Resepsionis')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 font-weight-bold text-dark">Manajemen Booking</h1>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Cari booking code atau nama customer..." value="{{ request()->get('search') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Booking Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #2365A2; color: white;">
                    <tr>
                        <th>Booking Code</th>
                        <th>Customer</th>
                        <th>Tipe Kamar</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $booking->booking_code }}</span>
                            </td>
                            <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                            <td>{{ $booking->roomType->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif($booking->status === 'checked_in')
                                    <span class="badge bg-info">Checked In</span>
                                @elseif($booking->status === 'checked_out')
                                    <span class="badge bg-secondary">Checked Out</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('resepsionis.bookings.show', $booking->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('resepsionis.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-sm btn-success" title="Confirm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'confirmed')
                                        <form action="{{ route('resepsionis.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="checked_in">
                                            <button type="submit" class="btn btn-sm btn-primary" title="Check In">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'checked_in')
                                        <form action="{{ route('resepsionis.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="checked_out">
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Check Out">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox" style="font-size: 2rem;"></i><br>
                                Tidak ada booking ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end mt-3">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
