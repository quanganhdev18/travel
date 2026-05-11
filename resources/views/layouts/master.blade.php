<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Travel Wonder - Nền tảng du lịch hàng đầu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/premium-theme.css') }}">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-premium fixed-top {{ request()->is('/') ? '' : 'navbar-solid' }}">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-cursor-fill"></i> Travel<span>Wonder</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Điểm đến</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tour trọn gói</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Vé tham quan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="#"><i class="bi bi-tags-fill me-1"></i>Khuyến mãi</a>
                    </li>
                </ul>
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item me-lg-3 mb-2 mb-lg-0">
                        <a class="nav-link" href="#" style="font-weight: 500;"><i
                                class="bi bi-question-circle me-1"></i> Hỗ trợ</a>
                    </li>
                    @guest
                    <li class="nav-item me-2 mb-2 mb-lg-0">
                        <a class="btn-login-premium text-decoration-none d-block text-center" href="{{ route('login') }}">Đăng
                            nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn-register-premium text-decoration-none d-block text-center"
                            href="{{ route('register') }}">Đăng ký</a>
                    </li>
                    @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span style="font-weight: 600; color: #1a2b4c;">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                            style="border-radius: 12px; margin-top: 10px;">
                            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2"></i> Hồ sơ của
                                    tôi</a></li>
                            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-bag-check me-2"></i> Đặt chỗ của
                                    tôi</a></li>
                            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-heart me-2"></i> Danh sách đã
                                    lưu</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger"><i
                                            class="bi bi-box-arrow-right me-2"></i> Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="{{ request()->is('/') ? '' : 'pt-5 mt-4' }}">
        @if(session('success'))
        <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="container mt-4">
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer-premium">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <a class="navbar-brand text-white text-decoration-none d-inline-block mb-3" href="{{ url('/') }}"
                        style="font-size: 28px;">
                        <i class="bi bi-cursor-fill text-primary"></i> Travel<span class="text-white">Wonder</span>
                    </a>
                    <p class="mb-4">Đối tác du lịch đáng tin cậy của bạn. Chúng tôi mang đến những trải nghiệm du lịch
                        tuyệt vời với mức giá tốt nhất, kết nối hàng ngàn điểm đến trên toàn thế giới.</p>
                    <div class="d-flex">
                        <a href="#" class="social-circle"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-title">Về Travel</h5>
                    <a href="#" class="footer-link">Cách đặt chỗ</a>
                    <a href="#" class="footer-link">Liên hệ chúng tôi</a>
                    <a href="#" class="footer-link">Trợ giúp</a>
                    <a href="#" class="footer-link">Tuyển dụng</a>
                    <a href="#" class="footer-link">Về chúng tôi</a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Sản phẩm</h5>
                    <a href="#" class="footer-link">Vé máy bay</a>
                    <a href="#" class="footer-link">Khách sạn</a>
                    <a href="#" class="footer-link">Tour du lịch</a>
                    <a href="#" class="footer-link">Vé tham quan & vui chơi</a>
                    <a href="#" class="footer-link">Dịch vụ đưa đón sân bay</a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Khác</h5>
                    <a href="#" class="footer-link">Chính sách bảo mật</a>
                    <a href="#" class="footer-link">Điều khoản sử dụng</a>
                    <a href="#" class="footer-link">Quy chế hoạt động</a>
                    <a href="#" class="footer-link">Đăng ký đối tác</a>
                    <a href="#" class="footer-link">Chương trình đại lý</a>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="mb-0">&copy; {{ date('Y') }} Travel Wonder. Bảo lưu mọi quyền.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/animations.js') }}"></script>
</body>

</html>