@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark">Laporan & Analitik</h1>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.report') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.report') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <!-- Total Bookings -->
        <div class="col-md-3 mb-3">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Pemesanan</p>
                            <h4 class="text-primary">{{ $totalBookings }}</h4>
                        </div>
                        <i class="fas fa-calendar fa-2x text-primary opacity-50"></i>
                    </div>
                    <small class="text-success">{{ $completedBookings }} selesai</small>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="col-md-3 mb-3">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Pemesanan Pending</p>
                            <h4 class="text-warning">{{ $pendingBookings }}</h4>
                        </div>
                        <i class="fas fa-hourglass fa-2x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-md-3 mb-3">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Pendapatan</p>
                            <h4 class="text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                        </div>
                        <i class="fas fa-money-bill fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Occupancy Rate -->
        <div class="col-md-3 mb-3">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Tingkat Okupansi</p>
                            <h4 class="text-info">{{ $occupancyRate }}%</h4>
                        </div>
                        <i class="fas fa-door-open fa-2x text-info opacity-50"></i>
                    </div>
                    <small class="text-muted">{{ $bookedRooms }}/{{ $totalRooms }} kamar</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Statistik Transaksi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Transaksi</span>
                            <strong>{{ $totalTransactions }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success">Berhasil</span>
                            <strong class="text-success">{{ $successfulTransactions }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-danger">Gagal</span>
                            <strong class="text-danger">{{ $failedTransactions }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-danger" style="width: {{ $totalTransactions > 0 ? ($failedTransactions / $totalTransactions * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Statistics -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Statistik Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small">Total Pelanggan</p>
                        <h4 class="text-success">{{ $totalCustomers }}</h4>
                    </div>
                    <div>
                        <p class="text-muted small">Pelanggan Aktif (Periode Ini)</p>
                        <h4 class="text-info">{{ $activeCustomers }}</h4>
                    </div>
                </div>
            </card>
        </div>

        <!-- Room Statistics -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Statistik Kamar</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small">Total Kamar</p>
                        <h4 class="text-info">{{ $totalRooms }}</h4>
                    </div>
                    <div>
                        <p class="text-muted small">Kamar Terpesan</p>
                        <h4 class="text-warning">{{ $bookedRooms }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Hotels -->
    @if($topHotels->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Hotel Terbaik (Berdasarkan Pemesanan)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Hotel</th>
                            <th>Kota</th>
                            <th>Jumlah Pemesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topHotels as $hotel)
                        <tr>
                            <td><strong>{{ $hotel->name }}</strong></td>
                            <td>{{ $hotel->city }}</td>
                            <td><span class="badge bg-primary">{{ $hotel->room_types_count }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Transaksi Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Kode Pemesanan</th>
                            <th>Total</th>
                            <th>Metode Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>#{{ $transaction->id }}</td>
                            <td>{{ $transaction->booking->booking_code ?? '-' }}</td>
                            <td><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
                            <td>{{ $transaction->payment_method }}</td>
                            <td>
                                @if($transaction->status === 'success')
                                    <span class="badge bg-success">Berhasil</span>
                                @elseif($transaction->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Gagal</span>
                                @endif
                            </td>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada data transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $transactions->links('vendor.pagination.custom') }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: box-shadow 0.3s ease;
    }

    .card-header {
        border-bottom: 2px solid #f0f0f0;
        font-weight: 600;
    }
</style>
@endsection
