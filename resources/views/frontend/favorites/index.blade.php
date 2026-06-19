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
                    <div class="card h-100 shadow-sm border-0">
                        <img src="{{ $imageUrl }}"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;"
                             alt="{{ $tour->title }}">

                        <div class="card-body">
                            <h5 class="card-title">{{ $tour->title }}</h5>

                            <p class="text-muted mb-1">
                                {{ $tour->destination->name ?? 'Đang cập nhật' }}
                            </p>

                            <p class="fw-bold text-danger">
                                {{ number_format($tour->base_price, 0, ',', '.') }}đ
                            </p>

                            <a href="{{ route('frontend.tours.show', $tour->slug) }}"
                               class="btn btn-primary w-100">
                                Xem chi tiết
                            </a>
                            <form action="{{ route('frontend.favorites.destroy', $tour->id) }}"
                                method="POST"
                                class="mt-2">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger w-100">
                                    <i class="bi bi-trash"></i>
                                    Xóa khỏi danh sách
                                </button>
                            </form>
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

@endsection
