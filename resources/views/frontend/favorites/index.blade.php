@extends('layouts.master')

@section('title', 'Tour đã lưu')

@section('content')

<div class="container py-5">
    <h2 class="mb-4 fw-bold">Tour đã lưu</h2>

    @if($favorites->count() > 0)
        <div class="row g-4">
            @foreach($favorites as $favorite)
                @php
                    $tour = $favorite->tour;
                    $primaryImage = $tour->tour_images->where('is_primary', 1)->first()
                        ?? $tour->tour_images->first();

                    $imageUrl = $primaryImage
                        ? asset($primaryImage->image_url)
                        : 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';
                @endphp

                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{ route('frontend.tours.show', $tour->slug) }}" class="text-decoration-none h-100 d-block">
                        <div class="combo-card">
                            <div class="combo-card-img-wrapper">
                                <span class="combo-badge">
                                    <span class="badge-icon">25%</span> Hot Deal
                                </span>
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $tour->title }}"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';">
                            </div>
                            <div class="combo-card-body">
                                <h3 class="combo-title">{{ $tour->title }}</h3>
                                <div class="combo-stars">
                                    @php $stars = $tour->hotel_stars ?? 4; @endphp
                                    @for($i = 1; $i <= $stars; $i++)
                                    <i class="bi bi-star-fill text-warning"></i>
                                    @endfor
                                </div>
                                <div class="combo-location">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $tour->destination->name ?? 'Đang cập nhật' }}</span>
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
                    <form action="{{ route('frontend.favorites.destroy', $tour->id) }}"
                        method="POST"
                        class="mt-2 px-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm">
                            <i class="bi bi-trash me-1"></i>{{ __('Xóa khỏi danh sách') }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <h4>Bạn chưa lưu tour nào</h4>
            <p class="text-muted">Hãy bấm vào trái tim ở card tour để lưu tour yêu thích.</p>

            <a href="{{ route('frontend.tours.index') }}" class="btn btn-primary">
                Xem tour
            </a>
        </div>
    @endif
</div>

@endsection
