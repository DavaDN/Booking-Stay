@extends('layouts.sidebar')

@section('content')
<style>
    .header {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .header h5 {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 10px 0;
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

    .table-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .table {
        margin: 0;
    }

    .table thead {
        background: #f8f9fa;
    }

    .table th {
        border: none;
        font-weight: 600;
        color: #586A80;
        padding: 15px;
    }

    .table td {
        padding: 15px;
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    .pagination {
        margin-top: 20px;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #999;
    }
</style>

<div class="header">
    <h5>Daftar Customer</h5>
    <p class="text-muted mb-0">Kelola semua data customer Anda</p>
    
    <form method="GET" class="search-box mt-1">
        <input type="text" name="search" placeholder="Cari customer..." value="{{ request('search') }}">
        <button type="submit" class="btn" style="background: #3498db; color: white;">Cari</button>
    </form>
</div>

<div class="table-container">
    @if ($customers->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone ?? '-' }}</td>
                        <td>{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</td>
                        <td>
                            <form action="{{ route('admin.customers.destroy', $item->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end">
              {{ $customers->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-users" style="font-size: 3rem; color: #ddd; margin-bottom: 10px;"></i>
            <p>Tidak ada data customer</p>
        </div>
    @endif
</div>
@endsection
