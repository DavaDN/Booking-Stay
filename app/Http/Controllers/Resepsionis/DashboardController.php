<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show resepsionis dashboard
     */
    public function index()
    {
        // Scope by resepsionis hotel
        $user = Auth::guard('resepsionis')->user();
        $hotelId = $user->hotel_id ?? null;

        // Get statistics
        $pendingBookingsQuery = Booking::where('status', 'pending');
        $confirmedBookingsQuery = Booking::where('status', 'confirmed');
        $checkedInBookingsQuery = Booking::where('status', 'checked_in');

        if ($hotelId) {
            $pendingBookingsQuery->whereHas('roomType', fn($q) => $q->where('hotel_id', $hotelId));
            $confirmedBookingsQuery->whereHas('roomType', fn($q) => $q->where('hotel_id', $hotelId));
            $checkedInBookingsQuery->whereHas('roomType', fn($q) => $q->where('hotel_id', $hotelId));
        }

        $pendingBookings = $pendingBookingsQuery->count();
        $confirmedBookings = $confirmedBookingsQuery->count();
        $checkedInBookings = $checkedInBookingsQuery->count();

        // Get recent data
        $pendingBookingsListQuery = Booking::where('status', 'pending')->with(['customer', 'roomType'])->latest();
        $confirmedBookingsListQuery = Booking::where('status', 'confirmed')->with(['customer', 'roomType'])->latest();

        if ($hotelId) {
            $pendingBookingsListQuery->whereHas('roomType', fn($q) => $q->where('hotel_id', $hotelId));
            $confirmedBookingsListQuery->whereHas('roomType', fn($q) => $q->where('hotel_id', $hotelId));
        }

        $pendingBookingsList = $pendingBookingsListQuery->limit(5)->get();
        $confirmedBookingsList = $confirmedBookingsListQuery->limit(5)->get();

        $recentTransactionsQuery = Transaction::with(['booking.customer'])->latest();
        if ($hotelId) {
            $recentTransactionsQuery->whereHas('booking.roomType', fn($q) => $q->where('hotel_id', $hotelId));
        }
        $recentTransactions = $recentTransactionsQuery->limit(5)->get();

        return view('resepsionis.dashboard', compact(
            'pendingBookings',
            'confirmedBookings',
            'checkedInBookings',
            'pendingBookingsList',
            'confirmedBookingsList',
            'recentTransactions'
        ));
    }
}
