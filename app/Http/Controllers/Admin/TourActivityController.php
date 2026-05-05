<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourItinerary;
use App\Models\TourActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourActivityController extends Controller
{
    public function store(Request $request, TourItinerary $itinerary)
    {
        $request->validate([
            'activity_type' => 'required|string',
            'start_time' => 'nullable',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $data = $request->except('image_upload');

        if ($request->hasFile('image_upload')) {
            $path = $request->file('image_upload')->store('activities', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $itinerary->activities()->create($data);
        return back()->with('success', 'Thêm hoạt động thành công!');
    }

    public function destroy(TourActivity $activity)
    {
        if ($activity->image_url && str_starts_with($activity->image_url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $activity->image_url));
        }

        $activity->delete();
        return back()->with('success', 'Đã xóa hoạt động!');
    }
}
