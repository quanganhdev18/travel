<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTours = Tour::count();
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('booking_status', 'confirmed')->sum('total_price');
        $totalUsers = User::count();

        $recentBookings = Booking::with(['tour_schedule.tour', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalTours',
            'totalBookings',
            'totalRevenue',
            'totalUsers',
            'recentBookings'
        ));
    }
}
