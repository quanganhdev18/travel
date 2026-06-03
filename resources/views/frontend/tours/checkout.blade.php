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
                    <input type="hidden" name="total_price" id="input_total_price" value="{{ $totalPrice }}">
                    <input type="hidden" name="transport_price" id="input_transport_price" value="0">
                    <input type="hidden" name="transport_data" id="input_transport_data" value="">


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

                    <!-- Section 2: Danh sách Hành khách -->
                    <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-people"></i>
                            {{ __('Thông Tin Hành Khách') }}
                        </h4>

                        @for($i = 0; $i < $adults; $i++)
                        <div class="card mb-4 border shadow-sm">
                            <div class="card-header bg-light fw-bold text-primary d-flex justify-content-between align-items-center">
                                <span>{{ __('Người lớn') }} {{ $i + 1 }}</span>
                                @if($i == 0) <span class="badge bg-primary">Người đại diện</span> @endif
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-600 text-dark">{{ __('Họ và Tên') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="passengers[adult][{{$i}}][full_name]" class="form-control" required placeholder="Nhập tên đầy đủ" {{ $i == 0 ? 'id=customer_name' : '' }} value="{{ $i == 0 ? ($identity->full_name ?? $user->name) : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-600 text-dark">{{ __('Số CCCD/Hộ Chiếu') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="passengers[adult][{{$i}}][identity_number]" class="form-control" required placeholder="Nhập số CCCD/Passport" {{ $i == 0 ? 'id=identity_number' : '' }} value="{{ $i == 0 ? ($identity->identity_number ?? '') : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-600 text-dark">{{ __('Ngày Sinh') }} <span class="text-danger">*</span></label>
                                        <input type="date" name="passengers[adult][{{$i}}][date_of_birth]" class="form-control" required {{ $i == 0 ? 'id=date_of_birth' : '' }} value="{{ $i == 0 ? ($identity->date_of_birth ?? '') : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-600 text-dark">{{ __('Giới Tính') }} <span class="text-danger">*</span></label>
                                        <select name="passengers[adult][{{$i}}][gender]" class="form-select" required {{ $i == 0 ? 'id=gender' : '' }}>
                                            <option value="">{{ __('-- Chọn --') }}</option>
                                            <option value="male" {{ $i == 0 && ($identity->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('Nam') }}</option>
                                            <option value="female" {{ $i == 0 && ($identity->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('Nữ') }}</option>
                                            <option value="other" {{ $i == 0 && ($identity->gender ?? '') == 'other' ? 'selected' : '' }}>{{ __('Khác') }}</option>
                                        </select>
                                    </div>
                                    
                                    @if($i == 0)
                                        <input type="hidden" name="issue_date" id="issue_date" value="{{ $identity->issue_date ?? '2020-01-01' }}">
                                        <input type="hidden" name="expiry_date" id="expiry_date" value="{{ $identity->expiry_date ?? '2040-01-01' }}">
                                        <input type="hidden" name="issue_place" id="issue_place" value="{{ $identity->issue_place ?? 'Hà Nội' }}">
                                        
                                        <div class="col-12 mt-3 p-3 bg-light rounded border">
                                            <label class="form-label fw-600 text-dark">{{ __('Quét CCCD tự động điền (Tùy chọn)') }}</label>
                                            <div class="row g-2">
                                                <div class="col-md-5">
                                                    <input type="file" name="front_image" id="front_image" class="form-control form-control-sm" accept="image/*" placeholder="Mặt trước">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="file" name="back_image" id="back_image" class="form-control form-control-sm" accept="image/*" placeholder="Mặt sau">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-primary btn-sm w-100 h-100" id="btn-scan-cccd">
                                                        <i class="bi bi-upc-scan"></i> Quét
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endfor

                        @for($i = 0; $i < $children; $i++)
                        <div class="card mb-4 border shadow-sm">
                            <div class="card-header bg-light fw-bold text-info">
                                {{ __('Trẻ em') }} {{ $i + 1 }}
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-600 text-dark">{{ __('Họ và Tên') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="passengers[child][{{$i}}][full_name]" class="form-control" required placeholder="Nhập tên đầy đủ">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-600 text-dark">{{ __('Ngày Sinh') }} <span class="text-danger">*</span></label>
                                        <input type="date" name="passengers[child][{{$i}}][date_of_birth]" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-600 text-dark">{{ __('Giới Tính') }} <span class="text-danger">*</span></label>
                                        <select name="passengers[child][{{$i}}][gender]" class="form-select" required>
                                            <option value="">{{ __('-- Chọn --') }}</option>
                                            <option value="male">{{ __('Nam') }}</option>
                                            <option value="female">{{ __('Nữ') }}</option>
                                            <option value="other">{{ __('Khác') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>

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
                            <div id="transport_loading" style="display: none;" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Đang tìm kiếm chuyến đi phù hợp nhất...</div>
                            </div>
                            <div id="transport_results"></div>
                        </div>
                    </div>
                    <!-- Section 4: Phương Thức Thanh Toán -->
                    <div class="mb-5">
                        <h4 class="form-section-title">
                            <i class="bi bi-credit-card"></i>
                            {{ __('Phương Thức Thanh Toán') }}
                        </h4>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_cod" value="cod" checked>
                                <label class="transport-option w-100 p-4 text-start" for="payment_cod">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-wallet2 text-muted" style="font-size: 32px;"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold fs-5 text-dark">{{ __('Tiền mặt / Chuyển khoản') }}</div>
                                            <div class="small text-muted mt-1">{{ __('Thanh toán sau hoặc trực tiếp') }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_vnpay" value="vnpay">
                                <label class="transport-option w-100 p-4 text-start" for="payment_vnpay">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-credit-card-2-back text-muted" style="font-size: 32px;"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold fs-5 text-dark">{{ __('Thanh toán qua VNPay') }}</div>
                                            <div class="small text-muted mt-1">{{ __('Cổng thanh toán điện tử VNPay') }}</div>
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
                    <div class="text-muted fw-500 mb-2">{{ __('Tổng Tiền Cần Thanh Toán:') }}</div>
                    <div class="text-danger fw-bold lh-1" style="font-size: 2rem;" id="display_total_price">
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
        $destinationLoc = $schedule->tour->destination->name ?? '';
        $originCode = $iataMap[$departureLoc] ?? 'HAN';
        $destinationCode = $iataMap[$destinationLoc] ?? 'SGN';
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

    const baseTourPrice = {{ $totalPrice }};
    const totalPersonsCount = {{ $totalPersons }};

    function formatCurrencyVND(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }

    function updateTotalDisplay(transportPrice = 0) {
        const finalPrice = baseTourPrice + transportPrice;
        
        inputTransportPrice.value = transportPrice;
        inputTotalPrice.value = finalPrice;
        
        if (transportPrice > 0) {
            transportFeeRow.style.setProperty('display', 'flex', 'important');
            displayTransportPrice.textContent = formatCurrencyVND(transportPrice);
        } else {
            transportFeeRow.style.setProperty('display', 'none', 'important');
        }
        
        displayTotalPrice.textContent = formatCurrencyVND(finalPrice);
    }

    // Function handle selecting a transport option
    window.selectTransportOption = function(price, dataStr) {
        // Parse data
        let data = JSON.parse(decodeURIComponent(dataStr));
        inputTransportData.value = JSON.stringify(data);
        
        // Highlight selected
        document.querySelectorAll('.transport-item-card').forEach(el => el.classList.remove('border-primary', 'bg-light'));
        event.currentTarget.classList.add('border-primary', 'bg-light');
        
        // Update price
        updateTotalDisplay(parseFloat(price));
    };

    transportRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Reset state
            updateTotalDisplay(0);
            inputTransportData.value = '';
            
            if (this.value === 'self') {
                transportContainer.style.display = 'none';
                return;
            }
            
            transportContainer.style.display = 'block';
            transportLoading.style.display = 'block';
            transportResults.innerHTML = '';
            
            if (this.value === 'flight') {
                // Fetch Flight
                fetch(`/api/flights/search?passengers=${totalPersonsCount}&origin={{ $originCode }}&destination={{ $destinationCode }}&departure_date={{ $departureDate }}`)
                    .then(res => res.json())
                    .then(data => {
                        transportLoading.style.display = 'none';
                        // Chỉ lọc các chuyến bay của Duffel Airways
                        let duffelOffers = (data.data || []).filter(offer => (offer.owner.name || '').includes('Duffel'));
                        
                        if(data.success && duffelOffers.length > 0) {
                            let html = '<h5 class="fw-bold mb-3">Chọn Chuyến Bay</h5>';
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
                                <div class="card mb-3 transport-item-card" style="cursor:pointer;" onclick="selectTransportOption(${priceVND}, '${dataStr}')">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="fw-bold text-primary"><i class="bi bi-airplane-engines me-2"></i>${offer.owner.name}</div>
                                            <div class="fw-bold text-danger fs-5">+ ${formatCurrencyVND(priceVND)}</div>
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
                            transportResults.innerHTML = '<div class="alert alert-warning">Không tìm thấy chuyến bay mẫu của Duffel Airways phù hợp.</div>';
                        }
                    })
                    .catch(err => {
                        transportLoading.style.display = 'none';
                        transportResults.innerHTML = '<div class="alert alert-danger">Lỗi kết nối khi tìm chuyến bay.</div>';
                    });
            } else if (this.value === 'bus') {
                // Mock Bus Data
                setTimeout(() => {
                    transportLoading.style.display = 'none';
                    let buses = [
                        { id: 'b1', name: 'Nhà Xe Phương Trang', time: '20:00', price: 400000 * totalPersonsCount },
                        { id: 'b2', name: 'Nhà Xe Hải Vân', time: '21:30', price: 350000 * totalPersonsCount }
                    ];
                    
                    let html = '<h5 class="fw-bold mb-3">Chọn Chuyến Xe</h5>';
                    buses.forEach(bus => {
                        let dataStr = encodeURIComponent(JSON.stringify({
                            bus_id: bus.id,
                            provider: bus.name,
                            time: bus.time,
                            price: bus.price
                        }));
                        
                        html += `
                        <div class="card mb-3 transport-item-card" style="cursor:pointer;" onclick="selectTransportOption(${bus.price}, '${dataStr}')">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-primary"><i class="bi bi-bus-front"></i> ${bus.name}</div>
                                    <div class="small text-muted">Khởi hành: ${bus.time}</div>
                                </div>
                                <div class="fw-bold text-danger fs-5">
                                    + ${formatCurrencyVND(bus.price)}
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    transportResults.innerHTML = html;
                }, 800);
            }
        });
    });
</script>
@endsection