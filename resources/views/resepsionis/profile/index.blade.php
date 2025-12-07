@extends('layouts.sb-resepsionis')

@section('content')

<div class="page-header">
    <h2 class="page-title">Profile</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">

        <div class="mb-3">
            <label class="form-label">Name</label>
            <p>{{ $user->name }}</p>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <p>{{ $user->email }}</p>
        </div>

        <a href="{{ route('resepsionis.profile.edit') }}" class="btn btn-primary">Edit Profile</a>

    </div>
</div>
@endsection
