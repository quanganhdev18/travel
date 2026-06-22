@extends('layouts.master')

@section('title', 'Tài khoản cá nhân - Travel Wonder')

@section('content')
<style>
    /* ===== Profile Page Styles ===== */
    .profile-page {
        background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 50%, #f0f9ff 100%);
        min-height: 100vh;
    }

    /* Hero Banner */
    .profile-hero {
        background: linear-gradient(135deg, #0B132B 0%, #1a2b4c 40%, #007CE8 100%);
        padding: 60px 0 120px;
        position: relative;
        overflow: hidden;
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .profile-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 80px;
        background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 50%, #f0f9ff 100%);
        border-radius: 50% 50% 0 0 / 30px 30px 0 0;
    }

    .floating-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.15;
        pointer-events: none;
    }

    .orb-1 {
        width: 300px; height: 300px;
        background: #007CE8;
        top: -100px; right: -50px;
    }

    .orb-2 {
        width: 200px; height: 200px;
        background: #F5A623;
        bottom: 20px; left: 10%;
    }

    /* Profile Card */
    .profile-card-main {
        margin-top: -80px;
        position: relative;
        z-index: 10;
    }

    .profile-avatar-section {
        background: white;
        border-radius: 28px;
        padding: 36px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255,255,255,0.8);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .profile-avatar-section::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #007CE8, #00c6ff, #F5A623);
    }

    .avatar-container {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
    }

    .avatar-ring {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        padding: 4px;
        background: linear-gradient(135deg, #007CE8, #00c6ff, #F5A623);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-inner {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid white;
        background: white;
    }

    .avatar-inner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .avatar-ring:hover .avatar-inner img {
        transform: scale(1.05);
    }

    .avatar-initials {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        font-weight: 800;
        color: white;
        letter-spacing: -1px;
    }

    .avatar-edit-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        color: white;
        border: 3px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 124, 232, 0.4);
    }

    .avatar-edit-btn:hover {
        transform: scale(1.1) rotate(15deg);
        box-shadow: 0 6px 20px rgba(0, 124, 232, 0.5);
    }

    .user-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, rgba(0,124,232,0.1), rgba(0,198,255,0.1));
        border: 1px solid rgba(0,124,232,0.2);
        color: #007CE8;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .profile-stat-item {
        background: linear-gradient(135deg, #f8faff, #f0f4ff);
        border: 1px solid rgba(0,124,232,0.1);
        border-radius: 16px;
        padding: 16px 8px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .profile-stat-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 124, 232, 0.15);
        border-color: rgba(0,124,232,0.3);
    }

    .profile-stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    .profile-stat-label {
        font-size: 0.72rem;
        color: #718096;
        font-weight: 600;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Sidebar Nav */
    .profile-nav {
        margin-top: 24px;
    }

    .profile-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 18px;
        border-radius: 14px;
        color: #718096;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.92rem;
        transition: all 0.25s ease;
        margin-bottom: 6px;
        position: relative;
        overflow: hidden;
    }

    .profile-nav-item::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(0,124,232,0.08), rgba(0,198,255,0.05));
        opacity: 0;
        transition: opacity 0.25s ease;
        border-radius: 14px;
    }

    .profile-nav-item:hover::before,
    .profile-nav-item.active::before {
        opacity: 1;
    }

    .profile-nav-item:hover,
    .profile-nav-item.active {
        color: #007CE8;
        transform: translateX(4px);
    }

    .profile-nav-item.active {
        box-shadow: 0 4px 15px rgba(0, 124, 232, 0.15);
    }

    .profile-nav-item .nav-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        background: rgba(0,124,232,0.08);
        color: #007CE8;
        transition: all 0.25s ease;
        flex-shrink: 0;
    }

    .profile-nav-item.active .nav-icon {
        background: linear-gradient(135deg, #007CE8, #0056b3);
        color: white;
        box-shadow: 0 4px 12px rgba(0, 124, 232, 0.3);
    }

    /* Content Cards */
    .content-card {
        background: white;
        border-radius: 24px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
        animation: slideInUp 0.4s ease forwards;
    }

    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .content-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f2f5;
    }

    .header-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 6px 16px rgba(0, 124, 232, 0.3);
        flex-shrink: 0;
    }

    .header-icon.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3);
    }

    .header-icon.success {
        background: linear-gradient(135deg, #10b981, #059669);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
    }

    /* Form Styles */
    .form-label-custom {
        font-weight: 700;
        font-size: 0.85rem;
        color: #374151;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-field {
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        padding: 12px 16px;
        font-weight: 500;
        color: #1f2937;
        transition: all 0.25s ease;
        background: #fafbff;
        font-size: 0.95rem;
        width: 100%;
        box-sizing: border-box;
    }

    .input-field:focus {
        border-color: #007CE8;
        background: white;
        outline: none;
        box-shadow: 0 0 0 4px rgba(0, 124, 232, 0.1);
    }

    .input-field.is-invalid {
        border-color: #ef4444;
        background: #fff5f5;
    }

    .input-with-icon {
        position: relative;
    }

    .input-with-icon .field-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 16px;
        pointer-events: none;
        transition: color 0.25s ease;
    }

    .input-with-icon .input-field {
        padding-left: 44px;
    }

    .input-with-icon:focus-within .field-icon {
        color: #007CE8;
    }

    .input-with-icon .toggle-password {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        cursor: pointer;
        font-size: 16px;
        transition: color 0.2s;
        background: none;
        border: none;
        padding: 0;
    }

    .input-with-icon .toggle-password:hover {
        color: #007CE8;
    }

    /* Buttons */
    .btn-save {
        background: linear-gradient(135deg, #007CE8, #0056b3);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 13px 32px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 6px 20px rgba(0, 124, 232, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(0, 124, 232, 0.4);
        color: white;
    }

    .btn-save:active {
        transform: translateY(0);
    }

    .btn-danger-outline {
        background: transparent;
        color: #ef4444;
        border: 2px solid #fecaca;
        border-radius: 14px;
        padding: 12px 32px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-danger-outline:hover {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
    }

    /* Password Strength */
    .password-strength {
        height: 4px;
        border-radius: 4px;
        background: #e5e7eb;
        margin-top: 8px;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.4s ease, background 0.4s ease;
        width: 0%;
    }

    .strength-text {
        font-size: 0.78rem;
        font-weight: 600;
        margin-top: 5px;
    }

    /* Identity Card */
    .identity-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }

    .identity-field {
        background: #f8faff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px 16px;
    }

    .identity-field-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .identity-field-value {
        font-weight: 700;
        color: #1a2b4c;
        font-size: 0.95rem;
    }

    /* Avatar Upload Modal */
    .avatar-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .avatar-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .avatar-modal {
        background: white;
        border-radius: 28px;
        padding: 40px;
        max-width: 460px;
        width: 90%;
        text-align: center;
        transform: scale(0.9) translateY(20px);
        transition: all 0.3s ease;
        box-shadow: 0 40px 80px rgba(0,0,0,0.2);
    }

    .avatar-modal-overlay.active .avatar-modal {
        transform: scale(1) translateY(0);
    }

    .avatar-preview-wrap {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 4px solid #007CE8;
        overflow: hidden;
        margin: 0 auto 20px;
        box-shadow: 0 8px 30px rgba(0,124,232,0.2);
    }

    .avatar-preview-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        padding: 30px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fafbff;
    }

    .drop-zone:hover,
    .drop-zone.dragover {
        border-color: #007CE8;
        background: rgba(0,124,232,0.04);
    }

    /* Alert Styles */
    .alert-custom {
        border: none;
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .alert-success-custom {
        background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.05));
        border-left: 4px solid #10b981;
        color: #065f46;
    }

    .alert-danger-custom {
        background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.05));
        border-left: 4px solid #ef4444;
        color: #7f1d1d;
    }

    /* Joined date */
    .joined-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(0,0,0,0.04);
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-hero { padding: 40px 0 100px; }
        .content-card { padding: 20px; }
        .profile-avatar-section { padding: 24px; }
    }
