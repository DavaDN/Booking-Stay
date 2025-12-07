@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 450px;">
    <h3 class="fw-bold mb-3 text-center">Register Akun</h3>

    <form action="{{ route('customer.register.submit') }}" method="POST">
        @csrf

        <label>Nama</label>
        <input type="text" name="name" class="form-control mb-2" required>

        <label>Email</label>
        <input type="email" name="email" class="form-control mb-2" required>

        <label>No HP</label>
        <input type="text" name="phone" class="form-control mb-2" required>

        <label>Password</label>
        <input type="password" name="password" class="form-control mb-2" required>

        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control mb-2" required>

        <button class="btn btn-primary w-100 mt-3">Register</button>
    </form>

    <p class="text-center mt-3">
        Sudah punya akun?
        <a href="{{ route('customer.login') }}">Login</a>
    </p>
</div>
@endsection
