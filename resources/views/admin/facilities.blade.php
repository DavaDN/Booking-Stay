@extends('layouts.sidebar')

@section('content')
<style>
    body {
        background: #e6f0ff; /* biru terang */
    }
    .facility-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 20px 15px;
        position: relative;
        transition: 0.2s ease-in-out;
    }
    .facility-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.12);
    }
    .facility-card h6 {
        font-size: 14px;
        font-weight: 600;
        margin-top: 12px;
        color: #333;
    }
    .facility-actions {
        position: absolute;
        top: 8px;
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0 8px;
    }
    .facility-actions button {
        border-radius: 50%;
        width: 28px;
        height: 28px;
        font-size: 14px;
        padding: 0;
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Fasilitas Kamar</h5>
            <small class="text-muted">Kelola fasilitas yang tersedia di kamar</small>
        </div>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Fasilitas
        </button>
    </div>

    {{-- Pesan sukses --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Grid daftar fasilitas --}}
    <div class="row g-3">
        @forelse($facilities as $facility)
            <div class="col-md-3 col-sm-6">
                <div class="facility-card text-center">

                    {{-- Tombol Edit & Hapus --}}
                    <div class="facility-actions">
                        {{-- Edit --}}
                        <button class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $facility->id }}">
                            ✏️
                        </button>

                        {{-- Delete --}}
                        <form action="{{ route('facilities.destroy', $facility->id) }}" 
                              method="POST" onsubmit="return confirm('Yakin hapus?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑️</button>
                        </form>
                    </div>

                    {{-- Gambar --}}
                    @if($facility->image)
                        <img src="{{ asset('storage/'.$facility->image) }}" 
                             class="mx-auto d-block" width="50" height="50">
                    @else
                        <div class="text-muted small">No Image</div>
                    @endif

                    {{-- Nama fasilitas --}}
                    <h6>{{ $facility->name }}</h6>
                </div>
            </div>

            {{-- Modal Edit --}}
            <div class="modal fade" id="editModal{{ $facility->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Facility</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form action="{{ route('facilities.update', $facility->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $facility->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label>Image</label><br>
                            @if($facility->image)
                                <img src="{{ asset('storage/'.$facility->image) }}" width="80" class="mb-2">
                            @endif
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No facilities found</div>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Facility</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('facilities.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection