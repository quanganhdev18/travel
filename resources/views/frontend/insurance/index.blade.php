@extends('layouts.master')

@section('title', 'Bảo hiểm du lịch - Travel Wonder')

@section('content')
<style>
    body {
        background-color: #f8fafc;
    }

    /* Hero Banner Section */
    .insurance-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0369a1 100%);
        padding: 90px 0 110px;
        position: relative;
        overflow: hidden;
        color: #ffffff;
    }

    .insurance-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .insurance-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40px;
        background: linear-gradient(to top, #f8fafc, transparent);
        pointer-events: none;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #38bdf8;
        margin-bottom: 24px;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: -0.02em;
        margin-bottom: 20px;
        background: linear-gradient(to right, #ffffff, #e0f2fe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-desc {
        font-size: 1.2rem;
        color: #cbd5e1;
        max-width: 680px;
        line-height: 1.7;
        margin-bottom: 32px;
    }

    .hero-stats {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(56, 189, 248, 0.15);
        color: #38bdf8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .stat-text {
        display: flex;
        flex-direction: column;
    }

    .stat-number {
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
    }

    .stat-label {
        font-size: 0.825rem;
        color: #94a3b8;
    }

    /* Section Styling */
    .section-header {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 50px;
    }

    .section-subtitle {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #0284c7;
        margin-bottom: 10px;
    }

    .section-main-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 16px;
    }

    .section-description {
        font-size: 1rem;
        color: #64748b;
        line-height: 1.6;
    }

    /* Packages Section */
    .package-card {
        background: #ffffff;
        border-radius: 24px;
        border: 2px solid #e2e8f0;
        padding: 36px 28px;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .package-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -15px rgba(14, 165, 233, 0.18);
        border-color: #38bdf8;
    }

    .package-card.popular {
        border-color: #0284c7;
        box-shadow: 0 12px 30px -10px rgba(2, 132, 199, 0.2);
        background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%);
    }

    .popular-tag {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 6px 18px;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .package-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .package-summary {
        font-size: 0.95rem;
        color: #0284c7;
        font-weight: 600;
        margin-bottom: 24px;
        min-height: 48px;
    }

    .package-price-wrap {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .package-price {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
    }

    .package-unit {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
    }

    .package-features {
        list-style: none;
        padding: 0;
        margin: 0 0 32px;
        flex-grow: 1;
    }

    .package-features li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 0.95rem;
        color: #334155;
        margin-bottom: 14px;
        line-height: 1.5;
    }

    .package-features li i {
        color: #10b981;
        font-size: 1.1rem;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .btn-select-package {
        width: 100%;
        padding: 14px 24px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .package-card.popular .btn-select-package {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #ffffff;
        border: none;
        box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    }

    .package-card.popular .btn-select-package:hover {
        background: linear-gradient(135deg, #0369a1, #075985);
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(2, 132, 199, 0.4);
        color: #ffffff;
    }

    .package-card:not(.popular) .btn-select-package {
        background: #f1f5f9;
        color: #0f172a;
        border: 1px solid #cbd5e1;
    }

    .package-card:not(.popular) .btn-select-package:hover {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
        transform: translateY(-2px);
    }

    /* Benefits Section */
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 24px;
    }

    .benefit-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 32px 24px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .benefit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
        border-color: #bae6fd;
    }

    .benefit-icon-box {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        margin-bottom: 20px;
    }

    .benefit-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .benefit-check {
        color: #10b981;
        font-size: 1.1rem;
    }

    .benefit-desc {
        font-size: 0.925rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }

    /* Registration Form Card */
    .register-section-wrap {
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .register-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        color: #ffffff;
        padding: 40px 36px;
        position: relative;
    }

    .register-header-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .register-header-subtitle {
        color: #94a3b8;
        font-size: 0.95rem;
        margin: 0;
    }

    .register-body {
        padding: 40px 36px;
    }

    .form-label-custom {
        font-size: 0.9rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1.5px solid #cbd5e1;
        font-size: 0.975rem;
        color: #0f172a;
        transition: all 0.2s ease;
        background-color: #ffffff;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12);
        outline: none;
    }

    /* Dynamic Pricing Box inside Form */
    .price-calculator-box {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px 24px;
        margin-top: 10px;
    }

    .calc-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.925rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .calc-row.total {
        margin-bottom: 0;
        padding-top: 12px;
        border-top: 1px dashed #cbd5e1;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .total-price-val {
        color: #0284c7;
        font-size: 1.4rem;
        font-weight: 800;
    }

    .btn-submit-insurance {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        border: none;
        border-radius: 14px;
        padding: 16px 32px;
        font-size: 1.1rem;
        font-weight: 800;
        width: 100%;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 8px 24px rgba(2, 132, 199, 0.3);
        cursor: pointer;
    }

    .btn-submit-insurance:hover {
        background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(2, 132, 199, 0.4);
        color: #ffffff;
    }

    /* FAQ Section */
    .faq-accordion .accordion-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px !important;
        margin-bottom: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: all 0.25s ease;
    }

    .faq-accordion .accordion-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .faq-accordion .accordion-button {
        padding: 22px 28px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        background: #ffffff;
        box-shadow: none !important;
    }

    .faq-accordion .accordion-button:not(.collapsed) {
        color: #0284c7;
        background: #f0f9ff;
    }

    .faq-accordion .accordion-body {
        padding: 20px 28px 26px;
        font-size: 0.975rem;
        color: #475569;
        line-height: 1.7;
    }

    /* Success Alert Box */
    .registration-success-alert {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 2px solid #10b981;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .insurance-hero {
            padding: 60px 0 80px;
            text-align: center;
        }

        .hero-title {
            font-size: 2.2rem;
        }

        .hero-stats {
            justify-content: center;
        }

        .section-main-title {
            font-size: 1.8rem;
        }

        .register-header, .register-body {
            padding: 24px;
        }
    }
