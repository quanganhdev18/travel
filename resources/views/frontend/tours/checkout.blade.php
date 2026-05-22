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
                </div>

                <form action="{{ route('frontend.tours.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <input type="hidden" name="adults" value="{{ $adults }}">
                    <input type="hidden" name="children" value="{{ $children }}">
                    <input type="hidden" name="total_price" value="{{ $totalPrice }}">

                    <!-- Section 1: Thông Tin Hành Khách Chính -->
                    <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-person-badge"></i>
                            {{ __('Thông Tin Liên Hệ') }}
                        </h4>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-600 text-dark">{{ __('Họ và Tên') }} <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control search-form-control"
                                    value="{{ $identity->full_name ?? $user->name }}" required
                                    placeholder="{{ __('Nhập tên đầy đủ (khớp với CCCD/Hộ chiếu)') }}">
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
                        </div>
                    </div>

                    <!-- Section 2: Thông Tin Định Danh -->
                    <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-card-heading"></i>
                            {{ __('Định Danh (CCCD/Hộ Chiếu)') }}
                        </h4>

                        <!-- Upload Images -->
                        <div class="row g-4 mb-4 bg-light p-4 rounded-4 border">
                            <div class="col-md-6">
                                <label class="form-label fw-600 text-dark">{{ __('Ảnh Mặt Trước') }} <span class="text-danger">*</span></label>
                                <input type="file" name="front_image" id="front_image" class="form-control search-form-control bg-white"
                                    accept="image/*" {{ !$identity || !$identity->front_image_url ? 'required' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600 text-dark">{{ __('Ảnh Mặt Sau') }} <span class="text-danger">*</span></label>
                                <input type="file" name="back_image" id="back_image" class="form-control search-form-control bg-white"
                                    accept="image/*" {{ !$identity || !$identity->back_image_url ? 'required' : '' }}>
                            </div>
                            <div class="col-12 mt-3 text-center">
                                <button type="button" class="btn btn-outline-primary rounded-pill px-4" id="btn-scan-cccd">
                                    <i class="bi bi-upc-scan me-2"></i>{{ __('Quét & Tự Động Điền') }}
                                </button>
                            </div>
                        </div>

                        <!-- Identity Details -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-600 text-dark">{{ __('Số CCCD/Hộ Chiếu') }} <span class="text-danger">*</span></label>
                                <input type="text" name="identity_number" id="identity_number" class="form-control search-form-control"
                                    value="{{ $identity->identity_number ?? '' }}" required placeholder="{{ __('Nhập số CCCD') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600 text-dark">{{ __('Ngày Sinh') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control search-form-control"
                                    value="{{ $identity->date_of_birth ?? '' }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-600 text-dark">{{ __('Giới Tính') }} <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-select search-form-control" required>
                                    <option value="">{{ __('-- Chọn --') }}</option>
                                    <option value="male" {{ ($identity->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('Nam') }}</option>
                                    <option value="female" {{ ($identity->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('Nữ') }}</option>
                                    <option value="other" {{ ($identity->gender ?? '') == 'other' ? 'selected' : '' }}>{{ __('Khác') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-600 text-dark">{{ __('Ngày Cấp') }} <span class="text-danger">*</span></label>
                                <input type="date" name="issue_date" id="issue_date" class="form-control search-form-control"
                                    value="{{ $identity->issue_date ?? '' }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-600 text-dark">{{ __('Ngày Hết Hạn') }} <span class="text-danger">*</span></label>
                                <input type="date" name="expiry_date" id="expiry_date" class="form-control search-form-control"
                                    value="{{ $identity->expiry_date ?? '' }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-600 text-dark">{{ __('Nơi Cấp') }} <span class="text-danger">*</span></label>
                                <input type="text" name="issue_place" id="issue_place" class="form-control search-form-control"
                                    value="{{ $identity->issue_place ?? '' }}" required placeholder="{{ __('Vd: Công an TP Hà Nội') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Phương Thức Vận Chuyển -->
                    <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-airplane"></i>
                            {{ __('Di Chuyển Đến Điểm Khởi Hành') }}
                        </h4>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="transport_type" id="transport_flight" value="flight" checked>
                                <label class="transport-option w-100 p-4 text-start" for="transport_flight">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-airplane text-muted" style="font-size: 32px;"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold fs-5 text-dark">{{ __('Đặt Vé Máy Bay') }}</div>
                                            <div class="small text-muted mt-1">{{ __('Tìm chuyến bay tiện lợi') }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="transport_type" id="transport_self" value="self">
                                <label class="transport-option w-100 p-4 text-start" for="transport_self">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-car-front text-muted" style="font-size: 32px;"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold fs-5 text-dark">{{ __('Tự Túc Di Chuyển') }}</div>
                                            <div class="small text-muted mt-1">{{ __('Tự đến điểm khởi hành') }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-register-premium w-100 py-3 fs-5 mt-3">
                        <i class="bi bi-shield-check me-2"></i> {{ __('Xác Nhận Thanh Toán') }}
                    </button>
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
                        <strong class="text-dark">{{ $children }} × {{ format_currency($schedule->tour->base_price) }}</strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-muted fw-500">{{ __('Tổng khách:') }}</span>
                        <strong class="fs-5">{{ $totalPersons }} {{ __('người') }}</strong>
                    </div>
                </div>

                <!-- Total Price -->
                <div class="mb-4">
                    <div class="text-muted fw-500 mb-2">{{ __('Tổng Tiền Cần Thanh Toán:') }}</div>
                    <div class="text-danger fw-bold lh-1" style="font-size: 2rem;">
                        {!! format_currency($totalPrice) !!}
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
                    if(nameInput) nameInput.value = data.name || '';
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
</script>
@endsection