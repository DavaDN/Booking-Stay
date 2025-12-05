@extends('layouts.sidebar')

@section('title', 'Detail Transaksi - Resepsionis')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 font-weight-bold text-dark">Detail Transaksi</h1>
                <a href="{{ route('resepsionis.transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaction Info -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background-color: #2365A2; color: white;">
                    <h5 class="mb-0">Informasi Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">ID Transaksi:</label>
                        </div>
                        <div class="col-7">
                            <span class="badge bg-secondary" style="font-size: 0.9rem;">#{{ $transaction->id }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Status:</label>
                        </div>
                        <div class="col-7">
                            @if($transaction->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($transaction->status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($transaction->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Booking Code:</label>
                        </div>
                        <div class="col-7">
                            <span class="badge bg-secondary">{{ $transaction->booking->booking_code ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Metode Pembayaran:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">
                                <i class="fas fa-credit-card"></i>
                                {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Total:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0 font-weight-bold" style="color: #2365A2; font-size: 1.1rem;">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-5">
                            <label class="form-label text-muted small">Tanggal Pembayaran:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">
                                @if($transaction->payment_date)
                                    {{ \Carbon\Carbon::parse($transaction->payment_date)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Belum dibayar</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Info -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background-color: #2365A2; color: white;">
                    <h5 class="mb-0">Informasi Booking</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Status Booking:</label>
                        </div>
                        <div class="col-7">
                            @if($transaction->booking->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($transaction->booking->status === 'confirmed')
                                <span class="badge bg-success">Confirmed</span>
                            @elseif($transaction->booking->status === 'checked_in')
                                <span class="badge bg-info">Checked In</span>
                            @elseif($transaction->booking->status === 'checked_out')
                                <span class="badge bg-secondary">Checked Out</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($transaction->booking->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Tipe Kamar:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $transaction->booking->roomType->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Check-In:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ \Carbon\Carbon::parse($transaction->booking->check_in_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Check-Out:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ \Carbon\Carbon::parse($transaction->booking->check_out_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-5">
                            <label class="form-label text-muted small">Jumlah Kamar:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $transaction->booking->number_of_rooms }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header" style="background-color: #2365A2; color: white;">
                    <h5 class="mb-0">Informasi Customer</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Nama:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $transaction->booking->customer->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label text-muted small">Email:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $transaction->booking->customer->email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-5">
                            <label class="form-label text-muted small">Nomor HP:</label>
                        </div>
                        <div class="col-7">
                            <p class="mb-0">{{ $transaction->booking->customer->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($transaction->status === 'pending')
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('resepsionis.transactions.updateStatus', $transaction->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="paid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Konfirmasi Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
