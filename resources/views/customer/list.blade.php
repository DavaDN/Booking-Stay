@extends('layouts.app')

@section('title', 'Daftar Tipe Kamar - Booking Stay')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 font-weight-bold text-dark">Daftar Tipe Kamar</h1>
            <p class="text-muted">Pilih dan pesan tipe kamar sesuai kebutuhan Anda</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" class="row g-2">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control form-control-lg" placeholder="Cari tipe kamar atau deskripsi..." value="{{ request()->get('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #2365A2;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Kamar</h6>
                    <h3 class="font-weight-bold" style="color: #2365A2;">{{ $totalRooms }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Kamar Tersedia</h6>
                    <h3 class="font-weight-bold" style="color: #28a745;">{{ $availableRooms }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Types Grid -->
    <div class="row">
        @forelse($roomTypes as $roomType)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm" style="transition: all 0.3s ease;">
                    <div class="card-body d-flex flex-column">
                        <!-- Title & Price -->
                        <div class="mb-3">
                            <h5 class="card-title font-weight-bold" style="color: #2365A2;">{{ $roomType->name }}</h5>
                            <h4 class="font-weight-bold" style="color: #28a745;">
                                Rp {{ number_format($roomType->price, 0, ',', '.') }}<small class="text-muted" style="font-size: 0.7rem;">/malam</small>
                            </h4>
                        </div>

                        <!-- Description -->
                        <p class="card-text small text-muted mb-3" style="flex-grow: 1;">
                            {{ \Str::limit($roomType->description, 80, '...') }}
                        </p>

                        <!-- Capacity -->
                        <div class="mb-3">
                            <span class="badge" style="background-color: #D3E7FF; color: #2365A2;">
                                <i class="fas fa-users"></i> {{ $roomType->capacity }} Orang
                            </span>
                        </div>

                        <!-- Facilities -->
                        @if($roomType->facilities && $roomType->facilities->count() > 0)
                            <div class="mb-3">
                                <p class="small text-muted mb-2">Fasilitas:</p>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($roomType->facilities->take(3) as $facility)
                                        <span class="badge" style="background-color: #f0f0f0; color: #586A80; font-size: 0.75rem;">
                                            {{ $facility->name }}
                                        </span>
                                    @endforeach
                                    @if($roomType->facilities->count() > 3)
                                        <span class="badge bg-secondary" style="font-size: 0.75rem;">
                                            +{{ $roomType->facilities->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Available Rooms -->
                        @php
                            $availableCount = $roomType->rooms ? $roomType->rooms->where('status', 'available')->count() : 0;
                            $totalCount = $roomType->rooms ? $roomType->rooms->count() : 0;
                        @endphp
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-door-open"></i>
                                {{ $availableCount }}/{{ $totalCount }} Kamar Tersedia
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.list.show', $roomType->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="{{ route('customer.bookings.create') }}" class="btn btn-sm w-100" style="background-color: #2365A2; color: white;">
                                <i class="fas fa-calendar"></i> Pesan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #D3E7FF;"></i>
                    <h5 class="mt-3 text-muted">Tidak ada tipe kamar ditemukan</h5>
                    <p class="text-muted">Silakan coba pencarian lain atau kembali nanti</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-end">
            {{ $roomTypes->links() }}
        </div>
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
