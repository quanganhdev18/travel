<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;
use App\Models\Tour;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', 1)
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        // Lọc các điểm đến có tồn tại trong cột destination_id của bảng tours
        $destinations = Destination::whereIn('id', function ($query) {
            $query->select('destination_id')->from('tours')->whereNull('deleted_at');
        })->take(6)->get();

        $categories = Category::all();

        // Tôi bổ sung thêm 'tour_images' vào with() để tối ưu hóa truy vấn (tránh lỗi N+1)
        // vì ngoài giao diện welcome.blade.php anh đang gọi đến ảnh của tour
        $tours = Tour::with(['destination', 'tour_images'])
            ->latest()
            ->take(8)
            ->get();

        $tickets = Ticket::with('destination')
            ->latest()
            ->take(4)
            ->get();

        return view('welcome', compact('banners', 'destinations', 'categories', 'tours', 'tickets'));
    }
}
