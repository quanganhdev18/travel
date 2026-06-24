@extends('layouts.admin')

@section('page-title', 'Sửa mã giảm giá')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark">Sửa mã giảm giá</h4>

    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
        Quay lại
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã giảm giá</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                        value="{{ old('code', $coupon->code) }}" placeholder="VD: SALE25">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Loại giảm giá</label>
                    <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                        <option value="percent" {{ old('discount_type', $coupon->discount_type) == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Giảm tiền mặt (VNĐ)</option>
                    </select>
                    @error('discount_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá trị giảm</label>
                    <input type="number" name="discount_value" class="form-control @error('discount_value') is-invalid @enderror"
                        value="{{ old('discount_value', $coupon->discount_value) }}" placeholder="VD: 25 hoặc 100000">
                    @error('discount_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá trị đơn tối thiểu</label>
                    <input type="number" name="min_order_value" class="form-control"
                        value="{{ old('min_order_value', $coupon->min_order_value) }}" placeholder="VD: 500000">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giảm tối đa</label>
                    <input type="number" name="max_discount" class="form-control"
                        value="{{ old('max_discount', $coupon->max_discount) }}" placeholder="VD: 200000">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Số lượt sử dụng</label>
                    <input type="number" name="usage_limit" class="form-control"
                        value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="VD: 100">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror"
                        value="{{ old('valid_from', $coupon->valid_from?->format('Y-m-d')) }}">
                    @error('valid_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror"
                        value="{{ old('valid_until', $coupon->valid_until?->format('Y-m-d')) }}">
                    @error('valid_until')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Loại tour áp dụng</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                    <option value="">-- Áp dụng cho tất cả loại tour --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $coupon->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Cập nhật mã giảm giá
            </button>
        </form>
    </div>
</div>
@endsection
