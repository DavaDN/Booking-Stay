<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('landing.index') }}">
            <img src="{{ asset('images/LogoUKK.png') }}" alt="Logo" width="65" height="65" class="me-2">
            
        </a>

        <!-- Menu -->
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
            <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#">Search</a></li>
            <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#">Blog</a></li>
            <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#">Tentang Kami</a></li>

            @auth('customer_web')
            <!-- Kalau sudah login -->
            <li class="nav-item dropdown ms-3">
                <a class="btn btn-dark dropdown-toggle px-3" href="#" id="akunDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Akun
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="akunDropdown">
                    <li class="dropdown-item-text">
                        <strong>{{ Auth::guard('customer_web')->user()->name }}</strong>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </li>
            @else
            <!-- Kalau belum login -->
            <li class="nav-item ms-3">
                <a class="btn btn-outline-dark px-4" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
            </li>
            <li class="nav-item ms-2">
                <a class="btn btn-dark px-4" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
            </li>
            @endauth
        </ul>
    </div>
</nav>

<style>
    .navbar-nav .nav-link {
        margin: 0 8px;
        transition: color 0.2s ease;
    }
    .navbar-nav .nav-link:hover {
        color: #0d6efd; /* warna biru saat hover */
    }
    .navbar-brand img {
        object-fit: contain;
    }
</style>
