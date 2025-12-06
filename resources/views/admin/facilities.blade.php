@extends('layouts.sidebar')

@section('content')

<style>
    body {
        background: #D3E7FF; 
        font-family: sans-serif;
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .header-section h5 {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 20px;
        color: #333;
    }
    .header-section small { color: #666; }
    .add-button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .add-button:hover { background-color: #0056b3; }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 8px;
        font-size: 14px;
    }
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    .facility-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }
    .facility-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 30px 20px;
        position: relative;
        text-align: center;
        transition: 0.2s ease-in-out;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .facility-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.12);
    }
    .icon-container {
        height: 80px;
        width: 80px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    .facility-card h6 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    .facility-actions {
        position: absolute;
        top: 8px;
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0 10px;
    }
    .facility-actions button, .facility-actions a {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        color: #999;
        transition: color 0.2s;
    }
    .facility-actions button:hover, .facility-actions a:hover { color: #333; }
    .edit-icon { color: #ffc107; }
    .delete-icon { color: #dc3545; }
</style>

<div class="container">
    <div class="header-section">
        <div>
            <h5>Fasilitas Kamar</h5>
            <small>Kelola fasilitas yang tersedia di kamar</small>
        </div>
        <button class="add-button" onclick="openModal('createModal')">+ Tambah Fasilitas</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="facility-grid">
        @forelse($facilities as $facility)
            <div class="facility-card">
                <div class="facility-actions">
                    <a href="#" onclick="openModal('editModal{{ $facility->id }}')">
                        <span class="edit-icon">✏️</span>
                    </a>
                    <form action="{{ route('facilities.destroy', $facility->id) }}" method="POST" onsubmit="return confirm('Yakin hapus fasilitas ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"><span class="delete-icon">🗑️</span></button>
                    </form>
                </div>

                <div class="icon-container">
                    <img 
                        src="{{ $facility->image ? asset('storage/app/public/facilites' . $facility->image) : asset('images/default.png') }}" 
                        alt="{{ $facility->name }}" 
                        class="icon">
                </div>
                <h6>{{ $facility->name }}</h6>
            </div>

            <!-- Modal Edit -->
            <div id="editModal{{ $facility->id }}" class="modal">
                <div class="modal-content">
                    <h4>Edit Fasilitas</h4>
                    <span class="close-modal" onclick="closeModal('editModal{{ $facility->id }}')">&times;</span>
                    <form action="{{ route('facilities.update', $facility->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" value="{{ $facility->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Gambar</label>
                            @if($facility->image)
                                <img src="{{ asset('storage/'.$facility->image) }}" width="80" style="margin-bottom:10px;">
                            @endif
                            <input type="file" name="image">
                        </div>
                        <button type="submit" class="modal-button">Update</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Belum ada fasilitas</div>
        @endforelse
    </div>
</div>

<!-- Modal Create -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <h4>Tambah Fasilitas</h4>
        <span class="close-modal" onclick="closeModal('createModal')">&times;</span>
        <form action="{{ route('facilities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Gambar</label>
                <input type="file" name="image">
            </div>
            <button type="submit" class="modal-button">Simpan</button>
        </form>
    </div>
</div>

<style>
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
    .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); position: relative; }
    .close-modal { color: #aaa; position: absolute; top: 10px; right: 15px; font-size: 28px; cursor: pointer; }
    .close-modal:hover { color: #000; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
    .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    .modal-button { background-color: #2365A2; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; transition: background-color 0.2s; float: right; }
    .modal-button:hover { background-color: #0084ff; }
</style>

<script>
    function openModal(id) { document.getElementById(id).style.display = "block"; }
    function closeModal(id) { document.getElementById(id).style.display = "none"; }
    window.onclick = function(event) { if (event.target.classList.contains('modal')) event.target.style.display = "none"; }
</script>

@endsection
