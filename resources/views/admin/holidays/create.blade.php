@extends('layouts.admin')

@section('page-title', 'Thêm Ngày Lễ Mới')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Thông tin Ngày lễ</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.holidays.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên ngày lễ <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="VD: Tết Nguyên Đán">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Phụ thu (%) <span class="text-danger">*</span></label>
                        <input type="number" name="price_increase_percentage" class="form-control" value="20" step="0.01" min="0" max="100" required>
                        <div class="form-text">Mức phụ thu áp dụng (tính theo %). Ví dụ: 20 có nghĩa là tăng 20% giá gốc.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.holidays.index') }}" class="btn btn-light border">Hủy</a>
                        <button type="submit" class="btn btn-admin-primary">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
