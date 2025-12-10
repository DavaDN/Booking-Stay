@extends('layouts.sb-resepsionis')

@section('title', 'Create Reservation')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Create Reservation (Resepsionis)</h4>
        <a href="{{ route('resepsionis.reservations.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            Create Booking (Walk-in)
        </div>
        <div class="card-body">
            <form action="{{ route('resepsionis.reservations.pay') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Full name" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer Email (optional)</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="email@example.com">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Room Type</label>
                        <select id="walk_room_type" name="room_type_id" class="form-select" required>
                            <option value="">-- Select Room Type --</option>
                            @foreach($roomTypes as $rt)
                                <option value="{{ $rt->id }}" data-price="{{ $rt->price }}">{{ $rt->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Room Number</label>
                        <select id="walk_room_id" name="room_id" class="form-select" required>
                            <option value="">-- Select Room --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" data-room-type="{{ $room->room_type_id }}">Room {{ $room->number }} - {{ $room->roomType->name ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Check-in</label>
                        <input type="date" name="check_in_date" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Check-out</label>
                        <input type="date" name="check_out_date" class="form-control" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Price per day (IDR)</label>
                        <input type="text" id="walk_price_per_day" class="form-control" readonly placeholder="Auto from room type">
                        <input type="hidden" name="price_per_day" id="walk_price_per_day_hidden">
                        <input type="hidden" name="check_in_time" value="00:00:00">
                        <input type="hidden" name="check_out_time" value="00:00:00">
                    </div>
                </div>

                <button class="btn btn-success">Create & Pay</button>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // When room type changes, fetch rooms for that type (optionally filter by dates)
    (function() {
        var typeSelect = document.getElementById('walk_room_type');
        var roomSelect = document.getElementById('walk_room_id');
        var priceInput = document.getElementById('walk_price_per_day');
        var priceHidden = document.getElementById('walk_price_per_day_hidden');

        function setPriceForType() {
            var selectedTypeOption = typeSelect.options[typeSelect.selectedIndex];
            var price = selectedTypeOption ? selectedTypeOption.getAttribute('data-price') : null;
            if (price) {
                priceInput.value = parseFloat(price).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});
                priceHidden.value = price;
            } else {
                priceInput.value = '';
                priceHidden.value = '';
            }
        }

        async function fetchRoomsForType() {
            var type = typeSelect.value;
            // reset room select to placeholder
            roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
            if (!type) return;

            // include dates if provided to filter availability
            var checkIn = document.querySelector('input[name="check_in_date"]').value || null;
            var checkOut = document.querySelector('input[name="check_out_date"]').value || null;

            var url = new URL("{{ route('resepsionis.reservations.rooms_by_type') }}", window.location.origin);
            url.searchParams.append('room_type_id', type);
            if (checkIn) url.searchParams.append('check_in_date', checkIn);
            if (checkOut) url.searchParams.append('check_out_date', checkOut);

            try {
                var resp = await fetch(url.toString(), { credentials: 'same-origin' });
                if (!resp.ok) throw new Error('Failed fetching rooms');
                var rooms = await resp.json();
                rooms.forEach(function(r) {
                    var opt = document.createElement('option');
                    opt.value = r.id;
                    opt.textContent = 'Room ' + r.number + ' - ' + (r.room_type_name || '-');
                    opt.setAttribute('data-room-type', r.room_type_id);
                    roomSelect.appendChild(opt);
                });
            } catch (e) {
                console.warn('Could not load rooms for selected type', e);
            }
        }

        typeSelect.addEventListener('change', function() {
            setPriceForType();
            fetchRoomsForType();
        });

        // Also refetch rooms when dates change to reflect availability
        var checkInInput = document.querySelector('input[name="check_in_date"]');
        var checkOutInput = document.querySelector('input[name="check_out_date"]');
        [checkInInput, checkOutInput].forEach(function(el){
            if (el) el.addEventListener('change', function(){
                // only fetch if a room type is already selected
                if (typeSelect.value) fetchRoomsForType();
            });
        });

        // initialize price if a type is preselected
        setPriceForType();
    })();
</script>
@endsection
