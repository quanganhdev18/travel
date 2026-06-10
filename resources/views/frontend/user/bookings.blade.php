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
        <h2 class="section-heading">{{ __('Lịch sử đặt chỗ của tôi') }}</h2>
        <p class="section-subheading">{{ __('Quản lý các chuyến đi và vé máy bay của bạn.') }}</p>
    </div>

    <div class="row g-4">
        <div class="col-12">
            @forelse($bookings as $booking)
            <div class="premium-card booking-card mb-4 reveal-up">
                <div class="booking-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <span class="text-muted small fw-500">{{ __('Mã Đơn Hàng') }}</span>
                        <div class="fw-bold text-dark fs-6 mt-1">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div>
                        <span class="text-muted small fw-500">{{ __('Ngày Đặt') }}</span>
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
                            {{ __(ucfirst($statusText)) }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="d-flex align-items-start h-100">
                                <div class="bg-light p-3 rounded-4 me-3 d-none d-sm-flex align-items-center justify-content-center" style="min-width: 64px; height: 64px;">
                                    <i class="bi bi-briefcase text-primary fs-3"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-500 mb-1 text-uppercase">{{ __('Thông tin Tour') }}</div>
                                    <h4 class="fw-bold text-dark mb-2" style="font-size: 1.15rem; line-height: 1.4;">
                                        {{ $booking->tour_schedule->tour->title ?? 'Tên tour không tồn tại' }}
                                    </h4>
                                    @if(isset($booking->tour_schedule))
                                    <div class="d-flex flex-wrap gap-3 text-muted small fw-500 mt-3">
                                        <div class="d-flex align-items-center"><i class="bi bi-geo-alt text-danger me-1 fs-5"></i> {{ $booking->tour_schedule->tour->destination->name ?? '' }}</div>
                                        <div class="d-flex align-items-center"><i class="bi bi-calendar-check text-success me-1 fs-5"></i> {{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}</div>
                                        <div class="d-flex align-items-center"><i class="bi bi-people text-info me-1 fs-5"></i> {{ $booking->adults_count }} {{ __('người lớn') }}, {{ $booking->children_count }} {{ __('trẻ em') }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-5">
                            <div class="bg-light rounded-4 p-4 h-100 border border-light-subtle">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">{{ __('Chi tiết thanh toán') }}</h6>
                                
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">{{ __('Phương thức di chuyển:') }}</span>
                                    <span class="fw-500 text-end">
                                        @if($booking->transport_type == 'flight')
                                            <i class="bi bi-airplane text-danger me-1"></i> {{ __('Máy bay') }} 
                                            @if($booking->pnr_code) 
                                                <br><span class="text-danger fw-bold tracking-wide" style="letter-spacing: 1px;">(PNR: {{ $booking->pnr_code }})</span>
                                            @else 
                                                <br><span class="text-warning">({{ __('Chờ vé') }})</span>
                                            @endif
                                        @elseif($booking->transport_type == 'bus')
                                            <i class="bi bi-bus-front text-info me-1"></i> {{ __('Xe ô tô/khách') }}
                                        @else
                                            <i class="bi bi-car-front text-muted me-1"></i> {{ __('Tự túc') }}
                                        @endif
                                    </span>
                                </div>

                                @if($booking->transport_price > 0)
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">{{ __('Phí di chuyển:') }}</span>
                                    <span class="fw-500">{!! format_currency($booking->transport_price) !!}</span>
                                </div>
                                @endif

                                @if($booking->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">{{ __('Giảm giá:') }}</span>
                                    <span class="text-success fw-500">-{!! format_currency($booking->discount_amount) !!}</span>
                                </div>
                                @endif

                                <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                                    <span class="fw-bold text-dark mt-1">{{ __('Tổng thanh toán:') }}</span>
                                    <span class="fw-bold text-primary" style="font-size: 1.3rem;">{!! format_currency($booking->total_price) !!}</span>
                                </div>

                                <div class="mt-4">
                                    @php
                                        $paymentStatus = $booking->payment_status ?? 'unpaid';
                                        $paymentMethod = $booking->payment_method ?? 'transfer';
                                        $paymentType = $booking->payment_type ?? 'full';
                                    @endphp

                                    @if($paymentStatus === 'paid')
                                        <div class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold w-100 text-center" style="font-size: 0.85rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i> {{ __('Đã thanh toán (100%)') }}
                                        </div>
                                    @elseif($paymentStatus === 'deposited')
                                        <div class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2 fw-semibold w-100 text-center" style="font-size: 0.85rem;">
                                            <i class="bi bi-pie-chart-fill me-1"></i> {{ __('Đã cọc (30%)') }}
                                        </div>
                                    @elseif($paymentStatus === 'unpaid' || $paymentStatus === 'pending')
                                        @if($paymentMethod === 'vnpay')
                                            <div class="d-flex flex-column gap-2">
                                                <div class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-3 py-2 fw-semibold w-100 text-center" style="font-size: 0.85rem;">
                                                    <i class="bi bi-hourglass-split me-1"></i> {{ __('Chưa thanh toán (VNPay)') }}
                                                </div>
                                                @if($booking->booking_status !== 'cancelled')
                                                    <a href="{{ route('frontend.bookings.pay_vnpay', $booking->id) }}" class="btn btn-primary btn-sm rounded-pill fw-bold w-100 py-2 mt-1">
                                                        <i class="bi bi-credit-card me-1"></i> {{ __('Thanh toán ngay') }}
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2 fw-semibold w-100 text-center" style="font-size: 0.85rem;">
                                                <i class="bi bi-wallet2 me-1"></i> {{ __('Chờ thanh toán (Tiền mặt/Chuyển khoản)') }}
                                            </div>
                                        @endif
                                    @elseif($paymentStatus === 'failed')
                                        <div class="d-flex flex-column gap-2">
                                            <div class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-semibold w-100 text-center" style="font-size: 0.85rem;">
                                                <i class="bi bi-x-circle-fill me-1"></i> {{ __('Thanh toán VNPay lỗi') }}
                                            </div>
                                            @if($booking->booking_status !== 'cancelled')
                                                <a href="{{ route('frontend.bookings.pay_vnpay', $booking->id) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold w-100 py-2 mt-1">
                                                    <i class="bi bi-arrow-clockwise me-1"></i> {{ __('Thử lại VNPay') }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="glass-panel text-center py-5 reveal-up">
                <i class="bi bi-bag-x text-muted opacity-50 mb-4" style="font-size: 4rem; display: inline-block;"></i>
                <h3 class="fw-bold text-dark mb-3">{{ __('Bạn chưa có đơn đặt chỗ nào') }}</h3>
                <p class="text-muted fs-6 mb-4">{{ __('Hàng ngàn điểm đến tuyệt đẹp đang chờ bạn khám phá.') }}</p>
                <a href="{{ url('/') }}" class="btn btn-register-premium px-5 py-3 fs-5">
                    {{ __('Khám Phá Các Chuyến Đi') }}
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection