@extends('layouts.master')

@section('title', 'Travel Wonder - Đặt Tour & Vé Tham Quan')

@section('content')
<style>
.hero-section {
    position: relative;
    height: 400px;
    background-color: #f8f9fa;
}

.hero-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.search-container {
    margin-top: -100px;
    position: relative;
    z-index: 10;
}

.search-card {
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border: none;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 15px 20px;
    border-bottom: 3px solid transparent;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    background: transparent;
}

.section-title {
    font-weight: 700;
    color: #1a2b4c;
    margin-bottom: 24px;
}

.item-card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #eee;
    overflow: hidden;
}

.item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.item-img {
    height: 160px;
    object-fit: cover;
}

.dest-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    height: 200px;
}

.dest-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.dest-card:hover .dest-img {
    transform: scale(1.1);
}

.dest-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    color: white;
}
</style>

<div class="hero-section">
    <div id="heroCarousel" class="carousel slide h-100" data-bs-ride="carousel">
        <div class="carousel-inner h-100">
            @forelse($banners as $index => $banner)
            <div class="carousel-item h-100 {{ $index == 0 ? 'active' : '' }}">
                <img src="{{ $banner->image_url }}" class="hero-img d-block w-100" alt="{{ $banner->title }}">
            </div>
            @empty
            <div class="carousel-item h-100 active">
                <img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070"
                    class="hero-img d-block w-100" alt="Default Banner">
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="container search-container mb-5">
    <div class="card search-card">
        <div class="card-header bg-white border-bottom-0 pt-3 px-4">
            <ul class="nav nav-tabs border-0" id="searchTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tour-tab" data-bs-toggle="tab" data-bs-target="#tour"
                        type="button" role="tab"><i class="bi bi-briefcase-fill me-2"></i>Tour Du Lịch</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ticket-tab" data-bs-toggle="tab" data-bs-target="#ticket" type="button"
                        role="tab"><i class="bi bi-ticket-perforated-fill me-2"></i>Vé Tham Quan</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="searchTabsContent">
                <div class="tab-pane fade show active" id="tour" role="tabpanel">
                    <form action="#" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Điểm đến</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0"
                                    placeholder="Bạn muốn đi đâu?">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Ngày khởi hành</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Số khách</label>
                            <select class="form-select">
                                <option value="1">1 Người lớn, 0 Trẻ em</option>
                                <option value="2">2 Người lớn, 0 Trẻ em</option>
                                <option value="3">Gia đình</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-warning text-white w-100 fw-bold py-2"><i
                                    class="bi bi-search me-2"></i>Tìm kiếm</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="ticket" role="tabpanel">
                    <form action="#" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-7">
                            <label class="form-label text-muted small fw-bold">Tìm công viên giải trí, sự kiện</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0"
                                    placeholder="Tìm kiếm hoạt động vui chơi...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Ngày sử dụng</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-warning text-white w-100 fw-bold py-2">Tìm vé</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <h3 class="section-title">Điểm đến thịnh hành</h3>
    <div class="row g-3">
        @forelse($destinations as $dest)
        <div class="col-6 col-md-4 col-lg-2">
            <a href="#" class="text-decoration-none">
                <div class="dest-card">
                    <img src="{{ $dest->image_url ?? 'https://via.placeholder.com/300x400' }}" class="dest-img"
                        alt="{{ $dest->name }}">
                    <div class="dest-overlay">
                        <h5 class="mb-0 text-truncate">{{ $dest->name }}</h5>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted">Đang cập nhật điểm đến.</p>
        </div>
        @endforelse
    </div>
</div>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Tour Du Lịch Trọn Gói</h3>
        <a href="#" class="text-decoration-none text-primary fw-bold">Xem tất cả</a>
    </div>
    <div class="row g-4">
        @forelse($tours as $tour)
        <div class="col-12 col-md-6 col-lg-3">
            <!-- Thêm thẻ a bọc ngoài card -->
            <a href="{{ route('frontend.tours.show', $tour->slug) }}" class="text-decoration-none">
                <div class="card item-card h-100">
                    @php
                    $primaryImage = $tour->tour_images->where('is_primary', 1)->first() ?? $tour->tour_images->first();
                    @endphp

                    <img src="{{ $primaryImage ? asset($primaryImage->image_url) : 'https://via.placeholder.com/400x300' }}"
                        class="card-img-top item-img" alt="{{ $tour->title }}"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border"><i class="bi bi-clock"></i>
                                {{ $tour->duration_days }}N{{ $tour->duration_nights }}Đ</span>
                        </div>
                        <h6 class="card-title fw-bold text-dark"
                            style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $tour->title }}
                        </h6>
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt text-danger"></i>
                            {{ $tour->destination->name ?? '' }}
                        </p>
                        <div class="mt-auto">
                            <div class="text-danger fw-bold fs-5">{{ number_format($tour->base_price, 0, ',', '.') }}
                                VND
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted">Đang cập nhật tour.</p>
        </div>
        @endforelse
    </div>
</div>

<div class="container py-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Vé Vui Chơi & Hoạt Động</h3>
        <a href="#" class="text-decoration-none text-primary fw-bold">Xem tất cả</a>
    </div>
    <div class="row g-4">
        @forelse($tickets as $ticket)
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card item-card h-100">
                <img src="https://via.placeholder.com/400x300" class="card-img-top item-img" alt="{{ $ticket->title }}">
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title fw-bold text-dark"
                        style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $ticket->title }}
                    </h6>
                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt text-danger"></i>
                        {{ $ticket->destination->name ?? '' }}
                    </p>
                    <div class="mt-auto">
                        <span class="text-muted small">Từ</span>
                        <div class="text-danger fw-bold fs-5">Tra cứu giá</div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted">Đang cập nhật vé tham quan.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection