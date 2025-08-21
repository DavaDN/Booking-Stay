<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('landing.index') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="100" class="d-inline-block align-text-top">
        </a>

        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Search</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Tentang Kami</a></li>

            @auth('customer_web')
            <!-- Kalau sudah login -->
            <li class="nav-item dropdown">
                <a class="btn btn-dark dropdown-toggle" href="#" id="akunDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Akun
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="akunDropdown">
                    <li class="dropdown-item-text">
                        <strong>{{ Auth::guard('customer_web')->user()->name }}</strong>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
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
            <li class="nav-item">
                <a class="btn btn-outline-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
            </li>
            <li class="nav-item">
                <a class="btn btn-dark" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
            </li>
            @endauth

        </ul>
    </div>
</nav>