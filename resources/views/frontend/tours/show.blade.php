@extends('layouts.master')

@section('content')
<style>
    .custom-tabs .nav-link {
        color: #495057;
        background-color: #fff;
        transition: all 0.2s;
    }

    .custom-tabs .nav-link.active {
        color: #198754;
        background-color: #f8f9fa;
        border-left: 4px solid #198754 !important;
        font-weight: 500;
    }
</style>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#"
                    class="text-decoration-none">{{ $tour->destination->name ?? 'Điểm đến' }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $tour->title }}</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h2 class="mb-3">{{ $tour->title }}</h2>
        <div class="d-flex gap-3 text-muted mb-3">
            <span>Khởi hành từ: {{ $tour->departure_location->name ?? 'Đang cập nhật' }}</span>
            <span>Thời gian: {{ $tour->duration_days }} ngày {{ $tour->duration_nights }} đêm</span>
        </div>

        <div class="row g-2 mb-4">
            <div class="col-md-8">
                @php $primaryImage = $tour->tour_images->where('is_primary', 1)->first(); @endphp
                <img src="{{ $primaryImage ? $primaryImage->image_url : '/default-image.jpg' }}" class="w-100 rounded"
                    style="height: 400px; object-fit: cover;" alt="Ảnh chính">
            </div>
            <div class="col-md-4 d-flex flex-column gap-2">
                @foreach($tour->tour_images->where('is_primary', 0)->take(2) as $img)
                <img src="{{ $img->image_url }}" class="w-100 rounded" style="height: 196px; object-fit: cover;"
                    alt="Ảnh phụ">
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-12">
            <div class="mb-5">
                <h4 class="mb-3">Tổng quan</h4>
                <p class="text-muted lh-lg">{!! nl2br(e($tour->description)) !!}</p>
            </div>

            <div class="mb-5">
                <h4 class="mb-4">Lịch trình</h4>
                <div class="d-flex align-items-start">
                    <div class="nav flex-column nav-pills custom-tabs me-4 w-25" id="itinerary-tabs" role="tablist"
                        aria-orientation="vertical">
                        @foreach($tour->tour_itineraries as $index => $itinerary)
                        <button class="nav-link text-start p-3 mb-2 border {{ $index == 0 ? 'active' : '' }}"
                            data-bs-toggle="pill" data-bs-target="#day-{{ $itinerary->id }}" type="button" role="tab">
                            <div class="small text-muted">Ngày {{ $itinerary->day_number }}</div>
                            <div class="mt-1">{{ $itinerary->title }}</div>
                        </button>
                        @endforeach
                    </div>
                    <div class="tab-content w-75 p-4 border rounded bg-white" id="itinerary-tabContent">
                        @foreach($tour->tour_itineraries as $index => $itinerary)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="day-{{ $itinerary->id }}"
                            role="tabpanel">
                            <h5 class="mb-3">{{ $itinerary->title }}</h5>
                            <p class="text-muted">{!! nl2br(e($itinerary->description)) !!}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <h4 class="mb-4">Các hoạt động nổi bật</h4>
                @if($groupedActivities->isNotEmpty())
                <div class="d-flex align-items-start">
                    <div class="nav flex-column nav-pills custom-tabs me-4 w-25" id="activity-tabs" role="tablist"
                        aria-orientation="vertical">
                        @foreach($groupedActivities as $type => $activities)
                        <button class="nav-link text-start p-3 mb-2 border {{ $loop->first ? 'active' : '' }}"
                            data-bs-toggle="pill" data-bs-target="#activity-{{ \Illuminate\Support\Str::slug($type) }}"
                            type="button" role="tab">
                            {{ $type }}
                        </button>
                        @endforeach
                    </div>
                    <div class="tab-content w-75">
                        @foreach($groupedActivities as $type => $activities)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="activity-{{ \Illuminate\Support\Str::slug($type) }}" role="tabpanel">
                            <div class="row g-3">
                                @foreach($activities as $activity)
                                <div class="col-md-6">
                                    <div class="card h-100 border rounded shadow-sm">
                                        <img src="{{ $activity->image_url ?? '/default-activity.jpg' }}"
                                            class="card-img-top" style="height: 180px; object-fit: cover;"
                                            alt="Ảnh hoạt động">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $activity->title }}</h6>
                                            <p class="card-text text-muted small">{{ $activity->description }}</p>
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
                <p class="text-muted">Chưa có thông tin hoạt động chi tiết.</p>
                @endif
            </div>

            <div class="card border rounded shadow-sm mt-5 mb-5">
                <div class="card-body p-4">
                    <h5 class="mb-4">Chọn vé và khởi hành</h5>

                    <form action="{{ route('frontend.tours.checkout') }}" method="POST">
                        @csrf
                        <!-- 1. Thanh chọn lịch trình khởi hành -->
                        <div class="d-flex overflow-auto gap-2 pb-2 mb-4"
                            style="white-space: nowrap; scrollbar-width: none;">
                            <div class="d-flex flex-column align-items-center justify-content-center px-3 py-2 border rounded text-secondary bg-light"
                                style="min-width: 100px;">
                                <i class="bi bi-calendar3 mb-1 text-primary"></i>
                                <span class="text-primary" style="font-size: 14px;">Xem lịch</span>
                            </div>

                            @foreach($tour->tour_schedules as $index => $schedule)
                            @php
                            \Carbon\Carbon::setLocale('vi');
                            $date = \Carbon\Carbon::parse($schedule->departure_date);
                            $dayOfWeek = ucfirst($date->translatedFormat('l'));
                            if ($dayOfWeek == 'Chủ nhật') $dayOfWeek = 'CN';
                            $dayMonth = $date->format('d') . ' thg ' . $date->format('m');
                            @endphp
                            <input type="radio" class="btn-check" name="schedule_id" id="schedule-{{ $schedule->id }}"
                                value="{{ $schedule->id }}" {{ $index == 0 ? 'checked' : '' }} required>
                            <label
                                class="btn btn-outline-primary d-flex flex-column align-items-center justify-content-center px-3 py-2"
                                for="schedule-{{ $schedule->id }}" style="min-width: 90px; cursor: pointer;">
                                <span style="font-size: 14px;">{{ $dayOfWeek }}</span>
                                <span style="font-size: 14px;">{{ $dayMonth }}</span>
                            </label>
                            @endforeach
                        </div>

                        <!-- 2. Thẻ hiển thị giá và nút đặt vé -->
                        <div class="card border rounded bg-light">
                            <div
                                class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                <div>
                                    <h6 class="mb-1" style="font-size: 16px;">{{ $tour->title }}</h6>
                                    <p class="mb-0 text-muted small">Điểm xuất phát:
                                        {{ $tour->departure_location->name ?? 'Đang cập nhật' }}
                                    </p>
                                </div>

                                <div class="d-flex flex-column flex-lg-row align-items-start align-lg-center gap-3">
                                    <!-- Chọn số người lớn và trẻ em -->
                                    <div class="d-flex gap-2">
                                        <div class="d-flex flex-column align-items-center">
                                            <label class="form-label mb-2 small text-muted">Người lớn</label>
                                            <input type="number" class="form-control pax-input" name="adults"
                                                id="adults" value="1" min="1" max="10" style="width: 70px;">
                                        </div>
                                        <div class="d-flex flex-column align-items-center">
                                            <label class="form-label mb-2 small text-muted">Trẻ em</label>
                                            <input type="number" class="form-control pax-input" name="children"
                                                id="children" value="0" min="0" max="10" style="width: 70px;">
                                        </div>
                                    </div>

                                    <div class="ms-lg-3">
                                        <div class="mb-2">
                                            <span class="text-danger fw-bold" style="font-size: 22px;">
                                                <span id="totalPrice">{{ number_format($tour->base_price, 0, ',', '.') }}</span> ₫
                                            </span>
                                            <span class="text-muted small">/ tổng cộng</span>
                                        </div>

                                        @auth
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                                            ĐẶT VÉ NGAY
                                        </button>
                                        @else
                                        <a href="{{ route('login') }}" class="btn btn-secondary px-4 py-2 fw-bold">
                                            ĐĂNG NHẬP ĐỂ ĐẶT VÉ
                                        </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const basePrice = {
        !!$tour - > base_price!!
    };
    const adultsInput = document.getElementById('adults');
    const childrenInput = document.getElementById('children');
    const totalPriceSpan = document.getElementById('totalPrice');

    function updateTotalPrice() {
        const adults = parseInt(adultsInput.value) || 0;
        const children = parseInt(childrenInput.value) || 0;
        const totalPersons = adults + children;
        const total = basePrice * totalPersons;

        totalPriceSpan.textContent = new Intl.NumberFormat('vi-VN').format(total);
    }

    adultsInput.addEventListener('change', updateTotalPrice);
    childrenInput.addEventListener('change', updateTotalPrice);
</script>
@endsection