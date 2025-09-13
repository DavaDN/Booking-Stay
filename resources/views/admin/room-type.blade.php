@extends('layouts.sidebar')

@section('content')
<style>
    /* Header tanpa container biru */
    .header {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
    }
    .header h5 {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 4px 0;
    }
    .header p {
        font-size: 13px;
        color: #555;
        margin: 0 0 12px 90;
    }
    .btn-add {
        background: #3498db;
        color: #fff;
        border-radius: 8px;
        font-size: 13px;
        padding: 8px 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        text-decoration: none;
    }
    .btn-add:hover { background: #2980b9; }

    /* Ringkasan */
    .summary {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }
    .summary-card {
        flex: 1;
        padding: 18px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        text-align: center;
    }
    .summary-card h6 {
        font-size: 13px;
        margin-bottom: 6px;
        color: #555;
    }
    .summary-card span {
        font-size: 18px;
        font-weight: bold;
    }
    .summary-card:nth-child(1) span { color: #9b59b6; } /* ungu */
    .summary-card:nth-child(2) span { color: #2980b9; } /* biru */
    .summary-card:nth-child(3) span { color: #27ae60; } /* hijau */

    /* Card Room Type */
    .card-room-type {
        background: linear-gradient(135deg, #f8f9ff, #eef4ff);
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 20px;
        transition: transform .2s;
    }
    .card-room-type:hover { transform: translateY(-5px); }
    .card-room-type img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    .card-body {
        padding: 12px;
        font-size: 13px;
    }
    .card-body h6 {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .price {
        color: #27ae60;
        font-weight: bold;
        font-size: 14px;
    }
    .facilities span {
        background: #f2f2f2;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        margin: 2px;
        display: inline-block;
    }
    .card-footer {
        display: flex;
        justify-content: flex-end;
        padding: 10px;
        gap: 10px;
    }
    .btn-icon {
        border: none;
        background: none;
        cursor: pointer;
        font-size: 15px;
    }
    .btn-edit { color: #2980b9; }
    .btn-delete { color: #e74c3c; }
</style>

<!-- Header -->
<div class="header">
    <h5>Room Type Management</h5>
    <!-- <p>Kelola kategori kamar dan fasilitas</p> -->
    <a href="{{ route('room-types.create') }}" class="btn-add">
        <i class="fas fa-plus"></i> Tambah Tipe Kamar
    </a>
</div>

<!-- Ringkasan -->
<div class="summary">
    <div class="summary-card">
        <h6>Tipe Kamar</h6>
        <span>{{ $roomTypes->count() }}</span>
    </div>
    <div class="summary-card">
        <h6>Total Kamar</h6>
        <span>{{ $totalRooms ?? 0 }}</span>
    </div>
    <div class="summary-card">
        <h6>Kamar Tersedia</h6>
        <span>{{ $availableRooms ?? 0 }}</span>
    </div>
</div>

<!-- Daftar Room Type -->
<div class="row">
    @foreach($roomTypes as $type)
        <div class="col-md-4">
            <div class="card-room-type">
                <img src="{{ asset('images/kamarhotel2.jpg') }}" alt="Room Image">
                <div class="card-body">
                    <h6>{{ $type->name }}</h6>
                    <p class="mb-1">👥 {{ $type->capacity }} Orang</p>
                    <p class="price">Rp {{ number_format($type->price, 0, ',', '.') }}/malam</p>
                    <p class="mb-1">Total Kamar: {{ $type->rooms->count() ?? 0 }} • Occupancy: 75%</p>
                    <div class="facilities">
                        <span>WiFi</span>
                        <span>AC</span>
                        <span>TV</span>
                        <span>Bathroom</span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('room-types.edit', $type->id) }}" class="btn-icon btn-edit">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form action="{{ route('room-types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Hapus tipe kamar ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
