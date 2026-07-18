<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleGuide;
use App\Models\TourGuide;
use App\Models\TourSchedule;
use Illuminate\Http\Request;

class OngoingTourController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all'); // 'all', 'upcoming', 'ongoing'
        $today = now()->startOfDay();

        $query = TourSchedule::with(['tour', 'schedule_guides.tour_guide'])
            ->withCount(['bookings as total_guests' => function ($q) {
                $q->select(\DB::raw('SUM(adults_count + children_count)'));
            }]);

        if ($status === 'upcoming') {
            $query->where('departure_date', '>', $today);
        } elseif ($status === 'ongoing') {
            $query->where('departure_date', '<=', $today)
                ->where('return_date', '>=', $today);
        }

        $schedules = $query->orderBy('departure_date', 'asc')->paginate(15);
        $tourGuides = TourGuide::all(); // Cho modal gán hướng dẫn viên

        // Xác định các HDV bị trùng lịch cho mỗi schedule
        foreach ($schedules as $schedule) {
            $schedule->busy_guide_ids = ScheduleGuide::where('tour_schedule_id', '!=', $schedule->id)
                ->whereHas('tour_schedule', function ($q) use ($schedule) {
                    $q->where('departure_date', '<=', $schedule->return_date)
                        ->where('return_date', '>=', $schedule->departure_date);
                })
                ->pluck('guide_id')
                ->toArray();
        }

        $unassignedUpcomingSchedules = TourSchedule::with('tour')
            ->where('departure_date', '>=', $today)
            ->where('departure_date', '<=', $today->copy()->addDays(7))
            ->doesntHave('schedule_guides')
            ->orderBy('departure_date', 'asc')
            ->get();

        foreach ($unassignedUpcomingSchedules as $schedule) {
            $schedule->busy_guide_ids = ScheduleGuide::where('tour_schedule_id', '!=', $schedule->id)
                ->whereHas('tour_schedule', function ($q) use ($schedule) {
                    $q->where('departure_date', '<=', $schedule->return_date)
                        ->where('return_date', '>=', $schedule->departure_date);
                })
                ->pluck('guide_id')
                ->toArray();
        }

        return view('admin.ongoing_tours.index', compact('schedules', 'tourGuides', 'status', 'unassignedUpcomingSchedules'));
    }

    public function assignGuides(Request $request, TourSchedule $schedule)
    {
        $request->validate([
            'primary_guide_id' => 'nullable|exists:tour_guides,id',
            'backup_guide_id' => 'nullable|exists:tour_guides,id|different:primary_guide_id',
        ], [
            'backup_guide_id.different' => 'HDV dự bị không được trùng với HDV chính.',
        ]);

        $primaryId = $request->filled('primary_guide_id') ? $request->primary_guide_id : null;
        $backupId = $request->filled('backup_guide_id') ? $request->backup_guide_id : null;

        // Kiểm tra trùng lịch
        foreach (array_filter([$primaryId, $backupId]) as $guideId) {
            $isBusy = ScheduleGuide::where('tour_schedule_id', '!=', $schedule->id)
                ->where('guide_id', $guideId)
                ->whereHas('tour_schedule', function ($q) use ($schedule) {
                    $q->where('departure_date', '<=', $schedule->return_date)
                        ->where('return_date', '>=', $schedule->departure_date);
                })
                ->exists();

            if ($isBusy) {
                $guideName = TourGuide::find($guideId)?->name ?? 'Hướng dẫn viên';

                return redirect()->back()->with('error', "Hướng dẫn viên {$guideName} đã được phân công cho một lịch trình khác trùng thời gian này.");
            }
        }

        // Xóa phân công cũ và lưu mới
        $schedule->schedule_guides()->delete();

        if ($primaryId) {
            $schedule->schedule_guides()->create([
                'guide_id' => $primaryId,
                'is_backup' => false,
            ]);
        }

        if ($backupId) {
            $schedule->schedule_guides()->create([
                'guide_id' => $backupId,
                'is_backup' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Đã cập nhật danh sách Hướng dẫn viên cho Lịch trình!');
    }
}
