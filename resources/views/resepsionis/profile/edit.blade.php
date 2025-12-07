@extends('layouts.sb-resepsionis')

@section('content')
<div class="page-header">
    <h2 class="page-title">Edit Profile (Resepsionis)</h2>
</div>

<div class="card">
    <div class="card-body">

        <form action="{{ route('resepsionis.profile.update') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password (Optional)</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Leave empty if not changing">
            </div>

            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="{{ route('resepsionis.profile') }}" class="btn btn-secondary">Cancel</a>
        </form>

    </div>
</div>
@endsection
