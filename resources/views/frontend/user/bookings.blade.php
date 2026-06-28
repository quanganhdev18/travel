@extends('layouts.master')

@section('title', 'Đơn đặt tour - Travel Wonder')

@section('content')
<style>
    /* ===== Shared Profile Layout ===== */
    .profile-page { background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 50%, #f0f9ff 100%); min-height: 100vh; }

    .profile-hero {
        background: linear-gradient(135deg, #0B132B 0%, #1a2b4c 40%, #007CE8 100%);
        padding: 60px 0 120px; position: relative; overflow: hidden;
    }
    .profile-hero::before {
        content: ''; position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .profile-hero::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 80px;
        background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 50%, #f0f9ff 100%);
        border-radius: 50% 50% 0 0 / 30px 30px 0 0;
    }
    .floating-orb { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.15; pointer-events: none; }
    .orb-1 { width: 300px; height: 300px; background: #007CE8; top: -100px; right: -50px; }
    .orb-2 { width: 200px; height: 200px; background: #F5A623; bottom: 20px; left: 10%; }

    .profile-card-main { margin-top: -80px; position: relative; z-index: 10; }

    /* Sidebar */
    .profile-avatar-section {
        background: white; border-radius: 28px; padding: 36px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.08); border: 1px solid rgba(255,255,255,0.8);
        text-align: center; position: relative; overflow: hidden;
    }
    .profile-avatar-section::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #007CE8, #00c6ff, #F5A623);
    }
    .avatar-ring {
        width: 90px; height: 90px; border-radius: 50%; padding: 3px;
        background: linear-gradient(135deg, #007CE8, #00c6ff, #F5A623);
        display: inline-flex; align-items: center; justify-content: center;
    }
    .avatar-inner { width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 3px solid white; background: white; }
    .avatar-inner img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-initials {
        width: 100%; height: 100%; border-radius: 50%;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: 800; color: white;
    }
    .profile-stat-item {
        background: linear-gradient(135deg, #f8faff, #f0f4ff);
        border: 1px solid rgba(0,124,232,0.1); border-radius: 16px; padding: 14px 6px; text-align: center;
        transition: all 0.3s ease;
    }
    .profile-stat-item:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,124,232,0.15); }
    .profile-stat-value {
        font-size: 1.6rem; font-weight: 800;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1;
    }
    .profile-stat-label { font-size: 0.7rem; color: #718096; font-weight: 600; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .user-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, rgba(0,124,232,0.1), rgba(0,198,255,0.1));
        border: 1px solid rgba(0,124,232,0.2); color: #007CE8;
        padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; margin-bottom: 12px;
    }
    .joined-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(0,0,0,0.04); border-radius: 20px; padding: 4px 12px;
        font-size: 0.78rem; color: #6b7280; font-weight: 500;
    }
    .profile-nav-item {
        display: flex; align-items: center; gap: 12px; padding: 13px 18px; border-radius: 14px;
        color: #718096; text-decoration: none; font-weight: 600; font-size: 0.92rem;
        transition: all 0.25s ease; margin-bottom: 6px; position: relative; overflow: hidden;
    }
    .profile-nav-item::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(0,124,232,0.08), rgba(0,198,255,0.05));
        opacity: 0; transition: opacity 0.25s ease; border-radius: 14px;
    }
    .profile-nav-item:hover::before, .profile-nav-item.active::before { opacity: 1; }
    .profile-nav-item:hover, .profile-nav-item.active { color: #007CE8; transform: translateX(4px); }
    .profile-nav-item.active { box-shadow: 0 4px 15px rgba(0,124,232,0.15); }
    .profile-nav-item .nav-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
        background: rgba(0,124,232,0.08); color: #007CE8; transition: all 0.25s ease; flex-shrink: 0;
    }
    .profile-nav-item.active .nav-icon {
        background: linear-gradient(135deg, #007CE8, #0056b3); color: white;
        box-shadow: 0 4px 12px rgba(0,124,232,0.3);
    }

    /* ===== Booking Cards ===== */
    .booking-card {
        background: white; border-radius: 24px; overflow: hidden;
        border: 1px solid #f0f2f5; box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        transition: all 0.35s ease; margin-bottom: 20px;
        animation: slideInUp 0.4s ease both;
    }
    .booking-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.1); border-color: rgba(0,124,232,0.15); }

    @keyframes slideInUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform:translateY(0); } }

    .booking-card:nth-child(1) { animation-delay: 0.05s; }
    .booking-card:nth-child(2) { animation-delay: 0.10s; }
    .booking-card:nth-child(3) { animation-delay: 0.15s; }
    .booking-card:nth-child(4) { animation-delay: 0.20s; }

    .booking-head {
        background: linear-gradient(135deg, #f8faff, #f0f4ff);
        padding: 18px 28px; display: flex; justify-content: space-between;
        align-items: center; flex-wrap: wrap; gap: 12px;
        border-bottom: 1px solid #edf0f7;
    }
    .booking-id { font-family: 'Courier New', monospace; font-weight: 800; color: #0B132B; font-size: 1rem; letter-spacing: 1px; }
    .booking-date { color: #718096; font-size: 0.85rem; font-weight: 500; }

    .status-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 16px; border-radius: 30px; font-size: 0.82rem; font-weight: 700;
    }
    .status-chip .dot { width: 7px; height: 7px; border-radius: 50%; animation: pulse 2s infinite; }
    .chip-pending  { background: rgba(245,158,11,0.12); color: #92400e; }
    .chip-pending .dot  { background: #f59e0b; }
    .chip-confirmed { background: rgba(16,185,129,0.12); color: #065f46; }
    .chip-confirmed .dot { background: #10b981; }
    .chip-cancelled { background: rgba(239,68,68,0.12); color: #7f1d1d; }
    .chip-cancelled .dot { background: #ef4444; animation: none; }
    .chip-paid      { background: rgba(99,102,241,0.12); color: #3730a3; }
    .chip-paid .dot { background: #6366f1; }
    @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

    .booking-body { padding: 24px 28px; }
    .tour-thumb {
        width: 80px; height: 80px; border-radius: 16px; object-fit: cover;
        border: 2px solid #f0f2f5; flex-shrink: 0;
    }
    .tour-thumb-placeholder {
        width: 80px; height: 80px; border-radius: 16px; flex-shrink: 0;
        background: linear-gradient(135deg, #007CE8, #00c6ff);
        display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem;
    }
    .booking-divider { width: 1px; background: #e5e7eb; margin: 0 24px; align-self: stretch; }

    .info-label { font-size: 0.72rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .info-value { font-weight: 700; color: #1a2b4c; font-size: 0.95rem; }
    .price-big { font-size: 1.6rem; font-weight: 800; background: linear-gradient(135deg, #007CE8, #0056b3); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

    .btn-detail {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        color: white; border: none; border-radius: 12px; padding: 10px 22px;
        font-weight: 700; font-size: 0.88rem; text-decoration: none;
        transition: all 0.3s ease; box-shadow: 0 4px 14px rgba(0,124,232,0.3);
        cursor: pointer; white-space: nowrap;
    }
    .btn-detail:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,124,232,0.4); color: white; }

    .empty-state {
        background: white; border-radius: 28px; padding: 70px 40px; text-align: center;
        box-shadow: 0 4px 24px rgba(0,0,0,0.05); border: 1px solid #f0f2f5;
    }
    .empty-icon {
        width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 24px;
        background: linear-gradient(135deg, rgba(0,124,232,0.08), rgba(0,198,255,0.05));
        display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #007CE8;
    }

    /* Filters */
    .filter-chips { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
    .filter-chip {
        padding: 8px 18px; border-radius: 30px; font-size: 0.85rem; font-weight: 600;
        cursor: pointer; transition: all 0.25s ease; border: 2px solid #e5e7eb; background: white; color: #6b7280;
    }
    .filter-chip:hover { border-color: #007CE8; color: #007CE8; }
    .filter-chip.active { background: linear-gradient(135deg, #007CE8, #0056b3); border-color: transparent; color: white; box-shadow: 0 4px 14px rgba(0,124,232,0.3); }
</style>

<div class="profile-page">
    {{-- Hero --}}
    <div class="profile-hero">
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="container" style="position:relative;z-index:2;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,0.5);">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.profile') }}" class="text-white-50 text-decoration-none">Tài khoản</a></li>
                    <li class="breadcrumb-item text-white active">Đơn đặt tour</li>
                </ol>
            </nav>
            <h1 class="fw-800 mb-1" style="color:white;font-size:2rem;"><i class="bi bi-bag-check-fill me-2"></i>Đơn đặt tour</h1>
            <p class="mb-0" style="color:rgba(255,255,255,0.7);">Quản lý và theo dõi các chuyến đi của bạn</p>
        </div>
    </div>

    <div class="container profile-card-main pb-5">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="profile-avatar-section reveal-up">
                    <div class="mb-3">
                        <div class="avatar-ring" style="width:80px;height:80px;">
                            <div class="avatar-inner">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="avatar">
                                @else
                                    <div class="avatar-initials" style="font-size:1.6rem;">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="user-badge"><i class="bi bi-patch-check-fill"></i> Thành viên</div>
                    <h6 class="fw-800 mb-1" style="color:#0B132B;">{{ Auth::user()->name }}</h6>
                    <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>
                    <div class="joined-chip mb-4"><i class="bi bi-calendar3"></i> Tham gia {{ Auth::user()->created_at->format('m/Y') }}</div>

                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ $bookings->count() }}</div>
                                <div class="profile-stat-label">Đơn đặt</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ Auth::user()->wishlists->count() }}</div>
                                <div class="profile-stat-label">Yêu thích</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ Auth::user()->reviews->count() }}</div>
                                <div class="profile-stat-label">Đánh giá</div>
                            </div>
                        </div>
                    </div>

                    <nav>
                        <a href="{{ route('user.profile') }}" class="profile-nav-item">
                            <span class="nav-icon"><i class="bi bi-person-fill"></i></span> Thông tin cá nhân
                        </a>
                        <a href="{{ route('user.bookings') }}" class="profile-nav-item active">
                            <span class="nav-icon"><i class="bi bi-bag-check-fill"></i></span> Đơn đặt tour
                        </a>
                        <a href="{{ route('user.wishlists') }}" class="profile-nav-item">
                            <span class="nav-icon"><i class="bi bi-heart-fill"></i></span> Danh sách yêu thích
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Nội dung --}}
            <div class="col-lg-9">
                @if(session('success'))
                    <div class="alert border-0 rounded-4 mb-4" style="background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(5,150,105,0.05));border-left:4px solid #10b981 !important;color:#065f46;">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif

                {{-- Bộ lọc trạng thái --}}
                <div class="filter-chips reveal-up">
                    <button class="filter-chip active" data-filter="all">Tất cả <span class="ms-1 badge bg-white text-dark" style="border-radius:20px;">{{ $bookings->count() }}</span></button>
                    <button class="filter-chip" data-filter="pending">Chờ xác nhận</button>
                    <button class="filter-chip" data-filter="confirmed">Đã xác nhận</button>
                    <button class="filter-chip" data-filter="paid">Đã thanh toán</button>
                    <button class="filter-chip" data-filter="cancelled">Đã hủy</button>
                </div>

                @forelse($bookings as $booking)
                @php
                    $status    = strtolower($booking->booking_status ?? 'pending');
                    $chipClass = match($status) {
                        'confirmed' => 'chip-confirmed',
                        'cancelled' => 'chip-cancelled',
                        'paid'      => 'chip-paid',
                        default     => 'chip-pending',
                    };
                    $statusLabel = match($status) {
                        'pending'   => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'cancelled' => 'Đã hủy',
                        'paid'      => 'Đã thanh toán',
                        default     => ucfirst($status),
                    };
                    $tour = $booking->tour_schedule?->tour;
                    $primaryImg = $tour?->tour_images?->where('is_primary', 1)->first();
                    $imgUrl = $primaryImg?->image_url ?? null;
                @endphp
                <div class="booking-card" data-status="{{ $status }}">
                    {{-- Header --}}
                    <div class="booking-head">
                        <div>
                            <div class="booking-date mb-1"><i class="bi bi-calendar3 me-1"></i>{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                            <div class="booking-id">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="status-chip {{ $chipClass }}">
                                <span class="dot"></span>{{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="booking-body">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            {{-- Thumbnail --}}
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" class="tour-thumb" alt="tour">
                            @else
                                <div class="tour-thumb-placeholder"><i class="bi bi-map"></i></div>
                            @endif

                            {{-- Tour info --}}
                            <div class="flex-fill min-w-0">
                                <div class="info-label mb-1">Tên tour</div>
                                <h6 class="fw-800 mb-2" style="color:#0B132B;font-size:1rem;line-height:1.4;">
                                    {{ $tour?->title ?? 'Tour không tồn tại' }}
                                </h6>
                                <div class="d-flex flex-wrap gap-3 mb-0">
                                    @if($tour?->destination)
                                    <span class="small text-muted">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $tour->destination->name }}
                                    </span>
                                    @endif
                                    @if($booking->tour_schedule?->departure_date)
                                    <span class="small text-muted">
                                        <i class="bi bi-calendar-check-fill text-success me-1"></i>
                                        Khởi hành: {{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}
                                    </span>
                                    @endif
                                    @if($booking->pnr_code)
                                    <span class="small" style="color:#ef4444;font-weight:700;">
                                        <i class="bi bi-airplane me-1"></i>PNR: {{ $booking->pnr_code }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Price + action --}}
                            <div class="text-end flex-shrink-0">
                                <div class="info-label mb-1">Tổng tiền</div>
                                <div class="price-big">{{ number_format($booking->total_price, 0, ',', '.') }}<span style="font-size:1rem;color:#718096;">₫</span></div>
                                <div class="d-flex gap-2 mt-3 justify-content-end">
                                    <a href="{{ route('user.bookings.detail', $booking->id) }}" class="btn-detail">
                                        <i class="bi bi-eye"></i> Chi tiết
                                    </a>
                                    @if(in_array($status, ['pending', 'confirmed']))
                                    <form method="POST" action="{{ route('user.bookings.cancel', $booking->id) }}"
                                        onsubmit="return confirm('Bạn có chắc muốn hủy đơn này?')">
                                        @csrf
                                        <button type="submit" class="btn-detail" style="background:linear-gradient(135deg,#ef4444,#dc2626);box-shadow:0 4px 14px rgba(239,68,68,0.3);">
                                            <i class="bi bi-x-circle"></i> Hủy
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state reveal-up">
                    <div class="empty-icon"><i class="bi bi-bag-x"></i></div>
                    <h4 class="fw-800 mb-2" style="color:#0B132B;">Bạn chưa có đơn đặt tour nào</h4>
                    <p class="text-muted mb-4">Hàng ngàn điểm đến tuyệt đẹp đang chờ bạn khám phá!</p>
                    <a href="{{ url('/') }}" class="btn-detail" style="display:inline-flex;">
                        <i class="bi bi-compass"></i> Khám phá tour ngay
                    </a>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Reveal
    document.querySelectorAll('.reveal-up').forEach(el => {
        new IntersectionObserver(entries => {
            entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('active'); } });
        }, {threshold:0.08}).observe(el);
    });

    // Filter chips
    document.querySelectorAll('.filter-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.booking-card').forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection