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

        return view('welcome', compact(
            'banners',
            'adBanners',
            'destinations',
            'categories',
            'tours',
            'tickets'
        ));
    }

    public function tours()
    {
        $banners = Banner::where('is_active', 1)
            ->where(function($q) {
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

        $tours = Tour::with(['destination', 'tour_images'])
            ->latest()
            ->take(12)->get();

        return view('frontend.tours.index', compact('banners', 'tours', 'adBanners'));
    }

    public function searchTours(\Illuminate\Http\Request $request)
    {
        $banners = Banner::where('is_active', true)->where('position', 'top')->get();
        $destinations = Destination::all();
        $categories = Category::all();
        
        $query = Tour::with(['destination', 'tour_images']);
        
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
}
