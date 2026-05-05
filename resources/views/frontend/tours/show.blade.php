@extends('layouts.master')

@section('content')
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
        <div class="col-lg-8">
            <div class="mb-5">
                <h4 class="mb-3">Tổng quan</h4>
                <p class="text-muted lh-lg">{!! nl2br(e($tour->description)) !!}</p>
            </div>

            <div class="mb-5">
                <h4 class="mb-4">Lịch trình</h4>
                <div class="d-flex align-items-start">
                    <div class="nav flex-column nav-pills me-4 w-25" id="itinerary-tabs" role="tablist"
                        aria-orientation="vertical">
                        @foreach($tour->tour_itineraries as $index => $itinerary)
                        <button
                            class="nav-link text-start p-3 mb-2 border {{ $index == 0 ? 'active text-success bg-light' : 'text-dark bg-white' }}"
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
                    <div class="nav flex-column nav-pills me-4 w-25" id="activity-tabs" role="tablist"
                        aria-orientation="vertical">
                        @foreach($groupedActivities as $type => $activities)
                        <button
                            class="nav-link text-start p-3 mb-2 border {{ $loop->first ? 'active text-success bg-light' : 'text-dark bg-white' }}"
                            data-bs-toggle="pill" data-bs-target="#activity-{{ Str::slug($type) }}" type="button"
                            role="tab">
                            {{ $type }}
                        </button>
                        @endforeach
                    </div>
                    <div class="tab-content w-75">
                        @foreach($groupedActivities as $type => $activities)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="activity-{{ Str::slug($type) }}" role="tabpanel">
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
        </div>

        <div class="col-lg-4">
            <div class="card border rounded shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <span class="text-muted small">Giá từ</span>
                        <h3 class="text-danger mb-0">{{ number_format($tour->base_price, 0, ',', '.') }} ₫</h3>
                    </div>

                    <form action="#" method="GET">
                        <div class="mb-3">
                            <label class="form-label text-muted">Chọn ngày khởi hành</label>
                            <select class="form-select" name="schedule_id" required>
                                <option value="">-- Lịch trình có sẵn --</option>
                                @foreach($tour->tour_schedules as $schedule)
                                <option value="{{ $schedule->id }}">
                                    {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}
                                    (Còn {{ $schedule->available_seats }} chỗ)
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted">Số lượng khách</label>
                            <input type="number" class="form-control" name="guests" value="1" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Đặt ngay</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection