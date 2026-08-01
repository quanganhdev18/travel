<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question.vi' => 'required|max:500',
            'question.en' => 'nullable|max:500',
            'question.zh' => 'nullable|max:500',
            'answer.vi' => 'required',
            'answer.en' => 'nullable',
            'answer.zh' => 'nullable',
            'category.vi' => 'nullable|max:100',
            'category.en' => 'nullable|max:100',
            'category.zh' => 'nullable|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        Faq::create([
            'question' => [
                'vi' => $request->question['vi'],
                'en' => $request->question['en'] ?? $request->question['vi'],
                'zh' => $request->question['zh'] ?? $request->question['vi'],
            ],
            'answer' => [
                'vi' => $request->answer['vi'],
                'en' => $request->answer['en'] ?? $request->answer['vi'],
                'zh' => $request->answer['zh'] ?? $request->answer['vi'],
            ],
            'category' => $request->filled('category.vi') ? [
                'vi' => $request->category['vi'],
                'en' => $request->category['en'] ?? $request->category['vi'],
                'zh' => $request->category['zh'] ?? $request->category['vi'],
            ] : null,
            'order' => $request->order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'Thêm câu hỏi thường gặp thành công!');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question.vi' => 'required|max:500',
            'question.en' => 'nullable|max:500',
            'question.zh' => 'nullable|max:500',
            'answer.vi' => 'required',
            'answer.en' => 'nullable',
            'answer.zh' => 'nullable',
            'category.vi' => 'nullable|max:100',
            'category.en' => 'nullable|max:100',
            'category.zh' => 'nullable|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $faq->update([
            'question' => [
                'vi' => $request->question['vi'],
                'en' => $request->question['en'] ?? $request->question['vi'],
                'zh' => $request->question['zh'] ?? $request->question['vi'],
            ],
            'answer' => [
                'vi' => $request->answer['vi'],
                'en' => $request->answer['en'] ?? $request->answer['vi'],
                'zh' => $request->answer['zh'] ?? $request->answer['vi'],
            ],
            'category' => $request->filled('category.vi') ? [
                'vi' => $request->category['vi'],
                'en' => $request->category['en'] ?? $request->category['vi'],
                'zh' => $request->category['zh'] ?? $request->category['vi'],
            ] : null,
            'order' => $request->order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', 'Đã xóa câu hỏi thành công!');
    }

    public function trash()
    {
        $faqs = Faq::onlyTrashed()
            ->orderBy('order')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('admin.faqs.trash', compact('faqs'));
    }

    public function restore($id)
    {
        $faq = Faq::onlyTrashed()->findOrFail($id);
        $faq->restore();

        return redirect()->route('admin.faqs.trash')->with('success', 'Đã khôi phục câu hỏi!');
    }

    public function forceDelete($id)
    {
        $faq = Faq::onlyTrashed()->findOrFail($id);
        $faq->forceDelete();

        return redirect()->route('admin.faqs.trash')->with('success', 'Đã xóa vĩnh viễn câu hỏi!');
    }
}
