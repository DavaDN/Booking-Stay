{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Laravel vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token && window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
        });
    </script>
</head>

<body class="font-sans antialiased">

    {{-- ALERT ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3">
            <strong>Error!</strong>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- SESSION ERROR --}}
    @if (session('error'))
        <div class="alert alert-warning alert-dismissible fade show m-3">
            <strong>Perhatian!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- SESSION SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- NAVBAR --}}
    @include('layouts.navigation')

    <div class="min-h-screen">
        @yield('content')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    {{-- ========================================================= --}}
    {{-- ====================== MODAL LOGIN ======================== --}}
    {{-- ========================================================= --}}
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Login Customer</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('customer.login') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <label class="fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" required>

                        <label class="fw-semibold mt-3">Password</label>
                        <input type="password" name="password" class="form-control" required>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-dark px-4">Login</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- ==================== MODAL REGISTER ====================== --}}
    {{-- ========================================================= --}}
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content p-3">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Register Customer</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('customer.register') }}" method="POST">
                    @csrf

                    <div class="modal-body">

                        <label class="fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>

                        <label class="fw-semibold mt-3">Email</label>
                        <input type="email" name="email" class="form-control" required>

                        <label class="fw-semibold mt-3">Nomor HP</label>
                        <input type="text" name="phone" class="form-control" required>

                        <label class="fw-semibold mt-3">Password</label>
                        <input type="password" name="password" class="form-control" required>

                        <label class="fw-semibold mt-3">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary px-4">Register</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</body>
</html>
