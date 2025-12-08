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

    <style>
        /* Alerts fixed under the navbar */
        .site-alerts {
            position: fixed;
            top: 68px; /* adjust to match navbar height */
            left: 0;
            right: 0;
            z-index: 1050;
            pointer-events: none; /* allow clicks through outside the container */
        }
        .site-alerts .container {
            pointer-events: auto; /* allow interacting with alerts */
        }
        .site-alerts .alert {
            border-radius: 6px;
            margin-top: 6px;
        }
        /* push page content down slightly so fixed alerts don't cover it when present */
        body.has-site-alerts {
            padding-top: 110px; /* navbar (approx 68px) + alert area */
        }
    </style>

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

    {{-- NAVBAR --}}
    @include('layouts.navigation')

    {{-- Alerts: fixed below navbar; child views can suppress with section 'hide-layout-alerts'. --}}
    @unless(View::hasSection('hide-layout-alerts'))
        <div class="site-alerts">
            <div class="container">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Error!</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-warning alert-dismissible fade show">
                        <strong>Perhatian!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>
    @endunless

    <div class="min-h-screen">
        @yield('content')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
        // If alerts are present, add class to body to push content down
        document.addEventListener('DOMContentLoaded', function () {
            var siteAlerts = document.querySelector('.site-alerts');
            if (siteAlerts && siteAlerts.querySelector('.alert')) {
                document.body.classList.add('has-site-alerts');
            }
        });
    </script>


</body>
</html>
