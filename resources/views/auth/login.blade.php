<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat fixed;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .bg-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: -1;
    }

    .card {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        border: none;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.95);
    }
    </style>
</head>

<body class="position-relative py-4">
    <div class="bg-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Session Status -->
                @if (session('status'))
                <div class="alert alert-success mb-4 text-center">
                    {{ session('status') }}
                </div>
                @endif
                <div class="card p-3">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="h4 text-dark mb-1">Đăng nhập</h2>
                            <p class="text-muted small">Chào mừng anh trở lại hệ thống</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required autocomplete="current-password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                <label class="form-check-label" for="remember_me">Ghi nhớ đăng nhập</label>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary fw-bold">Đăng nhập</button>
                            </div>

                            <div class="text-center mt-3 small">
                                @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none d-block mb-2">Quên
                                    mật khẩu?</a>
                                @endif
                                <span class="text-muted">Chưa có tài khoản?</span> <a href="{{ route('register') }}"
                                    class="text-decoration-none fw-bold">Đăng ký</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>