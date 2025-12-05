@extends('layouts.app')

@section('title', $hotel->name . ' - Booking Stay')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('customer.hotels.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Hotel Main Image -->
    <div class="row mb-4">
        <div class="col-12">
            <div style="height: 400px; overflow: hidden; border-radius: 8px;">
                @if($hotel->image && file_exists(storage_path('app/public/' . $hotel->image)))
                    <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #2365A2, #D3E7FF); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-hotel" style="font-size: 6rem; color: white; opacity: 0.3;"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Hotel Info -->
        <div class="col-lg-8 mb-4">
            <!-- Basic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title font-weight-bold" style="color: #2365A2; margin-bottom: 1rem;">{{ $hotel->name }}</h2>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt" style="color: #2365A2;"></i>
                                <strong>Alamat:</strong>
                            </p>
                            <p>{{ $hotel->address }}, {{ $hotel->city }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-2">
                                <i class="fas fa-door-open" style="color: #2365A2;"></i>
                                <strong>Jumlah Kamar:</strong>
                            </p>
                            <p>{{ $hotel->rooms ? $hotel->rooms->count() : 0 }} Kamar</p>
                        </div>
                    </div>

                    <hr>

                    <h5 class="font-weight-bold mb-3" style="color: #2365A2;">Deskripsi</h5>
                    <p>{{ $hotel->description }}</p>
                </div>
            </div>

            <!-- Facilities -->
            @if($hotel->facilities && $hotel->facilities->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold" style="color: #2365A2; margin-bottom: 1rem;">
                            <i class="fas fa-star"></i> Fasilitas Hotel
                        </h5>
                        <div class="row">
                            @foreach($hotel->facilities as $facility)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        @if($facility->image && file_exists(storage_path('app/public/' . $facility->image)))
                                            <img src="{{ asset('storage/' . $facility->image) }}" alt="{{ $facility->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 1rem;">
                                        @else
                                            <div style="width: 40px; height: 40px; background-color: #D3E7FF; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                                <i class="fas fa-check" style="color: #2365A2; font-size: 1.2rem;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="mb-0 font-weight-bold">{{ $facility->name }}</p>
                                            <small class="text-muted">{{ \Str::limit($facility->description, 50, '...') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Available Room Types -->
            @if($hotel->rooms && $hotel->rooms->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold" style="color: #2365A2; margin-bottom: 1rem;">
                            <i class="fas fa-bed"></i> Tipe Kamar Tersedia
                        </h5>
                        
                        @php
                            $roomTypes = $hotel->rooms->groupBy('room_type_id');
                        @endphp

                        <div class="row">
                            @foreach($roomTypes as $typeId => $rooms)
                                @php
                                    $roomType = $rooms->first()->roomType;
                                    $availableCount = $rooms->where('status', 'available')->count();
                                @endphp
                                
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                        <h6 class="font-weight-bold" style="color: #2365A2;">{{ $roomType->name }}</h6>
                                        
                                        <div class="small mb-2">
                                            <p class="mb-1">
                                                <strong>Harga:</strong> Rp {{ number_format($roomType->price, 0, ',', '.') }}/malam
                                            </p>
                                            <p class="mb-1">
                                                <strong>Kapasitas:</strong> {{ $roomType->capacity }} Orang
                                            </p>
                                            <p class="mb-0">
                                                <strong>Kamar Tersedia:</strong>
                                                <span class="badge" style="background-color: #2365A2; color: white;">
                                                    {{ $availableCount }}/{{ $rooms->count() }}
                                                </span>
                                            </p>
                                        </div>

                                        @if($roomType->facilities && $roomType->facilities->count() > 0)
                                            <div class="small mt-2">
                                                <p class="mb-2 text-muted">Fasilitas:</p>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($roomType->facilities->take(3) as $fac)
                                                        <span class="badge" style="background-color: #D3E7FF; color: #2365A2; font-size: 0.75rem;">
                                                            {{ $fac->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar - Booking Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header" style="background-color: #2365A2; color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informasi Hotel
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Hotel Name</p>
                        <p class="font-weight-bold">{{ $hotel->name }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Lokasi</p>
                        <p class="font-weight-bold">
                            <i class="fas fa-map-marker-alt" style="color: #2365A2;"></i>
                            {{ $hotel->city }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Rating</p>
                        <div>
                            @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star" style="color: #FFD700;"></i>
                            @endfor
                            <small class="text-muted">(4.8/5)</small>
                        </div>
                    </div>

                    <hr>

                    <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary w-100">
                        <i class="fas fa-calendar"></i> Pesan Kamar
                    </a>

                    <a href="{{ route('customer.list') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-list"></i> Lihat Semua Tipe Kamar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 991px) {
        .sticky-top {
            position: static !important;
        }
    }
</style>
@endsection
