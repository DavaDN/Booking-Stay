@extends('layouts.sb-resepsionis')

@section('title', 'Daftar Transaksi - Resepsionis')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 font-weight-bold text-dark">Manajemen Transaksi</h1>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Cari booking code atau payment method..." value="{{ request()->get('search') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #2365A2; color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Booking Code</th>
                        <th>Customer</th>
                        <th>Metode Pembayaran</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>#{{ $transaction->id }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $transaction->booking->booking_code ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $transaction->booking->customer->name ?? 'N/A' }}</td>
                            <td>
                                <i class="fas fa-credit-card"></i>
                                {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                            </td>
                            <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td>
                                @if($transaction->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($transaction->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->payment_date)
                                    {{ \Carbon\Carbon::parse($transaction->payment_date)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('resepsionis.transactions.show', $transaction->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($transaction->status === 'pending')
                                        <form action="{{ route('resepsionis.transactions.updateStatus', $transaction->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" class="btn btn-sm btn-success" title="Konfirmasi Pembayaran">
                                                <i class="fas fa-check"></i>
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
                                Tidak ada transaksi ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end mt-3">
        {{ $transactions->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
