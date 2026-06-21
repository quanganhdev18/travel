@extends('layouts.master')

@section('content')
<style>
    .form-section-title {
        color: var(--dark-blue);
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .form-section-title i {
        background: rgba(0, 124, 232, 0.1);
        color: var(--primary-color);
        padding: 8px 12px;
        border-radius: 12px;
        font-size: 1.25rem;
    }
    .transport-option {
        border-radius: 20px;
        border: 2px solid #e2e8f0;
        transition: var(--transition-fast);
        background: white;
    }
    .btn-check:checked + .transport-option {
        border-color: var(--primary-color);
        background: rgba(0, 124, 232, 0.03);
        box-shadow: 0 4px 15px rgba(0, 124, 232, 0.15);
    }
    .btn-check:checked + .transport-option i {
        color: var(--primary-color) !important;
    }

    /* Wizard Styles */
    .wizard-steps { display: flex; justify-content: space-between; margin-bottom: 2rem; position: relative; }
    .wizard-steps::before { content: ''; position: absolute; top: 20px; left: 10%; width: 80%; height: 2px; background: #e2e8f0; z-index: 1; }
    .wizard-step { position: relative; z-index: 2; text-align: center; background: white; padding: 0 10px; flex: 1; }
    .wizard-step-circle { width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; color: #64748b; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; margin: 0 auto 8px auto; transition: all 0.3s; border: 4px solid white; }
    .wizard-step.active .wizard-step-circle { background: var(--primary-color); color: white; box-shadow: 0 0 0 4px rgba(0, 124, 232, 0.2); }
    .wizard-step.completed .wizard-step-circle { background: #10b981; color: white; }
    .wizard-step-title { font-size: 0.85rem; color: #64748b; font-weight: 500; }
    .wizard-step.active .wizard-step-title { color: var(--primary-color); font-weight: 600; }
    .wizard-panel { display: none; }
    .wizard-panel.active { display: block; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container py-5">
    <div class="row g-5">
        <!-- Main Booking Form -->
        <div class="col-lg-8 reveal-up">
            <div class="premium-card p-4 p-md-5 border-0">
                <!-- Header -->
                <div class="mb-5 border-bottom pb-4">
                    <h2 class="section-heading mb-2">{{ __('Hoàn Tất Đặt Tour') }}</h2>
                    <p class="text-muted fw-500 mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-check text-primary me-2"></i> {{ __('Hành trình:') }}
                        {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }} - 
                        {{ \Carbon\Carbon::parse($schedule->return_date)->format('d/m/Y') }}
                    </p>
                    @if($holidaySurcharge > 0)
                        <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                            <div>
                                <strong>Lưu ý phụ thu dịp Lễ/Tết:</strong> Tour khởi hành vào dịp lễ nên áp dụng phụ thu {{ $holidaySurcharge }}%. Giá trên đã bao gồm phụ thu.
                            </div>
                        </div>
                    @endif
                </div>

                <form action="{{ route('frontend.tours.store') }}" method="POST" id="checkout-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <input type="hidden" name="adults" value="{{ $adults }}">
                    <input type="hidden" name="children" value="{{ $children }}">
                    <input type="hidden" name="total_price" id="input_total_price" value="{{ $totalPrice }}">
                    <input type="hidden" name="transport_price" id="input_transport_price" value="0">
                    <input type="hidden" name="transport_data" id="input_transport_data" value="">

                    <!-- Wizard Progress -->
                    <div class="wizard-steps">
                        <div class="wizard-step active" id="step-nav-1">
                            <div class="wizard-step-circle">1</div>
                            <div class="wizard-step-title">Hành Khách</div>
                        </div>
                        <div class="wizard-step" id="step-nav-2">
                            <div class="wizard-step-circle">2</div>
                            <div class="wizard-step-title">Dịch Vụ</div>
                        </div>
                        <div class="wizard-step" id="step-nav-3">
                            <div class="wizard-step-circle">3</div>
                            <div class="wizard-step-title">Thanh Toán</div>
                        </div>
                    </div>

                    <!-- WIZARD STEP 1 -->
                    <div class="wizard-panel active" id="step-panel-1">
                        <!-- Section 1: Thông Tin Người Đặt -->
                        <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-person-badge"></i>
                            {{ __('Thông Tin Người Đặt') }}
                        </h4>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-600 text-dark">{{ __('Họ và Tên') }} <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control search-form-control"
                                    value="{{ $identity->full_name ?? $user->name }}" required
                                    placeholder="{{ __('Nhập tên đầy đủ (khớp với CCCD/Hộ chiếu)') }}"
                                    oninput="document.getElementById('hidden_adult_name').value = this.value">
                                <input type="hidden" name="passengers[adult][0][full_name]" id="hidden_adult_name" value="{{ $identity->full_name ?? $user->name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600 text-dark">{{ __('Số Điện Thoại') }} <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" class="form-control search-form-control"
                                    value="{{ $user->phone ?? '' }}" required placeholder="+84 (0)...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600 text-dark">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email" class="form-control search-form-control"
                                    value="{{ $user->email }}" required placeholder="email@example.com">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-600 text-dark">{{ __('Số CCCD/Hộ Chiếu') }} <span class="text-danger">*</span></label>
                                <input type="text" name="passengers[adult][0][identity_number]" id="identity_number" class="form-control search-form-control" required placeholder="Nhập số CCCD/Passport" value="{{ $identity->identity_number ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-600 text-dark">{{ __('Ngày Sinh') }} <span class="text-danger">*</span></label>
                                <input type="date" name="passengers[adult][0][date_of_birth]" id="date_of_birth" class="form-control search-form-control" required value="{{ $identity->date_of_birth ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-600 text-dark">{{ __('Giới Tính') }} <span class="text-danger">*</span></label>
                                <select name="passengers[adult][0][gender]" id="gender" class="form-select search-form-control" required>
                                    <option value="">{{ __('-- Chọn --') }}</option>
                                    <option value="male" {{ ($identity->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('Nam') }}</option>
                                    <option value="female" {{ ($identity->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('Nữ') }}</option>
                                    <option value="other" {{ ($identity->gender ?? '') == 'other' ? 'selected' : '' }}>{{ __('Khác') }}</option>
                                </select>
                            </div>
                            
                            <!-- Hidden identity details -->
                            <input type="hidden" name="issue_date" id="issue_date" value="{{ $identity->issue_date ?? '2020-01-01' }}">
                            <input type="hidden" name="expiry_date" id="expiry_date" value="{{ $identity->expiry_date ?? '2040-01-01' }}">
                            <input type="hidden" name="issue_place" id="issue_place" value="{{ $identity->issue_place ?? 'Hà Nội' }}">
                            
                            <!-- CCCD Scan block -->
                            <div class="col-12 mt-3">
                                <div class="p-3 bg-light rounded border">
                                    <label class="form-label fw-600 text-dark">{{ __('Quét CCCD tự động điền (Tùy chọn)') }}</label>
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-5">
                                            <div class="text-muted small mb-1">Mặt trước CCCD</div>
                                            <input type="file" name="front_image" id="front_image" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-md-5">
                                            <div class="text-muted small mb-1">Mặt sau CCCD</div>
                                            <input type="file" name="back_image" id="back_image" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary w-100" id="btn-scan-cccd" style="height: 38px;">
                                                <i class="bi bi-upc-scan"></i> Quét
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary px-5 py-2 btn-next" data-next="2">
                                Tiếp tục <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div> <!-- END WIZARD STEP 1 -->

                    <!-- WIZARD STEP 2 -->
                    <div class="wizard-panel" id="step-panel-2">
                        <!-- Section 3: Phương Thức Vận Chuyển -->
                        <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-airplane"></i>
                            {{ __('Di Chuyển Đến Điểm Khởi Hành') }}
                        </h4>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="transport_type" id="transport_flight" value="flight">
                                <label class="transport-option w-100 p-3 text-start" for="transport_flight">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-airplane text-muted" style="font-size: 28px;"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold fs-6 text-dark">{{ __('Vé Máy Bay') }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="transport_type" id="transport_bus" value="bus">
                                <label class="transport-option w-100 p-3 text-start" for="transport_bus">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-bus-front text-muted" style="font-size: 28px;"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold fs-6 text-dark">{{ __('Xe Khách') }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="transport_type" id="transport_self" value="self" checked>
                                <label class="transport-option w-100 p-3 text-start" for="transport_self">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-car-front text-muted" style="font-size: 28px;"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold fs-6 text-dark">{{ __('Tự Túc') }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Vùng hiển thị kết quả phương tiện (AJAX) -->
                        <div id="transport_options_container" style="display: none;" class="p-4 bg-light rounded border">
                            <div class="mb-4 pb-3 border-bottom">
                                <label class="form-label fw-bold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ __('Chọn điểm xuất phát của bạn') }}</label>
                                <select id="customer_origin_select" class="form-select form-select-lg border-primary shadow-sm" style="max-width: 400px;">
                                    <option value="HAN">{{ __('Hà Nội (HAN)') }}</option>
                                    <option value="SGN" selected>{{ __('TP. Hồ Chí Minh (SGN)') }}</option>
                                    <option value="DAD">{{ __('Đà Nẵng (DAD)') }}</option>
                                    <option value="HPH">{{ __('Hải Phòng (HPH)') }}</option>
                                    <option value="VCA">{{ __('Cần Thơ (VCA)') }}</option>
                                    <option value="PQC">{{ __('Phú Quốc (PQC)') }}</option>
                                </select>
                            </div>
                            <div id="transport_loading" style="display: none;" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">{{ __('Đang tìm kiếm chuyến đi phù hợp nhất...') }}</div>
                            </div>
                            <div id="transport_results"></div>
                        </div>
                    </div>
                    
                    @if($schedule->tour->tickets && $schedule->tour->tickets->isNotEmpty())
                    <!-- Section: Vé Tham Quan -->
                    <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-ticket-detailed"></i>
                            {{ __('Vé Tham Quan (Tùy chọn)') }}
                        </h4>
                        
                        <div class="row g-4">
                            @foreach($schedule->tour->tickets as $ticket)
                                <div class="col-12">
                                    <div class="card border shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold text-primary">{{ $ticket->title }}</h6>
                                            <small class="text-muted">{{ $ticket->description }}</small>
                                        </div>
                                        <div class="card-body p-0">
                                            <ul class="list-group list-group-flush">
                                                @foreach($ticket->ticket_options as $option)
                                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                    <div>
                                                        <div class="fw-bold">{{ $option->name }}</div>
                                                        <div class="text-danger fw-bold">{{ format_currency($option->price) }}</div>
                                                    </div>
                                                    <div class="d-flex align-items-center" style="width: 120px;">
                                                        <input type="number" name="tickets[{{ $option->id }}]" class="form-control ticket-qty-input text-center" value="0" min="0" max="99" data-price="{{ $option->price }}">
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($schedule->tour->addons && $schedule->tour->addons->isNotEmpty())
                    <!-- Section: Dịch vụ Addon -->
                    <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-plus-circle-dotted"></i>
                            {{ __('Dịch vụ bổ sung (Add-ons)') }}
                        </h4>
                        
                        <div class="row g-4">
                            @foreach($schedule->tour->addons as $addon)
                                @if($addon->is_active)
                                <div class="col-12">
                                    <div class="card border shadow-sm">
                                        <div class="card-body p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($addon->image_url)
                                                    <img src="{{ asset($addon->image_url) }}" alt="{{ $addon->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1 fw-bold text-primary">{{ $addon->name }}</h6>
                                                    <div class="text-danger fw-bold" id="addon_price_display_{{ $addon->id }}" data-base-price="{{ $addon->price }}">{{ format_currency($addon->price) }}</div>
                                                    @if($addon->description)
                                                        <small class="text-muted d-block mt-1">{{ $addon->description }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <div>
                                                    <label class="small text-muted mb-1">Ngày sử dụng</label>
                                                    <input type="date" name="addons[{{ $addon->id }}][usage_date]" class="form-control form-control-sm addon-date-input" 
                                                        value="{{ \Carbon\Carbon::parse($schedule->departure_date)->format('Y-m-d') }}"
                                                        min="{{ \Carbon\Carbon::parse($schedule->departure_date)->format('Y-m-d') }}"
                                                        max="{{ \Carbon\Carbon::parse($schedule->return_date)->format('Y-m-d') }}"
                                                        data-addon-id="{{ $addon->id }}"
                                                        >
                                                </div>
                                                <div>
                                                    <label class="small text-muted mb-1">Số lượng</label>
                                                    <input type="number" name="addons[{{ $addon->id }}][qty]" class="form-control form-control-sm addon-qty-input text-center" style="width: 70px;" value="0" min="0" max="99" data-addon-id="{{ $addon->id }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 btn-prev" data-prev="1">
                                <i class="bi bi-arrow-left me-2"></i> Quay lại
                            </button>
                            <button type="button" class="btn btn-primary px-5 py-2 btn-next" data-next="3">
                                Tiếp tục <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div> <!-- END WIZARD STEP 2 -->

                    <!-- WIZARD STEP 3 -->
                    <div class="wizard-panel" id="step-panel-3">
                        <!-- Section 4: Hình Thức Thanh Toán (100% or 30%) -->
                        <div class="mb-5">
                            <h4 class="form-section-title">
                                <i class="bi bi-cash-coin"></i>
                                {{ __('Hình Thức Thanh Toán') }}
                            </h4>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_type" id="payment_type_full" value="full" checked>
                                    <label class="transport-option w-100 p-4 text-start" for="payment_type_full">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check2-all text-muted" style="font-size: 32px;"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-5 text-dark">{{ __('Thanh toán 100%') }}</div>
                                                <div class="small text-muted mt-1">{{ __('Thanh toán toàn bộ giá trị đơn hàng') }}</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_type" id="payment_type_deposit" value="deposit">
                                    <label class="transport-option w-100 p-4 text-start" for="payment_type_deposit">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-pie-chart text-muted" style="font-size: 32px;"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-5 text-dark">{{ __('Đặt cọc 30% giữ chỗ') }}</div>
                                                <div class="small text-muted mt-1">{{ __('Phần còn lại thanh toán sau') }}</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Phương Thức Thanh Toán -->
                        <div class="mb-5">
                            <h4 class="form-section-title">
                                <i class="bi bi-credit-card"></i>
                                {{ __('Phương Thức Thanh Toán') }}
                            </h4>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="payment_transfer" value="transfer" checked>
                                    <label class="transport-option w-100 p-4 text-start" for="payment_transfer">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-bank text-muted" style="font-size: 32px;"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-5 text-dark">{{ __('Tiền mặt / Chuyển khoản') }}</div>
                                                <div class="small text-muted mt-1">{{ __('Chuyển khoản thủ công hoặc nộp tiền mặt') }}</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="payment_vnpay" value="vnpay">
                                    <label class="transport-option w-100 p-4 text-start" for="payment_vnpay">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-qr-code-scan text-muted" style="font-size: 32px;"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-5 text-dark">{{ __('Thanh toán qua VNPay') }}</div>
                                                <div class="small text-muted mt-1">{{ __('Cổng thanh toán điện tử an toàn') }}</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Mã Khuyến Mãi -->
                        <div class="mb-5">
                            <h4 class="form-section-title">
                                <i class="bi bi-tags"></i>
                                {{ __('Mã Khuyến Mãi') }}
                            </h4>
                            <div class="card border-0 bg-light p-4 rounded-4">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control form-control-lg border-primary" id="coupon_code_input" name="coupon_code" placeholder="{{ __('Nhập mã giảm giá') }}">
                                    <button class="btn btn-primary px-4" type="button" id="btn_apply_coupon">{{ __('Áp dụng') }}</button>
                                </div>
                                <div id="coupon_message" class="small mt-2" style="display: none;"></div>
                                <input type="hidden" name="discount_amount" id="input_discount_amount" value="0">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 btn-prev" data-prev="2">
                                <i class="bi bi-arrow-left me-2"></i> Quay lại
                            </button>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-register-premium px-5 py-2 fs-5">
                                <i class="bi bi-shield-check me-2"></i> {{ __('Xác Nhận Thanh Toán') }}
                            </button>
                        </div>
                    </div> <!-- END WIZARD STEP 3 -->
                </form>
            </div>
        </div>

        <!-- Booking Summary Sidebar -->
        <div class="col-lg-4 reveal-up" style="transition-delay: 0.2s;">
            <div class="glass-panel p-4 p-md-5 sticky-top" style="top: 100px;">
                <h4 class="fw-bold mb-4">{{ __('Tóm Tắt Đơn Hàng') }}</h4>

                <!-- Tour Info -->
                <div class="mb-4 pb-4 border-bottom">
                    <h6 class="fw-bold text-dark fs-5 mb-3 lh-base">{{ $schedule->tour->title }}</h6>
                    <div class="d-flex align-items-center text-muted fw-500 mb-2">
                        <i class="bi bi-geo-alt fs-5 text-danger me-3"></i> 
                        {{ $schedule->tour->destination->name ?? __('Đang cập nhật') }}
                    </div>
                    <div class="d-flex align-items-center text-muted fw-500">
                        <i class="bi bi-calendar-event fs-5 text-primary me-3"></i> 
                        {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}
                    </div>
                </div>

                <!-- Passenger Count -->
                <div class="mb-4 pb-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-500">{{ __('Người lớn:') }}</span>
                        <strong class="text-dark">{{ $adults }} × {{ format_currency($schedule->tour->base_price) }}</strong>
                    </div>
                    @if($children > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-500">{{ __('Trẻ em:') }}</span>
                        <strong class="text-dark">{{ $children }} × {{ format_currency($schedule->tour->child_price ?? ($schedule->tour->base_price * 0.75)) }}</strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-muted fw-500">{{ __('Tổng khách:') }}</span>
                        <strong class="fs-5">{{ $totalPersons }} {{ __('người') }}</strong>
                    </div>
                </div>

                <!-- Total Price -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" id="transport_fee_row" style="display: none !important;">
                        <span class="text-muted fw-500">{{ __('Phí di chuyển:') }}</span>
                        <strong class="text-dark" id="display_transport_price">0 đ</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" id="ticket_fee_row" style="display: none !important;">
                        <span class="text-muted fw-500">{{ __('Vé tham quan:') }}</span>
                        <strong class="text-dark" id="display_ticket_price">0 đ</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" id="addon_fee_row" style="display: none !important;">
                        <span class="text-muted fw-500">{{ __('Dịch vụ thêm:') }}</span>
                        <strong class="text-dark" id="display_addon_price">0 đ</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" id="coupon_fee_row" style="display: none !important;">
                        <span class="text-success fw-500"><i class="bi bi-tag-fill me-1"></i>{{ __('Giảm giá:') }}</span>
                        <strong class="text-success" id="display_coupon_discount">0 đ</strong>
                    </div>
                    <div class="text-muted fw-500 mb-2">{{ __('Tổng Tiền Đơn Hàng:') }}</div>
                    <div class="text-danger fw-bold lh-1" style="font-size: 2rem;" id="display_total_price">
                        {!! format_currency($totalPrice) !!}
                    </div>

                    <div id="deposit_amount_row" style="display: none;" class="mt-4 pt-3 border-top border-primary border-2">
                        <div class="text-primary fw-500 mb-2"><i class="bi bi-check2-circle me-1"></i>{{ __('Cần Thanh Toán Ngay (Cọc 30%):') }}</div>
                        <div class="text-primary fw-bold lh-1" style="font-size: 1.8rem;" id="display_deposit_price">
                            0 đ
                        </div>
                        <div class="text-muted small mt-2">{{ __('Còn lại (70%) thanh toán sau:') }} <span class="fw-bold text-dark" id="display_remaining_price">0 đ</span></div>
                    </div>
                </div>

                <div class="d-flex align-items-start text-muted small lh-lg mt-4 pt-4 border-top">
                    <i class="bi bi-shield-lock text-success fs-5 me-2 mt-n1"></i>
                    {{ __('Thông tin của bạn được bảo mật tuyệt đối an toàn.') }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btn-scan-cccd').addEventListener('click', function() {
        const frontImage = document.getElementById('front_image').files[0];
        const backImage = document.getElementById('back_image').files[0];
        
        if (!frontImage) {
            alert('Vui lòng tải lên ảnh mặt trước CCCD để hệ thống đọc dữ liệu.');
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang phân tích...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('front_image', frontImage);
        if (backImage) {
            formData.append('back_image', backImage);
        }

        fetch('/api/scan-cccd', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.message || 'Lỗi server');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const idInput = document.getElementById('identity_number');
                    const nameInput = document.getElementById('customer_name');
                    const dobInput = document.getElementById('date_of_birth');
                    const genderInput = document.getElementById('gender');
                    const issueDateInput = document.getElementById('issue_date');
                    const expiryDateInput = document.getElementById('expiry_date');
                    const issuePlaceInput = document.getElementById('issue_place');
                    
                    if(idInput) idInput.value = data.id || '';
                    if(nameInput) {
                        nameInput.value = data.name || '';
                        document.getElementById('hidden_adult_name').value = nameInput.value;
                    }
                    if(dobInput) dobInput.value = formatDob(data.dob) || '';
                    if(issueDateInput && data.issue_date) issueDateInput.value = formatDob(data.issue_date);
                    if(expiryDateInput && data.expiry_date && data.expiry_date !== 'N/A' && data.expiry_date !== 'KHÔNG THỜI HẠN') {
                        expiryDateInput.value = formatDob(data.expiry_date);
                    }
                    if(issuePlaceInput && data.issue_place) issuePlaceInput.value = data.issue_place;

                    if(genderInput) {
                        if (data.sex === 'nam') genderInput.value = 'male';
                        else if (data.sex === 'nữ') genderInput.value = 'female';
                    }
                } else {
                    fillMockData();
                }
            })
            .catch(error => {
                console.error("OCR Error:", error);
                fillMockData();
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    });

    function fillMockData() {
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const uniqueId = `036${timestamp}${random}`;

        const nameInput = document.getElementById('customer_name');
        if(nameInput && !nameInput.value) {
            nameInput.value = 'Nguyễn Văn A (Mock)';
            document.getElementById('hidden_adult_name').value = nameInput.value;
        }

        document.getElementById('identity_number').value = uniqueId;
        document.getElementById('date_of_birth').value = '1996-05-18';
        document.getElementById('gender').value = 'male';
        document.getElementById('issue_date').value = '2021-05-18';
        document.getElementById('expiry_date').value = '2036-05-18';
        document.getElementById('issue_place').value = 'cục cảnh sát quản lý hành chính về trật tự xã hội';
    }

    function formatDob(dobStr) {
        if (!dobStr) return '';
        const parts = dobStr.split('/');
        if (parts.length === 3) return `${parts[2]}-${parts[1]}-${parts[0]}`;
        return dobStr;
    }

    // TRANSPORT DYNAMIC LOGIC
    @php
        $iataMap = [
            'Đà Nẵng' => 'DAD',
            'Thành Phố Hồ Chí Minh' => 'SGN',
            'Hà Nội' => 'HAN',
            'Phú Quốc' => 'PQC',
            'Nha Trang' => 'CXR',
            'Huế' => 'HUI',
            'Vinh' => 'VII',
            'Đà Lạt' => 'DLI',
            'Hải Phòng' => 'HPH',
        ];
        $departureLoc = $schedule->tour->departure_location->name ?? '';
        $tourDepartureCode = $iataMap[$departureLoc] ?? 'SGN';
        $departureDate = \Carbon\Carbon::parse($schedule->departure_date)->format('Y-m-d');
    @endphp

    const transportRadios = document.querySelectorAll('input[name="transport_type"]');
    const transportContainer = document.getElementById('transport_options_container');
    const transportLoading = document.getElementById('transport_loading');
    const transportResults = document.getElementById('transport_results');
    
    const inputTransportPrice = document.getElementById('input_transport_price');
    const inputTransportData = document.getElementById('input_transport_data');
    const inputTotalPrice = document.getElementById('input_total_price');
    
    const displayTransportPrice = document.getElementById('display_transport_price');
    const displayTotalPrice = document.getElementById('display_total_price');
    const transportFeeRow = document.getElementById('transport_fee_row');
    
    const displayTicketPrice = document.getElementById('display_ticket_price');
    const ticketFeeRow = document.getElementById('ticket_fee_row');

    const baseTourPrice = {{ $totalPrice }};
    const totalPersonsCount = {{ $totalPersons }};
    
    let currentTransportPrice = 0;
    let currentTicketPrice = 0;

    const currency = '{{ Session::get("currency", "VND") }}';
    let rate = 1;
    let symbol = ' VNĐ';
    let prefix = false;

    switch (currency) {
        case 'USD': rate = 25000; symbol = '$'; prefix = true; break;
        case 'EUR': rate = 27000; symbol = '€'; prefix = true; break;
        case 'CNY': rate = 3500; symbol = '¥'; prefix = true; break;
        case 'VND':
        default: rate = 1; symbol = ' VNĐ'; prefix = false; break;
    }

    function formatCurrencyDynamic(amount) {
        const converted = amount / rate;
        let formatted = new Intl.NumberFormat(currency === 'VND' ? 'vi-VN' : 'en-US', {
            minimumFractionDigits: currency === 'VND' ? 0 : 2,
            maximumFractionDigits: currency === 'VND' ? 0 : 2
        }).format(converted);
        return prefix ? symbol + formatted : formatted + symbol;
    }

    function updateTotalDisplay(transportPrice = currentTransportPrice, ticketPrice = currentTicketPrice) {
        currentTransportPrice = transportPrice;
        currentTicketPrice = ticketPrice;
        
        const finalPrice = baseTourPrice + transportPrice + ticketPrice + currentAddonPrice;
        
        inputTransportPrice.value = transportPrice;
        inputTotalPrice.value = finalPrice;
        
        if (transportPrice > 0) {
            transportFeeRow.style.setProperty('display', 'flex', 'important');
            displayTransportPrice.textContent = formatCurrencyDynamic(transportPrice);
        } else {
            transportFeeRow.style.setProperty('display', 'none', 'important');
        }
        
        if (ticketPrice > 0) {
            ticketFeeRow.style.setProperty('display', 'flex', 'important');
            displayTicketPrice.textContent = formatCurrencyDynamic(ticketPrice);
        } else {
            ticketFeeRow.style.setProperty('display', 'none', 'important');
        }

        const displayAddonPrice = document.getElementById('display_addon_price');
        const addonFeeRow = document.getElementById('addon_fee_row');
        if (currentAddonPrice > 0) {
            addonFeeRow.style.setProperty('display', 'flex', 'important');
            displayAddonPrice.textContent = formatCurrencyDynamic(currentAddonPrice);
        } else {
            addonFeeRow.style.setProperty('display', 'none', 'important');
        }
        
        displayTotalPrice.textContent = formatCurrencyDynamic(finalPrice);
    }

    // Xử lý khi thay đổi số lượng vé tham quan
    const ticketInputs = document.querySelectorAll('.ticket-qty-input');
    ticketInputs.forEach(input => {
        input.addEventListener('input', function() {
            let totalTicket = 0;
            ticketInputs.forEach(inp => {
                let qty = parseInt(inp.value);
                if(isNaN(qty) || qty < 0) qty = 0;
                totalTicket += qty * parseFloat(inp.dataset.price);
            });
            updateTotalDisplay(currentTransportPrice, totalTicket);
        });
    });

    // Xử lý Dịch vụ Addon
    const holidaysData = @json($holidays);
    let currentAddonPrice = 0;
    
    function getHolidaySurcharge(dateStr) {
        let maxSurcharge = 0;
        holidaysData.forEach(holiday => {
            if (dateStr >= holiday.start_date && dateStr <= holiday.end_date) {
                if (parseFloat(holiday.price_increase_percentage) > maxSurcharge) {
                    maxSurcharge = parseFloat(holiday.price_increase_percentage);
                }
            }
        });
        return maxSurcharge;
    }

    function updateAddonsTotal() {
        let totalAddons = 0;
        const addonRows = document.querySelectorAll('.addon-qty-input');
        addonRows.forEach(input => {
            let qty = parseInt(input.value);
            if(isNaN(qty) || qty < 0) qty = 0;
            
            if(qty > 0) {
                const addonId = input.dataset.addonId;
                const dateInput = document.querySelector(`.addon-date-input[data-addon-id="${addonId}"]`);
                const usageDate = dateInput.value;
                const priceDisplay = document.getElementById(`addon_price_display_${addonId}`);
                const basePrice = parseFloat(priceDisplay.dataset.basePrice);
                
                const surcharge = getHolidaySurcharge(usageDate);
                const finalPrice = basePrice * (1 + surcharge / 100);
                
                totalAddons += finalPrice * qty;
                
                if(surcharge > 0) {
                    priceDisplay.innerHTML = `${formatCurrencyDynamic(finalPrice)} <span class="badge bg-danger ms-1 px-1 py-0" style="font-size:0.6rem">+${surcharge}% Lễ</span>`;
                } else {
                    priceDisplay.innerHTML = formatCurrencyDynamic(basePrice);
                }
            } else {
                const addonId = input.dataset.addonId;
                const priceDisplay = document.getElementById(`addon_price_display_${addonId}`);
                const basePrice = parseFloat(priceDisplay.dataset.basePrice);
                priceDisplay.innerHTML = formatCurrencyDynamic(basePrice);
            }
        });
        
        currentAddonPrice = totalAddons;
        updateTotalDisplay(currentTransportPrice, currentTicketPrice);
    }

    const addonInputs = document.querySelectorAll('.addon-qty-input, .addon-date-input');
    addonInputs.forEach(input => {
        input.addEventListener('change', updateAddonsTotal);
        input.addEventListener('input', updateAddonsTotal);
    });

    window.selectTransportOption = function(price, dataStr) {
        // Parse data
        let data = JSON.parse(decodeURIComponent(dataStr));
        inputTransportData.value = JSON.stringify(data);
        
        // Highlight selected
        document.querySelectorAll('.transport-item-card').forEach(el => {
            el.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            const icon = el.querySelector('.selected-icon');
            if(icon) icon.style.display = 'none';
        });
        
        event.currentTarget.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        const icon = event.currentTarget.querySelector('.selected-icon');
        if(icon) icon.style.display = 'block';
        
        // Update price
        updateTotalDisplay(parseFloat(price));
    };

    function loadTransportOptions() {
        const selectedRadio = document.querySelector('input[name="transport_type"]:checked');
        if (!selectedRadio || selectedRadio.value === 'self') {
            transportContainer.style.display = 'none';
            return;
        }

        transportContainer.style.display = 'block';
        transportLoading.style.display = 'block';
        transportResults.innerHTML = '';
        
        const customerOrigin = document.getElementById('customer_origin_select').value;
        const flightDestination = '{{ $tourDepartureCode }}';
        
        if (selectedRadio.value === 'flight') {
            // Fetch Flight
            fetch(`/api/flights/search?passengers=${totalPersonsCount}&origin=${customerOrigin}&destination=${flightDestination}&departure_date={{ $departureDate }}`)
                    .then(res => res.json())
                    .then(data => {
                        transportLoading.style.display = 'none';
                        // Chỉ lọc các chuyến bay của Duffel Airways
                        let duffelOffers = (data.data || []).filter(offer => (offer.owner.name || '').includes('Duffel'));
                        
                        if(data.success && duffelOffers.length > 0) {
                            let html = '<h5 class="fw-bold mb-3">{{ __("Chọn Chuyến Bay") }}</h5>';
                            duffelOffers.forEach(offer => {
                                let priceVND = parseFloat(offer.total_amount) * 25000;
                                if (offer.total_currency === 'VND') {
                                    priceVND = parseFloat(offer.total_amount);
                                }
                                
                                let slice = offer.slices[0];
                                let segmentFirst = slice.segments[0];
                                let segmentLast = slice.segments[slice.segments.length - 1];
                                
                                let departTime = new Date(segmentFirst.departing_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
                                let arriveTime = new Date(segmentLast.arriving_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
                                
                                let dur = slice.duration || '';
                                let hMatch = dur.match(/(\d+)H/);
                                let mMatch = dur.match(/(\d+)M/);
                                let durationStr = (hMatch ? hMatch[1] + 'h ' : '') + (mMatch ? mMatch[1] + 'm' : '0m');
                                
                                let originCode = slice.origin.iata_code || '';
                                let destCode = slice.destination.iata_code || '';

                                let dataStr = encodeURIComponent(JSON.stringify({
                                    offer_id: offer.id,
                                    provider: offer.owner.name,
                                    price: priceVND
                                }));
                                
                                html += `
                                <div class="card mb-3 transport-item-card position-relative transition-all" style="cursor:pointer; border-width: 2px;" onclick="selectTransportOption(${priceVND}, '${dataStr}')">
                                    <div class="selected-icon position-absolute top-0 end-0 mt-2 me-2 text-primary" style="display:none; font-size: 1.5rem;">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="fw-bold text-primary"><i class="bi bi-airplane-engines me-2"></i>${offer.owner.name}</div>
                                            <div class="fw-bold text-danger fs-5">+ ${formatCurrencyDynamic(priceVND)}</div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                                            <div class="text-center">
                                                <div class="fw-bold fs-4 text-dark lh-1 mb-1">${departTime}</div>
                                                <div class="small fw-500 text-muted">${originCode}</div>
                                            </div>
                                            <div class="text-center flex-grow-1 px-4">
                                                <div class="small text-muted mb-2 fw-500">${durationStr}</div>
                                                <div class="position-relative w-100" style="height: 2px; background-color: #dee2e6;">
                                                    <i class="bi bi-airplane-fill text-primary position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); background: #f8f9fa; padding: 0 5px;"></i>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <div class="fw-bold fs-4 text-dark lh-1 mb-1">${arriveTime}</div>
                                                <div class="small fw-500 text-muted">${destCode}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                `;
                            });
                            transportResults.innerHTML = html;
                        } else {
                            const errorMsg = data.message ? data.message : '{{ __("Không tìm thấy chuyến bay mẫu phù hợp.") }}';
                            transportResults.innerHTML = `<div class="alert alert-warning">${errorMsg}</div>`;
                        }
                    })
                    .catch(err => {
                        transportLoading.style.display = 'none';
                        transportResults.innerHTML = '<div class="alert alert-danger">{{ __("Lỗi kết nối khi tìm chuyến bay.") }}</div>';
                    });
            } else if (selectedRadio.value === 'bus') {
                // Mock Bus Data
                setTimeout(() => {
                    transportLoading.style.display = 'none';
                    let buses = [
                        { id: 'b1', name: 'Nhà Xe Phương Trang', time: '20:00', price: 400000 * totalPersonsCount },
                        { id: 'b2', name: 'Nhà Xe Hải Vân', time: '21:30', price: 350000 * totalPersonsCount }
                    ];
                    
                    let html = '<h5 class="fw-bold mb-3">{{ __("Chọn Chuyến Xe") }}</h5>';
                    buses.forEach(bus => {
                        let dataStr = encodeURIComponent(JSON.stringify({
                            bus_id: bus.id,
                            provider: bus.name,
                            time: bus.time,
                            price: bus.price
                        }));
                        
                        html += `
                        <div class="card mb-3 transport-item-card position-relative transition-all" style="cursor:pointer; border-width: 2px;" onclick="selectTransportOption(${bus.price}, '${dataStr}')">
                            <div class="selected-icon position-absolute top-0 end-0 mt-2 me-2 text-primary" style="display:none; font-size: 1.5rem;">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-primary"><i class="bi bi-bus-front"></i> ${bus.name}</div>
                                    <div class="small text-muted">{{ __("Khởi hành:") }} ${bus.time}</div>
                                </div>
                                <div class="fw-bold text-danger fs-5">
                                    + ${formatCurrencyDynamic(bus.price)}
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    transportResults.innerHTML = html;
                }, 800);
            }
    }

    transportRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateTotalDisplay(0, currentTicketPrice);
            inputTransportData.value = '';
            loadTransportOptions();
        });
    });

    document.getElementById('customer_origin_select').addEventListener('change', function() {
        updateTotalDisplay(0, currentTicketPrice);
        inputTransportData.value = '';
        loadTransportOptions();
    });
    // WIZARD LOGIC
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', function() {
            // Validate current step
            const currentStep = this.closest('.wizard-panel');
            const inputs = currentStep.querySelectorAll('input[required], select[required]');
            let isValid = true;
            inputs.forEach(input => {
                if(!input.checkValidity()) {
                    input.reportValidity();
                    isValid = false;
                }
            });
            
            if(isValid) {
                const nextId = this.dataset.next;
                
                // Add validation for Transport selection in Step 2
                if (currentStep.id === 'step-panel-2') {
                    const selectedTransport = document.querySelector('input[name="transport_type"]:checked').value;
                    if ((selectedTransport === 'flight' || selectedTransport === 'bus') && !inputTransportData.value) {
                        alert('Vui lòng click chọn một chuyến bay/xe khách cụ thể hoặc chọn phương thức Tự Túc trước khi tiếp tục.');
                        return;
                    }
                }

                // Update panels
                document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
                document.getElementById('step-panel-' + nextId).classList.add('active');
                // Update nav steps
                document.querySelectorAll('.wizard-step').forEach(s => {
                    const stepNum = parseInt(s.id.replace('step-nav-', ''));
                    s.classList.remove('active');
                    if(stepNum < nextId) s.classList.add('completed');
                    if(stepNum == nextId) s.classList.add('active');
                });
                window.scrollTo(0, 0);
            }
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', function() {
            const prevId = this.dataset.prev;
            // Update panels
            document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('step-panel-' + prevId).classList.add('active');
            // Update nav steps
            document.querySelectorAll('.wizard-step').forEach(s => {
                const stepNum = parseInt(s.id.replace('step-nav-', ''));
                s.classList.remove('active');
                if(stepNum >= prevId) s.classList.remove('completed');
                if(stepNum == prevId) s.classList.add('active');
            });
            window.scrollTo(0, 0);
        });
    });

    // COUPON LOGIC
    let currentCouponDiscount = 0;
    
    document.getElementById('btn_apply_coupon').addEventListener('click', function() {
        const code = document.getElementById('coupon_code_input').value.trim();
        const msg = document.getElementById('coupon_message');
        const applyBtn = this;
        
        if (!code) {
            msg.style.display = 'block';
            msg.className = 'small mt-2 text-danger';
            msg.textContent = 'Vui lòng nhập mã khuyến mãi.';
            return;
        }
        
        // Calculate subtotal before discount
        const subtotal = baseTourPrice + currentTransportPrice + currentTicketPrice + currentAddonPrice;
        
        applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        applyBtn.disabled = true;
        
        fetch('/api/coupons/apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                code: code,
                order_value: subtotal
            })
        })
        .then(response => response.json())
        .then(data => {
            msg.style.display = 'block';
            if (data.success) {
                msg.className = 'small mt-2 text-success fw-bold';
                msg.textContent = `Áp dụng thành công! Giảm ${formatCurrencyDynamic(data.discount_amount)}`;
                
                currentCouponDiscount = data.discount_amount;
                document.getElementById('input_discount_amount').value = currentCouponDiscount;
                
                const row = document.getElementById('coupon_fee_row');
                row.style.setProperty('display', 'flex', 'important');
                document.getElementById('display_coupon_discount').textContent = '- ' + formatCurrencyDynamic(currentCouponDiscount);
                
                updateTotalDisplay();
            } else {
                msg.className = 'small mt-2 text-danger';
                msg.textContent = data.message || 'Mã không hợp lệ hoặc đã hết hạn.';
                
                currentCouponDiscount = 0;
                document.getElementById('input_discount_amount').value = 0;
                document.getElementById('coupon_fee_row').style.setProperty('display', 'none', 'important');
                
                updateTotalDisplay();
            }
        })
        .catch(err => {
            msg.style.display = 'block';
            msg.className = 'small mt-2 text-danger';
            msg.textContent = 'Lỗi kết nối. Vui lòng thử lại sau.';
        })
        .finally(() => {
            applyBtn.innerHTML = 'Áp dụng';
            applyBtn.disabled = false;
        });
    });

    // Override updateTotalDisplay to include discount
    const originalUpdateTotalDisplay = updateTotalDisplay;
    updateTotalDisplay = function(transportPrice = currentTransportPrice, ticketPrice = currentTicketPrice) {
        currentTransportPrice = transportPrice;
        currentTicketPrice = ticketPrice;
        const subtotal = baseTourPrice + currentTransportPrice + currentTicketPrice + currentAddonPrice;
        const finalTotal = Math.max(0, subtotal - currentCouponDiscount);
        originalUpdateTotalDisplay(transportPrice, ticketPrice); // call original to update DOM text for other items
        document.getElementById('display_total_price').innerHTML = formatCurrencyDynamic(finalTotal);

        // Deposit calculation
        const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
        const depositRow = document.getElementById('deposit_amount_row');
        if (paymentType === 'deposit') {
            const depositAmount = finalTotal * 0.3;
            const remainingAmount = finalTotal - depositAmount;
            document.getElementById('display_deposit_price').innerHTML = formatCurrencyDynamic(depositAmount);
            document.getElementById('display_remaining_price').innerHTML = formatCurrencyDynamic(remainingAmount);
            depositRow.style.setProperty('display', 'block', 'important');
        } else {
            depositRow.style.setProperty('display', 'none', 'important');
        }
    };

    // Listen to payment type change
    document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateTotalDisplay();
            
            // Highlight selected payment type card
            document.querySelectorAll('input[name="payment_type"]').forEach(el => {
                el.nextElementSibling.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            });
            this.nextElementSibling.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        });
    });

    // Listen to payment method change (for UX highlighting)
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="payment_method"]').forEach(el => {
                el.nextElementSibling.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            });
            this.nextElementSibling.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        });
    });

    // Initial highlight
    document.querySelector('input[name="payment_type"]:checked').nextElementSibling.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    document.querySelector('input[name="payment_method"]:checked').nextElementSibling.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
</script>
@endsection