@extends('layouts.sb-resepsionis')

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
                            <td>{{ $booking->customer->name ?? $booking->customer_name ?? 'N/A' }}</td>
                            <td>{{ $booking->roomType->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            <td>
                                    @if($booking->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($booking->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($booking->status === 'check-in')
                                        <span class="badge bg-info">Check In</span>
                                    @elseif($booking->status === 'check-out')
                                        <span class="badge bg-secondary">Check Out</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($booking->status) }}</span>
                                    @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('resepsionis.bookings.show', $booking->id) }}" class="btn btn-sm btn-info d-flex align-items-center" title="Lihat Detail" style="margin-right: 10px;">
                                        <i class="fas fa-eye me-1"></i>
                                        <span>Detail</span>
                                    </a>

                                    
                                    @if($booking->status === 'paid')
                                        <form action="{{ route('resepsionis.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="check-in">
                                            <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center" title="Check In">
                                                <i class="fas fa-sign-in-alt me-1"></i>
                                                <span>Check In</span>
                                            </button>
                                        </form>

                                    {{-- After check-in, show check-out but disable until check-out datetime reached --}}
                                    @elseif($booking->status === 'check-in')
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            $checkoutAt = \Carbon\Carbon::parse($booking->check_out);
                                        @endphp

                                        @if($now->gte($checkoutAt))
                                            <form action="{{ route('resepsionis.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="check-out">
                                                <button type="submit" class="btn btn-sm btn-secondary d-flex align-items-center" title="Check Out">
                                                        <i class="fas fa-sign-out-alt me-1"></i>
                                                        <span>Check Out</span>
                                                    </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-secondary d-flex align-items-center" disabled title="Check-out only available on {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y H:i') }}">
                                                <i class="fas fa-sign-out-alt me-1"></i>
                                                <span>Check Out</span>
                                            </button>
                                        @endif
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
        {{ $bookings->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
