@extends('layouts.app')

@section('title', $hotel->name . ' - Booking Stay')

@section('content')
<div class="container-fluid py-4">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Hotel
            </a>
        </div>
    </div>

    <!-- Hotel Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <!-- Hotel Image -->
                <div style="height: 400px; overflow: hidden; position: relative;">
                    @if($hotel->image && file_exists(storage_path('app/public/' . $hotel->image)))
                        <img src="{{ asset('storage/' . $hotel->image) }}"
                             alt="{{ $hotel->name }}"
                             style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #2365A2, #D3E7FF); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-hotel" style="font-size: 8rem; color: white; opacity: 0.3;"></i>
                        </div>
                    @endif
                </div>

                <!-- Hotel Info -->
                <div class="card-body p-4">
                    <h1 class="h2 font-weight-bold mb-3" style="color: #2365A2;">{{ $hotel->name }}</h1>

                    <div class="d-flex align-items-start mb-4">
                        <i class="fas fa-map-marker-alt mt-1 mr-3" style="color: #2365A2; font-size: 1.2rem;"></i>
                        <div>
                            <p class="font-weight-bold mb-0">{{ $hotel->city }}</p>
                            <p class="text-muted mb-0">{{ $hotel->address }}</p>
                        </div>
                    </div>

                    @if($hotel->description)
                    <div class="mb-4">
                        <h4 class="font-weight-bold mb-3">Tentang Hotel</h4>
                        <p class="text-muted" style="line-height: 1.8;">{{ $hotel->description }}</p>
                    </div>
                    @endif

                    <!-- Facilities -->
                    @if($hotel->facilities && $hotel->facilities->count() > 0)
                    <div class="mb-4">
                        <h4 class="font-weight-bold mb-3">Fasilitas Hotel</h4>
                        <div class="row">
                            @foreach($hotel->facilities as $facility)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="d-flex align-items-center p-3 rounded" style="background-color: #F8F9FA;">
                                    <i class="fas fa-check-circle mr-2" style="color: #2365A2;"></i>
                                    <span class="small">{{ $facility->name }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Room Types Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="font-weight-bold mb-4">Tipe Kamar Tersedia</h3>

                    @if($hotel->roomTypes && $hotel->roomTypes->count() > 0)
                    <div class="row">
                        @foreach($hotel->roomTypes as $roomType)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border shadow-sm">
                                <!-- Room Type Image (jika ada) -->
                                @if($roomType->image && file_exists(storage_path('app/public/' . $roomType->image)))
                                <div style="height: 200px; overflow: hidden;">
                                    <img src="{{ asset('storage/' . $roomType->image) }}"
                                         alt="{{ $roomType->name }}"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                @else
                                <div style="height: 200px; background: linear-gradient(135deg, #D3E7FF, #2365A2); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-bed" style="font-size: 3rem; color: white; opacity: 0.5;"></i>
                                </div>
                                @endif

                                <div class="card-body">
                                    <h5 class="font-weight-bold mb-2" style="color: #2365A2;">{{ $roomType->name }}</h5>

                                    @if($roomType->description)
                                    <p class="text-muted small mb-3">
                                        {{ \Str::limit($roomType->description, 80) }}
                                    </p>
                                    @endif

                                    <!-- Price -->
                                    <div class="mb-3">
                                        <span class="h4 font-weight-bold" style="color: #2365A2;">
                                            Rp {{ number_format($roomType->price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-muted small">/malam</span>
                                    </div>

                                    <!-- Available Rooms -->
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-door-open"></i>
                                            {{ $roomType->available_rooms ?? 0 }} kamar tersedia
                                        </small>
                                    </div>

                                    <!-- Room Facilities (jika ada) -->
                                    @if($roomType->facilities && $roomType->facilities->count() > 0)
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">Fasilitas:</small>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($roomType->facilities->take(3) as $facility)
                                                <span class="badge badge-light border small">
                                                    {{ $facility->name }}
                                                </span>
                                            @endforeach
                                            @if($roomType->facilities->count() > 3)
                                                <span class="badge badge-secondary small">
                                                    +{{ $roomType->facilities->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Action Button -->
                                    <a href="{{ route('customer.list.show', $roomType->id) }}"
                                       class="btn btn-block"
                                       style="background-color: #2365A2; color: white;">
                                        <i class="fas fa-calendar-check"></i> Lihat Detail & Pesan
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-bed" style="font-size: 4rem; color: #D3E7FF;"></i>
                        <h5 class="mt-3 text-muted">Belum ada tipe kamar tersedia</h5>
                        <p class="text-muted">Silakan hubungi hotel untuk informasi lebih lanjut</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    }
</style>
@endsection
