@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý mã giảm giá</h2>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            + Thêm mã giảm giá
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã giảm giá</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td>{{ $coupon->code }}</td>

                            <td>
                                {{ $coupon->discount_type == 'percent' ? 'Phần trăm' : 'Tiền mặt' }}
                            </td>

                            <td>
                                @if($coupon->discount_type == 'percent')
                                    {{ $coupon->discount_value }}%
                                @else
                                    {{ number_format($coupon->discount_value, 0, ',', '.') }}đ
                                @endif
                            </td>

                            <td>
                                {{ $coupon->valid_from ? $coupon->valid_from->format('d/m/Y') : '' }}
                            </td>

                            <td>
                                {{ $coupon->valid_until ? $coupon->valid_until->format('d/m/Y') : '' }}
                            </td>

                            <td>
                                @if($coupon->valid_until && $coupon->valid_until->isPast())
                                    <span class="badge bg-danger">Hết hạn</span>
                                @else
                                    <span class="badge bg-success">Hoạt động</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Chưa có mã giảm giá nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
