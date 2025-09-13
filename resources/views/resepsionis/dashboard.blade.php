@extends('layouts.sb-resepsionis')

@section('content')
    <!-- Topbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-semibold">Dashboard Resepsionis</h5>
        <i class="bi bi-person-circle profile-icon fs-4"></i>
    </div>

    <!-- Statistik -->
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-0 rounded-3 hover-card">
                <div class="d-flex align-items-center">
                    <i class="bi bi-people fs-4 text-primary me-2"></i>
                    <div>
                        <small class="text-muted">Tamu yang Menginap</small>
                        <h6 class="fw-bold mb-0">130</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-0 rounded-3 hover-card">
                <div class="d-flex align-items-center">
                    <i class="bi bi-journal-text fs-4 text-success me-2"></i>
                    <div>
                        <small class="text-muted">Reservasi Hari Ini</small>
                        <h6 class="fw-bold mb-0">20</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-0 rounded-3 hover-card">
                <div class="d-flex align-items-center">
                    <i class="bi bi-door-open fs-4 text-warning me-2"></i>
                    <div>
                        <small class="text-muted">Kamar Tersedia</small>
                        <h6 class="fw-bold mb-0">100</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-0 rounded-3 hover-card">
                <div class="d-flex align-items-center">
                    <i class="bi bi-cash-stack fs-4 text-danger me-2"></i>
                    <div>
                        <small class="text-muted">Pendapatan Hari Ini</small>
                        <h6 class="fw-bold mb-0">Rp 12.000.000</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Reservasi -->
    <div class="card shadow-sm mt-4 border-0 rounded-3">
        <div class="card-header bg-primary text-white fw-semibold small">Daftar Reservasi</div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Tamu</th>
                        <th>Nomor Kamar</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Tanto</td>
                        <td>01 PopArt Pod</td>
                        <td>Regular</td>
                        <td><span class="badge bg-success">Check-In</span></td>
                        <td>20-08-2025</td>
                        <td>30-08-2025</td>
                    </tr>
                    <tr>
                        <td>Atthar</td>
                        <td>04 PopArt Pod</td>
                        <td>Regular</td>
                        <td><span class="badge bg-success">Check-In</span></td>
                        <td>28-08-2025</td>
                        <td>29-08-2025</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .profile-icon {
            cursor: pointer;
            color: #555;
            transition: color 0.3s ease;
        }
        .profile-icon:hover {
            color: #0d6efd;
        }
    </style>
@endsection
