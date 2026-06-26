<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function myBookings()
    {
        $bookings = Booking::with([
            'tour_schedule.tour.tour_images',
            'tour_schedule.tour.primaryImage',
            'tour_schedule.tour.destination',
            'booking_passengers',
            'addons',
            'coupon',
            'payments',
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Phân loại bookings
        $activeBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_UPCOMING,
            Booking::TOUR_IN_PROGRESS,
            Booking::TOUR_CHECKING_IN,
        ]);

        $pastBookings = $bookings->whereIn('tour_status', [
            Booking::TOUR_COMPLETED,
            Booking::TOUR_CANCELLED_ADMIN,
            Booking::TOUR_CANCELLED_CUSTOMER,
        ]);

        return view('frontend.user.bookings', compact('bookings', 'activeBookings', 'pastBookings'));
    }
}
