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
        <form action="{{ route('frontend.tours.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                <select name="destination_id" class="form-select search-form-control {{ isset($filterErrors['destination_id']) ? 'is-invalid' : '' }}">
                    <option value="">{{ __('Tất cả điểm đến') }}</option>
                    @foreach($allDestinations as $dest)
                        <option value="{{ $dest->id }}" {{ request('destination_id') == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                    @endforeach
                </select>
                @if(isset($filterErrors['destination_id']))
                    <div class="text-danger small mt-1 position-absolute">{{ $filterErrors['destination_id'][0] }}</div>
                @endif
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">{{ __('Ngày khởi hành từ') }}</label>
                <input type="date" name="departure_date" class="form-control search-form-control {{ isset($filterErrors['departure_date']) ? 'is-invalid' : '' }}"
                    value="{{ request('departure_date') }}" min="{{ date('Y-m-d') }}">
                @if(isset($filterErrors['departure_date']))
                    <div class="text-danger small mt-1 position-absolute">{{ $filterErrors['departure_date'][0] }}</div>
                @endif
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">{{ __('Ngân sách') }}</label>
                <select name="budget" class="form-select search-form-control {{ isset($filterErrors['budget']) ? 'is-invalid' : '' }}">
                    <option value="all">{{ __('Tất cả mức giá') }}</option>
                    <option value="under_5m" {{ request('budget') == 'under_5m' ? 'selected' : '' }}>{{ __('Dưới 5 triệu') }}</option>
                    <option value="5m_10m" {{ request('budget') == '5m_10m' ? 'selected' : '' }}>{{ __('5 - 10 triệu') }}</option>
                    <option value="10m_20m" {{ request('budget') == '10m_20m' ? 'selected' : '' }}>{{ __('10 - 20 triệu') }}</option>
                    <option value="over_20m" {{ request('budget') == 'over_20m' ? 'selected' : '' }}>{{ __('Trên 20 triệu') }}</option>
                </select>
                @if(isset($filterErrors['budget']))
                    <div class="text-danger small mt-1 position-absolute">{{ $filterErrors['budget'][0] }}</div>
                @endif
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-search-primary w-100">
                    <i class="bi bi-search me-2"></i>{{ __('Tìm kiếm') }}
                </button>
            </div>
            
            <div class="col-12 mt-4">
                <label class="form-label text-muted small fw-bold me-3">{{ __('Xếp hạng khách sạn:') }}</label>
                <div class="d-inline-flex gap-4 flex-wrap align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hotel_stars" id="star_all" value="" {{ !request('hotel_stars') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="star_all">Tất cả</label>
                    </div>
                    @foreach([5, 4, 3, 2, 1] as $star)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hotel_stars" id="star_{{ $star }}" value="{{ $star }}" {{ request('hotel_stars') == $star ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="star_{{ $star }}">{{ $star }} Sao</label>
                    </div>
                    @endforeach
                </div>
                @if(isset($filterErrors['hotel_stars']))
                    <div class="text-danger small mt-1">{{ $filterErrors['hotel_stars'][0] }}</div>
                @endif
            </div>
        </form>

        @if(request()->hasAny(['destination_id', 'departure_date', 'budget', 'hotel_stars', 'keyword']))
        <div class="d-flex align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
            <small class="text-muted me-2">
                <i class="bi bi-funnel me-1"></i>
                {{ __('Tìm thấy') }} <strong class="text-primary">{{ $tours->total() }}</strong> {{ __('tour phù hợp') }}
            </small>
            
            @if(request('destination_id') && !isset($filterErrors['destination_id']))
                @php $dest = $allDestinations->firstWhere('id', request('destination_id')); @endphp
                @if($dest)
                <a href="{{ request()->fullUrlWithQuery(['destination_id' => null]) }}" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-geo-alt me-1"></i>{{ $dest->name }} <i class="bi bi-x"></i>
                </a>
                @endif
            @endif

            @if(request('departure_date') && !isset($filterErrors['departure_date']))
                <a href="{{ request()->fullUrlWithQuery(['departure_date' => null]) }}" class="badge bg-info bg-opacity-10 text-info text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-calendar me-1"></i>Từ {{ \Carbon\Carbon::parse(request('departure_date'))->format('d/m/Y') }} <i class="bi bi-x"></i>
                </a>
            @endif

            @if(request('hotel_stars') && !isset($filterErrors['hotel_stars']))
                <a href="{{ request()->fullUrlWithQuery(['hotel_stars' => null]) }}" class="badge bg-warning bg-opacity-10 text-warning text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-star-fill me-1"></i>Từ {{ request('hotel_stars') }} sao <i class="bi bi-x"></i>
                </a>
            @endif

            @if(request('budget') && request('budget') !== 'all' && !isset($filterErrors['budget']))
                @php
                    $budgetLabels = [
                        'under_5m' => 'Dưới 5 triệu',
                        '5m_10m' => '5 - 10 triệu',
                        '10m_20m' => '10 - 20 triệu',
                        'over_20m' => 'Trên 20 triệu',
                    ];
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['budget' => null]) }}" class="badge bg-success bg-opacity-10 text-success text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-currency-dollar me-1"></i>{{ $budgetLabels[request('budget')] ?? '' }} <i class="bi bi-x"></i>
                </a>
            @endif
            
            @if(request('keyword'))
                <a href="{{ request()->fullUrlWithQuery(['keyword' => null]) }}" class="badge bg-secondary bg-opacity-10 text-secondary text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-search me-1"></i>{{ request('keyword') }} <i class="bi bi-x"></i>
                </a>
            @endif

            <a href="{{ route('frontend.tours.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill ms-auto">
                {{ __('Xóa bộ lọc') }}
            </a>
        </div>
        <style>
            .hover-opacity:hover { opacity: 0.8; }
        </style>
        @endif
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
                <a href="{{ route('frontend.tours.search') }}" class="btn-xem-them text-decoration-none">
                    {{ __('Xem thêm') }} <i class="bi bi-arrow-right-circle-fill"></i>
                </a>
            </div>

            <div class="row g-4 px-3">
                @forelse($tours as $tour)
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('frontend.tours.show', $tour->slug) }}" class="text-decoration-none h-100 d-block">
                        <div class="combo-card">
                            <div class="combo-card-img-wrapper">

    @if($tour->duration_days && $tour->duration_nights)
    <div class="tour-duration-badge">
        {{ $tour->duration_days }}N{{ $tour->duration_nights }}Đ
    </div>
    @endif

    @auth
    @php
        $isFavorite = \App\Models\Favorite::where('user_id', auth()->id())
            ->where('tour_id', $tour->id)
            ->exists();
    @endphp

    <form action="{{ route('frontend.favorites.toggle', $tour->id) }}"
          method="POST"
          class="favorite-form"
          onclick="event.stopPropagation();">
        @csrf

        <button type="submit"
                class="favorite-btn {{ $isFavorite ? 'active' : '' }}">
            <i class="bi {{ $isFavorite ? 'bi-heart-fill' : 'bi-heart' }}"></i>
        </button>
    </form>
    @endauth

                                @php
                                $primaryImage = $tour->tour_images->where('is_primary', 1)->first() ?? $tour->tour_images->first();
                                $fallbackImage = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';
                                $destName = mb_strtolower($tour->destination->name ?? '', 'UTF-8');
                                if (str_contains($destName, 'nha trang') || str_contains($destName, 'phú quốc') || str_contains($destName, 'quy nhơn') || str_contains($destName, 'vũng tàu') || str_contains($destName, 'biển')) {
                                    $fallbackImage = 'https://images.unsplash.com/photo-1596395819057-cbcf88eb0dfb?q=80&w=800'; // beach
                                } elseif (str_contains($destName, 'hà nội') || str_contains($destName, 'sapa') || str_contains($destName, 'đà lạt') || str_contains($destName, 'mộc châu')) {
                                    $fallbackImage = 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?q=80&w=800'; // mountain/culture
                                } elseif (str_contains($destName, 'hạ long') || str_contains($destName, 'vịnh')) {
                                    $fallbackImage = 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800'; // halong bay
                                } elseif (str_contains($destName, 'đà nẵng') || str_contains($destName, 'hội an') || str_contains($destName, 'huế')) {
                                    $fallbackImage = 'https://images.unsplash.com/photo-1555921015-c262060f5899?q=80&w=800'; // hoi an/danang
                                } elseif (str_contains($destName, 'hồ chí minh') || str_contains($destName, 'sài gòn')) {
                                    $fallbackImage = 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?q=80&w=800'; // HCM
                                }
                                @endphp
                                <img src="{{ $primaryImage ? asset($primaryImage->image_url) : $fallbackImage }}"
                                    alt="{{ $tour->title }}">
                            </div>
                            <div class="combo-card-body">
                                <h3 class="combo-title" style="font-size: 1.05rem;">{{ $tour->title }}</h3>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="combo-stars" style="font-size: 0.9rem;">
                                        @php $stars = $tour->hotel_stars ?? 0; @endphp
                                        @for($i=1; $i<=5; $i++)
                                            <i class="bi bi-star-fill {{ $i <= $stars ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                        @endfor
                                    </div>
                                    @if(isset($tour->avg_rating) && $tour->review_count > 0)
                                        <span class="badge bg-success" style="font-size: 0.75rem;">{{ $tour->avg_rating }} <i class="bi bi-star-fill"></i></span>
                                        <small class="text-muted" style="font-size: 0.75rem;">({{ $tour->review_count }} đánh giá)</small>
                                    @endif
                                </div>
                                <div class="combo-location d-flex align-items-center gap-3">
                                    <div class="text-truncate">
                                        <i class="bi bi-geo-alt text-danger"></i>
                                        <span class="text-muted">{{ $tour->destination->name ?? 'Không xác định' }}</span>
                                    </div>
                                    @if(isset($tour->seats_left))
                                    <div class="text-nowrap" style="font-size: 0.85rem;">
                                        <i class="bi bi-person-check text-success"></i>
                                        <span class="text-success fw-bold">{{ $tour->seats_left }}</span> <span class="text-muted">chỗ</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="combo-footer mt-3 border-top pt-3">
                                    <div>
                                        <div class="combo-price-label">{{ __('Giá từ:') }}</div>
                                        <div class="combo-price-val">{{ number_format($tour->base_price, 0, ',', '.') }}đ<span class="text-muted fw-normal" style="font-size: 0.8rem;">/người lớn</span></div>
                                    </div>
                                    <span class="btn btn-combo-detail">{{ __('Xem chi tiết') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="bi bi-search" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted">{{ __('Không tìm thấy tour phù hợp với bộ lọc của bạn') }}</h5>
                    <p class="text-muted">{{ __('Gợi ý: Thử bỏ bớt điều kiện lọc để xem thêm tour.') }}</p>
                    <a href="{{ route('frontend.tours.index') }}" class="btn btn-primary rounded-pill mt-2">
                        <i class="bi bi-arrow-repeat me-1"></i>{{ __('Xem tất cả tour') }}
                    </a>
                </div>
                @endforelse
            </div>
            
            <div class="d-flex justify-content-center mt-5">
                {{ $tours->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</section>

<!-- Ad Banners (Horizontal) -->
@if(isset($adBanners) && $adBanners->count() > 0)
<section class="container mb-5 reveal-up">
    <div class="position-relative">
        <!-- Navigation Buttons -->
        @if($adBanners->count() > 2)
        <button class="banner-nav-btn banner-nav-prev" type="button" onclick="scrollBanners('prev')">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="banner-nav-btn banner-nav-next" type="button" onclick="scrollBanners('next')">
            <i class="bi bi-chevron-right"></i>
        </button>
        @endif

        <!-- Banners Container -->
        <div class="banner-scroll-container" id="bannerScrollContainer">
            <div class="banner-scroll-wrapper">
                @foreach($adBanners as $ad)
                    @php
                        $adImgSrc = Str::startsWith($ad->image_url, ['http://', 'https://'])
                              ? $ad->image_url
                              : asset($ad->image_url);
                    @endphp

                    <div class="banner-scroll-item">
                        <a href="{{ $ad->target_url ?? '#' }}"
                            class="d-block overflow-hidden rounded-4 shadow-sm position-relative banner-card"
                            style="height: 200px;">
                            <img src="{{ $adImgSrc }}"
                                alt="{{ $ad->title }}"
                                class="w-100 h-100 object-fit-cover hover-scale"
                                style="transition: transform 0.4s ease;">
                            
                            @if($ad->coupon && $ad->coupon->valid_until >= now())
                                <div class="position-absolute top-0 start-0 m-3">
                                    <div class="coupon-badge bg-danger text-white px-3 py-2 rounded-3 shadow-lg">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-ticket-perforated-fill"></i>
                                            <div>
                                                <div class="fw-bold" style="font-size: 0.75rem;">MÃ: {{ $ad->coupon->code }}</div>
                                                <div style="font-size: 0.7rem;">
                                                    @if($ad->coupon->discount_type === 'percentage')
                                                        Giảm {{ $ad->coupon->discount_value }}%
                                                    @else
                                                        Giảm {{ format_currency($ad->coupon->discount_value) }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<style>
    .banner-scroll-container {
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .banner-scroll-container::-webkit-scrollbar {
        display: none;
    }

    .banner-scroll-wrapper {
        display: flex;
        gap: 1.5rem;
        padding: 0.5rem 0;
    }

    .banner-scroll-item {
        flex: 0 0 calc(50% - 0.75rem);
        min-width: 300px;
    }

    @media (min-width: 768px) {
        .banner-scroll-item {
            flex: 0 0 calc(33.333% - 1rem);
        }
    }

    @media (min-width: 1200px) {
        .banner-scroll-item {
            flex: 0 0 calc(25% - 1.125rem);
        }
    }

    .banner-card {
        display: block;
        transition: all 0.3s ease;
    }

    .banner-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
    }

    .hover-scale:hover {
        transform: scale(1.05);
    }
    
    .coupon-badge {
        backdrop-filter: blur(10px);
        animation: pulse-subtle 2s ease-in-out infinite;
        z-index: 2;
    }
    
    @keyframes pulse-subtle {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    /* Navigation Buttons */
    .banner-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: white;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
        font-size: 1.25rem;
    }

    .banner-nav-btn:hover {
        background: var(--primary-color, #007CE8);
        color: white;
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .banner-nav-prev {
        left: -20px;
    }

    .banner-nav-next {
        right: -20px;
    }

    @media (max-width: 768px) {
        .banner-nav-btn {
            width: 38px;
            height: 38px;
            font-size: 1rem;
        }

        .banner-nav-prev {
            left: -10px;
        }

        .banner-nav-next {
            right: -10px;
        }

        .banner-scroll-item {
            flex: 0 0 calc(80% - 0.75rem);
            min-width: 280px;
        }
    }
</style>

<script>
function scrollBanners(direction) {
    const container = document.getElementById('bannerScrollContainer');
    const items = container.querySelectorAll('.banner-scroll-item');
    
    if (items.length === 0) return;
    
    // Lấy chiều rộng của 1 banner item + gap
    const itemWidth = items[0].offsetWidth;
    const gap = 24; // 1.5rem = 24px
    const scrollAmount = itemWidth + gap;
    
    if (direction === 'prev') {
        container.scrollLeft -= scrollAmount;
    } else {
        container.scrollLeft += scrollAmount;
    }
}

// Auto-scroll on swipe for mobile
let startX = 0;
const container = document.getElementById('bannerScrollContainer');

if (container) {
    container.addEventListener('touchstart', (e) => {
        startX = e.touches[0].pageX;
    });

    container.addEventListener('touchend', (e) => {
        const endX = e.changedTouches[0].pageX;
        const diff = startX - endX;
        
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                scrollBanners('next');
            } else {
                scrollBanners('prev');
            }
        }
    });
}
</script>
@endif

<!-- Full Width Banner -->
<section class="full-width-banner position-relative reveal-up mt-0">
    <div class="banner-bg">
        <img src="https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=1920&auto=format&fit=crop" alt="Halong Bay">
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
<style>
.combo-card-img-wrapper {
    position: relative;
}

.tour-duration-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    z-index: 10;
    background: rgba(255, 255, 255, 0.95);
    color: #1e3a5f;
    font-weight: 700;
    font-size: 0.875rem;
    padding: 6px 12px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.favorite-form {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 9999;
    margin: 0;
}

.favorite-btn {
    width: 46px;
    height: 46px;
    border: none;
    border-radius: 50%;
    background: #ffffff;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 18px rgba(0,0,0,.15);
    cursor: pointer;
    transition: all 0.3s ease;
}

.favorite-btn i {
    font-size: 22px;
    line-height: 1;
}

.favorite-btn:hover {
    transform: scale(1.08);
}

.favorite-btn.active {
    color: #ff3366;
}

.favorite-btn.active i {
    color: #ff3366;
}
</style>
@endsection
