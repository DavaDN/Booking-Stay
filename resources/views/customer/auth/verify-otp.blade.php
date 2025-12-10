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
            max-width: 420px;
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

        .otp-info {
            background: #f0f4ff;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            color: #666;
            margin-bottom: 25px;
            border-left: 4px solid #4A90E2;
        }

        .form-group {
            margin-bottom: 20px;
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
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f9f9f9;
            font-weight: 600;
            letter-spacing: 2px;
            text-align: center;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4A90E2;
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .btn-verify {
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
            margin-top: 10px;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.4);
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .btn-resend {
            width: 100%;
            padding: 12px;
            background: white;
            color: #4A90E2;
            border: 2px solid #4A90E2;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        .btn-resend:hover {
            background: #f0f4ff;
            transform: translateY(-2px);
        }

        .auth-footer {
            padding: 0 30px 30px;
            text-align: center;
        }

        .auth-footer a {
            color: #4A90E2;
            text-decoration: none;
            font-size: 14px;
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

        .alert-warning-custom {
            background: #ffd;
            color: #8a7000;
            border-left-color: #ff9800;
        }

        .alert-success-custom {
            background: #efe;
            color: #3c3;
            border-left-color: #3c3;
        }
    </style>

    <div class="auth-container">
        <div class="auth-header">
            <h2>Verifikasi OTP</h2>
            <p>Masukkan kode OTP yang sudah dikirim ke email Anda</p>
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

            @if(session('error'))
                <div class="alert-custom alert-warning-custom">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert-custom alert-success-custom">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="otp-info">
                <i class="fas fa-info-circle"></i> Kami telah mengirimkan kode OTP 6 digit ke email Anda. Kode ini berlaku selama 5 menit.
            </div>

            <form id="verifyForm" action="{{ route('customer.verify-otp') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="otp">Kode OTP</label>
                    <input type="text" id="otp" name="otp" placeholder="000000" maxlength="6" 
                           pattern="[0-9]{6}" required autofocus>
                    <input type="hidden" name="email" value="{{ session('otp_email') ?? old('email') }}">
                </div>

                <button type="submit" class="btn-verify">Verifikasi Sekarang</button>
            </form>

            <form id="resendForm" action="{{ route('customer.resend-otp') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ session('otp_email') ?? old('email') }}">
                <button type="submit" class="btn-resend">
                    <i class="fas fa-redo"></i> Kirim Ulang OTP
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <a href="{{ route('customer.login.form') }}">← Kembali ke Login</a>
        </div>
    </div>
</div>
@endsection
