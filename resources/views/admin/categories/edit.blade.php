@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Danh mục: ' . $category->name)

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}"
                class="text-decoration-none fw-semibold"><i class="bi bi-tags me-1"></i>Quản lý Danh mục</a></li>
        <li class="breadcrumb-item text-muted">{{ \Illuminate\Support\Str::limit($category->name, 30) }}</li>
        <li class="breadcrumb-item active fw-bold" aria-current="page">Chỉnh sửa</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Tên danh mục <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light border">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary">Cập nhật thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection