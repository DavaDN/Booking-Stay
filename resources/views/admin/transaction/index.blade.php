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

    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-paid { background: #d4edda; color: #155724; }
    .badge-failed { background: #f8d7da; color: #721c24; }
    .badge-cancelled { background: #e7e8ea; color: #383d41; }

    .btn {
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        font-size: 12px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-info {
        background: #3498db;
        color: white;
    }

    .btn-info:hover {
        background: #2980b9;
    }

    .btn-success {
        background: #27ae60;
        color: white;
    }

    .btn-success:hover {
        background: #229954;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #999;
    }
</style>

<div class="header">
    <h5>Daftar Transaksi</h5>
    <p class="text-muted mb-0">Kelola semua transaksi pembayaran</p>
    
    <form method="GET" class="search-box mt-1">
        <input type="text" name="search" placeholder="Cari booking code..." value="{{ request('search') }}">
        <button type="submit" class="btn" style="background: #3498db; color: white;">Cari</button>
    </form>

    <div class="d-flex justify-content-end mt-1" style="gap:8px; margin-left: -250px;">
        @if(Route::has('admin.transactions.pdf'))
            <a href="{{ route('admin.transactions.pdf') }}" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
        @endif

        @if(Route::has('admin.transactions.excel'))
            <a href="{{ route('admin.transactions.excel') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Download Excel
            </a>
        @endif
    </div>
</div>

<div class="table-container">
    @if ($transaction->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Booking Code</th>
                    <th>Customer</th>
                    <th>Metode Pembayaran</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        <td><strong>{{ $item->booking->booking_code ?? 'N/A' }}</strong></td>
                        <td>{{ $item->booking->customer->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $item->payment_method)) }}</td>
                        <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.transactions.destroy', $item->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end mt-4">
            {{ $transaction->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-credit-card" style="font-size: 3rem; color: #ddd; margin-bottom: 10px;"></i>
            <p>Tidak ada data transaksi</p>
        </div>
    @endif
</div>
@endsection
