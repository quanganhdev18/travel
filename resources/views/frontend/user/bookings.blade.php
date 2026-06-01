@extends('layouts.master')

@section('content')
<style>
    .booking-card {
        border-radius: 20px;
        transition: var(--transition-normal);
        border: 1px solid #edf2f7;
    }
    .booking-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(0, 124, 232, 0.2);
    }
    .booking-header {
        background: rgba(248, 249, 250, 0.8);
        border-bottom: 1px solid #edf2f7;
        padding: 16px 24px;
        border-radius: 20px 20px 0 0;
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .status-pending { background: rgba(245, 166, 35, 0.1); color: var(--secondary-color); }
    .status-confirmed { background: rgba(25, 135, 84, 0.1); color: #198754; }
    .status-cancelled { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
</style>

<div class="container py-5">
    <div class="mb-5 reveal-up">
        <h2 class="section-heading">Lịch sử đặt chỗ của tôi</h2>
        <p class="section-subheading">Quản lý các chuyến đi và vé máy bay của bạn.</p>
    </div>

    <div class="row g-4">
        <div class="col-12">
            @forelse($bookings as $booking)
            <div class="premium-card booking-card mb-4 reveal-up">
                <div class="booking-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <span class="text-muted small fw-500">Mã Đơn Hàng</span>
                        <div class="fw-bold text-dark fs-6 mt-1">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div>
                        <span class="text-muted small fw-500">Ngày Đặt</span>
                        <div class="fw-bold text-dark fs-6 mt-1"><i class="bi bi-calendar3 me-2 text-primary"></i>{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="text-end">
                        @php
                            $statusClass = 'status-pending';
                            $statusText = $booking->booking_status;
                            if(strtolower($statusText) == 'confirmed' || strtolower($statusText) == 'đã xác nhận') {
                                $statusClass = 'status-confirmed';
                            } elseif (strtolower($statusText) == 'cancelled' || strtolower($statusText) == 'đã hủy') {
                                $statusClass = 'status-cancelled';
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            <i class="bi bi-circle-fill me-1" style="font-size: 8px; position: relative; top: -1px;"></i> 
                            {{ ucfirst($statusText) }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5 row align-items-center">
                    <div class="col-md-6 mb-4 mb-md-0 border-md-end pe-md-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-light p-3 rounded-4 me-3 d-none d-sm-block">
                                <i class="bi bi-briefcase text-primary fs-3"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-500 mb-1 text-uppercase">Thông tin Tour</div>
                                <h4 class="fw-bold text-dark mb-2" style="font-size: 1.1rem; line-height: 1.4;">
                                    {{ $booking->tour_schedule->tour->title ?? 'Tên tour không tồn tại' }}
                                </h4>
                                @if(isset($booking->tour_schedule))
                                <div class="text-muted small fw-500">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i> {{ $booking->tour_schedule->tour->destination->name ?? '' }} 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-calendar-check me-1 text-success"></i> Khởi hành: {{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4 mb-md-0 px-md-4">
                        <div class="text-muted small fw-500 mb-1 text-uppercase text-center text-md-start">Phương thức di chuyển</div>
                        @if($booking->transport_type == 'flight')
                            @if($booking->pnr_code)
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                                <i class="bi bi-airplane text-danger fs-4 me-2"></i>
                                <div class="fw-bold text-danger fs-4 tracking-wide" style="letter-spacing: 2px;">{{ $booking->pnr_code }}</div>
                            </div>
                            @else
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start text-warning mt-2">
                                <i class="bi bi-airplane fs-5 me-2"></i> Chờ vé máy bay
                            </div>
                            @endif
                        @elseif($booking->transport_type == 'bus')
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start text-info mt-2">
                                <i class="bi bi-bus-front fs-5 me-2"></i> Đi bằng xe ô tô/xe khách
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start text-muted mt-2">
                                <i class="bi bi-car-front fs-5 me-2 opacity-50"></i> Di chuyển tự túc
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-3 text-center text-md-end ps-md-4">
                        <div class="text-muted small fw-500 mb-1 text-uppercase">Tổng thanh toán</div>
                        <div class="fw-bold text-dark" style="font-size: 1.5rem;">
                            {!! format_currency($booking->total_price) !!}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="glass-panel text-center py-5 reveal-up">
                <i class="bi bi-bag-x text-muted opacity-50 mb-4" style="font-size: 4rem; display: inline-block;"></i>
                <h3 class="fw-bold text-dark mb-3">Bạn chưa có đơn đặt chỗ nào</h3>
                <p class="text-muted fs-6 mb-4">Hàng ngàn điểm đến tuyệt đẹp đang chờ bạn khám phá.</p>
                <a href="{{ url('/') }}" class="btn btn-register-premium px-5 py-3 fs-5">
                    Khám Phá Các Chuyến Đi
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection