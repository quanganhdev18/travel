@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Điểm đến và khởi hành: ' . $destination->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.destinations.index') }}"
                class="text-decoration-none fw-semibold"><i class="bi bi-geo-alt me-1"></i>Quản lý Điểm đến và khởi hành</a></li>
        <li class="breadcrumb-item text-muted">{{ \Illuminate\Support\Str::limit($destination->name, 30) }}</li>
        <li class="breadcrumb-item active fw-bold" aria-current="page">Chỉnh sửa</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('admin.destinations.update', $destination->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Tên điểm <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $destination->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="4">{{ old('description', $destination->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">Ảnh đại diện mới (Bỏ trống nếu giữ nguyên ảnh
                            cũ)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                            name="image" accept="image/*">
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if($destination->image_url)
                        <div class="mt-3">
                            <div class="text-muted mb-2">Ảnh hiện tại:</div>
                            <img src="{{ $destination->image_url }}" alt="Current Image"
                                class="rounded border shadow-sm" style="max-height: 150px;">
                        </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.destinations.index') }}" class="btn btn-light border">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary">Cập nhật thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection