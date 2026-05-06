<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class UserController extends Controller
{
    public function myBookings()
    {
        $bookings = Booking::with(['tour_schedule.tour'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.user.bookings', compact('bookings'));
    }
}
