@extends('layouts.app')

@section('title', 'Semua Tipe Kamar - Booking Stay')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 font-weight-bold text-dark">Semua Tipe Kamar</h1>
            <p class="text-muted">Temukan kamar yang sesuai dengan kebutuhan Anda</p>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('customer.list') }}">
                        <div class="row">
                            <!-- Search -->
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold mb-1">Cari Kamar</label>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Nama kamar atau deskripsi..."
                                       value="{{ request('search') }}">
                            </div>

                            <!-- Hotel Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold mb-1">Hotel</label>
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
                            <div class="col-md-2 mb-3">
                                <label class="small font-weight-bold mb-1">Kota</label>
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

                            <!-- Min Price -->
                            <div class="col-md-2 mb-3">
                                <label class="small font-weight-bold mb-1">Harga Min</label>
                                <input type="number"
                                       name="min_price"
                                       class="form-control"
                                       placeholder="0"
                                       value="{{ request('min_price') }}">
                            </div>

                            <!-- Max Price -->
                            <div class="col-md-2 mb-3">
                                <label class="small font-weight-bold mb-1">Harga Max</label>
                                <input type="number"
                                       name="max_price"
                                       class="form-control"
                                       placeholder="10000000"
                                       value="{{ request('max_price') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <a href="{{ route('customer.list') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Types Grid -->
    <div class="row">
        @forelse($roomTypes as $roomType)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card h-100 border-0 shadow-sm room-card" style="transition: all 0.3s ease;">
                    <!-- Room Image -->
                    <div style="height: 200px; overflow: hidden; position: relative;">
                        @if($roomType->image && file_exists(storage_path('app/public/' . $roomType->image)))
                            <img src="{{ asset('storage/' . $roomType->image) }}"
                                 alt="{{ $roomType->name }}"
                                 class="card-img-top"
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
                                Tidak tersedia
                            </span>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Room Type Name -->
                        <h5 class="card-title font-weight-bold mb-2" style="color: #2365A2;">
                            {{ $roomType->name }}
                        </h5>

                        <!-- Hotel Name -->
                        @php $hotel = $roomType->hotel ?? ($roomType->hotels->first() ?? null); @endphp
                        <p class="text-muted small mb-2">
                            <i class="fas fa-hotel"></i>
                            {{ optional($hotel)->name ?? '-' }}
                        </p>

                        <!-- Location -->
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ optional($hotel)->city ?? '-' }}
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
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($roomType->facilities->take(2) as $facility)
                                    <span class="badge badge-light border small">
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

<style>
    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    }

    .badge.gap-1 {
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
    }
</style>
@endsection
