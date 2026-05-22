@extends('layouts.master')

@section('content')
<style>
    .gallery-main {
        height: 500px;
        object-fit: cover;
        border-radius: 24px;
        transition: var(--transition-slow);
    }
    .gallery-main:hover {
        transform: scale(1.02);
    }
    .gallery-sub {
        height: 240px;
        object-fit: cover;
        border-radius: 20px;
        transition: var(--transition-normal);
        cursor: pointer;
    }
    .gallery-sub:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-hover);
    }
    .overflow-hidden-rounded {
        overflow: hidden;
        border-radius: 24px;
    }
    .tour-nav-tabs .nav-link {
        color: var(--text-muted);
        background: transparent;
        border: none;
        padding: 16px 24px;
        font-weight: 600;
        border-radius: 12px;
        transition: var(--transition-fast);
        text-align: left;
    }
    .tour-nav-tabs .nav-link:hover {
        background: rgba(0, 124, 232, 0.05);
        color: var(--primary-color);
    }
    .tour-nav-tabs .nav-link.active {
        color: white;
        background: var(--primary-color);
        box-shadow: 0 4px 15px rgba(0, 124, 232, 0.3);
    }
    .booking-sidebar {
        position: sticky;
        top: 100px;
    }
    .schedule-btn {
        border-radius: 16px;
        padding: 12px 16px;
        border: 2px solid transparent;
        background: var(--light-gray);
        color: var(--text-muted);
        transition: var(--transition-fast);
        cursor: pointer;
        text-align: center;
        min-width: 100px;
    }
    .btn-check:checked + .schedule-btn {
        background: rgba(0, 124, 232, 0.1);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
</style>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4 reveal-up">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-primary fw-500">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">{{ $tour->destination->name ?? 'Điểm đến' }}</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ $tour->title }}</li>
        </ol>
    </nav>

    <div class="mb-5 reveal-up" style="transition-delay: 0.1s;">
        <h1 class="section-heading mb-3">{{ $tour->title }}</h1>
        <div class="d-flex flex-wrap gap-4 text-muted fw-500 mb-4">
            <span class="d-flex align-items-center"><i class="bi bi-geo-alt fs-5 me-2 text-danger"></i> {{ __('Khởi hành từ:') }} {{ $tour->departure_location->name ?? __('Đang cập nhật') }}</span>
            <span class="d-flex align-items-center"><i class="bi bi-clock fs-5 me-2 text-warning"></i> {{ $tour->duration_days }} {{ __('ngày') }} {{ $tour->duration_nights }} {{ __('đêm') }}</span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8 overflow-hidden-rounded">
                @php $primaryImage = $tour->tour_images->where('is_primary', 1)->first(); @endphp
                <img src="{{ $primaryImage ? $primaryImage->image_url : 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200' }}" class="w-100 gallery-main" alt="{{ __('Ảnh chính') }}">
            </div>
            <div class="col-md-4 d-flex flex-column gap-3">
                @php $subImages = $tour->tour_images->where('is_primary', 0)->take(2); @endphp
                @forelse($subImages as $img)
                    <div class="overflow-hidden-rounded h-50">
                        <img src="{{ $img->image_url }}" class="w-100 gallery-sub" alt="{{ __('Ảnh phụ') }}">
                    </div>
                @empty
                    <div class="overflow-hidden-rounded h-50"><img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=600" class="w-100 gallery-sub" alt="{{ __('Ảnh phụ') }}"></div>
                    <div class="overflow-hidden-rounded h-50"><img src="https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=600" class="w-100 gallery-sub" alt="{{ __('Ảnh phụ') }}"></div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <div class="glass-panel p-4 p-md-5 mb-5 reveal-up">
                <h3 class="section-heading mb-4 text-dark fs-4">{{ __('Tổng quan') }}</h3>
                <p class="text-muted lh-lg fs-6">{!! nl2br(e($tour->description)) !!}</p>
            </div>

            <div class="glass-panel p-4 p-md-5 mb-5 reveal-up">
                <h3 class="section-heading mb-4 text-dark fs-4">{{ __('Lịch trình chi tiết') }}</h3>
                <div class="d-flex flex-column flex-md-row align-items-start gap-4">
                    <div class="nav flex-column nav-pills tour-nav-tabs w-100 w-md-25" id="itinerary-tabs" role="tablist" aria-orientation="vertical">
                        @foreach($tour->tour_itineraries as $index => $itinerary)
                        <button class="nav-link mb-2 {{ $index == 0 ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#day-{{ $itinerary->id }}" type="button" role="tab">
                            <div class="small text-uppercase mb-1" style="opacity: 0.8; letter-spacing: 1px;">{{ __('Ngày') }} {{ $itinerary->day_number }}</div>
                            <div class="fw-bold">{{ $itinerary->title }}</div>
                        </button>
                        @endforeach
                    </div>
                    <div class="tab-content w-100 w-md-75 p-4 rounded-4 bg-white shadow-sm border-0" id="itinerary-tabContent">
                        @foreach($tour->tour_itineraries as $index => $itinerary)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="day-{{ $itinerary->id }}" role="tabpanel">
                            <h4 class="mb-4 fw-bold text-dark">{{ $itinerary->title }}</h4>
                            <p class="text-muted lh-lg fs-6">{!! nl2br(e($itinerary->description)) !!}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="glass-panel p-4 p-md-5 mb-5 reveal-up">
                <h3 class="section-heading mb-4 text-dark fs-4">{{ __('Các hoạt động nổi bật') }}</h3>
                @if($groupedActivities->isNotEmpty())
                <div class="d-flex flex-column flex-md-row align-items-start gap-4">
                    <div class="nav flex-column nav-pills tour-nav-tabs w-100 w-md-25" id="activity-tabs" role="tablist" aria-orientation="vertical">
                        @foreach($groupedActivities as $type => $activities)
                        <button class="nav-link mb-2 {{ $loop->first ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#activity-{{ \Illuminate\Support\Str::slug($type) }}" type="button" role="tab">
                            {{ $type }}
                        </button>
                        @endforeach
                    </div>
                    <div class="tab-content w-100 w-md-75">
                        @foreach($groupedActivities as $type => $activities)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="activity-{{ \Illuminate\Support\Str::slug($type) }}" role="tabpanel">
                            <div class="row g-4">
                                @foreach($activities as $activity)
                                <div class="col-md-6">
                                    <div class="premium-card h-100">
                                        <div class="card-img-wrapper" style="height: 180px;">
                                            <img src="{{ $activity->image_url ?? 'https://images.unsplash.com/photo-1542314831-c6a4d142104d?q=80&w=400' }}" class="card-img-top" alt="{{ __('Ảnh hoạt động') }}">
                                        </div>
                                        <div class="card-body p-4">
                                            <h5 class="card-title fw-bold fs-6 mb-2">{{ $activity->title }}</h5>
                                            <p class="card-text text-muted small lh-lg mb-0">{{ $activity->description }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <p class="text-muted">{{ __('Chưa có thông tin hoạt động chi tiết.') }}</p>
                @endif
            </div>
        </div>

        <!-- Sidebar Booking -->
        <div class="col-lg-4">
            <div class="premium-card booking-sidebar p-4 p-md-5 reveal-up">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="fs-1 fw-bold text-secondary">{{ format_currency($tour->base_price) }}</div>
                    <span class="ms-2 text-muted">{{ __('/ khách') }}</span>
                </div>

                <h5 class="mb-4 fw-bold text-dark fs-5">{{ __('Chọn lịch trình khởi hành') }}</h5>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4 rounded-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($tour->tour_schedules->isEmpty())
                    <div class="alert alert-warning mb-4 rounded-4">
                        {{ __('Hiện tại tour này chưa có lịch khởi hành mới. Vui lòng quay lại sau!') }}
                    </div>
                    <button type="button" class="btn btn-secondary w-100 py-3 fs-5" disabled>
                        {{ __('Tạm ngưng nhận khách') }}
                    </button>
                @else
                    <form action="{{ route('frontend.tours.checkout') }}" method="POST">
                        @csrf
                        <!-- Thanh chọn lịch trình khởi hành -->
                        <div class="d-flex overflow-auto gap-3 pb-3 mb-4" style="scrollbar-width: none;">
                            @foreach($tour->tour_schedules as $index => $schedule)
                            @php
                            \Carbon\Carbon::setLocale('vi');
                            $date = \Carbon\Carbon::parse($schedule->departure_date);
                            $dayOfWeek = ucfirst($date->translatedFormat('l'));
                            if ($dayOfWeek == 'Chủ nhật') $dayOfWeek = 'CN';
                            $dayMonth = $date->format('d/m');
                            @endphp
                            <div>
                                <input type="radio" class="btn-check" name="schedule_id" id="schedule-{{ $schedule->id }}" value="{{ $schedule->id }}" {{ $index == 0 ? 'checked' : '' }} required>
                                <label class="schedule-btn d-flex flex-column align-items-center" for="schedule-{{ $schedule->id }}">
                                    <span class="fw-bold fs-6 mb-1">{{ $dayOfWeek }}</span>
                                    <span class="small opacity-75">{{ $dayMonth }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <!-- Chọn số khách -->
                        <div class="row g-3 mb-4 pb-4 border-bottom">
                            <div class="col-6">
                                <label class="form-label mb-2 fw-600 text-dark">{{ __('Người lớn') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person-fill"></i></span>
                                    <input type="number" class="form-control search-form-control border-start-0 ps-0" name="adults" id="adults" value="1" min="1" max="10">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-2 fw-600 text-dark">{{ __('Trẻ em') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person-badge"></i></span>
                                    <input type="number" class="form-control search-form-control border-start-0 ps-0" name="children" id="children" value="0" min="0" max="10">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="text-muted fw-600">{{ __('Tổng cộng:') }}</span>
                            <span class="fs-3 fw-bold text-danger"><span id="totalPrice">{{ format_currency($tour->base_price) }}</span></span>
                        </div>

                        @auth
                        <button type="submit" class="btn btn-register-premium w-100 py-3 fs-5">
                            {{ __('Đặt Ngay Chuyến Đi') }}
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-login-premium w-100 py-3 text-center d-block bg-white text-primary" style="border: 2px solid var(--primary-color);">
                            {{ __('Đăng nhập để Đặt') }}
                        </a>
                        @endauth
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const basePriceVND = {{ $tour->base_price }};
        const currency = '{{ Session::get("currency", "VND") }}';
        
        let rate = 1;
        let symbol = ' VNĐ';
        let prefix = false;

        switch (currency) {
            case 'USD': rate = 25000; symbol = '$'; prefix = true; break;
            case 'EUR': rate = 27000; symbol = '€'; prefix = true; break;
            case 'CNY': rate = 3500; symbol = '¥'; prefix = true; break;
            case 'VND': default: rate = 1; symbol = ' VNĐ'; prefix = false; break;
        }

        const adultsInput = document.getElementById('adults');
        const childrenInput = document.getElementById('children');
        const totalPriceSpan = document.getElementById('totalPrice');

        function updateTotalPrice() {
            const adults = parseInt(adultsInput.value) || 0;
            const children = parseInt(childrenInput.value) || 0;
            const totalPersons = adults + children;
            const totalVND = basePriceVND * totalPersons;
            const convertedTotal = totalVND / rate;

            let formatted = new Intl.NumberFormat(currency === 'VND' ? 'vi-VN' : 'en-US', {
                minimumFractionDigits: currency === 'VND' ? 0 : 2,
                maximumFractionDigits: currency === 'VND' ? 0 : 2
            }).format(convertedTotal);

            if (prefix) {
                totalPriceSpan.textContent = symbol + formatted;
            } else {
                totalPriceSpan.textContent = formatted + symbol;
            }
        }

        if(adultsInput && childrenInput) {
            adultsInput.addEventListener('input', updateTotalPrice);
            childrenInput.addEventListener('input', updateTotalPrice);
        }
    });
</script>
@endsection