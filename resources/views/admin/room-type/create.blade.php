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
    <h5>Tambah Tipe Kamar</h5>
    <a href="{{ route('admin.room-types.index') }}" class="btn-cancel">Kembali</a>
</div>

<div class="card-form">
    <form action="{{ route('admin.room-types.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama Tipe Kamar</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Deluxe Room" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kapasitas (Orang)</label>
            <input type="number" name="capacity" class="form-control" placeholder="Contoh: 2" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga per Malam</label>
            <input type="number" name="price" class="form-control" placeholder="Contoh: 350000" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat tipe kamar"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto Tipe Kamar (Opsional)</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Fasilitas</label>
            <div class="row">
                @foreach ($facilities as $facility)
                    <div class="col-md-4">
                        <label>
                            <input type="checkbox" name="facility_ids[]" value="{{ $facility->id }}">
                            {{ $facility->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            @error('facility_ids')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="submit" class="btn-submit">Simpan</button>
        </div>
    </form>
</div>

@endsection
