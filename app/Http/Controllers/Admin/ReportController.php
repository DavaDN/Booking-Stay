<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::createFromFormat('Y-m-d', $request->query('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->query('end_date') ? Carbon::createFromFormat('Y-m-d', $request->query('end_date')) : Carbon::now();

        // Booking statistics
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->where('status', 'checked_out')->count();
        $pendingBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count();

        // Transaction statistics
        $totalTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
        $successfulTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->where('status', 'success')->count();
        $failedTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->where('status', 'failed')->count();

        // Revenue
        $totalRevenue = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'success')
            ->sum('total');

        // Room utilization
        $rooms = Room::with('roomType')->get();
        $totalRooms = $rooms->count();
        $bookedRooms = $rooms->where('status', 'booked')->count();
        $occupancyRate = $totalRooms > 0 ? round(($bookedRooms / $totalRooms) * 100, 2) : 0;

        // Top hotels by bookings
        $topHotels = Hotel::withCount(['roomTypes' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('bookings', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }])->orderBy('room_types_count', 'desc')->limit(5)->get();

        // Customer statistics
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::whereHas('bookings', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        // Detailed transactions
        $transactions = Transaction::with('booking')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.report.index', compact(
            'totalBookings',
            'completedBookings',
            'pendingBookings',
            'totalTransactions',
            'successfulTransactions',
            'failedTransactions',
            'totalRevenue',
            'totalRooms',
            'bookedRooms',
            'occupancyRate',
            'topHotels',
            'totalCustomers',
            'activeCustomers',
            'transactions',
            'startDate',
            'endDate'
        ));
    }
}
