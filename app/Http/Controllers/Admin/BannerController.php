<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_url' => 'nullable|string',
            'target_url' => 'nullable|string',
            'position' => 'nullable|string|in:hero,home_ads',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $data = $request->except(['image']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        if (!$request->filled('sort_order')) {
            $data['sort_order'] = Banner::max('sort_order') + 1;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banners'), $imageName);
            $data['image_url'] = 'uploads/banners/' . $imageName;
        } elseif (!$request->filled('image_url')) {
            return back()->with('error', 'Vui lòng tải ảnh lên hoặc nhập URL ảnh')->withInput();
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm Banner thành công!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_url' => 'nullable|string',
            'target_url' => 'nullable|string',
            'position' => 'nullable|string|in:hero,home_ads',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $data = $request->except(['image']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            // Delete old image if it's a local file
            if ($banner->image_url && file_exists(public_path($banner->image_url))) {
                unlink(public_path($banner->image_url));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banners'), $imageName);
            $data['image_url'] = 'uploads/banners/' . $imageName;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật Banner thành công!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_url && file_exists(public_path($banner->image_url))) {
            unlink(public_path($banner->image_url));
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Đã xóa Banner!');
    }
}
