<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Hotel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Room statistics
        $totalRooms = Room::count();

        // Booking statistics
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $checkedInBookings = Booking::where('status', 'checked_in')->count();
        $checkedOutBookings = Booking::where('status', 'checked_out')->count();

        // Transaction statistics
        $totalTransactions = Transaction::count();
        $successTransactions = Transaction::where('status', 'success')->count();
        $totalRevenue = Transaction::where('status', 'success')->sum('total');

        // Customer statistics
        $totalCustomers = Customer::count();

        // Recent bookings
        $recentBookings = Booking::with(['customer', 'roomType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Room status by floor
        $roomsByFloor = Room::with('roomType')
            ->get()
            ->groupBy('floor')
            ->map(function ($rooms) {
                return [
                    'available' => $rooms->where('status', 'available')->count(),
                    'booked' => $rooms->where('status', 'booked')->count(),
                    'maintenance' => $rooms->where('status', 'maintenance')->count(),
                    'total' => $rooms->count(),
                ];
            });

        // Hotels with room count
        $hotels = Hotel::withCount('roomTypes')->get();

        return view('admin.dashboard', compact(
            'totalRooms',
            'totalBookings',
            'pendingBookings',
            'checkedInBookings',
            'checkedOutBookings',
            'totalTransactions',
            'successTransactions',
            'totalRevenue',
            'totalCustomers',
            'recentBookings',
            'roomsByFloor',
            'hotels'
        ));
    }
}
