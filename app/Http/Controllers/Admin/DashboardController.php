<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTours = Tour::count();
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('booking_status', 'confirmed')->sum('total_price');
        $totalUsers = User::count();

        $today = now()->startOfDay();

        $ongoingTours = TourSchedule::with(['tour', 'schedule_guides.tour_guide'])
            ->withCount(['bookings as total_guests' => function ($q) {
                $q->select(\DB::raw('SUM(adults_count + children_count)'));
            }])
            ->where('departure_date', '<=', $today)
            ->where('return_date', '>=', $today)
            ->orderBy('departure_date', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalTours',
            'totalBookings',
            'totalRevenue',
            'totalUsers',
            'ongoingTours'
        ));
    }
}
