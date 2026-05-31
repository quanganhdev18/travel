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
}
