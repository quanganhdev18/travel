<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourReport;
use Illuminate\Http\Request;

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
        return view('admin.tour_reports.show', compact('report'));
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
                ->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])
                ->update(['tour_status' => 'closed']);
        }

        return redirect()->route('admin.reports.index')->with('success', 'Đã duyệt báo cáo và quyết toán thành công. Tour đã được đóng.');
    }
}