</style>

{{-- Avatar Upload Modal --}}
<div class="avatar-modal-overlay" id="avatarModalOverlay">
    <div class="avatar-modal">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Cập nhật ảnh đại diện</h5>
            <button class="btn btn-sm btn-light rounded-circle" id="closeAvatarModal" style="width:32px;height:32px;padding:0;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="avatar-preview-wrap mb-3" id="previewWrap">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" alt="Preview" id="avatarPreview">
            @else
                <div class="avatar-initials" id="avatarPreview" style="border-radius:50%">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('user.avatar.update') }}" enctype="multipart/form-data" id="avatarUploadForm">
            @csrf
            <div class="drop-zone mb-4" id="dropZone">
                <i class="bi bi-cloud-arrow-up fs-2 text-primary mb-2 d-block"></i>
                <p class="mb-1 fw-600">Kéo thả ảnh vào đây hoặc</p>
                <label class="btn btn-sm btn-outline-primary rounded-pill px-4 cursor-pointer mb-0">
                    <i class="bi bi-folder2-open me-1"></i> Chọn ảnh
                    <input type="file" name="avatar" id="avatarFileInput" accept="image/*" class="d-none">
                </label>
                <p class="text-muted small mt-2 mb-0">PNG, JPG, GIF tối đa 2MB</p>
            </div>

            <div class="d-flex gap-3 justify-content-center">
                <button type="button" class="btn-danger-outline" id="cancelAvatarBtn">Hủy</button>
                <button type="submit" class="btn-save" id="uploadAvatarBtn" disabled>
                    <i class="bi bi-check2"></i> Lưu ảnh
                </button>
            </div>
        </form>
    </div>
