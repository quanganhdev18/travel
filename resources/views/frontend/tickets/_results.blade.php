<!-- Top Bar -->
<div class="search-results-header">
    <div class="search-results-count">
        {{ __('Kết quả:') }} <span>{{ $tickets->total() }} {{ __('vé tham quan') }}</span>
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

<!-- Grid of Tickets -->
<div class="row g-4 position-relative">
    <div id="loading-overlay" class="position-absolute w-100 h-100 d-none" style="background: rgba(255,255,255,0.7); z-index: 10; top: 0; left: 0;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    
    @forelse($tickets as $ticket)
    <div class="col-12 col-md-6">
        <a href="#" class="text-decoration-none h-100 d-block">
            <div class="combo-card h-100">
                <div class="combo-card-img-wrapper" style="height: 240px;">
                    <span class="combo-badge bg-warning text-dark">
                        <i class="bi bi-star-fill me-1"></i> {{ __('Hot') }}
                    </span>
                    @php
                    $fallbackImage = 'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=800';
                    $destName = mb_strtolower($ticket->destination->name ?? '', 'UTF-8');
                    if (str_contains($destName, 'nha trang') || str_contains($destName, 'phú quốc')) {
                        $fallbackImage = 'https://images.unsplash.com/photo-1582653291997-079a1c04e5d1?q=80&w=800'; // waterpark
                    } elseif (str_contains($destName, 'sapa') || str_contains($destName, 'đà nẵng') || str_contains($destName, 'hạ long')) {
                        $fallbackImage = 'https://images.unsplash.com/photo-1571509930722-df38ccfb8611?q=80&w=800'; // cable car
                    }
                    @endphp
                    <img src="{{ $fallbackImage }}"
                        alt="{{ $ticket->title }}" class="w-100 h-100 object-fit-cover">
                </div>
                <div class="combo-card-body">
                    <h3 class="combo-title" style="font-size: 1.05rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3em;">{{ $ticket->title }}</h3>
                    
                    <div class="combo-specs mt-3">
                        <div class="combo-specs-item mb-2">
                            <i class="bi bi-geo-alt text-danger" style="font-size: 1rem;"></i>
                            <span style="font-size: 0.9rem;">{{ $ticket->destination->name ?? 'Việt Nam' }}</span>
                        </div>
                        <div class="combo-specs-item">
                            <i class="bi bi-shield-check text-success" style="font-size: 1rem;"></i>
                            <span style="font-size: 0.85rem;" class="text-muted">{{ $ticket->cancellation_policy }}</span>
                        </div>
                    </div>
                    <div class="combo-footer mt-auto pt-3 border-top mt-3">
                        <div>
                            <div class="combo-price-label">{{ __('Từ:') }}</div>
                            @php
                                $minPrice = $ticket->ticket_options->min('price') ?? 0;
                            @endphp
                            <div class="combo-price-val">{{ format_currency($minPrice) }}</div>
                        </div>
                        <button class="btn btn-combo-detail" style="padding: 6px 12px; font-size: 0.85rem;">{{ __('Chọn vé') }}</button>
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
            <a href="{{ route('frontend.tickets.search') }}" class="btn btn-outline-primary rounded-pill mt-2">{{ __('Xóa bộ lọc') }}</a>
        </div>
    </div>
    @endforelse
</div>

@if($tickets->hasPages())
<div class="mt-4 d-flex justify-content-center ajax-pagination">
    {{ $tickets->links('pagination::bootstrap-5') }}
</div>
@endif
