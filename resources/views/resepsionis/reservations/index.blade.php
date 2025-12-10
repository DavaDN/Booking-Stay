@extends('layouts.sb-resepsionis')

@section('title', 'Reservations')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Reservation Management</h4>
        <span class="badge bg-primary">
            Total: {{ $reservations->count() }}
        </span>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form Add Reservation --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            Add New Reservation
        </div>

        <div class="card-body">
            <form action="{{ route('reservations.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->name }} ({{ $c->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Room</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">-- Select Room --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" data-room-type="{{ $room->room_type_id }}">
                                    Room {{ $room->number }} - {{ $room->roomType->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="row g-3 mb-3">

                    <div class="col-md-3">
                        <label class="form-label">Check-in Date</label>
                        <input type="date" name="check_in_date" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Check-in Time</label>
                        <input type="time" name="check_in_time" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Check-out Date</label>
                        <input type="date" name="check_out_date" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Check-out Time</label>
                        <input type="time" name="check_out_time" class="form-control" required>
                    </div>

                </div>

                <button class="btn btn-primary px-4">
                    Save Reservation
                </button>
            </form>
        </div>
    </div>

    {{-- Walk-in Booking by Resepsionis (creates Booking + Transaction and opens Midtrans) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            Create Booking (Walk-in)
        </div>
        <div class="card-body">
            <form action="{{ route('resepsionis.bookings.create') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Customer (existing)</label>
                        <select name="customer_id" class="form-select">
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Or New Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Full name">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">New Customer Email</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="email@example.com">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Room Type</label>
                        <select id="walk_room_type" name="room_type_id" class="form-select" required>
                            <option value="">-- Select Room Type --</option>
                            @foreach($rooms as $room)
                                @if($room->roomType)
                                    <option value="{{ $room->roomType->id }}">{{ $room->roomType->name }}</option>
                                @endif
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
                        <input type="date" name="check_in" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Check-out</label>
                        <input type="date" name="check_out" class="form-control" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Price per day (IDR)</label>
                        <input type="number" name="price_per_day" class="form-control" required>
                    </div>
                </div>

                <button class="btn btn-success">Create & Pay (Midtrans)</button>
            </form>
        </div>
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
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $row->customer->name }}</td>
                            <td>{{ $row->customer->email }}</td>
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
                                <span class="badge 
                                    @if($row->status == 'booked') bg-info
                                    @elseif($row->status == 'check_in') bg-success
                                    @elseif($row->status == 'check_out') bg-dark
                                    @else bg-danger
                                    @endif
                                ">
                                    {{ strtoupper($row->status) }}
                                </span>
                            </td>
                            <td class="text-center">

                                {{-- Button Edit --}}
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit{{ $row->id }}">
                                    Edit
                                </button>

                                {{-- Delete --}}
                                <form action="{{ route('reservations.destroy', $row->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this reservation?')">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="edit{{ $row->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <form action="{{ route('reservations.update', $row->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Reservation</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-2">
                                                <label>Check-in Date</label>
                                                <input type="date" name="check_in_date" class="form-control"
                                                    value="{{ $row->check_in_date }}">
                                            </div>

                                            <div class="mb-2">
                                                <label>Check-in Time</label>
                                                <input type="time" name="check_in_time" class="form-control"
                                                    value="{{ $row->check_in_time }}">
                                            </div>

                                            <div class="mb-2">
                                                <label>Check-out Date</label>
                                                <input type="date" name="check_out_date" class="form-control"
                                                    value="{{ $row->check_out_date }}">
                                            </div>

                                            <div class="mb-2">
                                                <label>Check-out Time</label>
                                                <input type="time" name="check_out_time" class="form-control"
                                                    value="{{ $row->check_out_time }}">
                                            </div>

                                            <div class="mb-2">
                                                <label>Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="booked" {{ $row->status == 'booked' ? 'selected' : '' }}>Booked</option>
                                                    <option value="check_in" {{ $row->status == 'check_in' ? 'selected' : '' }}>Check In</option>
                                                    <option value="check_out" {{ $row->status == 'check_out' ? 'selected' : '' }}>Check Out</option>
                                                    <option value="cancelled" {{ $row->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary">Update</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

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
    </div>

</div>
@endsection

@section('scripts')
<script>
    // filter room options when selecting room type in walk-in booking form
    document.getElementById('walk_room_type').addEventListener('change', function() {
        var type = this.value;
        var roomSelect = document.getElementById('walk_room_id');
        Array.from(roomSelect.options).forEach(function(opt){
            if (!opt.value) return; // keep placeholder
            var rt = opt.getAttribute('data-room-type');
            opt.style.display = rt == type ? '' : 'none';
        });
        roomSelect.value = '';
    });
</script>
@endsection
