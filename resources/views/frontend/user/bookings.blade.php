@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h4 style="color: #1a2b4c;">Lịch sử đặt chỗ của tôi</h4>
        <p class="text-muted">Danh sách các tour du lịch và vé máy bay anh đã đặt.</p>
    </div>

    <div class="row">
        <div class="col-12">
            @forelse($bookings as $booking)
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body row align-items-center">
                    <div class="col-md-5">
                        <div class="text-muted small">Mã đơn: #{{ $booking->id }} | Ngày đặt:
                            {{ $booking->created_at->format('d/m/Y') }}
                        </div>
                        <div style="font-size: 18px; font-weight: 500; color: #007CE8; margin-top: 5px;">
                            {{ $booking->tour_schedule->tour->title ?? 'Tên tour không tồn tại' }}
                        </div>
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <div class="text-muted small">Mã máy bay (PNR)</div>
                        @if($booking->pnr_code)
                        <div style="font-size: 20px; font-weight: 500; color: #e53e3e;">{{ $booking->pnr_code }}</div>
                        @else
                        <div class="text-muted">Không có dữ liệu vé máy bay</div>
                        @endif
                    </div>
                    <div class="col-md-3 mt-3 mt-md-0 text-md-end">
                        <div class="text-muted small">Tổng tiền</div>
                        <div style="font-size: 18px; font-weight: 500;">
                            {{ number_format($booking->total_price, 0, ',', '.') }} ₫
                        </div>
                        <span class="badge bg-primary mt-1">{{ $booking->booking_status }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <p class="text-muted">Anh chưa có đơn đặt chỗ nào.</p>
                <a href="{{ url('/') }}" class="btn" style="background-color: #007CE8; color: white;">Khám phá tour
                    ngay</a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection