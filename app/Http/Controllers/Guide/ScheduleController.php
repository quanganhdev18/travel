<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tourGuide = $user->tour_guide;

        if (! $tourGuide) {
            return redirect()->route('guide.dashboard')->with('error', 'Tài khoản chưa được liên kết với hồ sơ Hướng dẫn viên.');
        }

        $schedules = $tourGuide->schedule_guides()
            ->with(['tour_schedule.tour', 'tour_schedule.bookings' => function ($q) {
                $q->where('status', 'paid');
            }])
            ->latest()
            ->paginate(15);

        return view('guide.schedules.index', compact('schedules'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $tourGuide = $user->tour_guide;

        if (! $tourGuide) {
            return redirect()->route('guide.dashboard')->with('error', 'Tài khoản chưa được liên kết với hồ sơ Hướng dẫn viên.');
        }

        $scheduleGuide = $tourGuide->schedule_guides()->with(['tour_schedule.tour', 'tour_schedule.bookings.passengers', 'tour_schedule.bookings.user'])->findOrFail($id);

        return view('guide.schedules.show', compact('scheduleGuide'));
    }
}
