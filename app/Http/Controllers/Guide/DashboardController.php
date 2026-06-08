<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tourGuide = $user->tour_guide;

        $stats = [
            'total_schedules' => 0,
            'upcoming_schedules' => 0,
        ];

        if ($tourGuide) {
            $stats['total_schedules'] = $tourGuide->schedule_guides()->count();
            $stats['upcoming_schedules'] = $tourGuide->schedule_guides()
                ->whereHas('tour_schedule', function ($q) {
                    $q->where('departure_date', '>=', now());
                })->count();
        }

        return view('guide.dashboard', compact('tourGuide', 'stats'));
    }
}
