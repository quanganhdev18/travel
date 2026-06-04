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
                <form action="{{ route('frontend.tours.search') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
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
                            <input type="date" class="form-control" name="departure_date" value="{{ request('departure_date') }}">
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
                    <div id="results-container">
                        @include('frontend.tours._results')
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('searchForm');
        const container = document.getElementById('results-container');

        function fetchResults(url) {
            const loading = document.getElementById('loading-overlay');
            if(loading) loading.classList.remove('d-none');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error fetching data:', error));
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const url = form.action + '?' + params.toString();
            fetchResults(url);
        });

        form.addEventListener('change', function(e) {
            if (e.target.name === 'sort' || e.target.type === 'radio' || e.target.type === 'checkbox' || e.target.tagName === 'SELECT' || e.target.type === 'date') {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        });
        
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'sortSelect') {
                // Remove the old hidden sort input if exists
                let oldSort = form.querySelector('input[name="sort"][type="hidden"]');
                if (oldSort) oldSort.remove();
                
                // Add new hidden sort input
                const hiddenSort = document.createElement('input');
                hiddenSort.type = 'hidden';
                hiddenSort.name = 'sort';
                hiddenSort.value = e.target.value;
                form.appendChild(hiddenSort);
                
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        });

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.ajax-pagination a');
            if (pageLink) {
                e.preventDefault();
                fetchResults(pageLink.href);
                // Scroll to top of results
                document.querySelector('.search-results-header').scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
@endsection
