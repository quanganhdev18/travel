<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        Booking::updateUpcomingTourStatuses();

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

    /**
     * Các trạng thái tour mà admin không được phép thay đổi.
     * Sau khi tour bắt đầu, quyền điều hành thuộc về Hướng dẫn viên.
     */
    private const GUIDE_CONTROLLED_STATUSES = [
        Booking::TOUR_IN_PROGRESS,
        Booking::TOUR_CHECKING_IN,
        Booking::TOUR_COMPLETED,
    ];

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid_30,paid_100,failed',
            'tour_status' => 'nullable|in:upcoming,in_progress,checking_in,completed,cancelled_by_customer,cancelled_by_admin',
            'current_checkin_step' => 'nullable|string|max:255',
        ]);

        $booking = Booking::with('tour_schedule')->findOrFail($id);

        // Kiểm tra xem admin có bị khóa quyền đổi tour_status không
        $isTourStatusLocked = in_array($booking->tour_status, self::GUIDE_CONTROLLED_STATUSES);

        if ($isTourStatusLocked && $request->filled('tour_status') && $request->tour_status !== $booking->tour_status) {
            return back()->with('error', 'Tour đang được điều hành bởi Hướng dẫn viên. Admin không thể thay đổi trạng thái tour lúc này.');
        }

        // Chỉ cập nhật tour_status nếu tour chưa bị khoá
        if (! $isTourStatusLocked && $request->filled('tour_status')) {
            $validStatuses = Booking::getValidNextStatuses($booking->tour_status);
            if (! in_array($request->tour_status, $validStatuses)) {
                return back()->with('error', 'Không thể chuyển đổi trạng thái tour từ trạng thái hiện tại sang trạng thái này (Không được nhảy cóc).');
            }

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

            $booking->tour_status = $request->tour_status;

            if ($request->tour_status === Booking::TOUR_CHECKING_IN) {
                $booking->current_checkin_step = $request->current_checkin_step;
            } else {
                $booking->current_checkin_step = null;
            }
        }

        $booking->payment_status = $request->payment_status;

        if ($booking->payment_status === Booking::PAYMENT_PAID_100) {
            $booking->paid_amount = $booking->total_price;
            if (in_array($booking->booking_status, ['pending', 'confirmed'])) {
                $booking->booking_status = 'paid';
            }
        } elseif ($booking->payment_status === Booking::PAYMENT_PAID_30) {
            $booking->paid_amount = $booking->total_price * 0.3;
            if ($booking->booking_status === 'pending') {
                $booking->booking_status = 'confirmed';
            }
        } elseif ($booking->payment_status === Booking::PAYMENT_FAILED || $booking->payment_status === Booking::PAYMENT_PENDING) {
            $booking->paid_amount = 0;
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

    public function liveStatuses(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));

        if (empty($ids)) {
            return response()->json([]);
        }

        $bookings = Booking::whereIn('id', $ids)->get(['id', 'payment_status', 'booking_status', 'tour_status', 'paid_amount', 'total_price', 'cancel_reason']);

        $data = [];
        foreach ($bookings as $b) {
            $data[$b->id] = [
                'id' => $b->id,
                'payment_status' => $b->payment_status,
                'booking_status' => $b->booking_status,
                'tour_status' => $b->tour_status,
                'paid_amount' => (float) $b->paid_amount,
                'total_price' => (float) $b->total_price,
                'cancel_reason' => $b->cancel_reason,
            ];
        }

        return response()->json($data);
    }
}
