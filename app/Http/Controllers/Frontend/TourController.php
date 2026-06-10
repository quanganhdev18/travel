<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tour;

class TourController extends Controller
{
    public function show($slug)
    {
        $tour = Tour::with([
            'destination',
            'departure_location',
            'tour_images',
            'tour_schedules' => function ($q) {
                $q->whereDate('departure_date', '>=', \Carbon\Carbon::today())->orderBy('departure_date', 'asc');
            },
            'tour_itineraries.activities',
            'categories',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $groupedActivities = $tour->tour_itineraries
            ->flatMap->activities
            ->groupBy('activity_type');

        $relatedTours = Tour::with(['destination', 'tour_images'])
            ->where('id', '!=', $tour->id)
            ->where('destination_id', $tour->destination_id)
            ->whereHas('activeSchedules', function ($q) {
                $q->whereDate('departure_date', '>=', \Carbon\Carbon::today());
            })
            ->take(4)
            ->get();

        return view('frontend.tours.show', compact(
            'tour',
            'groupedActivities',
            'relatedTours'
        ));
    }
}
