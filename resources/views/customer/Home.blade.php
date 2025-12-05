@extends('layouts.app')

@section('title', 'Daftar Hotel - Booking Stay')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 font-weight-bold text-dark">Daftar Hotel</h1>
            <p class="text-muted">Temukan dan pesan hotel pilihan Anda</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" class="row g-2">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control form-control-lg" placeholder="Cari hotel, kota, atau deskripsi..." value="{{ request()->get('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hotel Grid -->
    <div class="row">
        @forelse($hotels as $hotel)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm transition" style="cursor: pointer; transition: all 0.3s ease;">
                    <!-- Hotel Image -->
                    <div style="height: 250px; overflow: hidden; position: relative;">
                        @if($hotel->image && file_exists(storage_path('app/public/' . $hotel->image)))
                            <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" class="card-img-top" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #2365A2, #D3E7FF); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-hotel" style="font-size: 4rem; color: white; opacity: 0.3;"></i>
                            </div>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title font-weight-bold" style="color: #2365A2;">{{ $hotel->name }}</h5>
                        
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $hotel->city }}, {{ $hotel->address }}
                        </p>

                        <p class="card-text small text-muted" style="flex-grow: 1;">
                            {{ \Str::limit($hotel->description, 100, '...') }}
                        </p>

                        <!-- Facilities -->
                        @if($hotel->facilities && $hotel->facilities->count() > 0)
                            <div class="mb-3">
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($hotel->facilities->take(3) as $facility)
                                        <span class="badge" style="background-color: #D3E7FF; color: #2365A2; font-size: 0.75rem;">
                                            {{ $facility->name }}
                                        </span>
                                    @endforeach
                                    @if($hotel->facilities->count() > 3)
                                        <span class="badge bg-secondary" style="font-size: 0.75rem;">
                                            +{{ $hotel->facilities->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Number of Rooms -->
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-door-open"></i>
                                {{ $hotel->rooms ? $hotel->rooms->count() : 0 }} Kamar Tersedia
                            </small>
                        </div>

                        <a href="{{ route('customer.hotels.show', $hotel->id) }}" class="btn btn-sm w-100" style="background-color: #2365A2; color: white;">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #D3E7FF;"></i>
                    <h5 class="mt-3 text-muted">Tidak ada hotel ditemukan</h5>
                    <p class="text-muted">Silakan coba pencarian lain atau kembali nanti</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-end">
            {{ $hotels->links() }}
        </div>
    </div>
</div>

<style>
    .transition:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
