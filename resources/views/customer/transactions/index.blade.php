@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #2365A2;
        --secondary: #D3E7FF;
        --text: #586A80;
        --success: #27ae60;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }

    .header {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .header h3 {
        margin: 0 0 10px 0;
        color: var(--primary);
        font-size: 24px;
        font-weight: 700;
    }

    .header p {
        margin: 0;
        color: var(--text);
    }

    .search-box {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .search-box input {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        flex: 1;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #1a4d7a;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .transaction-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border-left: 4px solid var(--primary);
    }

    .transaction-card h5 {
        margin: 0 0 10px 0;
        color: var(--primary);
        font-weight: 600;
    }

    .transaction-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin: 15px 0;
    }

    .info-item {
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .info-item label {
        display: block;
        font-size: 12px;
        color: var(--text);
        margin-bottom: 5px;
    }

    .info-item value {
        display: block;
        font-weight: 600;
        color: #333;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-paid { background: #d4edda; color: #155724; }
    .badge-failed { background: #f8d7da; color: #721c24; }
    .badge-cancelled { background: #e7e8ea; color: #383d41; }

    .no-data {
        background: white;
        border-radius: 8px;
        padding: 60px 20px;
        text-align: center;
        color: #999;
    }

    .no-data i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .action-buttons .btn {
        flex: 1;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .transaction-info {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <div class="header">
        <h3><i class="fas fa-credit-card"></i> Riwayat Transaksi</h3>
        <p>Lihat dan kelola semua transaksi pembayaran Anda</p>
        
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari booking code..." onkeyup="filterTransactions()">
            <a href="{{ route('customer.transactions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Pembayaran Baru
            </a>
        </div>
    </div>

    @if ($transactions->count() > 0)
        <div id="transactionsList">
            @foreach ($transactions as $transaction)
                <div class="transaction-card" data-booking="{{ $transaction->booking->booking_code }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h5>{{ $transaction->booking->booking_code ?? 'N/A' }}</h5>
                        <span class="badge badge-{{ $transaction->status }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>

                    <div class="transaction-info">
                        <div class="info-item">
                            <label>Metode Pembayaran</label>
                            <value>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</value>
                        </div>
                        <div class="info-item">
                            <label>Jumlah</label>
                            <value style="color: var(--success);">Rp {{ number_format($transaction->total, 0, ',', '.') }}</value>
                        </div>
                        <div class="info-item">
                            <label>Tanggal</label>
                            <value>{{ $transaction->created_at->format('d/m/Y H:i') }}</value>
                        </div>
                        @if ($transaction->payment_date)
                            <div class="info-item">
                                <label>Tanggal Pembayaran</label>
                                <value>{{ $transaction->payment_date->format('d/m/Y H:i') }}</value>
                            </div>
                        @endif
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('customer.transactions.show', $transaction->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        @if ($transaction->status === 'pending')
                            <form action="{{ route('customer.transactions.update', $transaction->id) }}" method="POST" style="flex: 1;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" class="btn" style="background: #e74c3c; color: white; width: 100%; justify-content: center;" onclick="return confirm('Batalkan transaksi ini?')">
                                    <i class="fas fa-times"></i> Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $transactions->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-credit-card"></i>
            <p>Anda belum memiliki transaksi.</p>
            <a href="{{ route('customer.bookings.index') }}" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-calendar"></i> Lihat Booking Saya
            </a>
        </div>
    @endif
</div>

<script>
    function filterTransactions() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const transactions = document.querySelectorAll('.transaction-card');

        transactions.forEach(transaction => {
            const bookingCode = transaction.getAttribute('data-booking');
            if (bookingCode.toUpperCase().indexOf(filter) > -1) {
                transaction.style.display = '';
            } else {
                transaction.style.display = 'none';
            }
        });
    }
</script>
@endsection