</div>

<div class="profile-page">
    {{-- Hero Banner --}}
    <div class="profile-hero">
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="text-white">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color: rgba(255,255,255,0.5);">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Trang chủ</a></li>
                        <li class="breadcrumb-item text-white active">Tài khoản của tôi</li>
                    </ol>
                </nav>
                <h1 class="fw-800 mb-1" style="color:white; font-size:2rem;">Tài khoản của tôi</h1>
                <p class="mb-0" style="color: rgba(255,255,255,0.7);">Quản lý thông tin cá nhân và bảo mật tài khoản</p>
            </div>
        </div>
    </div>

    <div class="container profile-card-main pb-5">
        <div class="row g-4">

            {{-- ======= SIDEBAR ======= --}}
            <div class="col-lg-3">
                <div class="profile-avatar-section reveal-up">

                    {{-- Avatar --}}
                    <div class="avatar-container mb-1">
                        <div class="avatar-ring">
                            <div class="avatar-inner" id="mainAvatarWrapper" data-user-avatar>
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="Ảnh đại diện" id="mainAvatarImg">
                                @else
                                    <div class="avatar-initials">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <button class="avatar-edit-btn" id="openAvatarModal" title="Đổi ảnh đại diện">
                            <i class="bi bi-camera-fill"></i>
                        </button>
                    </div>

                    <div class="user-badge">
                        <i class="bi bi-patch-check-fill"></i> Thành viên
                    </div>

                    <h5 class="fw-800 mb-1" style="color: #0B132B;">{{ $user->name }}</h5>
                    <p class="text-muted small mb-1">{{ $user->email }}</p>
                    @if($user->phone)
                        <p class="text-muted small mb-3">
                            <i class="bi bi-telephone-fill me-1 text-primary"></i>{{ $user->phone }}
                        </p>
                    @endif

                    <div class="joined-chip mb-4">
                        <i class="bi bi-calendar3"></i>
                        Tham gia {{ $user->created_at->format('m/Y') }}
                    </div>

                    {{-- Thống kê --}}
                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ $user->bookings->count() }}</div>
                                <div class="profile-stat-label">Đơn đặt</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ $user->wishlists->count() }}</div>
                                <div class="profile-stat-label">Yêu thích</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ $user->reviews->count() }}</div>
                                <div class="profile-stat-label">Đánh giá</div>
                            </div>
                        </div>
                    </div>

                    {{-- Nav --}}
                    <nav class="profile-nav">
                        <a href="{{ route('user.profile') }}" class="profile-nav-item active">
                            <span class="nav-icon"><i class="bi bi-person-fill"></i></span>
                            Thông tin cá nhân
                        </a>
                        <a href="{{ route('user.bookings') }}" class="profile-nav-item">
                            <span class="nav-icon"><i class="bi bi-bag-check-fill"></i></span>
                            Đơn đặt tour
                        </a>
                        <a href="{{ route('user.wishlists') }}" class="profile-nav-item">
                            <span class="nav-icon"><i class="bi bi-heart-fill"></i></span>
                            Danh sách yêu thích
                        </a>
                    </nav>
                </div>
            </div>

            {{-- ======= NỘI DUNG CHÍNH ======= --}}
            <div class="col-lg-9">

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert-custom alert-success-custom reveal-up">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-custom alert-danger-custom reveal-up">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{-- ===== Thông tin cơ bản ===== --}}
                <div class="content-card reveal-up">
                    <div class="content-card-header">
                        <div class="header-icon">
                            <i class="bi bi-person-vcard-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-800 mb-0" style="color:#0B132B;">Thông tin cơ bản</h5>
                            <p class="text-muted small mb-0">Cập nhật tên, email và số điện thoại của bạn</p>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert-custom alert-danger-custom mb-4">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.profile.update') }}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Họ và tên <span class="text-danger">*</span></label>
                                <div class="input-with-icon">
                                    <i class="bi bi-person field-icon"></i>
                                    <input type="text" name="name" class="input-field @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" placeholder="Nguyễn Văn A" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Số điện thoại</label>
                                <div class="input-with-icon">
                                    <i class="bi bi-phone field-icon"></i>
                                    <input type="tel" name="phone" class="input-field"
                                        value="{{ old('phone', $user->phone) }}" placeholder="0912 345 678">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Email <span class="text-danger">*</span></label>
                                <div class="input-with-icon">
                                    <i class="bi bi-envelope field-icon"></i>
                                    <input type="email" name="email" class="input-field @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" placeholder="example@email.com" required>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end pt-2">
                                <button type="submit" class="btn-save">
                                    <i class="bi bi-check2-circle"></i>
                                    Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ===== Thông tin định danh ===== --}}
                @if($user->identity)
                <div class="content-card reveal-up">
                    <div class="content-card-header">
                        <div class="header-icon success">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-800 mb-0" style="color:#0B132B;">Thông tin định danh</h5>
                            <p class="text-muted small mb-0">CCCD / Hộ chiếu được xác minh. Liên hệ hỗ trợ để thay đổi.</p>
                        </div>
                        <span class="badge ms-auto" style="background: linear-gradient(135deg,#10b981,#059669); border-radius: 10px; padding: 6px 14px;">
                            <i class="bi bi-patch-check-fill me-1"></i>Đã xác minh
                        </span>
                    </div>

                    <div class="identity-info-grid">
                        <div class="identity-field">
                            <div class="identity-field-label">Số CCCD / Hộ chiếu</div>
                            <div class="identity-field-value">{{ $user->identity->identity_number }}</div>
                        </div>
                        <div class="identity-field">
                            <div class="identity-field-label">Họ và tên đầy đủ</div>
                            <div class="identity-field-value">{{ $user->identity->full_name }}</div>
                        </div>
                        <div class="identity-field">
                            <div class="identity-field-label">Ngày sinh</div>
                            <div class="identity-field-value">{{ $user->identity->date_of_birth->format('d/m/Y') }}</div>
                        </div>
                        <div class="identity-field">
                            <div class="identity-field-label">Giới tính</div>
                            <div class="identity-field-value">
                                @if($user->identity->gender === 'male') <i class="bi bi-gender-male text-primary me-1"></i>Nam
                                @elseif($user->identity->gender === 'female') <i class="bi bi-gender-female text-danger me-1"></i>Nữ
                                @else <i class="bi bi-gender-ambiguous text-secondary me-1"></i>Khác
                                @endif
                            </div>
                        </div>
                        <div class="identity-field">
                            <div class="identity-field-label">Ngày hết hạn giấy tờ</div>
                            <div class="identity-field-value">{{ $user->identity->expiry_date->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ===== Đổi mật khẩu ===== --}}
                <div class="content-card reveal-up">
                    <div class="content-card-header">
                        <div class="header-icon warning">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-800 mb-0" style="color:#0B132B;">Thay đổi mật khẩu</h5>
                            <p class="text-muted small mb-0">Sử dụng mật khẩu mạnh để bảo vệ tài khoản</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('user.password.change') }}" id="passwordForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label-custom">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <div class="input-with-icon">
                                    <i class="bi bi-lock field-icon"></i>
                                    <input type="password" name="current_password" id="currentPassword" class="input-field" placeholder="Nhập mật khẩu hiện tại" required>
                                    <button type="button" class="toggle-password" data-target="currentPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Mật khẩu mới <span class="text-danger">*</span></label>
                                <div class="input-with-icon">
                                    <i class="bi bi-lock-fill field-icon"></i>
                                    <input type="password" name="password" id="newPassword" class="input-field" placeholder="Tối thiểu 8 ký tự" required>
                                    <button type="button" class="toggle-password" data-target="newPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength mt-2">
                                    <div class="password-strength-bar" id="strengthBar"></div>
                                </div>
                                <div class="strength-text" id="strengthText"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <div class="input-with-icon">
                                    <i class="bi bi-lock-fill field-icon"></i>
                                    <input type="password" name="password_confirmation" id="confirmPassword" class="input-field" placeholder="Nhập lại mật khẩu mới" required>
                                    <button type="button" class="toggle-password" data-target="confirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="strength-text" id="matchText"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-end pt-2">
                                <button type="submit" class="btn-save" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 6px 20px rgba(245,158,11,0.3);">
                                    <i class="bi bi-shield-check"></i>
                                    Cập nhật mật khẩu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>{{-- end col-lg-9 --}}
        </div>{{-- end row --}}
    </div>{{-- end container --}}
