@extends('layouts.app')

@section('title', 'Semua Tipe Kamar - Booking Stay')

@section('content')
<style>
    body {
        padding-top: 70px;
        background-color: #f8f9fa;
    }
    
    .page-header {
        background: linear-gradient(135deg, #2365A2 0%, #1a4d7a 100%);
        color: white;
        padding: 40px 20px;
        margin-bottom: 30px;
        border-radius: 8px;
    }
    
    .page-header h1 {
        margin-bottom: 10px;
        font-size: 32px;
    }
    
    .sidebar-filter {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        height: fit-content;
        position: sticky;
        top: 80px;
    }
    
    .filter-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .filter-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .filter-section h6 {
        color: #2365A2;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        margin-bottom: 15px;
        letter-spacing: 0.5px;
    }
    
    .filter-section label {
        display: block;
        margin-bottom: 10px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        transition: color 0.2s ease;
    }
    
    .filter-section label:hover {
        color: #2365A2;
    }
    
    .filter-section input[type="text"],
    .filter-section input[type="number"],
    .filter-section select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        margin-bottom: 10px;
        transition: border-color 0.2s ease;
    }
    
    .filter-section input:focus,
    .filter-section select:focus {
        outline: none;
        border-color: #2365A2;
        box-shadow: 0 0 0 3px rgba(35, 101, 162, 0.1);
    }
    
    .filter-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 20px;
    }
    
    .filter-buttons .btn {
        padding: 10px;
        font-size: 13px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .filter-buttons .btn-search {
        background: #2365A2;
        color: white;
        grid-column: 1 / -1;
    }
    
    .filter-buttons .btn-search:hover {
        background: #1a4d7a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(35, 101, 162, 0.2);
    }
    
    .filter-buttons .btn-reset {
        background: #f0f0f0;
        color: #333;
        grid-column: 1 / -1;
    }
    
    .filter-buttons .btn-reset:hover {
        background: #e0e0e0;
    }
    
    .room-card {
        transition: all 0.3s ease;
        height: 100%;
    }

    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    }
</style>

<div class="container-fluid py-4" style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="h2 font-weight-bold">
            <i class="fas fa-door-open"></i> Semua Tipe Kamar
        </h1>
        <p class="text-white-50">Temukan kamar yang sesuai dengan kebutuhan Anda</p>
    </div>

    <!-- Main Content with Sidebar -->
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 mb-4">
            <div class="sidebar-filter">
                <form method="GET" action="{{ route('customer.list') }}" id="filterForm">
                    <!-- Search -->
                    <div class="filter-section">
                        <h6><i class="fas fa-search"></i> Cari Kamar</h6>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Nama kamar..."
                               value="{{ request('search') }}">
                    </div>

                    <!-- Hotel Filter -->
                    <div class="filter-section">
                        <h6><i class="fas fa-hotel"></i> Hotel</h6>
                        <select name="hotel_id" class="form-control">
                            <option value="">Semua Hotel</option>
                            @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}"
                                        {{ request('hotel_id') == $hotel->id ? 'selected' : '' }}>
                                    {{ $hotel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City Filter -->
                    <div class="filter-section">
                        <h6><i class="fas fa-map-marker-alt"></i> Kota</h6>
                        <select name="city" class="form-control">
                            <option value="">Semua Kota</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}"
                                        {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="filter-section">
                        <h6><i class="fas fa-money-bill-wave"></i> Kisaran Harga</h6>
                        <label style="margin-bottom: 8px; color: #666;">Min (Rp)</label>
                        <input type="number"
                               name="min_price"
                               class="form-control"
                               placeholder="0"
                               value="{{ request('min_price') }}">
                        
                        <label style="margin-bottom: 8px; color: #666; margin-top: 10px;">Max (Rp)</label>
                        <input type="number"
                               name="max_price"
                               class="form-control"
                               placeholder="10000000"
                               value="{{ request('max_price') }}">
                    </div>

                    <!-- Filter Buttons -->
                    <div class="filter-buttons">
                        <button type="submit" class="btn btn-search">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ route('customer.list') }}" class="btn btn-reset" style="text-decoration: none; text-align: center;">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Room Types Grid -->
        <div class="col-lg-9">
            <div class="row">
        @forelse($roomTypes as $roomType)
            <div class="col-md-6 col-lg-4 col-xl-4 mb-4">
                <div class="card h-100 border-0 shadow-sm room-card">
                    <!-- Room Image -->
                    <div style="height: 200px; overflow: hidden; position: relative;">
                        @if($roomType->image && file_exists(storage_path('app/public/' . $roomType->image)))
                            <img src="{{ asset('storage/' . $roomType->image) }}"
                                 alt="{{ $roomType->name }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #D3E7FF, #2365A2); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-bed" style="font-size: 3rem; color: white; opacity: 0.3;"></i>
                            </div>
                        @endif

                        <!-- Available Badge -->
                        @if($roomType->available_rooms > 0)
                            <span class="badge badge-success position-absolute" style="top: 10px; right: 10px;">
                                {{ $roomType->available_rooms }} tersedia
                            </span>
                        @else
                            <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">
                                Penuh
                            </span>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Room Type Name -->
                        <h5 class="card-title font-weight-bold mb-2" style="color: #2365A2;">
                            {{ $roomType->name }}
                        </h5>

                        <!-- Hotel Name -->
                        <p class="text-muted small mb-2">
                            <i class="fas fa-hotel"></i>
                            {{ $roomType->hotel->name }}
                        </p>

                        <!-- Location -->
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $roomType->hotel->city }}
                        </p>

                        <!-- Description -->
                        @if($roomType->description)
                        <p class="card-text small text-muted mb-3" style="flex-grow: 1;">
                            {{ \Str::limit($roomType->description, 60, '...') }}
                        </p>
                        @endif

                        <!-- Facilities -->
                        @if($roomType->facilities && $roomType->facilities->count() > 0)
                        <div class="mb-3">
                            <div class="d-flex flex-wrap" style="gap: 0.25rem;">
                                @foreach($roomType->facilities->take(2) as $facility)
                                    <span class="badge badge-light border small" style="color: #000; font-weight: 600;">
                                        {{ $facility->name }}
                                    </span>
                                @endforeach
                                @if($roomType->facilities->count() > 2)
                                    <span class="badge badge-secondary small">
                                        +{{ $roomType->facilities->count() - 2 }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Price -->
                        <div class="mb-3">
                            <span class="h5 font-weight-bold" style="color: #2365A2;">
                                Rp {{ number_format($roomType->price, 0, ',', '.') }}
                            </span>
                            <span class="text-muted small">/malam</span>
                        </div>

                        <!-- Button -->
                        <a href="{{ route('customer.list.show', $roomType->id) }}"
                           class="btn btn-sm w-100"
                           style="background-color: #2365A2; color: white;">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-bed" style="font-size: 4rem; color: #D3E7FF;"></i>
                    <h5 class="mt-3 text-muted">Tidak ada tipe kamar ditemukan</h5>
                    <p class="text-muted">Silakan coba filter lain atau reset pencarian</p>
                    <a href="{{ route('customer.list') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-redo"></i> Reset Filter
                    </a>
                </div>
            </div>
        @endforelse
            </div>

            <!-- Pagination -->
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-end">
                    {{ $roomTypes->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
