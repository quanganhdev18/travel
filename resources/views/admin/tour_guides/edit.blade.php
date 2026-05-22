@extends('layouts.admin')

@section('page-title', 'Cập nhật Hướng Dẫn Viên')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="admin-card border-0">
            <div class="admin-card-header bg-white py-3">
                <h5 class="admin-card-title"><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa thông tin HDV</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.tour_guides.update', $tourGuide->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-500">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $tourGuide->name) }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required value="{{ old('phone', $tourGuide->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $tourGuide->email) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-500">Tiểu sử / Ghi chú (Bio)</label>
                        <textarea name="bio" class="form-control" rows="4">{{ old('bio', $tourGuide->bio) }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.tour_guides.index') }}" class="btn btn-light border px-4 me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-admin-primary px-4"><i class="bi bi-save me-2"></i> Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
