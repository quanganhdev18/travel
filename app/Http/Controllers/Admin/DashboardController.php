<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $rangeInput = $request->input('range');
        $validRanges = ['today', '7days', 'this_month', 'last_month', 'this_quarter', 'this_year', 'custom'];
        $range = in_array($rangeInput, $validRanges) ? $rangeInput : 'this_month';
        
        $customErrors = [];
        $now = now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        if ($range === 'custom') {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'from' => 'required|date_format:Y-m-d',
                'to' => 'required|date_format:Y-m-d|after_or_equal:from|before_or_equal:today',
            ], [
                'from.required' => 'Vui lòng chọn từ ngày.',
                'from.date_format' => 'Định dạng ngày không hợp lệ.',
                'to.required' => 'Vui lòng chọn đến ngày.',
                'to.date_format' => 'Định dạng ngày không hợp lệ.',
                'to.after_or_equal' => 'Đến ngày phải lớn hơn hoặc bằng từ ngày.',
                'to.before_or_equal' => 'Đến ngày không được ở tương lai.',
            ]);

            $validator->after(function ($validator) use ($request) {
                if ($request->filled('from') && $request->filled('to')) {
                    try {
                        $from = \Carbon\Carbon::parse($request->from);
                        $to = \Carbon\Carbon::parse($request->to);
                        if ($from->diffInDays($to) > 366) {
                            $validator->errors()->add('to', 'Khoảng cách tối đa 366 ngày.');
                        }
                    } catch (\Exception $e) {
                        // date_format will catch it
                    }
                }
            });

            if ($validator->fails()) {
                $customErrors = $validator->errors()->toArray();
                // Fallback safe dates so query doesn't break
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
            } else {
                $startDate = \Carbon\Carbon::parse($request->from)->startOfDay();
                $endDate = \Carbon\Carbon::parse($request->to)->endOfDay();
            }
        } else {
            switch ($range) {
                case 'today':
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case '7days':
                    $startDate = $now->copy()->subDays(6)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'last_month':
                    $startDate = $now->copy()->subMonth()->startOfMonth();
                    $endDate = $now->copy()->subMonth()->endOfMonth();
                    break;
                case 'this_quarter':
                    $startDate = $now->copy()->startOfQuarter();
                    $endDate = $now->copy()->endOfQuarter();
                    break;
                case 'this_year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfYear();
                    break;
                case 'this_month':
                default:
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    break;
            }
        }
        $totalTours = Tour::count();
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = Booking::where('payment_status', Booking::PAYMENT_PAID_100)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');
        $totalUsers = User::count();

        $today = now()->startOfDay();

        $ongoingTours = TourSchedule::with(['tour', 'schedule_guides.tour_guide'])
            ->withCount(['bookings as total_guests' => function ($q) {
                $q->select(\DB::raw('SUM(adults_count + children_count)'));
            }])
            ->where('departure_date', '<=', $today)
            ->where('return_date', '>=', $today)
            ->orderBy('departure_date', 'asc')
            ->take(5)
            ->get();

        $bookingStatusData = Booking::select('tour_status', \DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('tour_status')
            ->pluck('total', 'tour_status')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalTours',
            'totalBookings',
            'totalRevenue',
            'totalUsers',
            'ongoingTours',
            'range',
            'startDate',
            'endDate',
            'bookingStatusData',
            'customErrors'
        ));
    }
}
