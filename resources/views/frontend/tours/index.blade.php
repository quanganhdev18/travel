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
                    value="{{ request('departure_date') ?? request('date') }}" min="{{ date('Y-m-d') }}">
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
            

        </form>

        @if(request()->hasAny(['destination_id', 'departure_date', 'date', 'budget', 'keyword']))
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

            @if((request('departure_date') || request('date')) && !isset($filterErrors['departure_date']))
                @php
                    $selectedDate = request('departure_date') ?? request('date');
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['departure_date' => null, 'date' => null]) }}" class="badge bg-info bg-opacity-10 text-info text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-calendar me-1"></i>Từ {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }} <i class="bi bi-x"></i>
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
                 
                    Hot deal
                   
                </h2>
               
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 px-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('frontend.tours.index', array_merge(request()->except('category_id', 'page'), [])) }}"
                       class="filter-chip text-decoration-none {{ !request('category_id') || request('category_id') === 'all' ? 'active' : '' }}">
                        {{ __('Tất cả') }}
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('frontend.tours.index', array_merge(request()->except('category_id', 'page'), ['category_id' => $cat->id])) }}"
                       class="filter-chip text-decoration-none {{ request('category_id') == $cat->id ? 'active' : '' }}">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="row g-4 px-3">
                @forelse($tours as $tour)
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="tour-preview-wrapper h-100"
                         x-data="{ showPreview: false }"
                         @mouseenter="showPreview = true"
                         @mouseleave="showPreview = false">
                        
                        <a href="{{ route('frontend.tours.show', $tour->slug) }}" 
                           class="text-decoration-none h-100 d-block"
                           @mouseenter.stop>
                            <div class="combo-card h-100">
                            <div class="combo-card-img-wrapper">

    @if($tour->duration_days)
    <div class="tour-duration-badge">
        {{ $tour->duration_days }}N{{ $tour->duration_nights > 0 ? $tour->duration_nights . 'Đ' : '' }}
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
                                    $destinationName = optional($tour->destination)->name ?: 'Việt Nam';
                                    $stars = $tour->hotel_stars ?? 4;
                                @endphp
                                <img src="{{ $tourImage }}"
                                     alt="{{ $tour->title }}"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';">
                            </div>
                            <div class="combo-card-body">
                                <h3 class="combo-title">{{ $tour->title }}</h3>
                                <div class="combo-stars">
                                    @for($i = 1; $i <= $stars; $i++)
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @endfor
                                </div>
                                <div class="combo-location">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $destinationName }}</span>
                                </div>
                                <div class="combo-footer">
                                    <div>
                                        <div class="combo-price-label">{{ __('Giá từ:') }}</div>
                                        <div class="combo-price-val">{{ format_currency($tour->base_price ?? 0) }}</div>
                                    </div>
                                    <span class="btn btn-combo-detail">{{ __('Xem chi tiết') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Tour Preview Component -->
                    <x-tour-preview :tour="$tour" />
                    </div>
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
                {{ $tours->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>
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

/* Tour Preview Overlay Styles */
.tour-preview-wrapper {
    position: relative;
    cursor: pointer;
}

.tour-preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10;
    background: linear-gradient(to bottom, 
                rgba(0, 0, 0, 0.3) 0%, 
                rgba(0, 0, 0, 0.5) 100%);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.tour-preview-content {
    width: 100%;
    height: 100%;
    padding: 20px;
    color: #ffffff;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.tour-preview-content h5 {
    color: #ffffff !important;
    font-size: 0.95rem;
    line-height: 1.3;
    margin-bottom: 12px;
    font-weight: 700;
}

.tour-preview-content .badge {
    font-size: 0.65rem;
    padding: 4px 8px;
    white-space: nowrap;
}

.tour-preview-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.preview-item {
    display: flex;
    gap: 10px;
    align-items: start;
}

.preview-item i {
    font-size: 1rem;
    margin-top: 2px;
    flex-shrink: 0;
    color: #4ade80;
    opacity: 0.95;
}

.preview-item small {
    font-size: 0.65rem;
    line-height: 1.2;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    font-weight: 600;
}

.preview-item strong {
    font-size: 0.85rem;
    line-height: 1.3;
    color: #ffffff;
    font-weight: 600;
}

.preview-item .text-success {
    color: #4ade80 !important;
    font-size: 0.75rem !important;
    font-weight: 500 !important;
}

.preview-item.border-top {
    border-top: 1px solid rgba(255, 255, 255, 0.15) !important;
    padding-top: 8px;
    margin-top: 4px;
}

/* Adjust combo card */
.combo-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
}

.tour-preview-wrapper:hover .combo-card {
    transform: scale(1.03);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
}

/* Button styling in overlay */
.tour-preview-content .btn-primary {
    background: linear-gradient(135deg, #4ade80 0%, #3b82f6 100%);
    color: #ffffff;
    border: none;
    font-weight: 700;
    font-size: 0.85rem;
    padding: 10px 16px;
    transition: all 0.3s ease;
    margin-top: 8px;
    box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3);
    border-radius: 8px;
}

.tour-preview-content .btn-primary:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #4ade80 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 222, 128, 0.5);
}

/* Price styling */
.preview-item .fs-5 {
    font-size: 1.15rem !important;
    color: #4ade80 !important;
    font-weight: 800;
    text-shadow: 0 2px 8px rgba(74, 222, 128, 0.3);
}

/* Smooth badge styling */
.bg-primary-subtle {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Scrollbar for overlay content */
.tour-preview-content::-webkit-scrollbar {
    width: 3px;
}

.tour-preview-content::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 2px;
}

.tour-preview-content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

.tour-preview-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}

/* Icon colors for different types */
.preview-item:nth-child(1) i { color: #f59e0b; } /* Destination - amber */
.preview-item:nth-child(2) i { color: #3b82f6; } /* Duration - blue */
.preview-item:nth-child(3) i { color: #8b5cf6; } /* Departure - purple */
.preview-item:nth-child(4) i { color: #ec4899; } /* Schedule - pink */
.preview-item.border-top i { color: #4ade80; } /* Price - green */

/* Mobile adjustments */
@media (max-width: 767.98px) {
    .tour-preview-content {
        padding: 16px;
    }
    
    .tour-preview-content h5 {
        font-size: 0.9rem;
    }
    
    .preview-item {
        gap: 8px;
    }
    
    .preview-item i {
        font-size: 0.95rem;
    }
    
    .preview-item strong {
        font-size: 0.8rem;
    }
    
    .preview-item .fs-5 {
        font-size: 1rem !important;
    }
}
</style>
@endsection
