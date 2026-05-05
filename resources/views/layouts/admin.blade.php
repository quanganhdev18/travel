<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelWonder Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Sidebar chung */
    .sidebar {
        height: 100vh;
        width: 250px;
        background-color: #1a2b4c;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 20px;
        overflow-y: auto;
        z-index: 1000;
    }

    /* Tiêu đề nhóm - Đã sửa màu và khoảng cách */
    .sidebar .group-title {
        padding: 20px 20px 10px;
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 700;
        color: #ffffff;
        /* Màu xám xanh nhẹ, dễ đọc hơn trên nền tối */
        letter-spacing: 1.2px;
    }

    /* Link menu */
    .sidebar .nav-link {
        color: #a0aec0;
        padding: 12px 25px;
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
        /* Tạo sẵn viền tàng hình để không bị nhảy chữ */
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    /* Hiệu ứng Hover - Không thay đổi font-weight để tránh nhảy chữ */
    .sidebar .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.05);
        border-left: 4px solid #007CE8;
        /* Hiển thị viền màu xanh */
    }

    /* Trạng thái Active */
    .sidebar .nav-link.active {
        color: #fff;
        background-color: rgba(0, 124, 232, 0.1);
        border-left: 4px solid #007CE8;
    }

    /* Tùy chỉnh thanh cuộn */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .main-content {
        margin-left: 250px;
        padding: 20px;
    }

    .topbar {
        background: #fff;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .admin-brand {
        color: #fff;
        font-size: 22px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 30px;
        text-decoration: none;
        display: block;
    }

    .admin-brand span {
        color: #007CE8;
    }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="{{ url('/') }}" class="admin-brand"><i class="bi bi-cursor-fill"></i> Travel<span>Wonder</span></a>

        <div class="group-title text-uppercase">
            Bảng điều khiển</div>
        <ul class="nav flex-column mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="#">
                    <i class="bi bi-speedometer2 me-2"></i> Tổng quan
                </a>
            </li>
        </ul>

        <div class="group-title text-uppercase">
            Quản lý kinh doanh</div>
        <ul class="nav flex-column mb-4">
            <!-- Quản lý Tour -->
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/tours*') ? 'active' : '' }}"
                    href="{{ route('admin.tours.index') }}">
                    <i class="bi bi-briefcase me-2"></i> Tour du lịch
                </a>
            </li>
            <!-- Quản lý Vé tham quan -->
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/tickets*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-ticket-perforated me-2"></i> Vé tham quan
                </a>
            </li>
            <!-- Quản lý Đơn hàng -->
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/bookings*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-cart-check me-2"></i> Đơn đặt chỗ
                </a>
            </li>
            <!-- Quản lý Thanh toán & Hóa đơn -->
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/invoices*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-receipt me-2"></i> Hóa đơn & Thu chi
                </a>
            </li>
        </ul>

        <div class="group-title text-uppercase">Cấu
            hình hệ thống</div>
        <ul class="nav flex-column mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/destinations*') ? 'active' : '' }}"
                    href="{{ route('admin.destinations.index') }}">
                    <i class="bi bi-geo-alt me-2"></i> Điểm đến
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}"
                    href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-grid me-2"></i> Danh mục
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/banners*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-images me-2"></i> Banner quảng cáo
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-people me-2"></i> Người dùng
                </a>
            </li>
        </ul>

        <div class="group-title text-uppercase">
            Tương tác</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-star me-2"></i> Đánh giá
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-megaphone me-2"></i> Mã giảm giá
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-muted">@yield('page-title', 'Dashboard')</h5>
            <div class="dropdown">
                <a class="text-decoration-none text-dark dropdown-toggle" href="#" role="button"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>