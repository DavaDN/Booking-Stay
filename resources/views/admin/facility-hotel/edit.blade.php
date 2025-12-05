@extends('layouts.sidebar')

@section('content')
<style>
    .header {
        background: white;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .form-container {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
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
        background: #3498db;
        color: white;
    }

    .btn-primary:hover {
        background: #2980b9;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .error-message {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 5px;
    }

    .image-preview {
        margin-top: 10px;
    }

    .image-preview img {
        max-width: 300px;
        border-radius: 6px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }
</style>

<div class="header">
    <h5>Edit Fasilitas Hotel</h5>
    <a href="{{ route('facility-hotels.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="form-container">
    <form action="{{ route('facility-hotels.update', $facilityHotel->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="name">Nama Fasilitas *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $facilityHotel->name) }}" 
                    required
                    placeholder="Contoh: WiFi, Kolam Renang"
                >
                @if ($errors->has('name'))
                    <div class="error-message">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="image">Gambar Fasilitas</label>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/jpeg,image/jpg,image/png,image/webp"
                    onchange="previewImage(event)"
                >
                <small class="text-muted">Format: JPG, PNG, WebP. Max 2MB</small>
                @if ($errors->has('image'))
                    <div class="error-message">{{ $errors->first('image') }}</div>
                @endif
            </div>
        </div>

        @if ($facilityHotel->image)
            <div class="image-preview">
                <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Gambar Saat Ini:</p>
                <img src="{{ asset('storage/' . $facilityHotel->image) }}" alt="{{ $facilityHotel->name }}">
            </div>
        @endif

        <div id="imagePreview" class="image-preview" style="display: none;">
            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Preview Gambar Baru:</p>
            <img id="previewImg" src="" alt="Preview" style="max-width: 300px; border-radius: 6px;">
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea 
                id="description" 
                name="description" 
                placeholder="Jelaskan fasilitas ini..."
            >{{ old('description', $facilityHotel->description) }}</textarea>
            @if ($errors->has('description'))
                <div class="error-message">{{ $errors->first('description') }}</div>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Fasilitas
            </button>
            <a href="{{ route('facility-hotels.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endsection
