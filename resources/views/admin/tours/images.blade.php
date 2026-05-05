@extends('layouts.admin')
@section('page-title', 'Thư viện ảnh: ' . $tour->title)

@section('content')
<!-- Thêm Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.tours.index') }}" class="text-decoration-none fw-semibold">
                <i class="bi bi-box-seam me-1"></i>Quản lý Tour
            </a>
        </li>
        <li class="breadcrumb-item text-muted">{{ \Illuminate\Support\Str::limit($tour->title, 40) }}</li>
        <li class="breadcrumb-item active fw-bold" aria-current="page">Thư viện ảnh</li>
    </ol>
</nav>
<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Thêm ảnh mới (Nhập URL)</div>
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success small">
                    {{ session('success') }}
                </div>
                @endif
                <form action="{{ route('admin.tours.images.store', $tour->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn hình ảnh từ máy tính</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*" required>
                        <div class="form-text">Bạn có thể chọn nhiều ảnh cùng một lúc.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-upload me-2"></i> Tải ảnh lên
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Thư viện hiện tại</div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($tour->tour_images as $img)
                    <div class="col-md-6">
                        <div class="position-relative border rounded p-2">
                            <img src="{{ $img->image_url }}" class="img-fluid rounded"
                                style="height: 150px; width: 100%; object-fit: cover;">
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                @if($img->is_primary)
                                <span class="badge bg-success"><i class="bi bi-star-fill"></i> Ảnh chính</span>
                                @else
                                <form action="{{ route('admin.tours.images.set-primary', [$tour->id, $img->id]) }}"
                                    method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-light border">Đặt làm ảnh chính</button>
                                </form>
                                @endif
                                <form action="{{ route('admin.tours.images.destroy', [$tour->id, $img->id]) }}"
                                    method="POST"
                                    onsubmit="return confirm('Anh có chắc chắn muốn xóa ảnh này khỏi thư viện? Hành động này sẽ xóa luôn cả file ảnh gốc.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-white shadow-sm"
                                        title="Xóa ảnh">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function addInput() {
        const div = document.createElement('div');
        div.className = 'mb-3';
        div.innerHTML =
            '<input type="text" name="image_urls[]" class="form-control" placeholder="Dán link ảnh tiếp theo...">';
        document.getElementById('url-inputs').appendChild(div);
    }
</script>
@endsection