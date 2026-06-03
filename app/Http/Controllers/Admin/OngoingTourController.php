<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.ongoing_tours.index', compact('schedules', 'tourGuides', 'status'));
    }

    public function assignGuides(Request $request, TourSchedule $schedule)
    {
        $request->validate([
            'guide_ids' => 'nullable|array',
            'guide_ids.*' => 'exists:tour_guides,id',
        ]);

        // Cập nhật bảng schedule_guides (sync sẽ tự động xóa các liên kết cũ và tạo mới)
        // Lưu ý: bảng schedule_guides không có created_at/updated_at và incrementing id,
        // nếu dùng Eloquent belongsToMany, có thể gọi sync.
        // Tuy nhiên, Model TourSchedule không định nghĩa belongsToMany('App\Models\TourGuide', 'schedule_guides').
        // Hiện tại Model có hasMany(ScheduleGuide::class). Mình sẽ xóa cái cũ và insert cái mới.

        $schedule->schedule_guides()->delete();

        if ($request->has('guide_ids') && is_array($request->guide_ids)) {
            foreach ($request->guide_ids as $guideId) {
                $schedule->schedule_guides()->create([
                    'guide_id' => $guideId,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Đã cập nhật danh sách Hướng dẫn viên cho Lịch trình!');
    }
}
