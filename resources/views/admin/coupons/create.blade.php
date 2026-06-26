@extends('layouts.admin')

@section('page-title', 'Thêm mã giảm giá')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark">Thêm mã giảm giá</h4>

    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
        Quay lại
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã giảm giá</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="VD: SALE25">
                    @error('code')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Loại giảm giá</label>
                    <select name="discount_type" class="form-select">
                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Giảm tiền mặt (VNĐ)</option>
                    </select>
                    @error('discount_type')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá trị giảm</label>
                    <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value') }}" placeholder="VD: 25 hoặc 100000">
                    @error('discount_value')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá trị đơn tối thiểu</label>
                    <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value') }}" placeholder="VD: 500000">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giảm tối đa</label>
                    <input type="number" name="max_discount" class="form-control" value="{{ old('max_discount') }}" placeholder="VD: 200000">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Số lượt sử dụng</label>
                    <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}" placeholder="VD: 100">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="valid_from" id="valid_from" class="form-control" value="{{ old('valid_from') }}" min="{{ date('Y-m-d') }}">
                    @error('valid_from')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="valid_until" id="valid_until" class="form-control" value="{{ old('valid_until') }}">
                    @error('valid_until')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <small class="text-muted">Hạn dùng tối đa 1 năm kể từ ngày bắt đầu.</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Loại tour áp dụng</label>
                <select name="category_id" class="form-select">
                    <option value="">-- Áp dụng cho tất cả loại tour --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Lưu mã giảm giá
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const validFrom = document.getElementById('valid_from');
        const validUntil = document.getElementById('valid_until');

        function updateValidUntilRange() {
            if (!validFrom.value) return;
            const fromDate = new Date(validFrom.value);
            // min: ngày tiếp theo sau ngày bắt đầu
            const minDate = new Date(fromDate);
            minDate.setDate(minDate.getDate() + 1);
            // max: đúng 1 năm từ ngày bắt đầu
            const maxDate = new Date(fromDate);
            maxDate.setFullYear(maxDate.getFullYear() + 1);

            validUntil.min = minDate.toISOString().split('T')[0];
            validUntil.max = maxDate.toISOString().split('T')[0];

            // Reset nếu giá trị hiện tại nằm ngoài range
            if (validUntil.value && (validUntil.value <= validFrom.value || validUntil.value > validUntil.max)) {
                validUntil.value = '';
            }
        }

        if (validFrom && validUntil) {
            validFrom.addEventListener('change', updateValidUntilRange);
            // Trigger on load nếu đã có giá trị (old input)
            if (validFrom.value) updateValidUntilRange();
        }
    })();
</script>
@endpush
