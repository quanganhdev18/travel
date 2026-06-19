<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Tour;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with([
            'tour.destination',
            'tour.tour_images'
        ])
        ->where('user_id', Auth::id())
        ->latest()
        ->get();

        return view('frontend.favorites.index', compact('favorites'));
    }

    public function toggle(Tour $tour)
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('tour_id', $tour->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('success', 'Đã bỏ lưu tour.');
        }

        Favorite::create([
            'user_id' => Auth::id(),
            'tour_id' => $tour->id,
        ]);

        return back()->with('success', 'Đã lưu tour yêu thích.');
    }

    public function destroy(Tour $tour)
    {
        Favorite::where('user_id', Auth::id())
            ->where('tour_id', $tour->id)
            ->delete();

        return back()->with('success', 'Đã xóa tour khỏi danh sách đã lưu.');
    }
}
