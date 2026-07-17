<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'tour'])->latest()->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleHidden($id)
    {
        $review = Review::findOrFail($id);
        $review->is_hidden = ! $review->is_hidden;
        $review->save();

        $status = $review->is_hidden ? 'ẩn' : 'hiện';

        return back()->with('success', "Đã {$status} đánh giá thành công!");
    }
}
