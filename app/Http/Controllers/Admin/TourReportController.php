<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\GroupSplit;
use App\Models\TourReport;

class TourReportController extends Controller
{
    public function index()
    {
        $reports = TourReport::with(['tour_schedule.tour', 'tour_guide'])->latest()->paginate(15);

        return view('admin.tour_reports.index', compact('reports'));
    }

    public function show(TourReport $report)
    {
        $report->load(['tour_schedule.tour', 'tour_guide']);

        $passengerIds = $report->tour_schedule->bookings()
            ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
            ->whereNotIn('booking_status', ['cancelled'])
            ->get()
            ->flatMap(fn ($b) => $b->booking_passengers->pluck('id'))
            ->toArray();

        $freeTimePassengers = BookingPassenger::whereIn('id', $passengerIds)
            ->whereHas('group_splits', function ($q) {
                $q->where('status', '!=', GroupSplit::STATUS_CANCELLED);
            })
            ->with(['group_splits' => function ($q) {
                $q->where('status', '!=', GroupSplit::STATUS_CANCELLED)->orderBy('id', 'desc');
            }])
            ->get()
            ->map(function ($p) {
                $lastSplit = $p->group_splits->first();
                if ($lastSplit) {
                    $p->free_time_location = $p->free_time_location ?? ($lastSplit->split_location ?? $lastSplit->return_location);
                    $p->free_time_start = $p->free_time_start ?? $lastSplit->start_time;
                    $p->free_time_end = $p->free_time_end ?? $lastSplit->end_time;
                }

                return $p;
            });

        return view('admin.tour_reports.show', compact('report', 'freeTimePassengers'));
    }

    public function approve(TourReport $report)
    {
        if ($report->status === 'approved') {
            return redirect()->back()->with('info', 'Báo cáo này đã được duyệt trước đó.');
        }

        $report->update(['status' => 'approved']);

        // Cập nhật trạng thái schedule sang closed
        if ($report->tour_schedule) {
            $report->tour_schedule->update(['status' => 'closed']);

            // Cập nhật trạng thái của tất cả các booking không bị hủy sang closed
            $report->tour_schedule->bookings()
                ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
                ->update(['tour_status' => 'closed']);
        }

        return redirect()->route('admin.reports.index')->with('success', 'Đã duyệt báo cáo và quyết toán thành công. Tour đã được đóng.');
    }
}
