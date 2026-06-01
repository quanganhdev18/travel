@extends('layouts.master')

@section('title', 'Tìm kiếm Tour - Travel Wonder')

@section('content')

<!-- Hero Section -->
<section class="hero-premium">
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
        <h1 class="hero-title">{{ __('Khám Phá Thế Giới Cùng TravelWonder') }}</h1>
        <p class="hero-subtitle">{{ __('Hàng ngàn điểm đến tuyệt đẹp và trải nghiệm khó quên đang chờ đón bạn. Bắt đầu hành trình ngay hôm nay!') }}</p>
    </div>
</section>

<!-- Search Widget -->
<div class="container search-widget-wrapper">
    <div class="glass-panel search-glass">
        <ul class="nav nav-tabs search-tabs" id="searchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tour-tab" data-bs-toggle="tab" data-bs-target="#tour"
                    type="button" role="tab"><i class="bi bi-briefcase-fill me-2"></i>{{ __('Tour Du Lịch') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ticket-tab" data-bs-toggle="tab" data-bs-target="#ticket" type="button"
                    role="tab"><i class="bi bi-ticket-perforated-fill me-2"></i>{{ __('Vé Tham Quan') }}</button>
            </li>
        </ul>
        <div class="tab-content px-3 pb-3" id="searchTabsContent">
            <div class="tab-pane fade show active" id="tour" role="tabpanel">
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
                        <label class="form-label text-muted small fw-bold">{{ __('Số khách') }}</label>
                        <select class="form-select search-form-control">
                            <option value="1">{{ __('1 Người lớn, 0 Trẻ em') }}</option>
                            <option value="2">{{ __('2 Người lớn, 0 Trẻ em') }}</option>
                            <option value="3">{{ __('Gia đình') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search-primary w-100"><i
                                class="bi bi-search me-2"></i>{{ __('Tìm kiếm') }}</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="ticket" role="tabpanel">
                <form action="#" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label class="form-label text-muted small fw-bold">{{ __('Tìm công viên giải trí, sự kiện') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control search-form-control border-start-0 ps-0"
                                placeholder="{{ __('Tìm kiếm hoạt động vui chơi...') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">{{ __('Ngày sử dụng') }}</label>
                        <input type="date" class="form-control search-form-control">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search-primary w-100">{{ __('Tìm vé') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Search Layout -->
<section class="py-5" style="background-color: #f8fafc;">
    <div class="container">
        <form action="{{ route('frontend.tours.search') }}" method="GET" id="searchForm">
            <div class="row g-4">
                
                <!-- Left Sidebar Filters -->
                <div class="col-lg-3">
                    <div class="sidebar-filter">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="fw-bold fs-5"><i class="bi bi-sliders me-2"></i>{{ __('Tìm kiếm nâng cao') }}</div>
                            <a href="{{ route('frontend.tours.search') }}" class="text-decoration-none text-muted small">{{ __('Đặt lại') }}</a>
                        </div>
                        
                        <!-- Phương tiện -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Phương tiện') }}</div>
                            <div class="filter-btn-group">
                                <input type="radio" class="btn-check" name="transport" id="transport1" value="xe" {{ request('transport') == 'xe' ? 'checked' : '' }}>
                                <label class="filter-btn" for="transport1">{{ __('Xe') }}</label>
                                <input type="radio" class="btn-check" name="transport" id="transport2" value="bay" {{ request('transport') == 'bay' ? 'checked' : '' }}>
                                <label class="filter-btn" for="transport2">{{ __('Chuyến bay') }}</label>
                            </div>
                        </div>

                        <!-- Điểm khởi hành -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Điểm khởi hành') }}</div>
                            <select class="form-select" name="departure_id">
                                <option value="">{{ __('Tất cả') }}</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ request('departure_id') == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Điểm đến -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Điểm đến') }}</div>
                            <select class="form-select" name="destination_id">
                                <option value="">{{ __('Tất cả') }}</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ request('destination_id') == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ngày đi từ -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Ngày đi từ') }}</div>
                            <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                        </div>

                        <!-- Xếp hạng sao -->
                        <div class="mb-4">
                            <div class="filter-section-title d-flex justify-content-between">
                                {{ __('Xếp hạng sao') }} <i class="bi bi-chevron-up"></i>
                            </div>
                            <div class="filter-checkbox-list">
                                @for($i = 5; $i >= 1; $i--)
                                <label class="filter-checkbox">
                                    <input type="radio" name="stars" value="{{ $i }}" {{ request('stars') == $i ? 'checked' : '' }}>
                                    <div class="stars">
                                        @for($j = 1; $j <= $i; $j++)
                                        <i class="bi bi-star-fill"></i>
                                        @endfor
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>

                        <!-- Ngân sách -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Ngân sách') }}</div>
                            <div class="filter-btn-grid">
                                <input type="radio" class="btn-check" name="budget" id="budget1" value="under_5m" {{ request('budget') == 'under_5m' ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget1">{{ __('Dưới ₫5M') }}</label>

                                <input type="radio" class="btn-check" name="budget" id="budget2" value="5m_to_10m" {{ request('budget') == '5m_to_10m' ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget2">{{ __('₫5M - ₫10M') }}</label>
                                
                                <input type="radio" class="btn-check" name="budget" id="budget3" value="10m_to_20m" {{ request('budget') == '10m_to_20m' ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget3">{{ __('₫10M - ₫20M') }}</label>

                                <input type="radio" class="btn-check" name="budget" id="budget4" value="over_20m" {{ request('budget') == 'over_20m' ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget4">{{ __('Trên ₫20M') }}</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">{{ __('Áp dụng') }}</button>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-lg-9">
                    <!-- Top Bar -->
                    <div class="search-results-header">
                        <div class="search-results-count">
                            {{ __('Kết quả:') }} <span>{{ $tours->count() }} {{ __('gói combo') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small text-nowrap">{{ __('Sắp xếp theo:') }}</span>
                            <select class="form-select border-0 bg-transparent fw-medium" name="sort" style="width: auto;" onchange="document.getElementById('searchForm').submit()">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Mới nhất') }}</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Giá từ thấp đến cao') }}</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Giá từ cao đến thấp') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Grid of Tours -->
                    <div class="row g-4">
                        @forelse($tours as $tour)
                        <div class="col-12 col-md-6">
                            <a href="{{ route('frontend.tours.show', $tour->slug) }}" class="text-decoration-none h-100 d-block">
                                <div class="combo-card h-100">
                                    <div class="combo-card-img-wrapper" style="height: 240px;">
                                        <span class="combo-badge">
                                            <span class="badge-icon">Hot</span> Deal
                                        </span>
                                        @php
                                        $primaryImage = $tour->tour_images->where('is_primary', 1)->first() ?? $tour->tour_images->first();
                                        $fallbackImage = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';
                                        $destName = mb_strtolower($tour->destination->name ?? '', 'UTF-8');
                                        if (str_contains($destName, 'nha trang') || str_contains($destName, 'phú quốc') || str_contains($destName, 'quy nhơn') || str_contains($destName, 'vũng tàu') || str_contains($destName, 'biển')) {
                                            $fallbackImage = 'https://images.unsplash.com/photo-1596395819057-cbcf88eb0dfb?q=80&w=800';
                                        } elseif (str_contains($destName, 'hà nội') || str_contains($destName, 'sapa') || str_contains($destName, 'đà lạt') || str_contains($destName, 'mộc châu')) {
                                            $fallbackImage = 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?q=80&w=800';
                                        } elseif (str_contains($destName, 'hạ long') || str_contains($destName, 'vịnh')) {
                                            $fallbackImage = 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800';
                                        } elseif (str_contains($destName, 'đà nẵng') || str_contains($destName, 'hội an') || str_contains($destName, 'huế')) {
                                            $fallbackImage = 'https://images.unsplash.com/photo-1555921015-c262060f5899?q=80&w=800';
                                        } elseif (str_contains($destName, 'hồ chí minh') || str_contains($destName, 'sài gòn')) {
                                            $fallbackImage = 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?q=80&w=800';
                                        }
                                        @endphp
                                        <img src="{{ $primaryImage ? asset($primaryImage->image_url) : $fallbackImage }}"
                                            alt="{{ $tour->title }}" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="combo-card-body">
                                        <h3 class="combo-title" style="font-size: 1.05rem;">{{ $tour->title }}</h3>
                                        <div class="combo-stars" style="font-size: 0.9rem;">
                                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                        </div>
                                        <div class="combo-specs">
                                            <div class="combo-specs-row justify-content-between mb-1">
                                                <div class="combo-specs-item">
                                                    <i class="bi bi-geo-alt" style="font-size: 0.9rem;"></i>
                                                    <span class="text-truncate" style="max-width: 140px; font-size: 0.85rem;">{{ $tour->destination->name ?? 'TP. Hồ Chí Minh' }}</span>
                                                </div>
                                                <div class="combo-specs-item">
                                                    <i class="bi bi-airplane" style="font-size: 0.9rem;"></i>
                                                    <span style="font-size: 0.85rem;">{{ __('Máy bay') }}</span>
                                                </div>
                                            </div>
                                            <div class="combo-specs-item">
                                                <i class="bi bi-building" style="font-size: 0.9rem;"></i>
                                                <span style="font-size: 0.85rem;">{{ __('Khách sạn tương đương 4*') }}</span>
                                            </div>
                                        </div>
                                        <div class="combo-footer mt-auto pt-3">
                                            <div>
                                                <div class="combo-price-label">{{ __('Giá từ:') }}</div>
                                                <div class="combo-price-val">{{ number_format($tour->base_price, 0, ',', '.') }}đ</div>
                                            </div>
                                            <button class="btn btn-combo-detail" style="padding: 6px 12px; font-size: 0.85rem;">{{ __('Xem chi tiết') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center py-5 rounded-4 bg-white border-0 shadow-sm">
                                <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="fw-bold">{{ __('Không tìm thấy kết quả nào') }}</h5>
                                <p class="text-muted">{{ __('Vui lòng thử điều chỉnh lại bộ lọc tìm kiếm.') }}</p>
                                <a href="{{ route('frontend.tours.search') }}" class="btn btn-outline-primary rounded-pill mt-2">{{ __('Xóa bộ lọc') }}</a>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection
