@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 420px;">
    <h3 class="fw-bold mb-3 text-center">Login Customer</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('customer.login.submit') }}" method="POST">
        @csrf

        <label>Email</label>
        <input type="email" name="email" class="form-control mb-2" required>

        <label>Password</label>
        <input type="password" name="password" class="form-control mb-3" required>

        <button class="btn btn-primary w-100">Login</button>
    </form>

    <p class="text-center mt-3">
        Belum punya akun?
        <a href="{{ route('customer.register') }}">Daftar sekarang</a>
    </p>
</div>
@endsection
