<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Carbon\Carbon;

class TourController extends Controller
{
    public function show($slug)
    {
        $tour = Tour::with([
            'destination',
            'departure_location',
            'tour_images',
            'tour_schedules' => function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today())->orderBy('departure_date', 'asc');
            },
            'tour_itineraries.activities',
            'categories',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $groupedActivities = $tour->tour_itineraries
            ->flatMap->activities
            ->groupBy('activity_type');

        $categoryIds = $tour->categories->pluck('id')->toArray();

        $relatedTours = Tour::with(['destination', 'tour_images'])
            ->where('id', '!=', $tour->id)
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            })
            ->whereHas('activeSchedules', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today());
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
