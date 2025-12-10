<h3>New Paid Booking</h3>
<p><strong>Booking code:</strong> {{ $booking->booking_code }}</p>
<p><strong>Customer:</strong> {{ $booking->customer->name ?? $booking->customer_name }}</p>
<p><strong>Check-In:</strong> {{ optional($booking->check_in)->format('d/m/Y') ?? ($booking->check_in ?? '-') }}</p>
<p><strong>Check-Out:</strong> {{ optional($booking->check_out)->format('d/m/Y') ?? ($booking->check_out ?? '-') }}</p>
<p><strong>Room Type:</strong> {{ $booking->roomType->name ?? 'N/A' }}</p>
<p><strong>Number of rooms:</strong> {{ $booking->number_of_rooms }}</p>
@if(!empty($rooms) && count($rooms) > 0)
    <p><strong>Rooms assigned:</strong></p>
    <ul>
        @foreach($rooms as $r)
            <li>Room {{ $r->number }}</li>
        @endforeach
    </ul>
@endif
<p><strong>Total:</strong> Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</p>
<p>Please prepare the rooms and welcome the guest at check-in.</p>
