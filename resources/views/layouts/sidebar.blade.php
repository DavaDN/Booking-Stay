<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            /* WARNA DISESUAIKAN DENGAN DESAINMU */
            --primary: #2365A2;
            /* biru utama */
            --secondary: #D3E7FF;
            /* biru muda (active/bg lembut) */
            --text: #586A80;
            /* warna teks & icon */
            --text-light: #7f8c8d;
            --border: #dce6f0;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --sidebar-width: 220px;
        }

        body {
            background-color: var(--secondary);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.85rem;
            margin: 0;
            padding: 0;
            color: var(--text);
        }

        /* Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            border-right: 1px solid var(--border);
            padding: 0;
            z-index: 1000;
            /* Enhanced shadow sesuai design */
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.08);
        }

        .logo-container {
            padding: 20px 35px;
            border-bottom: 1px solid var(--border);
            text-align: center;
            background: linear-gradient(120deg, #3a4d56ff, #8fcef8ff);
        }

        .logo {
            max-width: 150px;
            height: auto;
            filter: brightness(0) invert(1);
        }

        .sidebar-nav {
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            font-weight: 500;
            position: relative;
        }

        .sidebar-nav a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1rem;
            color: var(--text);
        }

        /* hover dibuat lembut pakai warna bg aktif */
        .sidebar-nav a:hover {
            background: var(--secondary);
            color: var(--primary);
            transform: translateX(5px);
        }

        .sidebar-nav a:hover i {
            color: var(--primary);
        }

        /* ACTIVE mengikuti desain: bg D3E7FF, teks/icon 2365A2 */
        .sidebar-nav a.active {
            background: var(--secondary);
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(35, 101, 162, 0.20);
            font-weight: 600;
        }

        .sidebar-nav a.active i {
            color: var(--primary);
        }

        /* Logout button styling sesuai design (biru outline -> fill saat hover) */
        .logout-btn {
            position: absolute;
            bottom: 20px;
            left: 15px;
            right: 15px;
        }

        .logout-btn .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 15px;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid var(--primary);
            background: transparent;
            color: var(--primary);
        }

        .logout-btn .btn i {
            margin-right: 8px;
            font-size: 1rem;
        }

        .logout-btn .btn:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(35, 101, 162, 0.30);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }

        /* Header */
        .header {
            background: #fff;
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 999;
            height: 50px;
        }

        .page-title h1 {
            font-size: 1.0rem;
            font-weight: 600;
            margin: 0;
            color: #2c3e50;
        }

        .page-title p {
            color: var(--text-light);
            margin: 0;
            font-size: 0.78rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-icon,
        .user-profile {
            position: relative;
            cursor: pointer;
        }

        .notification-icon i {
            font-size: 1.1rem;
            color: #64748b;
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 0.65rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .user-profile {
            background-color: #000000;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Content */
        .content {
            padding: 20px;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
            background: #fff;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            padding: 12px 16px;
            border-radius: 10px 10px 0 0 !important;
            font-size: 0.88rem;
            color: #2c3e50;
        }

        .card-body {
            padding: 16px;
        }

        /* Stat Cards */
        .stat-card {
            text-align: center;
            padding: 15px 10px;
        }

        .stat-card h6 {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #2c3e50;
        }

        .stat-card i {
            font-size: 1.3rem;
            margin-top: 8px;
        }

        /* Activity List */
        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-info {
            flex: 1;
            font-size: 0.8rem;
        }

        .activity-info b {
            color: var(--primary);
            font-weight: 600;
        }

        .activity-badge {
            background: #3498db;
            /* dibiarkan seperti kode aslinu */
            color: white;
            padding: 3px 8px;
            border-radius: 16px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        /* Floor Status */
        .floor-status {
            margin-bottom: 16px;
        }

        .floor-status:last-child {
            margin-bottom: 0;
        }

        .floor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .floor-title {
            font-weight: 600;
            margin: 0;
            font-size: 0.83rem;
            color: #2c3e50;
        }

        .floor-badges {
            display: flex;
            gap: 8px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .badge-available {
            background-color: #e0f7ed;
            color: #27ae60;
        }

        .badge-occupied {
            background-color: #fde9e7;
            color: #e74c3c;
        }

        .badge-maintenance {
            background-color: #fef5e6;
            color: #f39c12;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }

            .sidebar .logo-container {
                padding: 15px 10px;
            }

            .logo {
                max-width: 40px;
            }

            .sidebar-nav a span {
                display: none;
            }

            .sidebar-nav a i {
                margin-right: 0;
                font-size: 1.1rem;
            }

            .logout-btn {
                padding: 10px;
            }

            .logout-btn .btn span {
                display: none;
            }

            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }
    </style>
</head>

<body>

    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo-container">
                <img src="/images/logo (2).png" alt="Hotel Logo" class="logo">
            </div>

            <div class="sidebar-nav">
                <!-- Urutan menu sesuai design -->
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>

                <a href="{{ route('facilities.index') }}" class="{{ request()->routeIs('facilities.*') ? 'active' : '' }}">
                    <i class="fas fa-cogs"></i> <span>Facility Room</span>
                </a>

                <a href="{{ route('room-types.index') }}" class="{{ request()->routeIs('room-types.*') ? 'active' : '' }}">
                    <i class="fas fa-door-open"></i> <span>Room Type</span>
                </a>

                <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                    <i class="fas fa-bed"></i> <span>Room</span>
                </a>

                <a href="{{ route('resepsionis.index') }}" class="{{ request()->routeIs('resepsionis.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i> <span>Receptionist</span>
                </a>

                <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> <span>Customer</span>
                </a>

                <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> <span>Report</span>
                </a>
            </div>

            <div class="logout-btn">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 btn-sm">
                        <i class="fas fa-sign-out-alt"></i> <span>Sign Out</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="page-title">
                    <h1>Hotel Management System</h1>
                </div>
                <div class="header-actions">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    <div class="user-profile">
                        <a href="{{ route('admin.profile') }}" style="color: inherit;">
                            <i class="fas fa-user"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>