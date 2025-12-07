@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark">Dashboard</h1>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small mb-2">Total Kamar</h6>
                            <h3 class="text-primary mb-0">{{ $totalRooms }}</h3>
                        </div>
                        <i class="fas fa-bed fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small mb-2">Tersedia</h6>
                            <h3 class="text-success mb-0">{{ $availableRooms }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small mb-2">Terisi</h6>
                            <h3 class="text-danger mb-0">{{ $bookedRooms }}</h3>
                        </div>
                        <i class="fas fa-door-closed fa-2x text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small mb-2">Maintenance</h6>
                            <h3 class="text-warning mb-0">{{ $maintenanceRooms }}</h3>
                        </div>
                        <i class="fas fa-tools fa-2x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-3">
        <!-- Recent Activity -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    @forelse($recentBookings as $booking)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <p class="mb-1 fw-bold">{{ $booking->customer->name }}</p>
                            <p class="mb-0 text-muted small">{{ $booking->roomType->name }}</p>
                        </div>
                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">Tidak ada aktivitas terbaru</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Floor Status -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Status Kamar per Lantai</h6>
                </div>
                <div class="card-body">
                    @forelse($roomsByFloor as $floor => $status)
                    <div class="floor-status py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold">Lantai {{ $floor }}</h6>
                            <small class="text-muted">Total: {{ $status['total'] }} kamar</small>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-success">Tersedia: {{ $status['available'] }}</span>
                            <span class="badge bg-danger">Terisi: {{ $status['booked'] }}</span>
                            <span class="badge bg-warning text-dark">Maintenance: {{ $status['maintenance'] }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">Tidak ada data kamar</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row g-3 mt-3">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small mb-2">Total Pemesanan</h6>
                    <h4 class="text-primary mb-2">{{ $totalBookings }}</h4>
                    <small class="text-success">{{ $checkedOutBookings }} selesai</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small mb-2">Total Pelanggan</h6>
                    <h4 class="text-info mb-0">{{ $totalCustomers }}</h4>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small mb-2">Total Pendapatan</h6>
                    <h4 class="text-success mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15) !important;
    }

    .card {
        border-radius: 10px;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .floor-status {
        transition: background-color 0.3s ease;
    }

    .floor-status:hover {
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .activity-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-available {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-occupied {
        background-color: #f8d7da;
        color: #721c24;
    }

    .badge-maintenance {
        background-color: #fff3cd;
        color: #856404;
    }
</style>
@endsection
