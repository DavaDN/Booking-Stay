@extends('customer.layouts.app')

@section('content')
<div class="container py-5" style="max-width: 500px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h4 class="text-center mb-3 fw-bold">Verifikasi OTP</h4>
            <p class="text-muted text-center mb-4">
                Masukkan kode OTP yang sudah dikirim ke email kamu.
            </p>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('customer.verify-otp') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode OTP</label>
                    <input type="text" name="otp" class="form-control" placeholder="Masukkan 6 digit OTP" required>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-2">
                    Verifikasi
                </button>

                <div class="text-center mt-3">
                    <a href="{{ route('customer.login.form') }}" class="text-decoration-none">
                        Kembali ke Login
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
