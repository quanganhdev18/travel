<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\TourSchedule;
use App\Models\TourReport;
use Illuminate\Http\Request;

class TourReportController extends Controller
{
    public function create(TourSchedule $schedule)
    {
        // Kiểm tra quyền (có phải HDV của tour này không)
        $isAssigned = $schedule->schedule_guides()->where('guide_id', auth()->user()->tour_guide->id ?? 0)->exists();
        if (!$isAssigned) abort(403);

        $firstBooking = $schedule->bookings->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])->first();
        $groupStatus = $firstBooking ? $firstBooking->tour_status : 'upcoming';

        if ($schedule->status !== 'completed' && $groupStatus !== 'completed') {
            return redirect()->back()->with('error', 'Chỉ có thể viết báo cáo khi Tour đã kết thúc.');
        }

        $report = TourReport::where('tour_schedule_id', $schedule->id)->first();
        if ($report) {
            return redirect()->back()->with('info', 'Bạn đã nộp báo cáo cho Tour này.');
        }

        return view('guide.reports.create', compact('schedule'));
    }

    public function store(Request $request, TourSchedule $schedule)
    {
        $isAssigned = $schedule->schedule_guides()->where('guide_id', auth()->user()->tour_guide->id ?? 0)->exists();
        if (!$isAssigned) abort(403);

        $request->validate([
            'actual_guests' => 'required|integer|min:0',
            'incident_notes' => 'nullable|string',
            'advance_amount' => 'required|numeric|min:0',
            'actual_expense' => 'required|numeric|min:0',
        ]);

        $balance = $request->advance_amount - $request->actual_expense;

        TourReport::create([
            'tour_schedule_id' => $schedule->id,
            'guide_id' => auth()->user()->tour_guide->id,
            'actual_guests' => $request->actual_guests,
            'incident_notes' => $request->incident_notes,
            'advance_amount' => $request->advance_amount,
            'actual_expense' => $request->actual_expense,
            'balance' => $balance,
            'status' => 'pending'
        ]);

        return redirect()->route('guide.schedules.show', $schedule->id)->with('success', 'Đã nộp báo cáo và quyết toán thành công. Chờ Kế toán duyệt.');
    }
}
