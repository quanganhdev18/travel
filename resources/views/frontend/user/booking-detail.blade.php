@extends('layouts.master')

@section('title', 'Chi tiết đơn - Travel Wonder')

@section('content')
<style>
    .detail-card { border-radius: 20px; border: 1px solid #edf2f7; background: white; overflow: hidden; margin-bottom: 24px; }
    .detail-card-header { background: rgba(0,124,232,0.04); border-bottom: 1px solid #edf2f7; padding: 14px 24px; font-weight: 700; color: #374151; }
    .detail-card-body { padding: 24px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
    .info-row:last-child { border-bottom: none; }
    .status-badge { padding: 6px 14px; border-radius: 30px; font-weight: 600; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 5px; }
    .s-pending   { background: rgba(245,166,35,0.12); color: #d97706; }
    .s-confirmed { background: rgba(25,135,84,0.12);  color: #198754; }
    .s-paid      { background: rgba(13,110,253,0.12); color: #0d6efd; }
    .s-cancelled { background: rgba(220,53,69,0.12);  color: #dc3545; }
    .s-completed { background: rgba(108,117,125,0.12);color: #6c757d; }
    .star-btn { background: none; border: none; font-size: 2rem; cursor: pointer; color: #d1d5db; transition: color 0.15s; padding: 0 2px; line-height: 1; }
    .star-btn.lit { color: #f59e0b; }
    .step-dot { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>

<div class="container py-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4 reveal-up">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-primary fw-500">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.bookings') }}" class="text-decoration-none text-primary fw-500">Đơn đặt tour</a></li>
            <li class="breadcrumb-item active fw-bold">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</li>
        </ol>
    </nav>

    @php
        $status  = strtolower($booking->booking_status ?? 'pending');
        $sMap = [
            'pending'   => ['cls'=>'s-pending',   'label'=>'Chờ xác nhận', 'icon'=>'bi-clock'],
            'confirmed' => ['cls'=>'s-confirmed',  'label'=>'Đã xác nhận',  'icon'=>'bi-check-circle'],
            'paid'      => ['cls'=>'s-paid',       'label'=>'Đã thanh toán','icon'=>'bi-credit-card'],
            'cancelled' => ['cls'=>'s-cancelled',  'label'=>'Đã hủy',       'icon'=>'bi-x-circle'],
            'completed' => ['cls'=>'s-completed',  'label'=>'Hoàn thành',   'icon'=>'bi-patch-check'],
        ];
        $si   = $sMap[$status] ?? ['cls'=>'s-pending','label'=>ucfirst($status),'icon'=>'bi-circle'];
        $tour = $booking->tour_schedule->tour ?? null;
        $pImg = $tour?->tour_images->where('is_primary',1)->first();
        $img  = $pImg?->image_url ?? 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';
    @endphp

    {{-- Tiêu đề --}}
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start mb-4 reveal-up">
        <div>
            <h2 class="fw-bold text-dark mb-1">Chi tiết đơn đặt</h2>
            <span class="text-muted small">Mã: <strong>#{{ str_pad($booking->id,6,'0',STR_PAD_LEFT) }}</strong> · {{ $booking->created_at->format('H:i d/m/Y') }}</span>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <span class="status-badge {{ $si['cls'] }}"><i class="bi {{ $si['icon'] }}"></i>{{ $si['label'] }}</span>
            @if(in_array($status,['pending','confirmed']))
            <form method="POST" action="{{ route('user.bookings.cancel',$booking->id) }}"
                onsubmit="return confirm('Hủy đơn này?')">
                @csrf
                <button class="btn btn-sm btn-outline-danger rounded-3 px-3">
                    <i class="bi bi-x me-1"></i>Hủy đơn
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Thông tin tour --}}
            <div class="detail-card reveal-up">
                <div class="detail-card-header"><i class="bi bi-briefcase me-2 text-primary"></i>Thông tin tour</div>
                <div class="detail-card-body">
                    <div class="d-flex gap-3">
                        <img src="{{ $img }}" class="rounded-3 flex-shrink-0"
                            style="width:110px;height:75px;object-fit:cover;" alt="tour">
                        <div>
                            <h5 class="fw-bold text-dark mb-2">{{ $tour?->title ?? 'Không tìm thấy tour' }}</h5>
                            <div class="d-flex flex-wrap gap-3 text-muted small">
                                @if($tour?->destination)
                                    <span><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $tour->destination->name }}</span>
                                @endif
                                @if($tour)
                                    <span><i class="bi bi-clock me-1 text-warning"></i>{{ $tour->duration_days }} ngày{{ $tour->duration_nights > 0 ? ' ' . $tour->duration_nights . ' đêm' : '' }}</span>
                                @endif
                                @if($booking->tour_schedule)
                                    <span><i class="bi bi-calendar-event me-1 text-success"></i>{{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}@if($tour?->departure_time) ({{ \Carbon\Carbon::parse($tour->departure_time)->format('H\hi') }})@endif</span>
                                    <span><i class="bi bi-calendar-check me-1 text-info"></i>Về: {{ \Carbon\Carbon::parse($booking->tour_schedule->return_date)->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thông tin đặt chỗ --}}
<div class="detail-card reveal-up">
    <div class="detail-card-header">
        <i class="bi bi-clipboard-check me-2 text-primary"></i>
        Chi tiết đặt chỗ
    </div>

    <div class="detail-card-body">
        {{-- Người lớn --}}
        <div class="info-row">
            <span class="text-muted small">
                <i class="bi bi-people me-2"></i>
                Người lớn
            </span>

            <span class="fw-bold">
                {{ $booking->adults_count }} người
            </span>
        </div>

        {{-- Trẻ em --}}
        <div class="info-row">
            <span class="text-muted small">
                <i class="bi bi-person-badge me-2"></i>
                Trẻ em
            </span>

            <span class="fw-bold">
                {{ $booking->children_count }} người
            </span>
        </div>

        {{-- Điểm tập kết --}}
        <div class="info-row">
            <span class="text-muted small">
                <i class="bi bi-geo-alt-fill text-success me-2"></i>
                Điểm tập kết
            </span>

            <span class="fw-bold text-end"
                  style="max-width:65%; word-break:break-word;">
                {{ $booking->meeting_point
                    ?: ($booking->tour_schedule?->tour?->meeting_point ?? 'Chưa cập nhật') }}
            </span>
        </div>

        {{-- Giảm giá --}}
        @if($booking->discount_amount)
            <div class="info-row">
                <span class="text-muted small">
                    <i class="bi bi-tags me-2"></i>
                    Giảm giá
                </span>

                <span class="fw-bold text-success">
                    -{{ number_format($booking->discount_amount, 0, ',', '.') }}₫
                </span>
            </div>
        @endif

        {{-- Tổng thanh toán --}}
        <div class="info-row">
            <span class="fw-bold text-dark fs-6">
                <i class="bi bi-wallet2 me-2"></i>
                Tổng thanh toán
            </span>

            <span class="fw-bold text-danger"
                  style="font-size:1.3rem;">
                {{ number_format($booking->total_price, 0, ',', '.') }}₫
            </span>
        </div>
    </div>
</div>

            {{-- Hành khách --}}
            <div class="detail-card reveal-up">
                <div class="detail-card-header d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-person-lines-fill me-2 text-primary"></i>Hành khách</div>
                    @if(in_array($status, ['pending', 'paid', 'confirmed']))
                        <a href="{{ route('frontend.bookings.passengers', $booking->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-pencil-square me-1"></i>Bổ sung / Sửa thông tin
                        </a>
                    @endif
                </div>
                <div class="detail-card-body">
                    @foreach($booking->booking_passengers as $p)
                    <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom':'' }}">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                            style="width:40px;height:40px;"><i class="bi bi-person text-primary"></i></div>
                        <div>
                            <div class="fw-bold text-dark">{{ $p->full_name }}</div>
                            <div class="text-muted small">
                                {{ $p->identity_number }} ·
                                {{ $p->gender==='male'?'Nam':($p->gender==='female'?'Nữ':'Khác') }} ·
                                {{ \Carbon\Carbon::parse($p->date_of_birth)->format('d/m/Y') }}
                            </div>
                        </div>
                        <span class="ms-auto badge bg-light text-muted rounded-3 small">{{ $p->passenger_type==='adult'?'Người lớn':'Trẻ em' }}</span>
                    </div>
                    @endforeach
                    @if($booking->booking_passengers->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-people" style="font-size:2rem;opacity:0.5;"></i>
                            <div class="mt-2">Bạn chưa cung cấp thông tin hành khách.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Đánh giá --}}
            @if(in_array($status,['completed','confirmed','paid']) && $tour)
            <div class="detail-card reveal-up">
                <div class="detail-card-header"><i class="bi bi-star me-2 text-warning"></i>Đánh giá chuyến đi</div>
                <div class="detail-card-body">
                    @if($existingReview)
                        <div class="p-4 rounded-4" style="background:rgba(245,166,35,0.06);border:1px solid rgba(245,166,35,0.2);">
                            <div class="d-flex align-items-center mb-2 gap-1">
                                @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$existingReview->rating?'-fill':'' }} text-warning" style="font-size:1.2rem;"></i>@endfor
                                <span class="ms-2 text-muted small">Đánh giá chung của bạn</span>
                            </div>
                            
                            @if($existingReview->guide_rating)
                            <div class="d-flex align-items-center mb-2 gap-1 mt-2 border-top pt-2">
                                @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$existingReview->guide_rating?'-fill':'' }} text-primary" style="font-size:1.2rem;"></i>@endfor
                                <span class="ms-2 text-muted small">Đánh giá Hướng dẫn viên ({{ $existingReview->tour_guide->name ?? 'HDV' }})</span>
                            </div>
                            @endif

                            @if($existingReview->comment)<p class="text-dark lh-lg mb-1 mt-3">{{ $existingReview->comment }}</p>@endif
                            <span class="text-muted small">{{ $existingReview->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @else
                        <form method="POST" action="{{ route('user.reviews.store') }}">
                            @csrf
                            <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                            <div class="mb-3">
                                <label class="form-label fw-600 text-dark mb-2">Đánh giá chuyến đi</label>
                                <div class="d-flex gap-1 starRow" data-input="ratingVal">
                                    @for($i=1;$i<=5;$i++)
                                    <button type="button" class="star-btn" data-v="{{ $i }}"><i class="bi bi-star-fill"></i></button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingVal" value="0" required>
                            </div>

                            @if($booking->tour_schedule && $booking->tour_schedule->schedule_guides->count() > 0)
                            <div class="mb-3 p-3 bg-light rounded border">
                                @php
                                    $mainGuide = $booking->tour_schedule->schedule_guides->where('is_backup', false)->first();
                                    $guide = $mainGuide ? $mainGuide->tour_guide : $booking->tour_schedule->schedule_guides->first()->tour_guide;
                                @endphp
                                <label class="form-label fw-600 text-dark mb-2">Đánh giá Hướng dẫn viên ({{ $guide->name }})</label>
                                <input type="hidden" name="guide_id" value="{{ $guide->id }}">
                                <div class="d-flex gap-1 starRow text-primary" data-input="guideRatingVal">
                                    @for($i=1;$i<=5;$i++)
                                    <button type="button" class="star-btn" data-v="{{ $i }}"><i class="bi bi-star-fill"></i></button>
                                    @endfor
                                </div>
                                <input type="hidden" name="guide_rating" id="guideRatingVal" value="0">
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-600 text-dark">Nhận xét chi tiết <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                                <textarea class="form-control" name="comment" rows="3"
                                    style="border-radius:12px;resize:none;"
                                    placeholder="Chia sẻ trải nghiệm chuyến đi, nhận xét về HDV..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-register-premium px-4 py-2">
                                <i class="bi bi-send me-2"></i>Gửi đánh giá
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar trạng thái --}}
        <div class="col-lg-4">
            <div class="detail-card reveal-up" style="position:sticky;top:100px;">
                <div class="detail-card-header">Tiến trình đơn hàng</div>
                <div class="detail-card-body">
                    @php
                        $steps = [
                            ['key'=>'pending',  'label'=>'Chờ xác nhận'],
                            ['key'=>'confirmed','label'=>'Đã xác nhận'],
                            ['key'=>'paid',     'label'=>'Đã thanh toán'],
                            ['key'=>'completed','label'=>'Hoàn thành'],
                        ];
                        $order = ['pending'=>0,'confirmed'=>1,'paid'=>2,'completed'=>3,'cancelled'=>-1];
                        $cur   = $order[$status] ?? 0;
                    @endphp

                    @if($status === 'cancelled')
                        <div class="text-center py-3">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size:2.5rem;"></i>
                            <div class="fw-bold text-danger mt-2">Đơn đã bị hủy</div>
                        </div>
                    @else
                        @foreach($steps as $idx => $step)
                        <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last?'border-bottom':'' }}">
                            <div class="step-dot" style="background:{{ $idx<=$cur?'var(--primary-color)':'#e5e7eb' }};">
                                <i class="bi bi-check text-white" style="font-size:0.85rem;"></i>
                            </div>
                            <span class="{{ $idx<=$cur?'fw-600 text-dark':'text-muted' }}">{{ $step['label'] }}</span>
                            @if($idx===$cur)<span class="ms-auto badge bg-primary-subtle text-primary rounded-pill small">Hiện tại</span>@endif
                        </div>
                        @endforeach
                    @endif

                    <div class="mt-3 d-grid gap-2">
                        <a href="{{ route('user.bookings') }}" class="btn btn-outline-secondary rounded-3">
                            <i class="bi bi-arrow-left me-2"></i>Danh sách đơn
                        </a>
                        @if($tour)
                        <a href="{{ route('frontend.tours.show',$tour->slug) }}" class="btn btn-outline-primary rounded-3">
                            <i class="bi bi-eye me-2"></i>Xem tour
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var starRows = document.querySelectorAll('.starRow');
    
    starRows.forEach(function(row) {
        var stars = row.querySelectorAll('.star-btn');
        var inputId = row.dataset.input;
        var inp = document.getElementById(inputId);
        
        if (!stars.length) return;

        function paint(val) {
            stars.forEach(function(b){ b.classList.toggle('lit', parseInt(b.dataset.v) <= val); });
            if (inp) inp.value = val;
        }
        
        stars.forEach(function(b){
            b.addEventListener('click',    function(){ paint(parseInt(b.dataset.v)); });
            b.addEventListener('mouseover',function(){ paint(parseInt(b.dataset.v)); });
        });
        
        row.addEventListener('mouseleave', function(){ paint(parseInt(inp.value)||0); });
    });
});
</script>
@endsection
