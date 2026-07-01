<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', 1)
            ->where(function ($q) {
                $q->where('position', 'hero')
                    ->orWhereNull('position');
            })
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $adBanners = Banner::where('is_active', 1)
            ->where('position', 'home_ads')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $destinations = Destination::withCount('tours')
            ->whereIn('id', function ($query) {
                $query->select('destination_id')
                    ->from('tours')
                    ->whereNull('deleted_at');
            })
            ->get();

        $categories = Category::all();

        $tours = Tour::with(['destination', 'tour_images', 'departure_location'])
            ->whereNull('deleted_at')
            ->whereHas('activeSchedules', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today());
            })
            ->latest()
            ->take(8)
            ->get();

        $tickets = Ticket::with('destination')
            ->latest()
            ->take(4)
            ->get();

        $allDestinations = Destination::all();

        return view('welcome', compact(
            'banners',
            'adBanners',
            'destinations',
            'categories',
            'tours',
            'tickets',
            'allDestinations'
        ));
    }

    public function tours(\App\Http\Requests\TourFilterRequest $request)
    {
        $banners = Banner::where('is_active', 1)
            ->where(function ($q) {
                $q->where('position', 'hero')->orWhereNull('position');
            })
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $adBanners = Banner::where('is_active', 1)
            ->where('position', 'home_ads')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $allDestinations = Destination::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $filterErrors = session('filter_errors', []);

        $departureDate = (!isset($filterErrors['departure_date']) && $request->filled('departure_date')) 
            ? $request->departure_date 
            : Carbon::today()->toDateString();

        $query = Tour::with(['destination', 'tour_images'])
            ->whereNull('deleted_at')
            ->withMin(['tour_schedules as next_departure' => function ($q) use ($departureDate) {
                $q->where('departure_date', '>=', $departureDate)
                  ->where('available_seats', '>', 0)
                  ->where('status', 'available');
            }], 'departure_date')
            ->withMin(['tour_schedules as seats_left' => function ($q) use ($departureDate) {
                $q->where('departure_date', '>=', $departureDate)
                  ->where('available_seats', '>', 0)
                  ->where('status', 'available');
            }], 'available_seats')
            ->withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews as review_count');

        // Bắt buộc có lịch khởi hành phù hợp
        $query->whereHas('tour_schedules', function ($q) use ($departureDate) {
            $q->where('departure_date', '>=', $departureDate)
              ->where('available_seats', '>', 0)
              ->where('status', 'available');
        });

        // Lọc Điểm đến
        if (!isset($filterErrors['destination_id']) && $request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        // Lọc Danh mục
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Lọc Ngân sách
        if (!isset($filterErrors['budget']) && $request->filled('budget')) {
            match ($request->budget) {
                'under_5m' => $query->where('base_price', '<', 5000000),
                '5m_10m' => $query->whereBetween('base_price', [5000000, 10000000]),
                '10m_20m' => $query->whereBetween('base_price', [10000000, 20000000]),
                'over_20m' => $query->where('base_price', '>', 20000000),
                default => null,
            };
        }

        // Vẫn giữ lại lọc keyword
        if ($request->filled('keyword')) {
            $keyword = mb_strtolower($request->keyword, 'UTF-8');
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(CAST(title AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%'])
                    ->orWhereHas('destination', function ($q2) use ($keyword) {
                        $q2->whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%']);
                    });
            });
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('base_price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('base_price', 'desc');
            } else {
                $query->orderBy('next_departure', 'asc');
            }
        } else {
            $query->orderBy('next_departure', 'asc');
        }

        $tours = $query->paginate(9);

        return view('frontend.tours.index', compact('banners', 'tours', 'adBanners', 'allDestinations', 'filterErrors', 'categories'));
    }

    public function searchTours(Request $request)
    {
        $banners = Banner::where('is_active', true)->where('position', 'top')->get();
        $destinations = Destination::orderBy('name')->get();
        $categories = Category::all();

        $query = Tour::with(['destination', 'departure_location', 'tour_images'])
            ->whereNull('deleted_at')
            ->whereHas('activeSchedules', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today());
            });

        // Keyword: tìm theo tên tour hoặc điểm đến
        if ($request->filled('keyword')) {
            $keyword = mb_strtolower($request->keyword, 'UTF-8');
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(CAST(title AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%'])
                    ->orWhereHas('destination', function ($q2) use ($keyword) {
                        $q2->whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%']);
                    });
            });
        }

        // Phương tiện: xe hoặc bay (lọc theo ai_tags hoặc title nếu chưa có cột riêng)
        if ($request->filled('transport')) {
            $transport = $request->transport;
            if (Schema::hasColumn('tours', 'transport_type')) {
                $query->where('transport_type', $transport);
            } else {
                // Fallback: tìm theo từ khóa trong title/ai_tags
                if ($transport === 'xe') {
                    $query->where(function ($q) {
                        $q->whereRaw("LOWER(CAST(title AS CHAR)) LIKE '%xe%'")
                            ->orWhereRaw("LOWER(CAST(ai_tags AS CHAR)) LIKE '%xe%'");
                    });
                } elseif ($transport === 'bay') {
                    $query->where(function ($q) {
                        $q->whereRaw("LOWER(CAST(title AS CHAR)) LIKE '%bay%'")
                            ->orWhereRaw("LOWER(CAST(ai_tags AS CHAR)) LIKE '%bay%'")
                            ->orWhereRaw("LOWER(CAST(title AS CHAR)) LIKE '%máy bay%'");
                    });
                }
            }
        }

        // Điểm khởi hành
        if ($request->filled('departure_id')) {
            $query->where('departure_location_id', $request->departure_id);
        }

        // Điểm đến
        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        // Ngày khởi hành
        if ($request->filled('date')) {
            $date = $request->date;
            $query->whereHas('activeSchedules', function ($q) use ($date) {
                $q->whereDate('departure_date', '>=', max($date, Carbon::today()->toDateString()));
            });
        }



        // Ngân sách
        if ($request->filled('budget')) {
            match ($request->budget) {
                'under_5m' => $query->where('base_price', '<', 5000000),
                '5m_to_10m' => $query->whereBetween('base_price', [5000000, 10000000]),
                '10m_to_20m' => $query->whereBetween('base_price', [10000000, 20000000]),
                'over_20m' => $query->where('base_price', '>', 20000000),
                default => null,
            };
        }

        // Sắp xếp
        if ($request->sort === 'price_asc') {
            $query->orderBy('base_price', 'asc');
        } elseif ($request->sort === 'price_desc') {
            $query->orderBy('base_price', 'desc');
        } else {
            $query->latest();
        }

        $tours = $query->get();

        return view('frontend.tours.search', compact('tours', 'destinations', 'categories', 'banners'));
    }

    public function searchDestinations(Request $request)
    {
        $keyword = mb_strtolower($request->get('q', ''), 'UTF-8');

        if (strlen($keyword) < 1) {
            return response()->json([]);
        }

        $destinations = Destination::whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%'])
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($destinations->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
        ]));
    }
}
