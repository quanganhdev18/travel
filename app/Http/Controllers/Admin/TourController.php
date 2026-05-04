<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Category;
use App\Models\TourSchedule;
use App\Models\Tour;
use App\Models\TourImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TourController extends Controller
{
    public function index()
    {
        // Lấy tour kèm theo destination và ảnh chính để hiển thị bảng
        $tours = Tour::with(['destination', 'tour_images' => function ($query) {
            $query->where('is_primary', 1);
        }])->latest()->paginate(10);

        return view('admin.tours.index', compact('tours'));
    }
    public function create()
    {
        $destinations = Destination::all();
        $categories = Category::all();

        return view('admin.tours.create', compact('destinations', 'categories'));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'title' => 'required|max:255',
            'base_price' => 'required|numeric',
            'destination_id' => 'required|exists:destinations,id',
            'duration_days' => 'required|integer',
            'duration_nights' => 'required|integer',
        ]);

        // 2. Tạo Tour và tự động sinh slug
        $tour = new Tour();
        $tour->title = $request->title;
        $tour->slug = Str::slug($request->title) . '-' . time(); // Đảm bảo slug là duy nhất
        $tour->description = $request->description;
        $tour->base_price = $request->base_price;
        $tour->destination_id = $request->destination_id;
        $tour->duration_days = $request->duration_days;
        $tour->duration_nights = $request->duration_nights;
        $tour->save();

        if ($request->hasFile('primary_image')) {
            $path = $request->file('primary_image')->store('tours', 'public');

            $tour->images()->create([
                'image_url' => '/storage/' . $path,
                'is_primary' => 1 // Đánh dấu đây là ảnh chính[cite: 8]
            ]);
        }

        // 3. Lưu danh mục (nhiều-nhiều) vào bảng tour_categories
        if ($request->has('categories')) {
            $tour->categories()->sync($request->categories);
        }

        // Trong TourController@store, sửa dòng return thành:
        return redirect()->route('admin.tours.schedules', $tour->id)->with('success', 'Đã tạo tour, vui lòng thêm lịch trình!');
    }


    // Hiển thị danh sách lịch trình của một tour
    public function schedules($id)
    {
        $tour = Tour::with('tour_schedules')->findOrFail($id);
        return view('admin.tours.schedules', compact('tour'));
    }

    // Lưu lịch trình mới
    public function storeSchedule(Request $request, $id)
    {
        $request->validate([
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'capacity' => 'required|integer|min:1',
        ]);

        TourSchedule::create([
            'tour_id' => $id,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'capacity' => $request->capacity,
            'available_seats' => $request->capacity, // Mặc định ban đầu chỗ trống bằng tổng chỗ
            'status' => 'available',
        ]);

        return back()->with('success', 'Đã thêm lịch trình mới thành công!');
    }
    public function images($id)
    {
        // Lưu ý: tour_images là tên hàm quan hệ anh đã xác nhận ở Model Tour.php
        $tour = Tour::with('tour_images')->findOrFail($id);

        return view('admin.tours.images', compact('tour'));
    }

    public function storeImages(Request $request, $id)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Chỉ chấp nhận file ảnh
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // Lưu file vào thư mục storage/app/public/tours
                $path = $file->store('tours', 'public');

                TourImage::create([
                    'tour_id' => $id,
                    'image_url' => '/storage/' . $path, // Lưu đường dẫn để hiển thị ra web[cite: 8]
                    'is_primary' => 0
                ]);
            }
        }

        return back()->with('success', 'Đã tải ảnh lên thành công!');
    }
}
