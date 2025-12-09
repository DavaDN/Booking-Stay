@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #2365A2;
        --secondary: #D3E7FF;
        --text: #586A80;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-container {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-container h3 {
        color: var(--primary);
        margin-bottom: 30px;
        font-size: 22px;
        font-weight: 700;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(35, 101, 162, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-group small {
        display: block;
        margin-top: 5px;
        color: #999;
        font-size: 12px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        padding: 10px 25px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        flex: 1;
        justify-content: center;
    }

    .btn-primary:hover {
        background: #1a4d7a;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
        flex: 1;
        justify-content: center;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .error-message {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 5px;
    }

    .alert {
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .room-select {
        border: 1px solid #ddd;
        border-radius: 6px;
        overflow: auto;
        max-height: 300px;
    }

    .room-option {
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .room-option:hover {
        background: var(--secondary);
    }

    .room-option input[type="checkbox"] {
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-secondary" style="width: auto;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-container">
        <h3>
            <i class="fas fa-calendar-alt"></i> Pesan Kamar Baru
        </h3>

        <form action="{{ route('customer.bookings.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="room_type_id">Pilih Tipe Kamar *</label>
                <select id="room_type_id" name="room_type_id" required onchange="updatePrice()">
                    <option value="">-- Pilih Tipe Kamar --</option>
                    @foreach ($roomTypes as $type)
                        <option value="{{ $type->id }}" data-price="{{ $type->price }}">
                            {{ $type->name }} - Rp {{ number_format($type->price, 0, ',', '.') }}/malam
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('room_type_id'))
                    <div class="error-message">{{ $errors->first('room_type_id') }}</div>
                @endif
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="check_in">Tanggal Check-In *</label>
                    <input 
                        type="date" 
                        id="check_in" 
                        name="check_in" 
                        value="{{ old('check_in') }}" 
                        required
                        min="{{ date('Y-m-d') }}"
                        onchange="updatePrice()"
                    >
                    @if ($errors->has('check_in'))
                        <div class="error-message">{{ $errors->first('check_in') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="check_out">Tanggal Check-Out *</label>
                    <input 
                        type="date" 
                        id="check_out" 
                        name="check_out" 
                        value="{{ old('check_out') }}" 
                        required
                        min="{{ date('Y-m-d') }}"
                        onchange="updatePrice()"
                    >
                    @if ($errors->has('check_out'))
                        <div class="error-message">{{ $errors->first('check_out') }}</div>
                    @endif
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="number_of_rooms">Jumlah Kamar *</label>
                    <input 
                        type="number" 
                        id="number_of_rooms" 
                        name="number_of_rooms" 
                        value="{{ old('number_of_rooms', 1) }}" 
                        required
                        min="1"
                        onchange="updatePrice()"
                    >
                    @if ($errors->has('number_of_rooms'))
                        <div class="error-message">{{ $errors->first('number_of_rooms') }}</div>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="special_requests">Permintaan Khusus (Opsional)</label>
                <textarea 
                    id="special_requests" 
                    name="special_requests" 
                    placeholder="Contoh: Kamar dengan pemandangan, dekat dengan elevator, dll..."
                >{{ old('special_requests') }}</textarea>
                <small>Tuliskan permintaan khusus Anda untuk membuat penginapan lebih nyaman</small>
                @if ($errors->has('special_requests'))
                    <div class="error-message">{{ $errors->first('special_requests') }}</div>
                @endif
            </div>

            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Ringkasan Harga:</strong>
                <div style="margin-top: 10px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Harga Per Malam:</span>
                        <span id="pricePerNight">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Jumlah Malam:</span>
                        <span id="nights">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Jumlah Kamar:</span>
                        <span id="roomCount">-</span>
                    </div>
                    <hr>
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 16px; color: #27ae60;">
                        <span>Total Harga:</span>
                        <span id="totalPrice">-</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Pesan Sekarang
                </button>
                <a href="{{ route('customer.bookings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function updatePrice() {
        const roomTypeSelect = document.getElementById('room_type_id');
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');
        const totalRoomInput = document.getElementById('number_of_rooms');

        const selectedOption = roomTypeSelect.options[roomTypeSelect.selectedIndex];
        const pricePerNight = selectedOption.getAttribute('data-price') || 0;
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const nights = checkIn && checkOut ? Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)) : 0;
        const totalRooms = parseInt(totalRoomInput.value) || 1;
        const totalPrice = pricePerNight * nights * totalRooms;

        // Update display
        document.getElementById('pricePerNight').textContent = pricePerNight > 0 ? 'Rp ' + parseInt(pricePerNight).toLocaleString('id-ID') : '-';
        document.getElementById('nights').textContent = nights > 0 ? nights : '-';
        document.getElementById('roomCount').textContent = totalRooms;
        document.getElementById('totalPrice').textContent = totalPrice > 0 ? 'Rp ' + parseInt(totalPrice).toLocaleString('id-ID') : '-';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', updatePrice);
</script>
@endsection
