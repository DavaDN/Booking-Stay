@extends('layouts.app')

@section('title', $roomType->name . ' - Booking Stay')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('customer.list') }}" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Title & Price -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title font-weight-bold" style="color: #2365A2; margin-bottom: 1rem;">{{ $roomType->name }}</h2>
                    
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold" style="color: #28a745;">
                                Rp {{ number_format($roomType->price, 0, ',', '.') }}
                                <small class="text-muted" style="font-size: 0.6rem;">/malam</small>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <span class="badge" style="background-color: #D3E7FF; color: #2365A2; padding: 0.5rem 1rem; font-size: 1rem;">
                                <i class="fas fa-users"></i> Max {{ $roomType->capacity }} Orang
                            </span>
                        </div>
                    </div>

                    <hr>

                    <!-- Description -->
                    <h5 class="font-weight-bold mb-2" style="color: #2365A2;">Deskripsi</h5>
                    <p>{{ $roomType->description }}</p>
                </div>
            </div>

            <!-- Facilities -->
            @if($roomType->facilities && $roomType->facilities->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold mb-3" style="color: #2365A2;">
                            <i class="fas fa-star"></i> Fasilitas Kamar
                        </h5>
                        <div class="row">
                            @foreach($roomType->facilities as $facility)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div style="flex-shrink: 0; margin-right: 1rem;">
                                            @if($facility->image && file_exists(storage_path('app/public/' . $facility->image)))
                                                <img src="{{ asset('storage/' . $facility->image) }}" alt="{{ $facility->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            @else
                                                <div style="width: 50px; height: 50px; background-color: #D3E7FF; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-check-circle" style="color: #2365A2; font-size: 1.5rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="mb-0 font-weight-bold">{{ $facility->name }}</p>
                                            <small class="text-muted">{{ $facility->description }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Available Rooms -->
            @if($roomType->rooms && $roomType->rooms->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold mb-3" style="color: #2365A2;">
                            <i class="fas fa-bed"></i> Kamar Tersedia
                        </h5>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>No Kamar</th>
                                        <th>Status</th>
                                        <th>Hotel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roomType->rooms as $room)
                                        <tr>
                                            <td class="font-weight-bold">{{ $room->number }}</td>
                                            <td>
                                                @if($room->status === 'available')
                                                    <span class="badge bg-success">Tersedia</span>
                                                @elseif($room->status === 'booked')
                                                    <span class="badge bg-warning text-dark">Terpesan</span>
                                                @else
                                                    <span class="badge bg-secondary">Maintenance</span>
                                                @endif
                                            </td>
                                            <td>{{ $room->roomType->hotel->name ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                Tidak ada kamar untuk tipe ini
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header" style="background-color: #2365A2; color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Ringkasan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Tipe Kamar</p>
                        <p class="font-weight-bold">{{ $roomType->name }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Harga Per Malam</p>
                        <p class="font-weight-bold" style="color: #28a745; font-size: 1.3rem;">
                            Rp {{ number_format($roomType->price, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Kapasitas</p>
                        <p class="font-weight-bold">
                            <i class="fas fa-users"></i> {{ $roomType->capacity }} Orang
                        </p>
                    </div>

                    @php
                        $availableCount = $roomType->rooms ? $roomType->rooms->where('status', 'available')->count() : 0;
                        $totalCount = $roomType->rooms ? $roomType->rooms->count() : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Ketersediaan</p>
                        <p class="font-weight-bold">
                            <span class="badge" style="background-color: #D3E7FF; color: #2365A2; padding: 0.5rem 1rem;">
                                {{ $availableCount }}/{{ $totalCount }} Tersedia
                            </span>
                        </p>
                    </div>

                    <hr>

                    <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-calendar"></i> Pesan Sekarang
                    </a>

                    <a href="{{ route('customer.list') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-list"></i> Kembali
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
