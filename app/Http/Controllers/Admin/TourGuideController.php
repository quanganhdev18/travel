<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourGuide;
use App\Models\User;
use Illuminate\Http\Request;

class TourGuideController extends Controller
{
    public function index()
    {
        $tourGuides = TourGuide::with('user')->withCount('schedule_guides')->latest()->paginate(10);

        return view('admin.tour_guides.index', compact('tourGuides'));
    }

    public function create()
    {
        $users = User::where('role', 'guide')->doesntHave('tour_guide')->get();

        return view('admin.tour_guides.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|max:255',
            'phone' => ['required', 'unique:tour_guides,phone', 'regex:/^(03|05|08|09)[0-9]{8}$/'],
            'email' => 'nullable|email|max:255|unique:tour_guides,email',
            'bio' => 'nullable|string',
        ], [
            'phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 03, 05, 08 hoặc 09.',
            'phone.unique' => 'Số điện thoại này đã tồn tại, vui lòng nhập số khác.',
            'email.unique' => 'Email này đã tồn tại, vui lòng nhập email khác.',
        ]);

        TourGuide::create($request->all());

        return redirect()->route('admin.tour_guides.index')->with('success', 'Đã thêm Hướng dẫn viên mới!');
    }

    public function edit(TourGuide $tourGuide)
    {
        $users = User::where('role', 'guide')
            ->where(function ($query) use ($tourGuide) {
                $query->doesntHave('tour_guide')
                    ->orWhere('id', $tourGuide->user_id);
            })->get();

        return view('admin.tour_guides.edit', compact('tourGuide', 'users'));
    }

    public function update(Request $request, TourGuide $tourGuide)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|max:255',
            'phone' => ['required', 'unique:tour_guides,phone,'.$tourGuide->id, 'regex:/^(03|05|08|09)[0-9]{8}$/'],
            'email' => 'nullable|email|max:255|unique:tour_guides,email,'.$tourGuide->id,
            'bio' => 'nullable|string',
        ], [
            'phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 03, 05, 08 hoặc 09.',
            'phone.unique' => 'Số điện thoại này đã tồn tại, vui lòng nhập số khác.',
            'email.unique' => 'Email này đã tồn tại, vui lòng nhập email khác.',
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
