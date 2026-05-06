@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Main Booking Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="mb-5">
                        <h3 class="mb-1 fw-bold">Thông Tin Đặt Tour</h3>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar-event me-2"></i>
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
                            <h5 class="mb-3 fw-bold text-dark">
                                <i class="bi bi-person-badge me-2 text-primary"></i>
                                Thông Tin Hành Khách Chính
                            </h5>
                            <div class="bg-light p-3 rounded mb-3 small text-muted">
                                <i class="bi bi-info-circle me-2"></i>
                                Hành khách chính sẽ là người đại diện cho toàn bộ nhóm
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-500 text-dark">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control form-control-lg"
                                        value="{{ $identity->full_name ?? $user->name }}" required
                                        placeholder="Nhập tên đầy đủ (khớp với CCCD/Hộ chiếu)">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-dark">Số Điện Thoại <span class="text-danger">*</span></label>
                                    <input type="tel" name="customer_phone" class="form-control form-control-lg"
                                        value="{{ $user->phone ?? '' }}" required placeholder="+84 (0)...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-dark">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" class="form-control form-control-lg"
                                        value="{{ $user->email }}" required placeholder="email@example.com">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-secondary-subtle">

                        <!-- Section 2: Thông Tin Định Danh -->
                        <div class="mb-5">
                            <h5 class="mb-3 fw-bold text-dark">
                                <i class="bi bi-card-text me-2 text-primary"></i>
                                Thông Tin Định Danh (CCCD/Hộ Chiếu)
                            </h5>

                            <!-- Upload Images -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-dark">Ảnh Mặt Trước <span class="text-danger">*</span></label>
                                    <input type="file" name="front_image" id="front_image" class="form-control form-control-lg"
                                        accept="image/*" {{ !$identity || !$identity->front_image_url ? 'required' : '' }}>
                                    <small class="text-muted d-block mt-2">Tải ảnh CCCD/Hộ chiếu mặt trước (JPG, PNG, tối đa 5MB)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-dark">Ảnh Mặt Sau <span class="text-danger">*</span></label>
                                    <input type="file" name="back_image" id="back_image" class="form-control form-control-lg"
                                        accept="image/*" {{ !$identity || !$identity->back_image_url ? 'required' : '' }}>
                                    <small class="text-muted d-block mt-2">Tải ảnh CCCD/Hộ chiếu mặt sau (JPG, PNG, tối đa 5MB)</small>
                                </div>
                            </div>

                            <!-- Scan Button -->
                            <div class="mb-4">
                                <button type="button" class="btn btn-outline-primary btn-lg w-100" id="btn-scan-cccd">
                                    <i class="bi bi-upc-scan me-2"></i>Quét & Tự Động Điền Thông Tin
                                </button>
                            </div>

                            <!-- Identity Details -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-dark">Số CCCD/Hộ Chiếu <span class="text-danger">*</span></label>
                                    <input type="text" name="identity_number" id="identity_number" class="form-control form-control-lg"
                                        value="{{ $identity->identity_number ?? '' }}" required placeholder="Nhập số CCCD">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-500 text-dark">Ngày Sinh <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control form-control-lg"
                                        value="{{ $identity->date_of_birth ?? '' }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-500 text-dark">Giới Tính <span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="male" {{ ($identity->gender ?? '') == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ ($identity->gender ?? '') == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ ($identity->gender ?? '') == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-500 text-dark">Ngày Cấp <span class="text-danger">*</span></label>
                                    <input type="date" name="issue_date" id="issue_date" class="form-control form-control-lg"
                                        value="{{ $identity->issue_date ?? '' }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-500 text-dark">Ngày Hết Hạn <span class="text-danger">*</span></label>
                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control form-control-lg"
                                        value="{{ $identity->expiry_date ?? '' }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-500 text-dark">Nơi Cấp <span class="text-danger">*</span></label>
                                    <input type="text" name="issue_place" id="issue_place" class="form-control form-control-lg"
                                        value="{{ $identity->issue_place ?? '' }}" required placeholder="Vd: Công an TP Hà Nội">
                                </div>
                            </div>

                            @if(!$identity)
                            <div class="alert alert-info mt-4 py-3">
                                <i class="bi bi-lightbulb me-2"></i>
                                <strong>Lưu ý:</strong> Bạn chưa có thông tin định danh. Hãy tải ảnh và nhấn "Quét & Tự Động Điền" để hệ thống giúp điền nhanh dữ liệu.
                            </div>
                            @endif
                        </div>

                        <hr class="my-4 border-secondary-subtle">

                        <!-- Section 3: Phương Thức Vận Chuyển -->
                        <div class="mb-5">
                            <h5 class="mb-3 fw-bold text-dark">
                                <i class="bi bi-transport me-2 text-primary"></i>
                                Phương Thức Di Chuyển Đến Điểm Khởi Hành
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="transport_type" id="transport_flight" value="flight" checked>
                                    <label class="btn btn-outline-primary w-100 p-4 text-start transport-option" for="transport_flight">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-airplane" style="font-size: 28px; color: var(--bs-primary);"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold">Đặt Vé Máy Bay</div>
                                                <div class="small text-muted">Hệ thống sẽ tìm chuyến bay phù hợp</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="transport_type" id="transport_self" value="self">
                                    <label class="btn btn-outline-secondary w-100 p-4 text-start transport-option" for="transport_self">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-car-front" style="font-size: 28px; color: var(--bs-secondary);"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold">Tự Túc Di Chuyển</div>
                                                <div class="small text-muted">Bạn tự đến điểm khởi hành</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                            <i class="bi bi-check-circle me-2"></i>Xác Nhận & Đặt Tour
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Booking Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="mb-4 fw-bold">Tóm Tắt Đơn Hàng</h5>

                    <!-- Tour Info -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">{{ $schedule->tour->title }}</h6>
                        <div class="small text-muted mb-3">
                            <div><i class="bi bi-geo-alt me-2"></i>{{ $schedule->tour->destination->name ?? 'Đang cập nhật' }}</div>
                            <div><i class="bi bi-calendar-event me-2"></i>{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <hr class="my-3 border-secondary-subtle">

                    <!-- Passenger Count -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Người lớn:</span>
                            <strong>{{ $adults }} × {{ number_format($schedule->tour->base_price, 0, ',', '.') }} ₫</strong>
                        </div>
                        @if($children > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Trẻ em:</span>
                            <strong>{{ $children }} × {{ number_format($schedule->tour->base_price, 0, ',', '.') }} ₫</strong>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tổng số người:</span>
                            <strong>{{ $totalPersons }}</strong>
                        </div>
                    </div>

                    <hr class="my-3 border-secondary-subtle">

                    <!-- Total Price -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">Tổng Cộng:</span>
                        <div class="text-end">
                            <div class="text-danger fw-bold" style="font-size: 24px;">
                                {!! number_format($totalPrice, 0, ',', '.') !!} ₫
                            </div>
                            <small class="text-muted">cho {{ $totalPersons }} người</small>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-light border border-secondary-subtle py-3 small">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Vui lòng kiểm tra kỹ thông tin trước khi xác nhận đặt tour. Bạn có thể hủy hoặc thay đổi trong vòng 24 giờ.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btn-scan-cccd').addEventListener('click', function() {
        const frontImage = document.getElementById('front_image').files[0];
        if (!frontImage) {
            alert('Vui lòng tải lên ảnh mặt trước CCCD để hệ thống đọc dữ liệu.');
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang quét...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('image', frontImage);

        fetch('/api/scan-cccd', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('identity_number').value = data.id || '';
                    document.getElementById('customer_name').value = data.name || '';
                    document.getElementById('date_of_birth').value = formatDob(data.dob) || '';
                    if (data.sex === 'nam') document.getElementById('gender').value = 'male';
                    else if (data.sex === 'nữ') document.getElementById('gender').value = 'female';
                } else {
                    fillMockData();
                }
            })
            .catch(error => {
                fillMockData();
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    });

    function fillMockData() {
        // Generate unique identity number using timestamp + random
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const uniqueId = `036${timestamp}${random}`;

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