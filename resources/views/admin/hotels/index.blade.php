@extends('layouts.sidebar')

@section('content')
<style>
    .header {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-content h5 {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 10px 0;
    }

    .header-content p {
        margin: 0;
        color: #999;
    }

    .btn-add {
        background: #3498db;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add:hover {
        background: #2980b9;
    }

    .search-box {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .search-box input {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        flex: 1;
    }

    .hotel-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .hotel-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .hotel-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .hotel-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .hotel-card-body {
        padding: 20px;
    }

    .hotel-card-body h6 {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 8px 0;
        color: #2c3e50;
    }

    .hotel-card-body p {
        font-size: 14px;
        color: #666;
        margin: 8px 0;
        line-height: 1.5;
    }

    .hotel-info {
        display: flex;
        gap: 15px;
        margin: 12px 0;
        font-size: 13px;
        color: #666;
    }

    .hotel-info span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .hotel-actions {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }

    .btn {
        flex: 1;
        padding: 8px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
    }

    .btn-edit {
        background: #f39c12;
        color: white;
    }

    .btn-edit:hover {
        background: #d68910;
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .no-data i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .hotel-grid {
            grid-template-columns: 1fr;
        }

        .header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-box {
            width: 100%;
        }
    }
</style>

<div class="header">
    <div class="header-content">
        <h5>Daftar Hotel</h5>
        <p>Kelola semua data hotel Anda</p>
    </div>
    <a href="{{ route('hotels.create') }}" class="btn-add">
        <i class="fas fa-plus"></i> Tambah Hotel
    </a>
</div>

<div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Cari hotel..." value="{{ request('search') }}">
        <button type="submit" class="btn" style="background: #3498db; color: white;">Cari</button>
    </form>
</div>

@if ($hotel->count() > 0)
    <div class="hotel-grid">
        @foreach ($hotel as $item)
            <div class="hotel-card">
                @if ($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                @else
                    <div style="height: 200px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-hotel" style="font-size: 2.5rem; color: white; opacity: 0.3;"></i>
                    </div>
                @endif
                <div class="hotel-card-body">
                    <h6>{{ $item->name }}</h6>
                    <p>{{ Str::limit($item->description, 60) }}</p>
                    <div class="hotel-info">
                        <span><i class="fas fa-map-marker-alt"></i> {{ $item->city }}</span>
                    </div>
                    <p style="color: #999; font-size: 12px;">{{ $item->address }}</p>
                    <div class="hotel-actions">
                        <a href="{{ route('hotels.edit', $item->id) }}" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('hotels.destroy', $item->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus?')" style="width: 100%;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-end mt-4">
        {{ $hotel->links() }}
    </div>
@else
    <div class="no-data" style="background: white; border-radius: 8px;">
        <i class="fas fa-hotel"></i>
        <p>Tidak ada data hotel</p>
    </div>
@endif
@endsection
