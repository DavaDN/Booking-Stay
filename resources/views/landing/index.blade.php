@extends('layouts.app')

@push('styles')
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
    }

    .hero {
        position: relative;
        height: 85vh;
        overflow: hidden;
    }

    .hero img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        top: 0;
        left: 0;
        z-index: -1;
    }

    .search-box {
        position: absolute;
        bottom: -40px;
        left: 50%;
        transform: translateX(-50%);
        background: #fff;
        border-radius: 12px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);
        display: flex;
        overflow: hidden;
        flex-wrap: wrap;
    }

    .search-box .item {
        padding: 15px 25px;
        border-right: 1px solid #ddd;
        flex: 1;
    }

    .search-box .item:last-child {
        border-right: none;
        background: #002f6c;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .search-box i {
        margin-right: 8px;
        color: #002f6c;
    }

    .search-box .item:last-child i {
        color: #fff;
        font-size: 22px;
    }

    .promo img {
        max-height: 120px;
        object-fit: cover;
        border-radius: 8px;
    }

    .features i {
        font-size: 30px;
        margin-bottom: 10px;
        color: #002f6c;
    }

    .about {
        background: #002f6c;
        color: white;
        border-radius: 50px 50px 0 0;
        padding: 40px;
        margin-top: 50px;
    }

    .features .shadow {
        transition: transform .2s ease-in-out;
    }

    .features .shadow:hover {
        transform: translateY(-5px);
    }

    .carousel-item img {
        height: 530px;         /* atur tinggi */
        object-fit: cover;     /* biar gambar tidak gepeng */
    }

    .search-box {
    display: flex;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    background: #fff;
    width: max-content;
    font-size: 14px; /* perkecil teks */
}

.search-box .item {
    padding: 10px 25px; /* lebih kecil */
    border-right: 1px solid #ddd;
    min-width: 140px; /* perkecil lebar minimal */
}

.search-box .item:last-child {
    border-right: none;
}

/* Tombol Search */
.search-box .search-btn {
    background: #0a2a5e;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px; /* icon lebih kecil */
    cursor: pointer;
    position: relative;
    padding: 0 25px; /* lebih kecil */
}

/* Efek melengkung */
.search-box .search-btn::before {
    content: "";
    position: absolute;
    left: -20px;
    top: 0;
    bottom: 0;
    width: 40px;
    background: #0a2a5e;
    clip-path: ellipse(100% 100% at 0% 50%);
}

</style>
@endpush

@section('content')
{{-- Hero Section --}}
<div id="hotelCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{ asset('images/OIP.jpeg') }}" class="d-block w-100" alt="Hotel 1">
        </div>
        <div class="carousel-item">
            <img src="{{ asset('images/OIP.jpeg') }}" class="d-block w-100" alt="Hotel 2">
        </div>
        <div class="carousel-item">
            <img src="{{ asset('images/OIP.jpeg') }}" class="d-block w-100" alt="Hotel 3">
        </div>
    </div>
</div>

<!-- tombol prev -->
<button class="carousel-control-prev" type="button" data-bs-target="#hotelCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
</button>

<!-- tombol next -->
<button class="carousel-control-next" type="button" data-bs-target="#hotelCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
</button>
</div>

<div class="search-box">
    <div class="item">
        <i class="fa-solid fa-hotel"></i>
        <small class="d-block text-muted">Jenis Kamar</small>
        <span class="fw-bold">STANDAR</span>
    </div>
    <div class="item">
        <i class="fa-solid fa-calendar-days"></i>
        <small class="d-block text-muted">Check-In</small>
        <span class="fw-bold">5 Agustus 2025</span>
    </div>
    <div class="item">
        <i class="fa-solid fa-calendar-check"></i>
        <small class="d-block text-muted">Check-Out</small>
        <span class="fw-bold">7 Agustus 2025</span>
    </div>
    <div class="item">
        <i class="fa-solid fa-user"></i>
        <small class="d-block text-muted">Per-orang</small>
        <span class="fw-bold">2 Dewasa, 1 Anak kecil</span>
    </div>
    <div class="item search-btn">
        <i class="fa-solid fa-magnifying-glass"></i>
    </div>
</div>


{{-- Promo Section --}}
<div class="container text-center mt-5 pt-100">
    <h2 class="text-primary fs-4 fw-semibold">Ingin menginap di kamar hotel terbaik?</h2>
    <p class="text-primary fs-5 fw-semibold">Kami punya yang kamu cari!</p>

    <div class="row promo mt-4">
        <div class="col-md-4">
            <img src="{{ asset('images/promo1.png') }}" class="img-fluid">
        </div>
        <div class="col-md-4">
            <img src="{{ asset('images/promo2.png') }}" class="img-fluid">
        </div>
        <div class="col-md-4">
            <img src="{{ asset('images/promo3.png') }}" class="img-fluid">
        </div>
    </div>
</div>

{{-- Room Types Section --}}
<div class="container mt-5">
    <h3 class="mb-4 text-center">Rekomendasi Kamar untukmu</h3>
    <div class="row">
        @foreach($roomTypes as $type)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <img src="https://via.placeholder.com/400x250?text={{ $type->name }}" class="card-img-top" alt="{{ $type->name }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $type->name }}</h5>
                    <p class="card-text">{{ $type->description ?? 'Tidak ada deskripsi.' }}</p>
                    <p class="text-muted small">{{ $type->rooms->count() }} kamar tersedia</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="text-center mt-3">
        <a href="#" class="btn btn-outline-primary">Lihat Semua Hotel</a>
    </div>
</div>

{{-- Features Section (2x2 Grid) --}}
<div class="container mt-5 text-center features">
    <h3 class="mb-4">Kenapa pilih kami?</h3>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="p-4 shadow rounded h-100">
                <i class="fa-solid fa-tags fa-2x mb-3 text-primary"></i>
                <h5 class="fw-bold">Harga Terjangkau</h5>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 shadow rounded h-100">
                <i class="fa-solid fa-building fa-2x mb-3 text-primary"></i>
                <h5 class="fw-bold">Fasilitas Lengkap</h5>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 shadow rounded h-100">
                <i class="fa-solid fa-headset fa-2x mb-3 text-primary"></i>
                <h5 class="fw-bold">Pelayanan Lengkap</h5>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 shadow rounded h-100">
                <i class="fa-solid fa-location-dot fa-2x mb-3 text-primary"></i>
                <h5 class="fw-bold">Lokasi Strategis</h5>
            </div>
        </div>
    </div>
</div>

{{-- About Section --}}
<div class="about text-center">
    <h3 class="fw-bold">Tentang Kami</h3>
    <p>
        BookingStay adalah platform pemesanan hotel yang dirancang untuk memberikan pengalaman menginap terbaik bagi setiap tamu.
        Kami menyediakan berbagai pilihan tipe kamar, fasilitas lengkap, serta promo menarik setiap bulannya.
        Dengan jaringan yang luas dan layanan 24 jam, BookingStay memastikan perjalananmu jadi lebih nyaman, mudah, dan menyenangkan.
    </p>
    <small class="d-block mt-3">Booking mudah, menginap tanpa ragu.</small>
</div>

{{-- Login Modal --}}
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Login Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('customer.login') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Register Modal --}}
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('customer.register') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>No HP</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection