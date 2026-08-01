@extends('layouts.admin')

@section('page-title', 'Thùng rác - FAQ đã xóa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-3" style="width: 48px; height: 48px;">
            <i class="bi bi-trash-fill fs-4 text-danger"></i>
        </div>
        <h4 class="mb-0 fw-bold text-dark">Thùng rác FAQ</h4>
    </div>
    <div>
        <a href="{{ route('admin.faqs.index') }}" class="btn btn-light border shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Câu hỏi & Câu trả lời</th>
                        <th style="width: 150px;">Danh mục</th>
                        <th style="width: 180px;">Thời gian xóa</th>
                        <th class="text-end pe-4" style="width: 220px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                    <tr>
                        <td class="ps-4">
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
                        <td>
                            <div class="d-flex align-items-center text-danger">
                                <i class="bi bi-clock me-2"></i>
                                <span class="small">{{ $faq->deleted_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <form action="{{ route('admin.faqs.restore', $faq->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" style="min-width: 95px;" title="Khôi phục FAQ">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Khôi phục
                                    </button>
                                </form>

                                <form action="{{ route('admin.faqs.force-delete', $faq->id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('CẢNH BÁO: Hành động này không thể hoàn tác! Toàn bộ dữ liệu sẽ bị xóa vĩnh viễn. Bạn có chắc chắn không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" style="min-width: 95px;" title="Xóa vĩnh viễn">
                                        <i class="bi bi-trash me-1"></i>Xóa vĩnh viễn
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                <p class="mb-0">Thùng rác hiện đang trống.</p>
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
