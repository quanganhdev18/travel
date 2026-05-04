<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Banner;
use App\Models\Destination;
use App\Models\Category;
use App\Models\Ticket;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', 1)
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $destinations = Destination::take(6)->get();

        $categories = Category::all();

        $tours = Tour::with('destination')
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
