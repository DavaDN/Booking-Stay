@extends('layouts.app')

@section('title', $roomType->name . ' - Booking Stay')

@section('content')
<div class="container-fluid py-4">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('customer.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kamar
            </a>
        </div>
    </div>

    <!-- Room Type Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <!-- Room Image -->
                <div style="height: 400px; overflow: hidden; position: relative;">
                    @if($roomType->image && file_exists(storage_path('app/public/' . $roomType->image)))
                        <img src="{{ asset('storage/' . $roomType->image) }}"
                             alt="{{ $roomType->name }}"
                             style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #D3E7FF, #2365A2); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-bed" style="font-size: 8rem; color: white; opacity: 0.3;"></i>
                        </div>
                    @endif

                    <!-- Availability Badge -->
                    @if($roomType->available_rooms > 0)
                        <span class="badge badge-success position-absolute" style="top: 20px; right: 20px; font-size: 1rem; padding: 10px 20px;">
                            <i class="fas fa-check-circle"></i> {{ $roomType->available_rooms }} Kamar Tersedia
                        </span>
                    @else
                        <span class="badge badge-danger position-absolute" style="top: 20px; right: 20px; font-size: 1rem; padding: 10px 20px;">
                            <i class="fas fa-times-circle"></i> Tidak Tersedia
                        </span>
                    @endif
                </div>

                <!-- Room Info -->
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-8">
                            <h1 class="h2 font-weight-bold mb-3" style="color: #2365A2;">
                                {{ $roomType->name }}
                            </h1>

                            <!-- Hotel Info -->
                            @php $hotel = $roomType->hotel ?? ($roomType->hotels->first() ?? null); @endphp
                            <div class="mb-4">
                                <h5 class="font-weight-bold mb-2">Hotel</h5>
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-hotel mt-1 mr-3" style="color: #2365A2; font-size: 1.2rem;"></i>
                                    <div>
                                        <p class="font-weight-bold mb-0">{{ optional($hotel)->name ?? '-' }}</p>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ optional($hotel)->city ?? '-' }}, {{ optional($hotel)->address ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($roomType->description)
                            <div class="mb-4">
                                <h5 class="font-weight-bold mb-2">Deskripsi Kamar</h5>
                                <p class="text-muted" style="line-height: 1.8;">
                                    {{ $roomType->description }}
                                </p>
                            </div>
                            @endif

                            <!-- Room Facilities -->
                            @if($roomType->facilities && $roomType->facilities->count() > 0)
                            <div class="mb-4">
                                <h5 class="font-weight-bold mb-3">Fasilitas Kamar</h5>
                                <div class="row">
                                    @foreach($roomType->facilities as $facility)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle mr-2" style="color: #2365A2;"></i>
                                            <span>{{ $facility->name }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Hotel Facilities -->
                            @if($roomType->hotel->facilities && $roomType->hotel->facilities->count() > 0)
                            <div class="mb-4">
                                <h5 class="font-weight-bold mb-3">Fasilitas Hotel</h5>
                                <div class="row">
                                    @foreach($roomType->hotel->facilities as $facility)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle mr-2" style="color: #28a745;"></i>
                                            <span>{{ $facility->name }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Booking Card -->
                        <div class="col-lg-4">
                            <div class="card border shadow-sm sticky-top" style="top: 20px;">
                                <div class="card-body">
                                    <h4 class="font-weight-bold mb-3">Informasi Pemesanan</h4>

                                    <!-- Price -->
                                    <div class="mb-4">
                                        <p class="text-muted small mb-1">Harga per malam</p>
                                        <h2 class="font-weight-bold" style="color: #2365A2;">
                                            Rp {{ number_format($roomType->price, 0, ',', '.') }}
                                        </h2>
                                    </div>

                                    <!-- Room Status -->
                                    <div class="mb-4">
                                        <p class="text-muted small mb-1">Status Ketersediaan</p>
                                        @if($roomType->available_rooms > 0)
                                            <p class="font-weight-bold text-success mb-0">
                                                <i class="fas fa-check-circle"></i>
                                                {{ $roomType->available_rooms }} Kamar Tersedia
                                            </p>
                                        @else
                                            <p class="font-weight-bold text-danger mb-0">
                                                <i class="fas fa-times-circle"></i>
                                                Tidak Tersedia
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Booking Button -->
                                    @if($roomType->available_rooms > 0)
                                        <a href="{{ route('bookings.create', ['room_type_id' => $roomType->id]) }}"
                                           class="btn btn-lg btn-block"
                                           style="background-color: #2365A2; color: white;">
                                            <i class="fas fa-calendar-check"></i> Pesan Sekarang
                                        </a>
                                    @else
                                        <button class="btn btn-lg btn-block btn-secondary" disabled>
                                            <i class="fas fa-times-circle"></i> Tidak Tersedia
                                        </button>
                                    @endif

                                    <hr>

                                    <!-- Additional Info -->
                                    <div class="small text-muted">
                                        <p class="mb-2">
                                            <i class="fas fa-info-circle"></i>
                                            Harga belum termasuk pajak dan biaya layanan
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-shield-alt"></i>
                                            Pembatalan gratis dalam 24 jam
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Rooms List (Optional) -->
    @if($availableRooms->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="font-weight-bold mb-4">Kamar yang Tersedia</h4>
                    <div class="row">
                        @foreach($availableRooms as $room)
                        <div class="col-md-3 mb-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="fas fa-door-open" style="font-size: 2rem; color: #2365A2;"></i>
                                    <h5 class="font-weight-bold mt-2 mb-0">{{ $room->room_number }}</h5>
                                    <small class="text-success">Tersedia</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
