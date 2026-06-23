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

                    $tourImage = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';

                    if ($primaryImage && !empty($primaryImage->image_url)) {
                        if (\Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://', 'https://'])) {
                            $tourImage = $primaryImage->image_url;
                        } else {
                            $tourImage = asset(ltrim($primaryImage->image_url, '/'));
                        }
                    }

                    $tourTitle = $tour->title ?? 'Chưa có tên tour';
                    $tourSlug = $tour->slug ?? $tour->id;
                    $destinationName = $tour->destination->name ?? 'Việt Nam';
                @endphp

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="premium-card h-100 overflow-hidden">
                        <a href="{{ route('frontend.tours.show', $tourSlug) }}"
                           class="text-decoration-none d-block text-dark">

                            <div class="card-img-wrapper">
                                <form action="{{ route('frontend.favorites.toggle', $tour->id) }}"
                                      method="POST"
                                      class="favorite-form"
                                      onclick="event.stopPropagation();">
                                    @csrf

                                    <button type="submit" class="favorite-btn active">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </form>

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
                                    style="display:block !important; color:#111827 !important; font-size:18px !important; font-weight:700 !important; line-height:1.4 !important; margin-bottom:12px !important; min-height:50px !important;">
                                    {{ $tourTitle }}
                                </h3>

                                <div class="location-text">
                                    <i class="bi bi-geo-alt text-danger"></i>
                                    {{ $destinationName }}
                                </div>

                                <div class="price-wrap">
                                    <span class="text-muted small">Chỉ từ</span>
                                    <div class="price-val">
                                        {{ format_currency($tour->base_price ?? 0) }}
                                    </div>
                                </div>
                            </div>
                        </a>

                        <div class="px-3 pb-3">
                            <a href="{{ route('frontend.tours.show', $tourSlug) }}"
                               class="btn btn-primary w-100">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
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

<style>
.card-img-wrapper {
    position: relative;
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
}

.favorite-btn i {
    font-size: 22px;
}

.favorite-btn.active {
    color: #ff3366;
}
</style>

@endsection
