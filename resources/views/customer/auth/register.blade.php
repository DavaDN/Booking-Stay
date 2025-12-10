@extends('layouts.app')

@section('content')
<div class="auth-page">
    <style>
        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .auth-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px;
            letter-spacing: -0.5px;
        }

        .auth-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .auth-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9f9f9;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4A90E2;
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .auth-footer {
            padding: 0 30px 30px;
            text-align: center;
        }

        .auth-footer p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .auth-footer a {
            color: #4A90E2;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: #357ABD;
            text-decoration: underline;
        }

        .alert-custom {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 14px;
            border-left: 4px solid;
        }

        .alert-danger-custom {
            background: #fee;
            color: #c33;
            border-left-color: #c33;
        }

        .alert-success-custom {
            background: #efe;
            color: #3c3;
            border-left-color: #3c3;
        }

        .password-info {
            font-size: 12px;
            color: #999;
            margin-top: 6px;
        }
    </style>

    <div class="auth-container">
        <div class="auth-header">
            <h2>Daftar Akun</h2>
            <p>Buat akun baru di Booking-Stay</p>
        </div>

        <div class="auth-body">
            @if($errors->any())
                <div class="alert-custom alert-danger-custom">
                    <i class="fas fa-exclamation-circle"></i> 
                    <strong>Terjadi Kesalahan:</strong>
                    <ul style="margin: 8px 0 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-custom alert-success-custom">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('customer.register') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap" 
                           value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan email Anda" 
                           value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone" placeholder="Contoh: 08123456789" 
                           value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                    <div class="password-info">💡 Gunakan kombinasi huruf, angka, dan simbol untuk keamanan lebih</div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="btn-register">Daftar Sekarang</button>
            </form>
        </div>

        <div class="auth-footer">
            <p>Sudah punya akun? 
                <a href="{{ route('customer.login.form') }}">Login di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
