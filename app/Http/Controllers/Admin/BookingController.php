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

        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Thống kê nhanh cho Dashboard
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('booking_status', 'pending')->count(),
            'confirmed' => Booking::where('booking_status', 'confirmed')->count(),
            'revenue' => Booking::where('booking_status', 'paid')->sum('total_price'),
            'flight_ticket_needed' => Booking::where('transport_type', 'flight')
                ->whereNull('pnr_code')
                ->whereIn('booking_status', ['confirmed', 'paid']) // Chỉ đếm những đơn đã xác nhận/thanh toán
                ->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,paid,cancelled,completed'
        ]);

        $booking = Booking::findOrFail($id);
        $booking->booking_status = $request->status;
        $booking->save();

        return back()->with('success', 'cập nhật trạng thái đơn hàng thành công.');
    }
}
