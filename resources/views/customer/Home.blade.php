@extends('layouts.app')

@section('title', 'Daftar Hotel - Booking Stay')

@section('content')
<style>
    body {
        padding-top: 70px;
        background-color: #f8f9fa;
    }

    .page-header {
        background: linear-gradient(135deg, #2365A2 0%, #1a4d7a 100%);
        color: white;
        padding: 50px 20px;
        margin-bottom: 40px;
        border-radius: 8px;
        text-align: center;
    }

    .page-header h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .page-header p {
        font-size: 16px;
        opacity: 0.95;
        margin-bottom: 0;
    }

    .search-section {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 40px;
    }

    .search-section form {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 15px;
    }

    .search-section input {
        padding: 14px 18px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .search-section input:focus {
        outline: none;
        border-color: #2365A2;
        box-shadow: 0 0 0 3px rgba(35, 101, 162, 0.1);
    }

    .search-section .btn-search {
        padding: 14px 40px;
        background: #2365A2;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-section .btn-search:hover {
        background: #1a4d7a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(35, 101, 162, 0.3);
    }

    .hotel-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .hotel-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .hotel-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .hotel-image {
        height: 240px;
        overflow: hidden;
        position: relative;
        background: linear-gradient(135deg, #D3E7FF, #2365A2);
    }

    .hotel-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hotel-image-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hotel-image-icon i {
        font-size: 4rem;
        color: white;
        opacity: 0.3;
    }

    .hotel-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .hotel-title {
        font-size: 18px;
        font-weight: 700;
        color: #2365A2;
        margin-bottom: 12px;
    }

    .hotel-location {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #666;
        margin-bottom: 12px;
    }

    .hotel-location i {
        color: #2365A2;
    }

    .hotel-description {
        font-size: 13px;
        color: #777;
        line-height: 1.6;
        margin-bottom: 15px;
        flex: 1;
    }

    .facilities-group {
        margin-bottom: 15px;
    }

    .facility-badge {
        display: inline-block;
        padding: 6px 12px;
        background: #E8F0F8;
        color: #2365A2;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 6px;
        margin-bottom: 6px;
    }

    .facility-badge.more {
        background: #f0f0f0;
        color: #555;
    }

    .hotel-rooms {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #666;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .hotel-rooms i {
        color: #2365A2;
    }

    .btn-detail {
        width: 100%;
        padding: 12px;
        background: #2365A2;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
    }

    .btn-detail:hover {
        background: #1a4d7a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(35, 101, 162, 0.3);
        text-decoration: none;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .empty-state i {
        font-size: 5rem;
        color: #D3E7FF;
        margin-bottom: 20px;
    }

    .empty-state h5 {
        color: #333;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #999;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 40px;
    }

    @media (max-width: 768px) {
        .hotel-grid {
            grid-template-columns: 1fr;
        }

        .search-section form {
            grid-template-columns: 1fr;
        }

        .page-header h1 {
            font-size: 28px;
        }
    }
</style>

<div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-hotel"></i> Daftar Hotel</h1>
        <p>Temukan dan pesan hotel pilihan Anda</p>
<div class="d-flex align-items-center justify-content-center ">
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 font-weight-bold text-dark">Daftar Hotel</h1>
            <p class="text-muted">Temukan dan pesan hotel pilihan Anda</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-section">
        <form method="GET">
            <input type="text" name="search" placeholder="🔍 Cari hotel, kota, atau deskripsi..." value="{{ request()->get('search') }}">
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>
    </div>

    <!-- Hotel Grid -->
    @if($hotels->count() > 0)
        <div class="hotel-grid">
            @foreach($hotels as $hotel)
                <div class="hotel-card">
                    <!-- Hotel Image -->
                    <div class="hotel-image">
                        @if($hotel->image && file_exists(storage_path('app/public/' . $hotel->image)))
                            <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}">
                        @else
                            <div class="hotel-image-icon">
                                <i class="fas fa-hotel"></i>
                            </div>
                        @endif
                    </div>

                    <div class="hotel-body">
                        <h3 class="hotel-title">{{ $hotel->name }}</h3>
                        
                        <div class="hotel-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $hotel->city }}, {{ $hotel->address }}</span>
                        </div>

                        <p class="hotel-description">
                            {{ \Str::limit($hotel->description, 100, '...') }}
                        </p>

                        <!-- Facilities -->
                        @if($hotel->facilities && $hotel->facilities->count() > 0)
                            <div class="facilities-group">
                                @foreach($hotel->facilities->take(3) as $facility)
                                    <span class="facility-badge">{{ $facility->name }}</span>
                                @endforeach
                                @if($hotel->facilities->count() > 3)
                                    <span class="facility-badge more">+{{ $hotel->facilities->count() - 3 }}</span>
                                @endif
                            </div>
                        @endif

                        <!-- Number of Rooms -->
                        <div class="hotel-rooms">
                            <i class="fas fa-door-open"></i>
                            <span>{{ $hotel->rooms ? $hotel->rooms->count() : 0 }} Kamar Tersedia</span>
                        </div>

                        <a href="{{ route('customer.hotels.show', $hotel->id) }}" class="btn-detail">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $hotels->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h5>Tidak ada hotel ditemukan</h5>
            <p>Silakan coba pencarian lain atau kembali nanti</p>
        </div>
    @endif
</div>
@endsection
