<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user.identity', 'tour_schedule.tour', 'booking_passengers']);

        // Tìm kiếm & Lọc (Business Logic)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('pnr_code', 'like', "%$search%")
                    ->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%$search%")
                            ->orWhere('phone', 'like', "%$search%");
                    });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('tour_status')) {
            $query->where('tour_status', $request->tour_status);
        }

        if ($request->filled('status') && $request->status === 'needs_flight') {
            $query->where('transport_type', 'flight')
                ->whereNull('pnr_code')
                ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER]);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Thống kê nhanh cho Dashboard
        $stats = [
            'total' => Booking::count(),
            'pending_payment' => Booking::where('payment_status', Booking::PAYMENT_PENDING)->count(),
            'upcoming_tours' => Booking::where('tour_status', Booking::TOUR_UPCOMING)->count(),
            'revenue' => Booking::where('payment_status', Booking::PAYMENT_PAID_100)->sum('total_price'),
            'flight_ticket_needed' => Booking::where('transport_type', 'flight')
                ->whereNull('pnr_code')
                ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
                ->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid_30,paid_100,failed',
            'tour_status' => 'required|in:upcoming,in_progress,checking_in,completed,cancelled_by_customer,cancelled_by_admin',
            'current_checkin_step' => 'nullable|string|max:255',
        ]);

        $booking = Booking::with('tour_schedule')->findOrFail($id);

        $isCurrentlyCancelled = in_array($booking->tour_status, [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER]);
        $willBeCancelled = in_array($request->tour_status, [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER]);

        if ($willBeCancelled && ! $isCurrentlyCancelled) {
            $totalPersons = $booking->adults_count + $booking->children_count;
            if ($booking->tour_schedule) {
                $booking->tour_schedule->increment('available_seats', $totalPersons);
            }
        }

        if (! $willBeCancelled && $isCurrentlyCancelled) {
            $totalPersons = $booking->adults_count + $booking->children_count;
            if ($booking->tour_schedule) {
                $booking->tour_schedule->decrement('available_seats', $totalPersons);
            }
        }

        $booking->payment_status = $request->payment_status;
        $booking->tour_status = $request->tour_status;

        if ($request->tour_status === Booking::TOUR_CHECKING_IN) {
            $booking->current_checkin_step = $request->current_checkin_step;
        } else {
            $booking->current_checkin_step = null;
        }

        $booking->save();

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function updatePnr(Request $request, $id)
    {
        $request->validate([
            'pnr_code' => 'required|string|max:20',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->pnr_code = strtoupper($request->pnr_code);
        $booking->save();

        return back()->with('success', 'Cập nhật mã PNR thành công.');
    }
}
