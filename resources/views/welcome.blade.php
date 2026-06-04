@extends('layouts.master')

@section('title', 'Travel Wonder - Đặt Tour & Vé Tham Quan')

@section('content')

<section class="hero-premium">
    <div class="hero-bg">
        @php
            $firstBanner = $banners->first();
            $bgImage = 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070';

            if ($firstBanner && $firstBanner->image_url) {
                $bgImage = \Illuminate\Support\Str::startsWith($firstBanner->image_url, ['http://', 'https://'])
                    ? $firstBanner->image_url
                    : asset(ltrim($firstBanner->image_url, '/'));
            }
        @endphp
        <img src="{{ $bgImage }}" alt="Hero Background">
    </div>

    <div class="hero-overlay"></div>

    <div class="hero-content">
        <h1 class="hero-title">{{ __('Khám Phá Thế Giới Cùng TravelWonder') }}</h1>
        <p class="hero-subtitle">
            {{ __('Hàng ngàn điểm đến tuyệt đẹp và trải nghiệm khó quên đang chờ đón bạn. Bắt đầu hành trình ngay hôm nay!') }}
        </p>
    </div>
</section>

<div class="container search-widget-wrapper">
    <div class="glass-panel search-glass">
        <ul class="nav nav-tabs search-tabs" id="searchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tour-tab" data-bs-toggle="tab" data-bs-target="#tour"
                    type="button" role="tab">
                    <i class="bi bi-briefcase-fill me-2"></i>{{ __('Tour Du Lịch') }}
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ticket-tab" data-bs-toggle="tab" data-bs-target="#ticket"
                    type="button" role="tab">
                    <i class="bi bi-ticket-perforated-fill me-2"></i>{{ __('Vé Tham Quan') }}
                </button>
            </li>
        </ul>

        <div class="tab-content px-3 pb-3" id="searchTabsContent">
            <div class="tab-pane fade show active" id="tour" role="tabpanel">
                <form action="{{ route('frontend.tours.search') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input type="text" name="keyword" class="form-control search-form-control border-start-0 ps-0"
                                placeholder="{{ __('Bạn muốn đi đâu?') }}" value="{{ request('keyword') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">{{ __('Ngày khởi hành') }}</label>
                        <input type="date" name="departure_date" class="form-control search-form-control" value="{{ request('departure_date') }}" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">{{ __('Số khách') }}</label>
                        <select name="guests" class="form-select search-form-control">
                            <option value="">{{ __('Chọn số khách') }}</option>
                            <option value="1" {{ request('guests') == '1' ? 'selected' : '' }}>{{ __('1 Người lớn, 0 Trẻ em') }}</option>
                            <option value="2" {{ request('guests') == '2' ? 'selected' : '' }}>{{ __('2 Người lớn, 0 Trẻ em') }}</option>
                            <option value="3" {{ request('guests') == '3' ? 'selected' : '' }}>{{ __('Gia đình') }}</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search-primary w-100">
                            <i class="bi bi-search me-2"></i>{{ __('Tìm kiếm') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="ticket" role="tabpanel">
                <form action="{{ route('frontend.tickets.search') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label class="form-label text-muted small fw-bold">
                            {{ __('Tìm công viên giải trí, sự kiện') }}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="keyword" class="form-control search-form-control border-start-0 ps-0"
                                placeholder="{{ __('Tìm kiếm hoạt động vui chơi...') }}" value="{{ request('keyword') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">{{ __('Ngày sử dụng') }}</label>
                        <input type="date" name="use_date" class="form-control search-form-control" value="{{ request('use_date') }}" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search-primary w-100">
                            {{ __('Tìm vé') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="container py-5 reveal-up">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="section-heading">{{ __('Điểm đến thịnh hành') }}</h2>
            <p class="section-subheading mb-0">{{ __('Khám phá những vùng đất được yêu thích nhất.') }}</p>
        </div>
    </div>

    <div class="row g-4">
        @forelse($destinations as $dest)
            <div class="col-6 col-md-6 col-lg-3">
                <a href="{{ route('frontend.tours.search', ['destination_id' => $dest->id]) }}" class="text-decoration-none">
                    <div class="dest-card-premium">
                        <img src="{{ $dest->image_url ?? 'https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=600' }}"
                            alt="{{ $dest->name ?? 'Điểm đến' }}">
                        <div class="dest-overlay">
                            <h5>{{ $dest->name ?? __('Điểm đến') }}</h5>
                            <span class="dest-count">100+ {{ __('Tours') }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">{{ __('Đang cập nhật điểm đến.') }}</p>
            </div>
        @endforelse
    </div>
</section>

<section class="container py-5 reveal-up">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="section-heading">{{ __('Tour Du Lịch Trọn Gói') }}</h2>
            <p class="section-subheading mb-0">{{ __('Trải nghiệm dịch vụ 5 sao với giá ưu đãi.') }}</p>
        </div>

        <a href="{{ route('frontend.tours.search') }}" class="btn-login-premium text-decoration-none d-none d-md-inline-block"
            style="color:var(--primary-color); border-color:var(--primary-color);">
            {{ __('Xem tất cả') }} <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row g-4">
        @forelse($tours as $tour)
            @php
                $tourTitle = $tour->title
                        ?? $tour->name
                        ?? $tour->tour_name
                        ?? 'Chưa có tên tour';

                if (!$tourTitle || trim($tourTitle) === '') {
                    $tourTitle = 'Chưa có tên tour';
                }

                $primaryImage = $tour->tour_images->where('is_primary', 1)->first()
                    ?? $tour->tour_images->first();

                $tourImage = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';

                if ($primaryImage && !empty($primaryImage->image_url)) {
                    if (\Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://', 'https://'])) {
                        $tourImage = $primaryImage->image_url;
                    } else {
                        $tourImage = asset(ltrim($primaryImage->image_url, '/'));
                    }
                }

                $tourSlug = $tour->slug ?? $tour->id;
                $destinationName = optional($tour->destination)->name ?: 'Việt Nam';
            @endphp

            <div class="col-12 col-md-6 col-lg-3">
                <div class="premium-card h-100 overflow-hidden">
                    <a href="{{ route('frontend.tours.show', $tourSlug) }}"
                       class="text-decoration-none d-block text-dark">

                        <div class="card-img-wrapper">
                            <span class="badge-glass">
                                <i class="bi bi-clock me-1"></i>
                                {{ $tour->duration_days ?? 0 }}N{{ $tour->duration_nights ?? 0 }}Đ
                            </span>

                            <img src="{{ $tourImage }}"
                                 class="card-img-top"
                                 alt="{{ $tourTitle }}"
                                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';">
                        </div>

                        <div class="card-body" style="display:block !important; height:auto !important; padding:20px !important;">
                            <h3 class="card-title"
                                style="display:block !important; visibility:visible !important; opacity:1 !important; color:#111827 !important; font-size:18px !important; font-weight:700 !important; line-height:1.4 !important; margin-bottom:12px !important; min-height:50px !important;">
                                {{ $tourTitle }}
                            </h3>

                            <div class="location-text">
                                <i class="bi bi-geo-alt text-danger"></i>
                                {{ $destinationName }}
                            </div>

                            <div class="price-wrap">
                                <span class="text-muted small">{{ __('Chỉ từ') }}</span>
                                <div class="price-val">
                                    {{ format_currency($tour->base_price ?? 0) }}
                                </div>
                            </div>
                        </div>
                    </a>

                    <div class="px-3 pb-3">
                        <a href="{{ route('frontend.tours.show', $tourSlug) }}"
                           class="btn btn-primary w-100">
                            {{ __('Xem chi tiết') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">{{ __('Đang cập nhật tour.') }}</p>
            </div>
        @endforelse
    </div>
</section>

@if(isset($adBanners) && $adBanners->count() > 0)
<section class="container mb-5 reveal-up">
    <div class="row g-4">
        @foreach($adBanners as $ad)
            @php
                $adImgSrc = \Illuminate\Support\Str::startsWith($ad->image_url, ['http://', 'https://'])
                    ? $ad->image_url
                    : asset(ltrim($ad->image_url, '/'));
            @endphp

            <div class="col-md-4">
                <a href="{{ $ad->target_url ?? '#' }}"
                    class="d-block overflow-hidden rounded-4 shadow-sm"
                    style="height: 200px;">
                    <img src="{{ $adImgSrc }}"
                        alt="{{ $ad->title ?? 'Banner' }}"
                        class="w-100 h-100 object-fit-cover hover-scale"
                        style="transition: transform 0.4s ease;">
                </a>
            </div>
        @endforeach
    </div>
</section>

<style>
    .hover-scale:hover {
        transform: scale(1.05);
    }
</style>
@endif

<section class="container py-5 mb-5 reveal-up">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="section-heading">{{ __('Vé Vui Chơi & Hoạt Động') }}</h2>
            <p class="section-subheading mb-0">{{ __('Giải trí không giới hạn với hàng ngàn sự kiện.') }}</p>
        </div>

        <a href="{{ route('frontend.tickets.search') }}" class="btn-login-premium text-decoration-none d-none d-md-inline-block"
            style="color:var(--primary-color); border-color:var(--primary-color);">
            {{ __('Xem tất cả') }} <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row g-4">
        @forelse($tickets as $ticket)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="premium-card">
                    <div class="card-img-wrapper">
                        <span class="badge-glass">
                            <i class="bi bi-star-fill text-warning me-1"></i>{{ __('Hot') }}
                        </span>

                        <img src="https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=800"
                            class="card-img-top"
                            alt="{{ $ticket->title ?? 'Vé tham quan' }}">
                    </div>

                    <div class="card-body">
                        <h3 class="card-title"
                            style="display:block !important; color:#111827 !important; font-size:18px !important; font-weight:700 !important; line-height:1.4 !important; margin-bottom:12px !important;">
                            {{ $ticket->title ?? $ticket->name ?? 'Chưa có tên vé' }}
                        </h3>

                        <div class="location-text">
                            <i class="bi bi-geo-alt text-danger"></i>
                            {{ $ticket->destination->name ?? 'Điểm vui chơi' }}
                        </div>

                        <div class="price-wrap">
                            <span class="text-muted small">{{ __('Từ') }}</span>
                            <div class="price-val" style="font-size: 1.1rem;">
                                {{ __('Tra cứu giá') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">{{ __('Đang cập nhật vé tham quan.') }}</p>
            </div>
        @endforelse
    </div>
</section>

@endsection
