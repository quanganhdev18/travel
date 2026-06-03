<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourGuide;
use Illuminate\Http\Request;

class TourGuideController extends Controller
{
    public function index()
    {
        $tourGuides = TourGuide::withCount('schedule_guides')->latest()->paginate(10);

        return view('admin.tour_guides.index', compact('tourGuides'));
    }

    public function create()
    {
        return view('admin.tour_guides.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'phone' => 'required|max:20',
            'email' => 'nullable|email|max:255',
            'bio' => 'nullable|string',
        ]);

        TourGuide::create($request->all());

        return redirect()->route('admin.tour_guides.index')->with('success', 'Đã thêm Hướng dẫn viên mới!');
    }

    public function edit(TourGuide $tourGuide)
    {
        return view('admin.tour_guides.edit', compact('tourGuide'));
    }

    public function update(Request $request, TourGuide $tourGuide)
    {
        $request->validate([
            'name' => 'required|max:255',
            'phone' => 'required|max:20',
            'email' => 'nullable|email|max:255',
            'bio' => 'nullable|string',
        ]);

        $tourGuide->update($request->all());

        return redirect()->route('admin.tour_guides.index')->with('success', 'Cập nhật Hướng dẫn viên thành công!');
    }

    public function destroy(TourGuide $tourGuide)
    {
        $tourGuide->delete();

        return redirect()->route('admin.tour_guides.index')->with('success', 'Đã xóa Hướng dẫn viên!');
    }
}
