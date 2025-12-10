@extends('layouts.sidebar')

@section('content')

<style>
    .header {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header h5 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .card-form {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .form-label {
        font-weight: 500;
        font-size: 13px;
    }

    .form-control {
        font-size: 13px;
        padding: 10px;
    }

    .btn-submit {
        background: #3498db;
        color: #fff;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-submit:hover {
        background: #2980b9;
    }

    .btn-cancel {
        background: #e74c3c;
        color: #fff;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-cancel:hover {
        background: #c0392b;
    }
</style>

<div class="header">
    <h5>Edit Tipe Kamar</h5>
    <a href="{{ route('admin.room-types.index') }}" class="btn-cancel">Kembali</a>
</div>

<div class="card-form">
    <form action="{{ route('admin.room-types.update', $roomType->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama Tipe Kamar</label>
            <input type="text" name="name" class="form-control" value="{{ $roomType->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kapasitas (Orang)</label>
            <input type="number" name="capacity" class="form-control" value="{{ $roomType->capacity }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga per Malam</label>
            <input type="number" name="price" class="form-control" value="{{ $roomType->price }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3">{{ $roomType->description }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar Lama</label><br>
            @if ($roomType->image)
                <img src="{{ asset('storage/room-types/' . $roomType->image) }}" width="150">
            @else
                <p>Tidak ada gambar</p>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Gambar Baru (Opsional)</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Fasilitas</label>
            <div>
                @foreach ($facilities as $facility)
                    <label style="display:block;">
                        <input type="checkbox" 
                               name="facility_ids[]" 
                               value="{{ $facility->id }}"
                               @if(in_array($facility->id, $selected)) checked @endif>
                        {{ $facility->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="submit" class="btn-submit">Update</button>
        </div>

    </form>
</div>

@endsection
