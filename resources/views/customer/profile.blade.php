@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #2365A2;
        --text: #586A80;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }

    .profile-card {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .profile-card h3 {
        color: var(--primary);
        margin-bottom: 25px;
        font-size: 22px;
        font-weight: 700;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(35, 101, 162, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
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
    }

    .btn-primary:hover {
        background: #1a4d7a;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
    }

    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .button-group .btn {
        flex: 1;
        justify-content: center;
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

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .danger-section {
        border-top: 2px solid #f0f0f0;
        padding-top: 20px;
        margin-top: 30px;
    }

    .danger-section h4 {
        color: #e74c3c;
        margin-bottom: 15px;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .button-group {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="profile-card">
        <h3>
            <i class="fas fa-user-circle"></i> Profil Saya
        </h3>

        <form action="{{ route('customer.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $customer->name) }}" 
                        required
                    >
                    @if ($errors->has('name'))
                        <div class="error-message">{{ $errors->first('name') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $customer->email) }}" 
                        required
                    >
                    @if ($errors->has('email'))
                        <div class="error-message">{{ $errors->first('email') }}</div>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Nomor Telepon</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $customer->phone) }}" 
                    placeholder="Contoh: 081234567890"
                    required
                >
                @if ($errors->has('phone'))
                    <div class="error-message">{{ $errors->first('phone') }}</div>
                @endif
            </div>

            <div style="border-top: 2px solid #f0f0f0; padding-top: 20px; margin-top: 20px;">
                <h4 style="color: var(--primary); margin-bottom: 15px; font-weight: 700;">Ubah Password (Opsional)</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Kosongkan jika tidak ingin mengubah"
                        >
                        <small style="color: #999;">Minimal 6 karakter</small>
                        @if ($errors->has('password'))
                            <div class="error-message">{{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            placeholder="Kosongkan jika tidak ingin mengubah"
                        >
                        @if ($errors->has('password_confirmation'))
                            <div class="error-message">{{ $errors->first('password_confirmation') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('customer.bookings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="profile-card danger-section">
        <h4>
            <i class="fas fa-exclamation-triangle"></i> Zona Berbahaya
        </h4>

        <div class="alert alert-warning">
            <strong><i class="fas fa-warning"></i> Perhatian:</strong> Menghapus akun Anda akan menghapus semua data yang terkait termasuk booking dan transaksi. Tindakan ini tidak dapat dibatalkan.
        </div>

        <form action="{{ route('customer.profile.delete') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan.');">
            @csrf
            @method('DELETE')
            
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Akun Saya
            </button>
        </form>
    </div>
</div>
@endsection
