<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Resepsionis</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9fafc;
            font-size: 0.9rem;
        }

        .sidebar {
            width: 220px;
            background: #fff;
            height: 100vh;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.08);
            position: fixed;
            padding-top: 20px;
            transition: all 0.3s ease;
        }

        .sidebar .logo img {
            width: 120px;
            margin: 0 auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .sidebar .logo img:hover {
            transform: scale(1.05);
        }

        .sidebar .nav-link {
            color: #444;
            font-weight: 500;
            padding: 10px 18px;
            font-size: 0.9rem;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link i {
            margin-right: 8px;
        }

        .sidebar .nav-link:hover {
            background: #f0f6ff;
            color: #0d6efd;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: #eaf2ff;
            color: #0d6efd;
            font-weight: 600;
        }

        .sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: 100%;
        }

        .sidebar .logout a {
            padding: 10px 18px;
            display: block;
            font-size: 0.9rem;
            border-radius: 8px;
            margin: 4px 12px;
            color: #dc3545;
            transition: all 0.2s ease;
        }

        .sidebar .logout a:hover {
            background: #ffe5e5;
            transform: translateX(4px);
        }

        .content {
            margin-left: 220px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo mb-4">
            <img src="{{ asset('images/logo (2).png') }}" alt="Logo">
        </div>
        <nav class="nav flex-column">
            <a href="{{ route('resepsionis.dashboard') }}"
                class="nav-link {{ request()->routeIs('resepsionis.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house"></i> Dashboard
            </a>
            @php
                // Determine reservations route robustly. Some environments may report Route::has
                // true but route() may still throw, so guard with try/catch and fallback.
                $reservasiRoute = url('resepsionis/reservations');
                if (Route::has('resepsionis.reservations.index')) {
                    try {
                        $reservasiRoute = route('resepsionis.reservations.index');
                    } catch (\Exception $e) {
                        $reservasiRoute = url('resepsionis/reservations');
                    }
                } elseif (Route::has('reservations.index')) {
                    try {
                        $reservasiRoute = route('reservations.index');
                    } catch (\Exception $e) {
                        $reservasiRoute = url('resepsionis/reservations');
                    }
                }
            @endphp
            <a href="{{ $reservasiRoute }}"
                class="nav-link {{ request()->routeIs('resepsionis.reservations.*') || request()->routeIs('reservations.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Reservasi
            </a>
            <div class="nav-item">
                    @php
                    $isReportActive = request()->routeIs('resepsionis.transactions.*') || request()->routeIs('resepsionis.bookings.*') || request()->routeIs('transactions.*') || request()->routeIs('bookings.*');
                    // Resolve transaction route safely
                    $transactionRoute = '#';
                    if (Route::has('resepsionis.transactions.index')) {
                        try { $transactionRoute = route('resepsionis.transactions.index'); } catch (\Exception $e) { $transactionRoute = '#'; }
                    } elseif (Route::has('transactions.index')) {
                        try { $transactionRoute = route('transactions.index'); } catch (\Exception $e) { $transactionRoute = '#'; }
                    }
                    // Resolve booking route safely
                    $bookingRoute = '#';
                    if (Route::has('resepsionis.bookings.index')) {
                        try { $bookingRoute = route('resepsionis.bookings.index'); } catch (\Exception $e) { $bookingRoute = '#'; }
                    } elseif (Route::has('bookings.index')) {
                        try { $bookingRoute = route('bookings.index'); } catch (\Exception $e) { $bookingRoute = '#'; }
                    }
                @endphp

                <a class="nav-link d-flex align-items-center justify-content-between {{ $isReportActive ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#reportMenu" role="button"
                    aria-expanded="{{ $isReportActive ? 'true' : 'false' }}"
                    aria-controls="reportMenu">
                    <span><i class="bi bi-file-earmark-text"></i> <span>Master Data</span></span>
                    <i class="bi bi-chevron-down"></i>
                </a>

                <div class="collapse {{ $isReportActive ? 'show' : '' }}" id="reportMenu">
                    <div class="sidebar-subnav"
                        style="padding-left:10px; padding-top:6px; display:flex; flex-direction:column; gap:6px;">
                        <a href="{{ $transactionRoute }}"
                            class="nav-link {{ request()->routeIs('resepsionis.transactions.*') || request()->routeIs('transactions.*') ? 'active' : '' }}">
                            <i class="bi bi-wallet2"></i> Transaction
                        </a>
                        <a href="{{ $bookingRoute }}"
                            class="nav-link {{ request()->routeIs('resepsionis.bookings.*') || request()->routeIs('bookings.*') ? 'active' : '' }}">
                            <i class="bi bi-card-checklist"></i> Booking
                        </a>
                    </div>
                </div>
            </div>



            <a href="#" class="nav-link">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
        </nav>

        <!-- Logout -->
        <div class="logout">
            <a href="{{ route('login') }}">
                <i class="bi bi-box-arrow-left"></i> Log-Out
            </a>
        </div>
    </div>

    <!-- Header -->
    <div class="header d-flex justify-content-between align-items-center p-3"
        style="background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-left: 220px;">

        <div class="page-title">
            <h4 class="m-0">Hotel Management System</h4>
        </div>

        <div class="header-actions d-flex align-items-center">

            <div class="notification-icon position-relative me-3" style="font-size: 20px;">
                <i class="bi bi-bell"></i>
                <span
                    class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    style="font-size: 10px;">
                    3
                </span>
            </div>

            <div class="user-profile" style="font-size: 24px;">
                <a href="{{ route('resepsionis.profile') }}" style="color: #0d6efd;">
                    <i class="bi bi-person-circle"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>

</body>

</html>
