@extends('layouts.sidebar')

@section('title', 'Detail Booking - Resepsionis')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 font-weight-bold text-dark">Detail Booking</h1>
                <a href="{{ route('resepsionis.bookings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Booking Info -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background-color: #2365A2; color: white;">
                    <h5 class="mb-0">Informasi Booking</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Booking Code:</label>
                        </div>
                        <div class="col-7">
                            <span class="badge bg-secondary" style="font-size: 0.9rem;">{{ $booking->booking_code }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Status:</label>
                        </div>
                        <div class="col-7">
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
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Tipe Kamar:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $booking->roomType->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Check-In:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Check-Out:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Jumlah Kamar:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $booking->number_of_rooms }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Total Harga:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0 font-weight-bold" style="color: #2365A2;">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-5">
                            <label class="form-label text-muted small">Special Requests:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0 small">{{ $booking->special_requests ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background-color: #2365A2; color: white;">
                    <h5 class="mb-0">Informasi Customer</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Nama:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $booking->customer->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Email:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $booking->customer->email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-5">
                            <label class="form-label text-muted small">Nomor HP:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $booking->customer->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Info -->
            @if($booking->transaction)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header" style="background-color: #2365A2; color: white;">
                        <h5 class="mb-0">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5">
                                <label class="form-label text-muted small">ID Transaksi:</label>
                            </div>
                            <div class="col-7">
                                <p class="mb-0">#{{ $booking->transaction->id }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-5">
                                <label class="form-label text-muted small">Status:</label>
                            </div>
                            <div class="col-7">
                                @if($booking->transaction->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($booking->transaction->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($booking->transaction->status) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-5">
                                <label class="form-label text-muted small">Payment Method:</label>
                            </div>
                            <div class="col-7">
                                <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $booking->transaction->payment_method)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex gap-2">
                    @if($booking->status === 'pending')
                        <form action="{{ route('resepsionis.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Konfirmasi Booking
                            </button>
                        </form>
                    @elseif($booking->status === 'confirmed')
                        <form action="{{ route('resepsionis.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="checked_in">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Check In
                            </button>
                        </form>
                    @elseif($booking->status === 'checked_in')
                        <form action="{{ route('resepsionis.bookings.updateStatus', $booking->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="checked_out">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-sign-out-alt"></i> Check Out
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