</style>

<!-- 1. Banner (Hero Section) -->
<section class="insurance-hero">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-badge">
                    <i class="bi bi-shield-check"></i> {{ __('Bảo vệ toàn diện mọi hành trình') }}
                </div>
                <h1 class="hero-title">{{ __('Bảo hiểm du lịch') }}</h1>
                <p class="hero-desc">
                    "{{ __('Bảo vệ chuyến đi của bạn với các gói bảo hiểm toàn diện, giúp bạn an tâm khám phá mọi điểm đến.') }}"
                </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="stat-text">
                            <span class="stat-number">50.000+</span>
                            <span class="stat-label">Khách hàng tin dùng</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-text">
                            <span class="stat-number">24/7</span>
                            <span class="stat-label">Cứu hộ & Hỗ trợ y tế</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div class="stat-text">
                            <span class="stat-number">100%</span>
                            <span class="stat-label">Bồi thường minh bạch</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center position-relative">
                <div class="p-4 rounded-4" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="bi bi-shield-lock-fill text-info" style="font-size: 5rem;"></i>
                    <h5 class="mt-3 text-white fw-bold">An Tâm Du Lịch</h5>
                    <p class="small text-white-50 mb-0">Hỗ trợ khẩn cấp toàn cầu 24/7</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container my-5">
    
    @if(session('success_registration'))
        <!-- Success Alert Card -->
        <div class="registration-success-alert shadow-sm text-center">
            <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle mb-3" style="width: 64px; height: 64px;">
                <i class="bi bi-check-lg fs-1"></i>
            </div>
            <h3 class="fw-bold text-success mb-2">Đăng Ký Bảo Hiểm Thành Công!</h3>
            <p class="text-muted mb-4">Cảm ơn bạn đã lựa chọn bảo hiểm du lịch Travel Wonder. Mã đăng ký của bạn là <strong class="text-dark">{{ session('success_registration')['code'] }}</strong>.</p>
            
            <div class="row justify-content-center text-start">
                <div class="col-md-8 col-lg-6">
                    <div class="bg-white p-4 rounded-4 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Họ và tên:</span>
                            <strong class="text-dark">{{ session('success_registration')['fullname'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Gói bảo hiểm:</span>
                            <strong class="text-primary">{{ session('success_registration')['package'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Thời gian:</span>
                            <span>{{ session('success_registration')['start_date'] }} - {{ session('success_registration')['end_date'] }} ({{ session('success_registration')['total_days'] }} ngày)</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <span class="fw-bold text-dark">Tổng chi phí:</span>
                            <strong class="fs-5 text-success">{{ session('success_registration')['total_price'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. Các gói bảo hiểm -->
    <section class="my-5" id="packagesSection">
        <div class="section-header">
            <span class="section-subtitle">DANH SÁCH GÓI BẢO HIỂM</span>
            <h2 class="section-main-title">Lựa Chọn Gói Bảo Hiểm Dành Cho Bạn</h2>
            <p class="section-description">
                Được thiết kế linh hoạt đáp ứng mọi nhu cầu từ cá nhân đến gia đình với mức chi phí tối ưu nhất.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($packages as $code => $pkg)
                <div class="col-lg-4 col-md-6">
                    <div class="package-card {{ $pkg['is_popular'] ? 'popular' : '' }}">
                        @if($pkg['is_popular'])
                            <div class="popular-tag">
                                <i class="bi bi-star-fill me-1"></i> {{ $pkg['badge'] }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="package-name">{{ $pkg['name'] }}</h3>
                            @if(!$pkg['is_popular'])
                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">{{ $pkg['badge'] }}</span>
                            @endif
                        </div>

                        <p class="package-summary">{{ $pkg['summary'] }}</p>

                        <div class="package-price-wrap">
                            <span class="package-price">{{ $pkg['price_formatted'] }}</span>
                            <span class="package-unit">/ ngày / người</span>
                        </div>

                        <ul class="package-features">
                            @foreach($pkg['features'] as $feature)
                                <li>
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="#registrationForm" 
                           class="btn-select-package" 
                           onclick="selectInsurancePackage('{{ $code }}', '{{ $pkg['name'] }}', {{ $pkg['price'] }})">
                            <i class="bi bi-shield-plus"></i> Chọn gói
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 3. Quyền lợi nổi bật -->
    <section class="my-5 py-4">
        <div class="section-header">
            <span class="section-subtitle">ĐẶC QUYỀN VƯỢT TRỘI</span>
            <h2 class="section-main-title">Quyền Lợi Nổi Bật</h2>
            <p class="section-description">
                Cam kết hỗ trợ tối đa cho hành khách trong mọi tình huống phát sinh trên chuyến đi.
            </p>
        </div>

        <div class="benefits-grid">
            @foreach($highlights as $item)
                <div class="benefit-card">
                    <div class="benefit-icon-box">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </div>
                    <h4 class="benefit-title">
                        <i class="bi bi-check-circle-fill benefit-check"></i>
                        {{ $item['title'] }}
                    </h4>
                    <p class="benefit-desc">{{ $item['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 4. Đăng ký bảo hiểm -->
    <section class="my-5" id="registrationForm">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="register-section-wrap">
                    <div class="register-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.5rem;">
                                <i class="bi bi-file-earmark-medical-fill"></i>
                            </div>
                            <div>
                                <h3 class="register-header-title">Đăng Ký Bảo Hiểm Du Lịch</h3>
                                <p class="register-header-subtitle">Điền thông tin bên dưới để kích hoạt gói bảo hiểm cho chuyến đi của bạn</p>
                            </div>
                        </div>
                    </div>

                    <div class="register-body">
                        <form action="{{ route('frontend.insurance.store') }}" method="POST" id="insuranceForm">
                            @csrf
                            <div class="row g-4">
                                <!-- Họ và tên -->
                                <div class="col-md-6">
                                    <label for="fullname" class="form-label-custom">
                                        <i class="bi bi-person-fill text-primary"></i> Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-custom @error('fullname') is-invalid @enderror" 
                                           id="fullname" 
                                           name="fullname" 
                                           placeholder="Ví dụ: Nguyễn Văn A" 
                                           value="{{ old('fullname', $user->name ?? '') }}" 
                                           required>
                                    @error('fullname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Số điện thoại -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label-custom">
                                        <i class="bi bi-telephone-fill text-primary"></i> Số điện thoại <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control form-control-custom @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           placeholder="Ví dụ: 0912345678" 
                                           value="{{ old('phone', $user->phone ?? '') }}" 
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label-custom">
                                        <i class="bi bi-envelope-fill text-primary"></i> Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-custom @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           placeholder="Ví dụ: email@example.com" 
                                           value="{{ old('email', $user->email ?? '') }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Chọn gói bảo hiểm -->
                                <div class="col-md-6">
                                    <label for="package_code" class="form-label-custom">
                                        <i class="bi bi-shield-shaded text-primary"></i> Chọn gói bảo hiểm <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-custom @error('package_code') is-invalid @enderror" 
                                            id="package_code" 
                                            name="package_code" 
                                            onchange="updatePriceCalculation()" 
                                            required>
                                        @foreach($packages as $code => $pkg)
                                            <option value="{{ $code }}" 
                                                    data-price="{{ $pkg['price'] }}"
                                                    {{ old('package_code', $selectedPackage) == $code ? 'selected' : '' }}>
                                                Gói {{ $pkg['name'] }} ({{ $pkg['price_formatted'] }}/ngày)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('package_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ngày khởi hành -->
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label-custom">
                                        <i class="bi bi-calendar-event-fill text-primary"></i> Ngày khởi hành <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control form-control-custom @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           min="{{ date('Y-m-d') }}"
                                           value="{{ old('start_date', date('Y-m-d')) }}" 
                                           onchange="updatePriceCalculation()" 
                                           required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ngày kết thúc -->
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label-custom">
                                        <i class="bi bi-calendar-check-fill text-primary"></i> Ngày kết thúc <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control form-control-custom @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date" 
                                           min="{{ date('Y-m-d') }}"
                                           value="{{ old('end_date', date('Y-m-d', strtotime('+3 days'))) }}" 
                                           onchange="updatePriceCalculation()" 
                                           required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bảng tính tổng chi phí tạm tính -->
                                <div class="col-12">
                                    <div class="price-calculator-box">
                                        <div class="calc-row">
                                            <span>Gói bảo hiểm đã chọn:</span>
                                            <strong id="summaryPackageName" class="text-dark">Gói Tiêu chuẩn</strong>
                                        </div>
                                        <div class="calc-row">
                                            <span>Đơn giá theo ngày:</span>
                                            <span id="summaryUnitPrice">199.000đ / ngày</span>
                                        </div>
                                        <div class="calc-row">
                                            <span>Tổng số ngày bảo hiểm:</span>
                                            <strong id="summaryTotalDays" class="text-dark">4 ngày</strong>
                                        </div>
                                        <div class="calc-row total">
                                            <span>Tổng chi phí dự kiến:</span>
                                            <span class="total-price-val" id="summaryTotalPrice">796.000đ</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nút Đăng ký ngay -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn-submit-insurance" id="btnSubmitForm">
                                        <i class="bi bi-shield-check"></i> Đăng ký ngay
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Câu hỏi thường gặp (FAQ) -->
    <section class="my-5 py-3">
        <div class="section-header">
            <span class="section-subtitle">GIẢI ĐÁP THẮC MẮC</span>
            <h2 class="section-main-title">Câu Hỏi Thường Gặp (FAQ)</h2>
            <p class="section-description">
                Tổng hợp những thắc mắc thường gặp về bảo hiểm du lịch giúp bạn hiểu rõ quyền lợi của mình.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion faq-accordion" id="insuranceFaqAccordion">
                    @foreach($faqs as $index => $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $faq['id'] }}">
                                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-{{ $faq['id'] }}" 
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                        aria-controls="collapse-{{ $faq['id'] }}">
                                    <i class="bi bi-question-circle-fill text-primary me-3"></i>
                                    {{ $faq['question'] }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $faq['id'] }}" 
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                 aria-labelledby="heading-{{ $faq['id'] }}" 
                                 data-bs-parent="#insuranceFaqAccordion">
                                <div class="accordion-body">
                                    {{ $faq['answer'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

</div>

@push('scripts')
<script>
    const packagePrices = {
        'co_ban': { name: 'Gói Cơ bản', price: 99000, formatted: '99.000đ' },
        'tieu_chuan': { name: 'Gói Tiêu chuẩn', price: 199000, formatted: '199.000đ' },
        'cao_cap': { name: 'Gói Cao cấp', price: 399000, formatted: '399.000đ' }
    };

    function selectInsurancePackage(code, name, price) {
        const selectElem = document.getElementById('package_code');
        if (selectElem) {
            selectElem.value = code;
            updatePriceCalculation();
        }
    }

    function updatePriceCalculation() {
        const selectElem = document.getElementById('package_code');
        const startDateElem = document.getElementById('start_date');
        const endDateElem = document.getElementById('end_date');

        if (!selectElem || !startDateElem || !endDateElem) return;

        const packageCode = selectElem.value;
        const pkg = packagePrices[packageCode] || packagePrices['tieu_chuan'];

        const startDate = new Date(startDateElem.value);
        const endDate = new Date(endDateElem.value);

        // Ensure valid dates
        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            return;
        }

        // Set min end date based on start date
        endDateElem.min = startDateElem.value;
        if (endDate < startDate) {
            endDateElem.value = startDateElem.value;
        }

        // Calculate total days (inclusive)
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        const totalDays = Math.max(1, diffDays);

        const totalPrice = pkg.price * totalDays;

        // Update DOM
        document.getElementById('summaryPackageName').textContent = pkg.name;
        document.getElementById('summaryUnitPrice').textContent = pkg.formatted + ' / ngày';
        document.getElementById('summaryTotalDays').textContent = totalDays + ' ngày';
        document.getElementById('summaryTotalPrice').textContent = totalPrice.toLocaleString('vi-VN') + 'đ';
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePriceCalculation();
    });
</script>
@endpush
@endsection
