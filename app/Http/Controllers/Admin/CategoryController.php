<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:255|unique:categories,name']);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) // Tự động tạo slug từ tên[cite: 7]
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|max:255|unique:categories,name,' . $category->id]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Category $category)
    {
        // Gỡ bỏ toàn bộ liên kết giữa danh mục này và các tour
        $category->tours()->detach();

        // Tiến hành xóa danh mục
        $category->delete();
        return back()->with('success', 'Đã xóa danh mục an toàn!');
    }
}
