@extends('layouts.master')

@section('title', 'Tour trọn gói - Travel Wonder')

@section('content')

<!-- Page Header -->
<section class="hero-premium" style="height: 40vh; min-height: 400px;">
    <div class="hero-bg">
        @php
            $firstBanner = $banners->first();
            $bgImage = 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070'; // fallback
            
            if ($firstBanner && $firstBanner->image_url) {
                $bgImage = Str::startsWith($firstBanner->image_url, ['http://', 'https://']) 
                           ? $firstBanner->image_url 
                           : asset($firstBanner->image_url);
            }
        @endphp
        <img src="{{ $bgImage }}" alt="Hero Background">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">{{ __('Tour Du Lịch Trọn Gói') }}</h1>
        <p class="hero-subtitle">{{ __('Trải nghiệm dịch vụ 5 sao với giá ưu đãi tốt nhất.') }}</p>
    </div>
</section>

<!-- Search Widget -->
<div class="container search-widget-wrapper">
    <div class="glass-panel search-glass px-4 py-3">
        <form action="#" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" class="form-control search-form-control border-start-0 ps-0"
                        placeholder="{{ __('Bạn muốn đi đâu?') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">{{ __('Ngày khởi hành') }}</label>
                <input type="date" class="form-control search-form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">{{ __('Mức giá') }}</label>
                <select class="form-select search-form-control">
                    <option value="">{{ __('Tất cả mức giá') }}</option>
                    <option value="1">{{ __('Dưới 5 triệu') }}</option>
                    <option value="2">{{ __('Từ 5 - 10 triệu') }}</option>
                    <option value="3">{{ __('Trên 10 triệu') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-search-primary w-100"><i
                        class="bi bi-search me-2"></i>{{ __('Tìm kiếm') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Hot Deal Combo Tours -->
<section class="container reveal-up mb-5">
    <div class="hot-deal-section">
        <div class="hot-deal-bg"></div>
        <div class="container position-relative z-index-1">
            <div class="text-center mb-5">
                <h2 class="hot-deal-title d-flex align-items-center justify-content-center gap-3">
                    <span class="hot-deal-badge-icon">25%</span>
                    Hot deal
                    <span class="hot-deal-badge-icon">25%</span>
                </h2>
                <p class="hot-deal-subtitle mx-auto mt-3">
                    {{ __('Với sự hợp tác giảm giá ưu đãi cùng hệ thống đối tác lớn, chúng tôi tự tin mang đến cho quý khách') }}<br>
                    {{ __('combo vé máy bay và khách sạn với giá tốt nhất!') }}
                </p>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 px-3">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="filter-chip active">{{ __('Tất cả') }}</button>
                    <button class="filter-chip">{{ __('Máy bay + Khách sạn') }}</button>
                    <button class="filter-chip">{{ __('Xe + Khách sạn') }}</button>
                </div>
            </div>

            <div class="row g-4 px-3">
                @forelse($tours as $tour)
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('frontend.tours.show', $tour->slug) }}" class="text-decoration-none h-100 d-block">
                        <div class="combo-card">
                            <div class="combo-card-img-wrapper">
                                <span class="combo-badge">
                                    <span class="badge-icon">25%</span> Hot Deal
                                </span>
                                @php
                                $primaryImage = $tour->tour_images->where('is_primary', 1)->first() ?? $tour->tour_images->first();
                                @endphp
                                <img src="{{ $primaryImage ? asset($primaryImage->image_url) : 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800' }}"
                                    alt="{{ $tour->title }}">
                            </div>
                            <div class="combo-card-body">
                                <h3 class="combo-title">{{ $tour->title }}</h3>
                                <div class="combo-stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <div class="combo-specs">
                                    <div class="combo-specs-row justify-content-between">
                                        <div class="combo-specs-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <span class="text-truncate" style="max-width: 140px;">{{ $tour->destination->name ?? 'TP. Hồ Chí Minh' }}</span>
                                        </div>
                                        <div class="combo-specs-item">
                                            <i class="bi bi-car-front"></i>
                                            <span>Xe</span>
                                        </div>
                                    </div>
                                    <div class="combo-specs-item mt-1">
                                        <i class="bi bi-building"></i>
                                        <span>Khách sạn tương đương 4*</span>
                                    </div>
                                </div>
                                <div class="combo-footer">
                                    <div>
                                        <div class="combo-price-label">{{ __('Giá từ:') }}</div>
                                        <div class="combo-price-val">{{ number_format($tour->base_price, 0, ',', '.') }}đ</div>
                                    </div>
                                    <button class="btn btn-combo-detail">{{ __('Xem chi tiết') }}</button>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted text-center">{{ __('Đang cập nhật tour combo.') }}</p>
                </div>
                @endforelse
            </div>
            
            <div class="mt-5 d-flex justify-content-center">
                {{ $tours->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</section>

<!-- Ad Banners (Horizontal) -->
@if(isset($adBanners) && $adBanners->count() > 0)
<section class="container mb-5 reveal-up">
    <div class="row g-4">
        @foreach($adBanners as $ad)
        <div class="col-md-4">
            <a href="{{ $ad->target_url ?? '#' }}" class="d-block overflow-hidden rounded-4 shadow-sm" style="height: 200px;">
                @php
                    $adImgSrc = Str::startsWith($ad->image_url, ['http://', 'https://']) 
                              ? $ad->image_url 
                              : asset($ad->image_url);
                @endphp
                <img src="{{ $adImgSrc }}" alt="{{ $ad->title }}" class="w-100 h-100 object-fit-cover hover-scale" style="transition: transform 0.4s ease;">
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

<!-- Full Width Banner -->
<section class="full-width-banner position-relative reveal-up mt-0">
    <div class="banner-bg">
        <img src="{{ asset('uploads/halong.jpg') }}" alt="Halong Bay">
    </div>
    <div class="banner-overlay"></div>
    <div class="container position-relative z-index-1 banner-content text-center text-white">
        <h2 class="banner-title mb-3 fw-bold text-white">{{ __('KỲ QUAN TRÙNG ĐIỆP, TRỌN VẸN CẢM XÚC') }}</h2>
        <p class="banner-subtitle mx-auto text-white">
            {{ __('Cùng Travel Wonder lênh đênh giữa vịnh di sản, đón hoàng hôn trên du thuyền và hòa') }}<br>
            {{ __('mình vào kiệt tác thiên nhiên thế giới') }}
        </p>
    </div>
</section>

@endsection
