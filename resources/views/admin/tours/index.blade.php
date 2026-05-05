@extends('layouts.admin')

@section('page-title', 'Danh sách Tour Du Lịch')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark">Quản lý Tour</h4>
    <div>
        <a href="{{ route('admin.tours.trash') }}" class="btn btn-outline-danger me-2">
            <i class="bi bi-trash"></i> Thùng rác
        </a>
        <a href="{{ route('admin.tours.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Thêm Tour mới
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted fw-600" style="width: 80px;">Ảnh</th>
                        <th class="py-3 text-muted fw-600">Thông tin Tour</th>
                        <th class="py-3 text-muted fw-600">Điểm đến</th>
                        <th class="py-3 text-muted fw-600">Giá cơ bản</th>
                        <th class="py-3 text-muted fw-600 text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tours as $tour)
                    <tr>
                        <td class="ps-4">
                            @php $primaryImage = $tour->tour_images->first(); @endphp
                            <img src="{{ $primaryImage ? $primaryImage->image_url : 'https://via.placeholder.com/60' }}"
                                class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $tour->title }}</div>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>{{ $tour->duration_days }}N{{ $tour->duration_nights }}Đ
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $tour->destination->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span
                                class="fw-bold text-primary">{{ number_format($tour->base_price, 0, ',', '.') }}đ</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <!-- đổi tên title để phân biệt rõ với chương trình chi tiết -->
                                <a href="{{ route('admin.tours.schedules', $tour->id) }}"
                                    class="btn btn-sm btn-white border" title="Ngày khởi hành">
                                    <i class="bi bi-calendar3"></i>
                                </a>

                                <!-- nút mới thêm để quản lý chương trình (itineraries) -->
                                <a href="{{ route('admin.tours.itineraries.index', $tour->id) }}"
                                    class="btn btn-sm btn-white border text-success"
                                    title="Chương trình tour (Itinerary)">
                                    <i class="bi bi-list-task"></i>
                                </a>

                                <a href="{{ route('admin.tours.images', $tour->id) }}"
                                    class="btn btn-sm btn-white border" title="Thư viện ảnh">
                                    <i class="bi bi-images"></i>
                                </a>
                                <a href="{{ route('admin.tours.edit', $tour->id) }}"
                                    class="btn btn-sm btn-white border text-primary" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.tours.destroy', $tour->id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc muốn chuyển tour này vào thùng rác?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white border text-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Chưa có dữ liệu tour nào được tạo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $tours->links() }}
</div>
@endsection