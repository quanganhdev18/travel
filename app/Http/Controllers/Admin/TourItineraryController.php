<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourItinerary;
use Illuminate\Http\Request;

class TourItineraryController extends Controller
{
    public function index(Tour $tour)
    {
        $itineraries = $tour->tour_itineraries()->with('activities')->orderBy('day_number')->get();
        return view('admin.itineraries.index', compact('tour', 'itineraries'));
    }

    public function store(Request $request, Tour $tour)
    {
        $request->validate([
            'day_number' => 'required|integer|min:1|max:' . $tour->duration_days,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ], [
            'day_number.max' => 'Số ngày không được vượt quá tổng số ngày của tour (' . $tour->duration_days . ' ngày).'
        ]);

        $tour->tour_itineraries()->create($request->all());
        return back()->with('success', 'Thêm ngày lịch trình thành công!');
    }

    public function destroy(TourItinerary $itinerary)
    {
        $itinerary->delete();
        return back()->with('success', 'Đã xóa ngày lịch trình!');
    }
}
