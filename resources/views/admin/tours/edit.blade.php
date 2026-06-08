@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Tour Du Lịch - ' . $tour->title)

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
        <li class="breadcrumb-item active fw-bold" aria-current="page">Chỉnh sửa</li>
    </ol>
</nav>
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
        @endif
        <form action="{{ route('admin.tours.update', $tour->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label text-muted">Tên Tour <span class="text-danger">*</span></label>
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">Tiếng Việt</span>
                            <input type="text" name="title[vi]" value="{{ old('title.vi', $tour->getTranslation('title', 'vi', false)) }}" class="form-control" placeholder="Nhập tên tour hiển thị..." required>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">English</span>
                            <input type="text" name="title[en]" value="{{ old('title.en', $tour->getTranslation('title', 'en', false)) }}" class="form-control" placeholder="Tour name...">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">中文</span>
                            <input type="text" name="title[zh]" value="{{ old('title.zh', $tour->getTranslation('title', 'zh', false)) }}" class="form-control" placeholder="旅游名称...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Mô tả chi tiết</label>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">Tiếng Việt</span>
                            <textarea name="description[vi]" class="form-control" rows="3" placeholder="Viết vài dòng mô tả về trải nghiệm của tour này...">{{ old('description.vi', $tour->getTranslation('description', 'vi', false)) }}</textarea>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">English</span>
                            <textarea name="description[en]" class="form-control" rows="3" placeholder="Tour description...">{{ old('description.en', $tour->getTranslation('description', 'en', false)) }}</textarea>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">中文</span>
                            <textarea name="description[zh]" class="form-control" rows="3" placeholder="旅游描述...">{{ old('description.zh', $tour->getTranslation('description', 'zh', false)) }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Giá cơ bản (Người lớn)</label>
                            <input type="number" name="base_price" value="{{$tour->base_price}}" class="form-control" placeholder="VD: 1500000" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Giá trẻ em (VNĐ)</label>
                            <input type="number" name="child_price" value="{{$tour->child_price}}" class="form-control" placeholder="VD: 1000000 (Tùy chọn)">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label text-muted">Số ngày</label>
                            <input type="number" name="duration_days" value="{{$tour->duration_days}}"
                                class="form-control" min="1" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label text-muted">Số đêm</label>
                            <input type="number" name="duration_nights" value="{{$tour->duration_nights}}"
                                class="form-control" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label text-muted">Ảnh đại diện Tour</label>
                        <input type="file" name="primary_image" class="form-control" accept="image/*">
                        <div class="form-text">Bỏ trống nếu muốn giữ nguyên ảnh cũ.</div>

                        @php
                        // Lấy ảnh được đánh dấu là ảnh chính trong thư viện của tour này
                        $primaryImage = $tour->tour_images->where('is_primary', 1)->first();
                        @endphp

                        @if($primaryImage)
                        <div class="mt-3 p-2 border rounded bg-light text-center">
                            <span class="text-muted small d-block mb-2 text-start">Ảnh hiện tại:</span>
                            <img src="{{ $primaryImage->image_url }}" alt="Ảnh chính" class="rounded shadow-sm"
                                style="max-height: 160px; max-width: 100%; object-fit: cover;">
                        </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Điểm khởi hành <span class="text-danger">*</span></label>
                        <select name="departure_location_id" class="form-control" required>
                            <option value="">-- Chọn điểm khởi hành --</option>
                            @foreach($destinations as $dest)
                            <option value="{{ $dest->id }}"
                                {{ (old('departure_location_id', $tour->departure_location_id ?? '') == $dest->id) ? 'selected' : '' }}>
                                {{ $dest->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Điểm đến</label>
                        <select name="destination_id" class="form-select" required>
                            <option value="">-- Chọn điểm đến --</option>
                            @foreach($destinations as $dest)
                            <option value="{{ $dest->id }}"
                                {{ (old('destination_id', $tour->destination_id ?? '') == $dest->id) ? 'selected' : '' }}>
                                {{ $dest->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Danh mục Tour</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($categories as $cat)
                            <div class="form-check">
                                <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                    {{ in_array($cat->id, $tourCategoryIds) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_{{ $cat->id }}">
                                    {{ $cat->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="text-end">
                <button type="reset" class="btn btn-light me-2">Nhập lại</button>
                <button type="submit" class="btn btn-primary">Lưu và Tiếp tục thêm lịch trình</button>
            </div>
        </form>
    </div>
</div>
@endsection