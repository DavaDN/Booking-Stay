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

    {{-- ALERTS: allow child views to suppress global alerts by defining section 'hide-layout-alerts' --}}
    @unless(View::hasSection('hide-layout-alerts'))
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
    @endunless

    {{-- NAVBAR --}}
    @include('layouts.navigation')

    <div class="min-h-screen">
        @yield('content')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')


</body>
</html>
