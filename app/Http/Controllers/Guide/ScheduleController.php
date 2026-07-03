<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\TourSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
                $q->whereIn('payment_status', ['paid_30', 'paid_100']);
            }])
            ->orderByDesc('tour_schedule_id')
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

        $scheduleGuide = $tourGuide->schedule_guides()
            ->with(['tour_schedule.tour', 'tour_schedule.bookings.booking_passengers', 'tour_schedule.bookings.user'])
            ->where('tour_schedule_id', $id)
            ->firstOrFail();

        return view('guide.schedules.show', compact('scheduleGuide'));
    }

    /**
     * Update checkin location for a tour schedule (AJAX).
     */
    public function updateCheckinLocation(TourSchedule $schedule)
    {
        $tourGuide = auth()->user()->tour_guide;
        abort_unless($tourGuide, 403);

        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $schedule->id)
            ->exists();
        abort_unless($assigned, 403);

        request()->validate(['location' => 'nullable|string|max:500']);

        $schedule->update(['checkin_location' => request('location')]);

        return response()->json([
            'message' => request('location') ? 'Đã cập nhật địa điểm check-in.' : 'Đã xóa địa điểm check-in.',
            'location' => $schedule->checkin_location,
        ]);
    }

    /**
     * Toggle check-in status for a passenger (AJAX).
     */
    public function toggleCheckin(BookingPassenger $passenger)
    {
        $this->authorizePassenger($passenger);

        $passenger->update(['checked_in' => ! $passenger->checked_in]);

        return response()->json([
            'checked_in' => $passenger->checked_in,
            'message' => $passenger->checked_in ? 'Đã điểm danh' : 'Đã bỏ điểm danh',
        ]);
    }

    /**
     * Save special note for a passenger (AJAX).
     */
    public function updateNote(BookingPassenger $passenger)
    {
        $this->authorizePassenger($passenger);

        request()->validate(['note' => 'nullable|string|max:1000']);

        $passenger->update(['special_note' => request('note')]);

        return response()->json(['message' => 'Đã lưu ghi chú thành công.']);
    }

    /**
     * Ensure the authenticated guide is assigned to the tour schedule of this passenger.
     */
    private function authorizePassenger(BookingPassenger $passenger): void
    {
        $tourGuide = auth()->user()->tour_guide;

        abort_unless($tourGuide, 403);

        $tourScheduleId = $passenger->booking->tour_schedule_id;

        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $tourScheduleId)
            ->exists();

        abort_unless($assigned, 403);
    }

    /**
     * Hướng dẫn viên cập nhật trạng thái tour cho một booking.
     * Chỉ cho phép đổi khi tour đang ở in_progress hoặc checking_in.
     */
    public function updateBookingStatus(Request $request, Booking $booking): RedirectResponse
    {
        $tourGuide = auth()->user()->tour_guide;
        abort_unless($tourGuide, 403);

        // Đảm bảo guide được phân công lịch trình này
        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $booking->tour_schedule_id)
            ->exists();
        abort_unless($assigned, 403);

        // Chỉ cho phép đổi khi booking đang trong trạng thái guide kiểm soát
        $allowedCurrentStatuses = [Booking::TOUR_IN_PROGRESS, Booking::TOUR_CHECKING_IN];
        abort_unless(in_array($booking->tour_status, $allowedCurrentStatuses), 403);

        $request->validate([
            'tour_status' => 'required|in:in_progress,checking_in,completed',
            'current_checkin_step' => 'nullable|string|max:255',
        ]);

        $booking->tour_status = $request->tour_status;

        if ($request->tour_status === Booking::TOUR_CHECKING_IN) {
            $booking->current_checkin_step = $request->current_checkin_step;
        } else {
            $booking->current_checkin_step = null;
        }

        $booking->save();

        return back()->with('success', 'Đã cập nhật trạng thái tour thành công.');
    }
}
