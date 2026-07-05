@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 reveal-up">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Đặt Tour Thành Công!</h2>
                    <p class="text-muted mb-4">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của TravelWondel. Vui lòng thanh toán để hoàn tất quá trình đặt tour.</p>

                    @if($booking->payment_method === 'transfer')
                        @php
                            // Lấy số tiền thực tế (đặt cọc hoặc toàn bộ)
                            $amount = $booking->payment_type === 'deposit' ? ($booking->total_price * 0.3) : $booking->total_price;
                            
                            $bankId = 'BIDV';
                            $accountNo = '0818802032';
                            $template = 'compact2';
                            $accountName = 'TRavelWondel';
                            $description = 'Dat tour TravelWondel';
                            
                            $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount=".round($amount)."&addInfo=".urlencode($description)."&accountName=".urlencode($accountName);
                        @endphp
                        
                        <div class="bg-light p-4 rounded-4 mb-4 text-center mx-auto" style="max-width: 450px;">
                            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-qr-code-scan me-2"></i>Quét mã QR để thanh toán</h5>
                            <img src="{{ $qrUrl }}" alt="VietQR" class="img-fluid rounded border bg-white p-2 mb-3" style="max-width: 250px;">
                            
                            <div class="text-start bg-white p-3 rounded border shadow-sm">
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                    <span class="text-muted">Ngân hàng:</span>
                                    <strong class="text-dark">{{ $bankId }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                    <span class="text-muted">Số tài khoản:</span>
                                    <strong class="text-dark fs-6">{{ $accountNo }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                    <span class="text-muted">Chủ tài khoản:</span>
                                    <strong class="text-dark">{{ $accountName }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                    <span class="text-muted">Số tiền:</span>
                                    <strong class="text-danger fs-5">{{ format_currency($amount) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between pt-1">
                                    <span class="text-muted">Nội dung:</span>
                                    <strong class="text-dark">{{ $description }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning text-start">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Lưu ý:</strong> Vui lòng nhập chính xác số tiền và nội dung chuyển khoản. Chúng tôi sẽ xác nhận đơn hàng ngay sau khi nhận được thanh toán.
                        </div>
                    @endif

                    <div class="mt-5 d-flex gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2">
                            <i class="bi bi-house me-2"></i> Về trang chủ
                        </a>
                        <a href="{{ route('user.bookings.detail', $booking->id) }}" class="btn btn-primary px-4 py-2">
                            Xem chi tiết đơn hàng <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
