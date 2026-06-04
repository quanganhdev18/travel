@extends('layouts.master')

@section('title', 'Tìm kiếm Vé - Travel Wonder')

@section('content')

<!-- Hero Section -->
<section class="hero-premium">
    <div class="hero-bg">
        @php
            $firstBanner = $banners->first();
            $bgImage = 'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=2070'; // fallback cho vé
            
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
        <h1 class="hero-title">{{ __('Vui Chơi Cùng TravelWonder') }}</h1>
        <p class="hero-subtitle">{{ __('Khám phá hàng ngàn công viên giải trí và hoạt động vui chơi hấp dẫn nhất.') }}</p>
    </div>
</section>

<!-- Search Widget -->
<div class="container search-widget-wrapper">
    <div class="glass-panel search-glass">
        <ul class="nav nav-tabs search-tabs" id="searchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tour-tab" data-bs-toggle="tab" data-bs-target="#tour"
                    type="button" role="tab" onclick="window.location.href='{{ route('frontend.tours.search') }}'"><i class="bi bi-briefcase-fill me-2"></i>{{ __('Tour Du Lịch') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ticket-tab" data-bs-toggle="tab" data-bs-target="#ticket" type="button"
                    role="tab"><i class="bi bi-ticket-perforated-fill me-2"></i>{{ __('Vé Tham Quan') }}</button>
            </li>
        </ul>
        <div class="tab-content px-3 pb-3" id="searchTabsContent">
            <div class="tab-pane fade show active" id="ticket" role="tabpanel">
                <form action="{{ route('frontend.tickets.search') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label class="form-label text-muted small fw-bold">{{ __('Tìm công viên giải trí, sự kiện') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="keyword" class="form-control search-form-control border-start-0 ps-0"
                                placeholder="{{ __('Tìm kiếm hoạt động vui chơi...') }}" value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">{{ __('Ngày sử dụng') }}</label>
                        <input type="date" name="use_date" class="form-control search-form-control" value="{{ request('use_date') }}" min="{{ date('Y-m-d') }}">
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
        <form action="{{ route('frontend.tickets.search') }}" method="GET" id="searchForm">
            <!-- Giữ lại các input cũ (nếu có ở form widget) -->
            @if(request('keyword')) <input type="hidden" name="keyword" value="{{ request('keyword') }}"> @endif
            @if(request('use_date')) <input type="hidden" name="use_date" value="{{ request('use_date') }}"> @endif
            
            <div class="row g-4">
                
                <!-- Left Sidebar Filters -->
                <div class="col-lg-3">
                    <div class="sidebar-filter">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="fw-bold fs-5"><i class="bi bi-sliders me-2"></i>{{ __('Tìm kiếm nâng cao') }}</div>
                            <a href="{{ route('frontend.tickets.search') }}" class="text-decoration-none text-muted small">{{ __('Đặt lại') }}</a>
                        </div>
                        
                        <!-- Điểm đến -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Điểm đến') }}</div>
                            <select class="form-select" name="destination_id" onchange="document.getElementById('searchForm').submit()">
                                <option value="">{{ __('Tất cả') }}</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ request('destination_id') == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">{{ __('Áp dụng') }}</button>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-lg-9">
                    <div id="results-container">
                        @include('frontend.tickets._results')
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
                let oldSort = form.querySelector('input[name="sort"][type="hidden"]');
                if (oldSort) oldSort.remove();
                
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
                document.querySelector('.search-results-header').scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
@endsection
