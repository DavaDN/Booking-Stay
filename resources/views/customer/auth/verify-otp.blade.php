@extends('customer.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm p-4" style="width:100%; max-width:450px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h4 class="text-center mb-3 fw-bold">Verifikasi OTP</h4>
            <p class="text-muted text-center mb-4">
                Masukkan kode OTP yang sudah dikirim ke email kamu.
            </p>

            {{-- Suppress layout-global alerts for this view and show inline alerts here --}}
            @section('hide-layout-alerts')@endsection

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-warning">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form id="verifyForm" action="{{ route('customer.verify-otp') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode OTP</label>

                    <div class="input-group">
                        <input type="text" name="otp" class="form-control" placeholder="Masukkan 6 digit OTP" required>
                        <button type="button" class="btn btn-outline-secondary" id="resendBtn" onclick="document.getElementById('resendForm').submit()">Kirim Ulang OTP</button>
                    </div>
                    {{-- include hidden email so controller can find customer even if session not available in POST --}}
                    <input type="hidden" name="email" value="{{ session('otp_email') ?? old('email') }}">
                </div>

                <button type="submit" class="btn btn-dark w-100 py-2">
                    Verifikasi
                </button>

                <div class="text-center mt-3">
                    <a href="{{ route('customer.login.form') }}" class="text-decoration-none">Kembali ke Login</a>
                </div>

            </form>

            {{-- Hidden resend form (not nested) --}}
            <form id="resendForm" action="{{ route('customer.resend-otp') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="email" value="{{ session('otp_email') ?? old('email') }}">
            </form>
        </div>
    </div>
</div>
</div>
@endsection
