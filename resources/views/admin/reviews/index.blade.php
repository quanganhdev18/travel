@extends('layouts.admin')

@section('page-title', 'Quản lý Đánh giá')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Danh sách Đánh giá từ Khách hàng</h3>
    </div>
    
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th>Khách hàng</th>
                        <th>Tour / Điểm đến</th>
                        <th>Đánh giá</th>
                        <th>Nội dung</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($review->user && $review->user->avatar)
                                        <img src="{{ asset($review->user->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover" width="32" height="32">
                                    @else
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="fw-medium text-dark">{{ $review->user->name ?? 'Khách ẩn danh' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark text-truncate" style="max-width: 200px;" title="{{ $review->tour->title ?? '' }}">
                                    {{ $review->tour->title ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-warning small" style="white-space: nowrap;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;" title="{{ $review->comment }}">
                                    {{ $review->comment }}
                                </div>
                            </td>
                            <td class="text-muted small">
                                {{ $review->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-center">
                                @if($review->is_hidden)
                                    <span class="badge badge-soft-danger"><i class="bi bi-eye-slash me-1"></i>Đã ẩn</span>
                                @else
                                    <span class="badge badge-soft-success"><i class="bi bi-eye me-1"></i>Hiển thị</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.reviews.toggle-hidden', $review->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-action {{ $review->is_hidden ? 'text-success' : 'text-danger' }}" 
                                            title="{{ $review->is_hidden ? 'Hiện đánh giá' : 'Ẩn đánh giá' }}"
                                            onclick="return confirm('Bạn có chắc chắn muốn {{ $review->is_hidden ? 'hiện' : 'ẩn' }} đánh giá này?')">
                                        <i class="bi {{ $review->is_hidden ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <div class="mb-3"><i class="bi bi-chat-square-text fs-1 opacity-50"></i></div>
                                Chưa có đánh giá nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reviews->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
