<!-- Top Bar -->
<div class="search-results-header">
    <div class="search-results-count">
        {{ __('Kết quả:') }} <span>{{ $tours->total() }} {{ __('gói combo') }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small text-nowrap">{{ __('Sắp xếp theo:') }}</span>
        <select class="form-select border-0 bg-transparent fw-medium" name="sort" id="sortSelect" style="width: auto;">
            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Mới nhất') }}</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Giá từ thấp đến cao') }}</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Giá từ cao đến thấp') }}</option>
        </select>
    </div>
</div>

<!-- Grid of Tours -->
<div class="row g-4 position-relative">
    <div id="loading-overlay" class="position-absolute w-100 h-100 d-none" style="background: rgba(255,255,255,0.7); z-index: 10; top: 0; left: 0;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    
    @forelse($tours as $tour)
    <div class="col-12 col-md-6">
        <a href="{{ route('frontend.tours.show', $tour->slug) }}" class="text-decoration-none h-100 d-block">
            <div class="combo-card h-100">
                <div class="combo-card-img-wrapper" style="height: 240px;">
                    <span class="combo-badge">
                        <span class="badge-icon">{{ __('Hot') }}</span> {{ __('Deal') }}
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
                        @php $stars = $tour->hotel_stars ?? 4; @endphp
                        @for($i=1; $i<=$stars; $i++)
                        <i class="bi bi-star-fill text-warning"></i>
                        @endfor
                    </div>
                    <div class="combo-location">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ $tour->destination->name ?? 'TP. Hồ Chí Minh' }}</span>
                    </div>
                    <div class="combo-footer">
                        <div>
                            <div class="combo-price-label">{{ __('Giá từ:') }}</div>
                            <div class="combo-price-val">{{ format_currency($tour->base_price ?? 0) }}</div>
                        </div>
                        <button class="btn btn-combo-detail">{{ __('Xem chi tiết') }}</button>
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

@if($tours->hasPages())
<div class="mt-4 d-flex justify-content-center ajax-pagination">
    {{ $tours->links('pagination::bootstrap-5') }}
</div>
@endif
