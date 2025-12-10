@extends('layouts.sb-resepsionis')

@section('title', 'Reservations')

@section('content')
    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Reservation Management</h4>
            <div class="d-flex align-items-center">
                <form class="me-3" method="GET" action="{{ route('resepsionis.reservations.index') }}">
                    <div class="input-group">
                        <input type="search" name="q" class="form-control form-control-sm"
                            placeholder="Search customer, email, room, room type" value="{{ request('q') }}">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">Search</button>
                    </div>
                </form>

                <span class="badge bg-primary me-2">
                    Total: {{ $reservations->total() ?? $reservations->count() }}
                </span>

                <form method="GET" class="d-flex" style="width:140px">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" {{ request()->get('per_page', 10) == 10 ? 'selected' : '' }}>10 / page</option>
                        <option value="15" {{ request()->get('per_page', 10) == 15 ? 'selected' : '' }}>15 / page</option>
                        <option value="25" {{ request()->get('per_page', 10) == 25 ? 'selected' : '' }}>25 / page</option>
                        <option value="50" {{ request()->get('per_page', 10) == 50 ? 'selected' : '' }}>50 / page</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('resepsionis.reservations.create') }}" class="btn btn-primary">Create Reservation</a>
            </div>

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white fw-semibold">
                Reservation List
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="50">No</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Room</th>
                            <th>Room Type</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $row)
                            <tr>
                                <td class="text-center">{{ $reservations->firstItem() + $loop->index }}</td>
                                <td>{{ $row->customer->name ?? $row->customer_name }}</td>
                                <td>{{ $row->customer->email ?? $row->customer_email }}</td>
                                <td class="text-center">Room {{ $row->room->number }}</td>
                                <td>{{ $row->room->roomType->name ?? '-' }}</td>
                                <td class="text-center">
                                    {{ $row->check_in_date }} <br>
                                    <small class="text-muted">{{ $row->check_in_time }}</small>
                                </td>
                                <td class="text-center">
                                    {{ $row->check_out_date }} <br>
                                    <small class="text-muted">{{ $row->check_out_time }}</small>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge 
                                    @if ($row->status == 'paid') bg-info
                                    @elseif($row->status == 'pending') bg-warning
                                    @elseif($row->status == 'check_in') bg-success
                                    @elseif($row->status == 'check_out') bg-dark
                                    @else bg-danger @endif
                                ">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                                <td class="text-center">

                                    {{-- If reservation is paid -> allow Check In --}}
                                    @if ($row->status === 'paid')
                                        <form action="{{ route('resepsionis.reservations.check_in', $row->id) }}"
                                            method="POST" class="d-inline ms-1"
                                            onsubmit="return confirm('Yakin ingin melakukan check-in untuk reservasi ini?')">
                                            @csrf
                                            <button class="btn btn-sm btn-primary">Check In</button>
                                        </form>

                                        {{-- If reservation currently checked-in -> allow Check Out --}}
                                    @elseif($row->status === 'check_in')
                                        <form action="{{ route('resepsionis.reservations.check_out', $row->id) }}"
                                            method="POST" class="d-inline ms-1"
                                            onsubmit="return confirm('Yakin ingin melakukan check-out untuk reservasi ini?')">
                                            @csrf
                                            <button class="btn btn-sm btn-warning">Check Out</button>
                                        </form>

                                        {{-- If not yet paid -> allow Pay --}}
                                    @elseif(in_array($row->status, ['pending']))
                                        <form
                                            action="{{ route('resepsionis.reservations.payExisting', $row->id ?? $row->id) }}"
                                            method="POST" class="d-inline ms-1">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Pay</button>
                                        </form>
                                    @endif

                                </td>
                            </tr>



                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    No reservations found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-end">
                    <!-- DEBUG: paginator_class={{ is_object($reservations) ? get_class($reservations) : gettype($reservations) }}; total={{ method_exists($reservations,'total') ? $reservations->total() : 'n/a' }}; per_page={{ request()->get('per_page', 10) }} -->
                    {{ $reservations->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        // Only attach walk-in form script if the create form is present on the page
        var walkRoomTypeEl = document.getElementById('walk_room_type');
        if (walkRoomTypeEl) {
            walkRoomTypeEl.addEventListener('change', function() {
                var type = this.value;
                var roomSelect = document.getElementById('walk_room_id');
                Array.from(roomSelect.options).forEach(function(opt) {
                    if (!opt.value) return; // keep placeholder
                    var rt = opt.getAttribute('data-room-type');
                    var hide = (type && rt != type);
                    opt.hidden = hide;
                    opt.disabled = hide;
                });
                // reset selection to placeholder
                roomSelect.value = '';
                // set price field from the selected room type option
                var selectedTypeOption = this.options[this.selectedIndex];
                var price = selectedTypeOption ? selectedTypeOption.getAttribute('data-price') : null;
                if (price) {
                    var priceEl = document.getElementById('walk_price_per_day');
                    var priceHidden = document.getElementById('walk_price_per_day_hidden');
                    if (priceEl) priceEl.value = parseFloat(price).toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                    if (priceHidden) priceHidden.value = price;
                } else {
                    var priceEl = document.getElementById('walk_price_per_day');
                    var priceHidden = document.getElementById('walk_price_per_day_hidden');
                    if (priceEl) priceEl.value = '';
                    if (priceHidden) priceHidden.value = '';
                }
            });
        }
    </script>
@endsection
