@extends('layouts.sidebar')

@section('content')
    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>Total Kamar</h6>
                    <h3>10</h3>
                    <i class="fas fa-bed text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>Tersedia</h6>
                    <h3>10</h3>
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>Terisi</h6>
                    <h3>10</h3>
                    <i class="fas fa-door-closed text-danger"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>Maintenance</h6>
                    <h3>10</h3>
                    <i class="fas fa-tools text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Floor Status -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Aktifitas Terbaru
                </div>
                <div class="card-body">
                    <div class="activity-item">
                        <div class="activity-info">
                            <b>Iwan</b> - Kamar I-1
                        </div>
                        <span class="activity-badge">Check-in</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-info">
                            <b>Tanto</b> - Kamar I-1
                        </div>
                        <span class="activity-badge">Check-in</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-info">
                            <b>Kodah</b> - Kamar I-1
                        </div>
                        <span class="activity-badge">Check-in</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-info">
                            <b>Sudi</b> - Kamar I-1
                        </div>
                        <span class="activity-badge">Check-in</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floor Status -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Status Kamar per Lantai
                </div>
                <div class="card-body">
                    <!-- Lantai 1 -->
                    <div class="floor-status">
                        <div class="floor-header">
                            <h6 class="floor-title">Lantai 1</h6>
                            <div class="floor-badges">
                                <span class="badge badge-available">Tersedia: 10</span>
                                <span class="badge badge-occupied">Terisi: 10</span>
                                <span class="badge badge-maintenance">Maintenance: 10</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lantai 2 -->
                    <div class="floor-status">
                        <div class="floor-header">
                            <h6 class="floor-title">Lantai 2</h6>
                            <div class="floor-badges">
                                <span class="badge badge-available">Tersedia: 10</span>
                                <span class="badge badge-occupied">Terisi: 10</span>
                                <span class="badge badge-maintenance">Maintenance: 10</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lantai 3 -->
                    <div class="floor-status">
                        <div class="floor-header">
                            <h6 class="floor-title">Lantai 3</h6>
                            <div class="floor-badges">
                                <span class="badge badge-available">Tersedia: 10</span>
                                <span class="badge badge-occupied">Terisi: 10</span>
                                <span class="badge badge-maintenance">Maintenance: 10</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lantai 4 -->
                    <div class="floor-status">
                        <div class="floor-header">
                            <h6 class="floor-title">Lantai 4</h6>
                            <div class="floor-badges">
                                <span class="badge badge-available">Tersedia: 10</span>
                                <span class="badge badge-occupied">Terisi: 10</span>
                                <span class="badge badge-maintenance">Maintenance: 10</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection