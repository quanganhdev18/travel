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

        $destinations = Destination::whereIn('id', function ($query) {
            $query->select('destination_id')
                ->from('tours')
                ->whereNull('deleted_at');
        })
            ->take(6)
            ->get();

        $categories = Category::all();

        $tours = Tour::with(['destination', 'tour_images', 'departure_location'])
            ->whereNull('deleted_at')
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

    public function tours(\Illuminate\Http\Request $request)
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

        $query = Tour::with(['destination', 'tour_images'])->whereNull('deleted_at');

        if ($request->filled('keyword')) {
            $keyword = mb_strtolower($request->keyword, 'UTF-8');
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(CAST(title AS CHAR)) LIKE BINARY ?', ['%' . $keyword . '%'])
                    ->orWhereHas('destination', function ($q2) use ($keyword) {
                        $q2->whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%' . $keyword . '%']);
                    });
            });
        }

        if ($request->filled('date')) {
            $date = $request->date;
            $query->whereHas('activeSchedules', function ($q) use ($date) {
                $q->whereDate('departure_date', '>=', $date);
            });
        }

        if ($request->filled('budget')) {
            match ($request->budget) {
                'under_5m' => $query->where('base_price', '<', 5000000),
                '5m_to_10m' => $query->whereBetween('base_price', [5000000, 10000000]),
                '10m_to_20m' => $query->whereBetween('base_price', [10000000, 20000000]),
                'over_20m' => $query->where('base_price', '>', 20000000),
                default => null,
            };
        }

        $tours = $query->latest()->get();
        $allDestinations = Destination::orderBy('name')->get(['id', 'name']);

        return view('frontend.tours.index', compact('banners', 'tours', 'adBanners', 'allDestinations'));
    }

    public function searchTours(\Illuminate\Http\Request $request)
    {
        $banners = Banner::where('is_active', true)->where('position', 'top')->get();
        $destinations = Destination::all();
        $categories = Category::all();
        
        $query = Tour::with(['destination', 'tour_images']);
        
        if ($request->filled('keyword')) {
            $keyword = mb_strtolower($request->keyword, 'UTF-8');
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(CAST(title AS CHAR)) LIKE BINARY ?', ['%' . $keyword . '%'])
                  ->orWhereHas('destination', function ($q2) use ($keyword) {
                      $q2->whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%' . $keyword . '%']);
                  });
            });
        }
        
        if ($request->filled('date')) {
            $date = $request->date;
            $query->whereHas('activeSchedules', function ($q) use ($date) {
                $q->whereDate('departure_date', '>=', $date);
            });
        }
        
        if ($request->filled('departure_id')) {
            $query->where('departure_location_id', $request->departure_id);
        }
        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }
        if ($request->filled('budget')) {
            if ($request->budget == 'under_5m') {
                $query->where('base_price', '<', 5000000);
            } elseif ($request->budget == '5m_to_10m') {
                $query->whereBetween('base_price', [5000000, 10000000]);
            } elseif ($request->budget == '10m_to_20m') {
                $query->whereBetween('base_price', [10000000, 20000000]);
            } elseif ($request->budget == 'over_20m') {
                $query->where('base_price', '>', 20000000);
            }
        }
        
        if ($request->filled('sort')) {
            if ($request->sort == 'price_asc') {
                $query->orderBy('base_price', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('base_price', 'desc');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }
        
        $tours = $query->get();
        
        return view('frontend.tours.search', compact('tours', 'destinations', 'categories', 'banners'));
    }

    public function searchDestinations(\Illuminate\Http\Request $request)
    {
        $keyword = mb_strtolower($request->get('q', ''), 'UTF-8');

        if (strlen($keyword) < 1) {
            return response()->json([]);
        }

        $destinations = Destination::whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%' . $keyword . '%'])
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($destinations->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
        ]));
    }
}