</div>{{-- end profile-page --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== Scroll reveal =====
    const revealEls = document.querySelectorAll('.reveal-up');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });
    revealEls.forEach(el => observer.observe(el));

    // ===== Avatar Modal =====
    const overlay = document.getElementById('avatarModalOverlay');
    const openBtn = document.getElementById('openAvatarModal');
    const closeBtn = document.getElementById('closeAvatarModal');
    const cancelBtn = document.getElementById('cancelAvatarBtn');
    const fileInput = document.getElementById('avatarFileInput');
    const uploadBtn = document.getElementById('uploadAvatarBtn');
    const dropZone = document.getElementById('dropZone');
    const preview = document.getElementById('avatarPreview');

    function openModal() { overlay.classList.add('active'); }
    function closeModal() {
        overlay.classList.remove('active');
        fileInput.value = '';
        uploadBtn.disabled = true;
        // Reset preview to original
        resetPreview();
    }

    function resetPreview() {
        @if($user->avatar)
            if (preview.tagName === 'IMG') {
                preview.src = "{{ $user->avatar }}";
            }
        @endif
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });

    function updateUserAvatars(imageSrc) {
        document.querySelectorAll('[data-user-avatar]').forEach(function (el) {
            el.innerHTML = '<img src="' + imageSrc + '" alt="Ảnh đại diện" class="rounded-circle w-100 h-100" style="object-fit:cover;width:100%;height:100%;">';
        });
    }

    // File input change
    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.size > 2 * 1024 * 1024) {
                alert('Ảnh quá lớn! Vui lòng chọn ảnh dưới 2MB.');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewWrap = document.getElementById('previewWrap');
                previewWrap.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="width:100%;height:100%;object-fit:cover;">';
                updateUserAvatars(e.target.result);
            };
            reader.readAsDataURL(file);
            uploadBtn.disabled = false;
        }
    });

    // Drag & Drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    // ===== Toggle Password Visibility =====
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    // ===== Password Strength =====
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const matchText = document.getElementById('matchText');

    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        return strength;
    }

    newPasswordInput.addEventListener('input', function() {
        const val = this.value;
        if (!val) {
            strengthBar.style.width = '0';
            strengthText.textContent = '';
            return;
        }
        const s = checkPasswordStrength(val);
        const levels = [
            { w: '20%', c: '#ef4444', t: '⚠️ Rất yếu', tc: '#ef4444' },
            { w: '40%', c: '#f97316', t: '⚠️ Yếu', tc: '#f97316' },
            { w: '60%', c: '#eab308', t: '👍 Trung bình', tc: '#eab308' },
            { w: '80%', c: '#22c55e', t: '✅ Mạnh', tc: '#22c55e' },
            { w: '100%', c: '#16a34a', t: '🔒 Rất mạnh', tc: '#16a34a' },
        ];
        const lvl = levels[Math.min(s - 1, 4)];
        strengthBar.style.width = lvl.w;
        strengthBar.style.background = lvl.c;
        strengthText.textContent = lvl.t;
        strengthText.style.color = lvl.tc;
    });

    confirmPasswordInput.addEventListener('input', function() {
        const pw = newPasswordInput.value;
        const cpw = this.value;
        if (!cpw) { matchText.textContent = ''; return; }
        if (pw === cpw) {
            matchText.textContent = '✅ Mật khẩu khớp';
            matchText.style.color = '#22c55e';
        } else {
            matchText.textContent = '❌ Mật khẩu không khớp';
            matchText.style.color = '#ef4444';
        }
    });
});
</script>
@endsection
