<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show resepsionis dashboard
     */
    public function index()
    {
        // Get statistics
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $checkedInBookings = Booking::where('status', 'checked_in')->count();

        // Get recent data
        $pendingBookingsList = Booking::where('status', 'pending')
                                      ->with(['customer', 'roomType'])
                                      ->latest()
                                      ->limit(5)
                                      ->get();

        $confirmedBookingsList = Booking::where('status', 'confirmed')
                                       ->with(['customer', 'roomType'])
                                       ->latest()
                                       ->limit(5)
                                       ->get();

        $recentTransactions = Transaction::with(['booking.customer'])
                                        ->latest()
                                        ->limit(5)
                                        ->get();

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
