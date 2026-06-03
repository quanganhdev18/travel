@extends('layouts.master')

@php
    $tourTitle = $tour->title ?? $tour->name ?? $tour->tour_name ?? 'Chi tiết tour';

    if (!$tourTitle || trim($tourTitle) === '') {
        $tourTitle = 'Chi tiết tour';
    }
@endphp

@section('title', $tourTitle)

@section('content')
<style>
    .gallery-main {
        height: 500px;
        object-fit: cover;
        border-radius: 24px;
        transition: var(--transition-slow);
        background: #eef2f7;
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
        background: #eef2f7;
    }

    .gallery-sub:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-hover);
    }

    .overflow-hidden-rounded {
        overflow: hidden;
        border-radius: 24px;
    }

    .tour-detail-wrapper {
        background: #f6f8fb;
    }

    .tour-title-detail {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        font-size: 32px;
        font-weight: 800;
        color: var(--dark-blue);
        line-height: 1.3;
    }

    .tour-info-line {
        color: var(--text-muted);
        font-weight: 500;
    }

    .tour-content-text {
        color: var(--text-muted);
        line-height: 1.8;
        font-size: 16px;
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

    .booking-price {
        font-size: 34px;
        font-weight: 800;
        color: var(--dark-blue);
    }

    .booking-total {
        font-size: 28px;
        font-weight: 800;
        color: #dc3545;
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

    .activity-simple-item {
        padding: 18px;
        border-radius: 18px;
        background: #fff;
        border: 1px solid #eef2f7;
        margin-bottom: 14px;
    }

    @media (max-width: 768px) {
        .gallery-main {
            height: 280px;
        }

        .gallery-sub {
            height: 160px;
        }

        .tour-title-detail {
            font-size: 24px;
        }

        .booking-sidebar {
            position: static;
        }
    }
</style>

@php
    $fallbackImage = asset('uploads/images/no-image.jpg');

    $tourImages = $tour->tour_images ?? collect();

    $primaryImage = $tourImages->where('is_primary', 1)->first()
        ?? $tourImages->first();

    if ($primaryImage && !empty($primaryImage->image_url)) {
        $mainImage = \Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://', 'https://'])
            ? $primaryImage->image_url
            : asset(ltrim($primaryImage->image_url, '/'));
    } else {
        $mainImage = $fallbackImage;
    }

    $subImageList = $tourImages
        ->where('id', '!=', optional($primaryImage)->id)
        ->take(2)
        ->map(function ($img) use ($fallbackImage) {
            if (!$img || empty($img->image_url)) {
                return $fallbackImage;
            }

            return \Illuminate\Support\Str::startsWith($img->image_url, ['http://', 'https://'])
                ? $img->image_url
                : asset(ltrim($img->image_url, '/'));
        })
        ->values();

    $destinationName = $tour->destination->name ?? 'Điểm đến';
    $departureName = $tour->departure_location->name ?? 'Đang cập nhật';
@endphp

<div class="tour-detail-wrapper">
    <div class="container py-5">

        <nav aria-label="breadcrumb" class="mb-4 reveal-up">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none text-primary fw-500">
                        Trang chủ
                    </a>
                </li>

                <li class="breadcrumb-item">
                    <span class="text-muted">
                        {{ $destinationName }}
                    </span>
                </li>

                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                    {{ $tourTitle }}
                </li>
            </ol>
        </nav>

        <div class="mb-5 reveal-up" style="transition-delay: 0.1s;">
            <h1 class="tour-title-detail mb-3">
                {{ $tourTitle }}
            </h1>

            <div class="d-flex flex-wrap gap-4 tour-info-line mb-4">
                <span class="d-flex align-items-center">
                    <i class="bi bi-geo-alt fs-5 me-2 text-danger"></i>
                    Khởi hành từ:
                    <strong class="ms-1 text-dark">
                        {{ $departureName }}
                    </strong>
                </span>

                <span class="d-flex align-items-center">
                    <i class="bi bi-clock fs-5 me-2 text-warning"></i>
                    {{ $tour->duration_days ?? 0 }} ngày {{ $tour->duration_nights ?? 0 }} đêm
                </span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <div class="overflow-hidden-rounded">
                        <img src="{{ $mainImage }}"
                             class="w-100 gallery-main"
                             alt="{{ $tourTitle }}"
                             onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                    </div>
                </div>

                <div class="col-md-4 d-flex flex-column gap-3">
                    @forelse($subImageList as $subImageUrl)
                        <div class="overflow-hidden-rounded h-50">
                            <img src="{{ $subImageUrl }}"
                                 class="w-100 gallery-sub"
                                 alt="{{ $tourTitle }}"
                                 onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                        </div>
                    @empty
                        <div class="alert alert-warning mb-0">
                            Tour này chưa có ảnh phụ trong database.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-lg-8">

                <div class="glass-panel p-4 p-md-5 mb-5 reveal-up">
                    <h3 class="section-heading mb-4 text-dark fs-4">
                        Tổng quan
                    </h3>

                    <p class="tour-content-text mb-0">
                        {!! nl2br(e($tour->description ?? 'Đang cập nhật mô tả tour.')) !!}
                    </p>
                </div>

                <div class="glass-panel p-4 p-md-5 mb-5 reveal-up">
                    <h3 class="section-heading mb-4 text-dark fs-4">
                        Lịch trình chi tiết
                    </h3>

                    @if($tour->tour_itineraries->count())
                        <div class="d-flex flex-column flex-md-row align-items-start gap-4">
                            <div class="nav flex-column nav-pills tour-nav-tabs w-100"
                                 style="max-width: 260px;"
                                 id="itinerary-tabs"
                                 role="tablist"
                                 aria-orientation="vertical">

                                @foreach($tour->tour_itineraries as $index => $itinerary)
                                    <button class="nav-link mb-2 {{ $index == 0 ? 'active' : '' }}"
                                            data-bs-toggle="pill"
                                            data-bs-target="#day-{{ $itinerary->id }}"
                                            type="button"
                                            role="tab">
                                        <div class="small text-uppercase mb-1" style="opacity: 0.8; letter-spacing: 1px;">
                                            Ngày {{ $itinerary->day_number }}
                                        </div>

                                        <div class="fw-bold">
                                            {{ $itinerary->title }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>

                            <div class="tab-content w-100 p-4 rounded-4 bg-white shadow-sm border-0"
                                 id="itinerary-tabContent">

                                @foreach($tour->tour_itineraries as $index => $itinerary)
                                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                         id="day-{{ $itinerary->id }}"
                                         role="tabpanel">

                                        <h4 class="mb-4 fw-bold text-dark">
                                            Ngày {{ $itinerary->day_number }}:
                                            {{ $itinerary->title }}
                                        </h4>

                                        <p class="tour-content-text">
                                            {!! nl2br(e($itinerary->description ?? 'Đang cập nhật lịch trình.')) !!}
                                        </p>

                                        @if($itinerary->activities && $itinerary->activities->count())
                                            <ul class="mt-3">
                                                @foreach($itinerary->activities as $activity)
                                                    <li class="tour-content-text mb-2">
                                                        <strong>{{ $activity->title }}:</strong>
                                                        {{ $activity->description }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">
                            Chưa có lịch trình chi tiết.
                        </p>
                    @endif
                </div>

                <div class="glass-panel p-4 p-md-5 mb-5 reveal-up">
                    <h3 class="section-heading mb-4 text-dark fs-4">
                        Các hoạt động nổi bật
                    </h3>

                    @if(isset($groupedActivities) && $groupedActivities->isNotEmpty())
                        @foreach($groupedActivities as $type => $activities)
                            <div class="mb-4">
                                <h5 class="fw-bold text-dark mb-3">
                                    {{ $type ?: 'Hoạt động' }}
                                </h5>

                                @foreach($activities as $activity)
                                    <div class="activity-simple-item">
                                        <div class="fw-bold text-dark mb-1">
                                            {{ $activity->title }}
                                        </div>

                                        <div class="text-muted small lh-lg">
                                            {{ $activity->description }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">
                            Chưa có thông tin hoạt động chi tiết.
                        </p>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card booking-sidebar p-4 p-md-5 reveal-up">
                    <div class="d-flex flex-column mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-end mb-2">
                            <div class="booking-price">
                                {{ format_currency($tour->base_price ?? 0) }}
                            </div>
                            <span class="ms-2 text-muted mb-2">
                                / người lớn
                            </span>
                        </div>
                        <div class="d-flex align-items-end">
                            <div class="fs-5 fw-bold text-info">
                                {{ format_currency($tour->child_price ?? (($tour->base_price ?? 0) * 0.75)) }}
                            </div>
                            <span class="ms-2 text-muted">
                                / trẻ em
                            </span>
                        </div>
                    </div>

                    <h5 class="mb-4 fw-bold text-dark fs-5">
                        Chọn lịch trình khởi hành
                    </h5>

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
                            Hiện tại tour này chưa có lịch khởi hành mới. Vui lòng quay lại sau!
                        </div>

                        <button type="button" class="btn btn-secondary w-100 py-3 fs-5" disabled>
                            Tạm ngưng nhận khách
                        </button>
                    @else
                        <form action="{{ route('frontend.tours.checkout') }}" method="POST">
                            @csrf

<style>
    .schedule-card-label {
        border: 2px solid #e9ecef;
        border-radius: 14px;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        background-color: #fff;
    }
    .schedule-card-label:hover {
        border-color: #b8daff;
        background-color: #f8fbff;
    }
    .btn-check:checked + .schedule-card-label {
        border-color: var(--primary-color);
        background-color: rgba(0, 124, 232, 0.04);
        box-shadow: 0 4px 15px rgba(0, 124, 232, 0.12);
    }
    .btn-check:disabled + .schedule-card-label {
        opacity: 0.7;
        cursor: not-allowed;
        background-color: #f8f9fa;
    }
    .schedule-wrapper {
        max-height: 340px;
        overflow-y: auto;
        padding-right: 6px;
    }
    /* Custom scrollbar */
    .schedule-wrapper::-webkit-scrollbar {
        width: 6px;
    }
    .schedule-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 10px;
    }
    .schedule-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1; 
        border-radius: 10px;
    }
    .schedule-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8; 
    }
</style>

                            <div class="schedule-wrapper d-flex flex-column gap-3 mb-4 pb-4 border-bottom">
                                @php
                                    $hasAvailableSchedule = false;
                                @endphp
                                @foreach($tour->tour_schedules as $index => $schedule)
                                    @php
                                        \Carbon\Carbon::setLocale('vi');
                                        $date = \Carbon\Carbon::parse($schedule->departure_date);
                                        $dayOfWeek = ucfirst($date->translatedFormat('l'));
                                        $formattedDate = $date->format('d/m/Y');
                                        
                                        $isFull = $schedule->available_seats <= 0;
                                        $seatClass = $schedule->available_seats < 5 ? 'text-danger' : 'text-success';
                                        
                                        if (!$isFull && !$hasAvailableSchedule) {
                                            $isChecked = true;
                                            $hasAvailableSchedule = true;
                                        } else {
                                            $isChecked = false;
                                        }
                                    @endphp

                                    <div class="position-relative">
                                        <input type="radio"
                                               class="btn-check"
                                               name="schedule_id"
                                               id="schedule-{{ $schedule->id }}"
                                               value="{{ $schedule->id }}"
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $isFull ? 'disabled' : '' }}
                                               required>

                                        <label class="schedule-card-label w-100 p-3 m-0 d-flex justify-content-between align-items-center"
                                               for="schedule-{{ $schedule->id }}">
                                            
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 text-center me-3 border" style="min-width: 65px;">
                                                    <div class="fw-bold fs-4 text-primary lh-1">{{ $date->format('d') }}</div>
                                                    <div class="small text-muted mt-1 fw-500">Thg {{ $date->format('m') }}</div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">{{ $dayOfWeek }}</div>
                                                    <div class="small text-muted mt-1"><i class="bi bi-calendar2-check text-primary me-1"></i> Khởi hành: {{ $formattedDate }}</div>
                                                </div>
                                            </div>

                                            <div class="text-end ms-2">
                                                @if($isFull)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Hết chỗ</span>
                                                @else
                                                    <div class="small text-muted mb-1">Số chỗ trống</div>
                                                    <span class="badge bg-light text-dark border px-2 py-1 fs-6"><span class="{{ $seatClass }} fw-bold">{{ $schedule->available_seats }}</span></span>
                                                @endif
                                            </div>
                                        </label>
                                        
                                        @if($isFull)
                                            <div class="position-absolute top-0 start-0 w-100 h-100 rounded-3" style="background: rgba(255,255,255,0.5); z-index: 1;"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="row g-3 mb-4 pb-4 border-bottom">
                                <div class="col-6">
                                    <label class="form-label mb-2 fw-600 text-dark">
                                        Người lớn
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted">
                                            <i class="bi bi-person-fill"></i>
                                        </span>

                                        <input type="number"
                                               class="form-control search-form-control border-start-0 ps-0"
                                               name="adults"
                                               id="adults"
                                               value="1"
                                               min="1"
                                               max="10">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="form-label mb-2 fw-600 text-dark">
                                        Trẻ em
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted">
                                            <i class="bi bi-person-badge"></i>
                                        </span>

                                        <input type="number"
                                               class="form-control search-form-control border-start-0 ps-0"
                                               name="children"
                                               id="children"
                                               value="0"
                                               min="0"
                                               max="10">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="text-muted fw-600">
                                    Tổng cộng:
                                </span>

                                <span class="booking-total" id="totalPrice">
                                    {{ format_currency($tour->base_price ?? 0) }}
                                </span>
                            </div>

                            @auth
                                <button type="submit" class="btn btn-register-premium w-100 py-3 fs-5">
                                    Đặt Ngay Chuyến Đi
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                   class="btn btn-login-premium w-100 py-3 text-center d-block bg-white text-primary"
                                   style="border: 2px solid var(--primary-color);">
                                    Đăng nhập để Đặt
                                </a>
                            @endauth
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const basePriceVND = {{ $tour->base_price ?? 0 }};
        const childPriceVND = {{ $tour->child_price ?? (($tour->base_price ?? 0) * 0.75) }};
        const currency = '{{ Session::get("currency", "VND") }}';

        let rate = 1;
        let symbol = ' VNĐ';
        let prefix = false;

        switch (currency) {
            case 'USD':
                rate = 25000;
                symbol = '$';
                prefix = true;
                break;

            case 'EUR':
                rate = 27000;
                symbol = '€';
                prefix = true;
                break;

            case 'CNY':
                rate = 3500;
                symbol = '¥';
                prefix = true;
                break;

            case 'VND':
            default:
                rate = 1;
                symbol = ' VNĐ';
                prefix = false;
                break;
        }

        const adultsInput = document.getElementById('adults');
        const childrenInput = document.getElementById('children');
        const totalPriceSpan = document.getElementById('totalPrice');

        function updateTotalPrice() {
            if (!adultsInput || !childrenInput || !totalPriceSpan) {
                return;
            }

            const adults = parseInt(adultsInput.value) || 0;
            const children = parseInt(childrenInput.value) || 0;
            const totalVND = (basePriceVND * adults) + (childPriceVND * children);
            const convertedTotal = totalVND / rate;

            let formatted = new Intl.NumberFormat(currency === 'VND' ? 'vi-VN' : 'en-US', {
                minimumFractionDigits: currency === 'VND' ? 0 : 2,
                maximumFractionDigits: currency === 'VND' ? 0 : 2
            }).format(convertedTotal);

            totalPriceSpan.textContent = prefix ? symbol + formatted : formatted + symbol;
        }

        if (adultsInput && childrenInput) {
            adultsInput.addEventListener('input', updateTotalPrice);
            childrenInput.addEventListener('input', updateTotalPrice);
        }
    });
</script>
@endsection
