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
    <!-- Bảng Tour đang diễn ra -->
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="bi bi-compass me-2 text-primary"></i>Các tour đang diễn ra</h5>
                <a href="{{ route('admin.ongoing_tours.index') }}" class="btn btn-sm btn-light border">Xem tất cả</a>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Tour</th>
                                <th>Khởi Hành</th>
                                <th>Số Khách</th>
                                <th>Hướng Dẫn Viên</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ongoingTours as $schedule)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="{{ $schedule->tour->title ?? '' }}">
                                        {{ $schedule->tour->title ?? 'N/A' }}
                                    </div>
                                    <small class="text-muted">Mã: #{{ str_pad($schedule->id, 5, '0', STR_PAD_LEFT) }}</small>
                                </td>
                                <td>
                                    <div class="fw-500 text-primary">
                                        {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}
                                    </div>
                                    <small class="text-muted">Đến {{ \Carbon\Carbon::parse($schedule->return_date)->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @php
                                        $guests = $schedule->total_guests ?? 0;
                                        $percent = $schedule->capacity > 0 ? round(($guests / $schedule->capacity) * 100) : 0;
                                    @endphp
                                    <div class="fw-bold">{{ $guests }} / {{ $schedule->capacity }}</div>
                                    <div class="progress mt-1" style="height: 5px; width: 80px;">
                                        <div class="progress-bar bg-{{ $percent >= 100 ? 'danger' : 'success' }}" style="width: {{ $percent }}%"></div>
                                    </div>
                                </td>
                                <td>
                                    @if($schedule->schedule_guides->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($schedule->schedule_guides as $sg)
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-2 py-1">
                                                    <i class="bi bi-person-fill me-1"></i>{{ $sg->tour_guide->name ?? 'N/A' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge-soft badge-soft-warning px-2 py-1">Chưa phân công</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Không có tour nào đang diễn ra hôm nay.</td>
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
                <h5 class="admin-card-title"><i class="bi bi-pie-chart me-2 text-primary"></i>Tổng quan Đơn đặt chỗ</h5>
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
