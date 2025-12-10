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

    .facility-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .facility-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .facility-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .facility-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .facility-card-body {
        padding: 20px;
    }

    .facility-card-body h6 {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 8px 0;
        color: #2c3e50;
    }

    .facility-card-body p {
        font-size: 13px;
        color: #666;
        margin: 8px 0;
        line-height: 1.5;
    }

    .facility-actions {
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
        background: white;
        border-radius: 8px;
    }

    .no-data i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .facility-grid {
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
        <h5>Fasilitas Hotel</h5>
        <p>Kelola semua fasilitas hotel Anda</p>
    </div>
    <a href="{{ route('admin.facility-hotels.create') }}" class="btn-add">
        <i class="fas fa-plus"></i> Tambah Fasilitas
    </a>
</div>

<div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Cari fasilitas..." value="{{ request('search') }}">
        <button type="submit" class="btn" style="background: #3498db; color: white;">Cari</button>
    </form>
</div>

@if ($facilityHotels->count() > 0)
    <div class="facility-grid">
        @foreach ($facilityHotels as $item)
            <div class="facility-card">
                @if ($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                @else
                    <div style="height: 180px; background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-star" style="font-size: 2.5rem; color: white; opacity: 0.3;"></i>
                    </div>
                @endif
                <div class="facility-card-body">
                    <h6>{{ $item->name }}</h6>
                    <p>{{ Str::limit($item->description, 60) }}</p>
                    <div class="facility-actions">
                        <a href="{{ route('admin.facility-hotels.edit', $item->id) }}" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.facility-hotels.destroy', $item->id) }}" method="POST" style="flex: 1;">
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
        {{ $facilityHotels->links('vendor.pagination.custom') }}
    </div>
@else
    <div class="no-data">
        <i class="fas fa-star"></i>
        <p>Tidak ada data fasilitas hotel</p>
    </div>
@endif
@endsection
