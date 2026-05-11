@extends('layouts.admin')

@section('page-title', 'Bảng Điều Khiển')

@section('content')
<div class="row g-4 mb-4">
    <!-- Thẻ Doanh Thu -->
    <div class="col-xl-3 col-md-6">
        <div class="admin-card h-100 border-0" style="background: linear-gradient(135deg, var(--admin-primary) 0%, #3b82f6 100%); color: white;">
            <div class="admin-card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase fw-600 mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Tổng Doanh Thu</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h3>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-currency-dollar fs-3"></i>
                </div>
            </div>
            <div class="px-4 py-2 bg-black bg-opacity-10" style="font-size: 0.8rem;">
                <i class="bi bi-arrow-up-right me-1"></i> Ghi nhận từ đơn hàng thành công
            </div>
        </div>
    </div>

    <!-- Thẻ Đơn Hàng -->
    <div class="col-xl-3 col-md-6">
        <div class="admin-card h-100 border-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
            <div class="admin-card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase fw-600 mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Tổng Đơn Đặt Chỗ</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalBookings }}</h3>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-cart-check fs-3"></i>
                </div>
            </div>
            <div class="px-4 py-2 bg-black bg-opacity-10" style="font-size: 0.8rem;">
                <a href="{{ route('admin.bookings.index') }}" class="text-white text-decoration-none">Xem chi tiết <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Thẻ Tours -->
    <div class="col-xl-3 col-md-6">
        <div class="admin-card h-100 border-0" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
            <div class="admin-card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase fw-600 mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Sản Phẩm Tour</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalTours }}</h3>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-briefcase fs-3"></i>
                </div>
            </div>
            <div class="px-4 py-2 bg-black bg-opacity-10" style="font-size: 0.8rem;">
                <a href="{{ route('admin.tours.index') }}" class="text-white text-decoration-none">Quản lý Tour <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Thẻ Users -->
    <div class="col-xl-3 col-md-6">
        <div class="admin-card h-100 border-0" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white;">
            <div class="admin-card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase fw-600 mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">Khách Hàng</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalUsers }}</h3>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-people fs-3"></i>
                </div>
            </div>
            <div class="px-4 py-2 bg-black bg-opacity-10" style="font-size: 0.8rem;">
                <i class="bi bi-person-check me-1"></i> Khách hàng đã đăng ký
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Bảng Đơn đặt chỗ mới nhất -->
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="bi bi-list-check me-2 text-primary"></i>Đơn đặt chỗ mới nhất</h5>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light border">Xem tất cả</a>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã Đơn</th>
                                <th>Khách Hàng</th>
                                <th>Sản Phẩm (Tour)</th>
                                <th>Tổng Tiền</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td><span class="fw-bold text-dark">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    <div class="fw-500 text-dark">{{ $booking->user->name ?? 'Khách lẻ' }}</div>
                                    <div class="small text-muted">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-500 text-dark text-truncate" style="max-width: 200px;" title="{{ $booking->tour_schedule->tour->title ?? 'N/A' }}">
                                        {{ $booking->tour_schedule->tour->title ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="fw-bold text-danger">{{ number_format($booking->total_price, 0, ',', '.') }} ₫</td>
                                <td>
                                    @php
                                        $badgeClass = 'badge-soft-secondary';
                                        $statusStr = strtolower($booking->booking_status);
                                        if($statusStr == 'confirmed' || $statusStr == 'đã xác nhận') $badgeClass = 'badge-soft-success';
                                        if($statusStr == 'pending' || $statusStr == 'chờ xử lý') $badgeClass = 'badge-soft-warning';
                                        if($statusStr == 'cancelled' || $statusStr == 'đã hủy') $badgeClass = 'badge-soft-danger';
                                    @endphp
                                    <span class="badge-soft {{ $badgeClass }}">{{ ucfirst($booking->booking_status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Không có đơn đặt chỗ nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ nhỏ (Placeholder) -->
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="bi bi-pie-chart me-2 text-primary"></i>Tổng quan Booking</h5>
            </div>
            <div class="admin-card-body d-flex align-items-center justify-content-center">
                <div style="width: 100%; max-width: 250px;">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('bookingChart').getContext('2d');
        const bookingChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Đã xác nhận', 'Chờ xử lý', 'Đã hủy'],
                datasets: [{
                    data: [65, 25, 10], // Sample data
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                cutout: '75%'
            }
        });
    });
</script>
@endsection
