@extends('layouts.admin')

@section('page-title', 'Quản lý Câu hỏi Thường gặp (FAQ)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-3" style="width: 48px; height: 48px;">
            <i class="bi bi-question-circle-fill fs-4 text-primary"></i>
        </div>
        <h4 class="mb-0 fw-bold text-dark">Danh sách FAQ</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.faqs.trash') }}" class="btn btn-danger text-white shadow-sm">
            <i class="bi bi-trash me-2"></i>Thùng rác
        </a>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Thêm FAQ
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 80px;">Thứ tự</th>
                        <th>Câu hỏi & Câu trả lời</th>
                        <th style="width: 150px;">Danh mục</th>
                        <th class="text-center" style="width: 120px;">Trạng thái</th>
                        <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                    <tr>
                        <td class="ps-4">
                            <span class="badge badge-soft-secondary">{{ $faq->order }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1">{{ Str::limit($faq->question, 100) }}</div>
                            <div class="text-muted small">{{ Str::limit(strip_tags($faq->answer), 120) }}</div>
                        </td>
                        <td>
                            @if($faq->category)
                                <span class="badge badge-soft-primary">{{ $faq->category }}</span>
                            @else
                                <span class="text-muted small">Chưa phân loại</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($faq->is_active)
                                <span class="badge badge-soft-success">Hoạt động</span>
                            @else
                                <span class="badge badge-soft-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.faqs.edit', $faq->id) }}"
                                    class="btn-action text-primary" 
                                    title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action text-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                <p class="mb-0">Chưa có câu hỏi nào. Hãy thêm FAQ mới!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($faqs->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $faqs->links() }}
</div>
@endif
@endsection
