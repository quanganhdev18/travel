@extends('layouts.guide')

@section('page-title', 'Chi tiết Lịch trình Tour')

@section('content')
@php
    $tourSchedule = $scheduleGuide->tour_schedule;
    $tour = $tourSchedule->tour;

    $statusClass = 'secondary';
    $statusText = 'Chưa xác định';

    if ($tourSchedule->departure_date > now()) {
        $statusClass = 'primary';
        $statusText = 'Sắp tới';
    } elseif ($tourSchedule->departure_date <= now() && $tourSchedule->return_date >= now()) {
        $statusClass = 'success';
        $statusText = 'Đang diễn ra';
    } else {
        $statusClass = 'secondary';
        $statusText = 'Đã kết thúc';
    }

    $allPassengers = $tourSchedule->bookings
        ->whereIn('payment_status', ['paid_30', 'paid_100'])
        ->flatMap(fn($b) => $b->booking_passengers);
    $checkedInCount = $allPassengers->where('checked_in', true)->count();
    $totalCount = $allPassengers->count();
@endphp

<div class="mb-3">
    <a href="{{ route('guide.schedules.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<!-- Card địa điểm check-in -->
<div class="card border-0 shadow-sm border-start border-4 border-warning mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <div class="rounded-circle bg-warning bg-opacity-15 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-geo-alt-fill text-warning fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:0.85rem;">Địa điểm Check-in hiện tại</div>
                    <div class="text-muted" style="font-size:0.75rem;">HDV cập nhật vị trí đoàn đang check-in</div>
                </div>
            </div>
            <div class="flex-grow-1 d-flex gap-2 align-items-center">
                <input
                    type="text"
                    id="checkin-location-input"
                    class="form-control form-control-sm"
                    placeholder="Ví dụ: Sảnh khách sạn Mường Thanh, Cổng chính Vịnh Hạ Long..."
                    value="{{ $tourSchedule->checkin_location ?? '' }}"
                    maxlength="500"
                    style="min-width:200px;"
                >
                <button
                    id="save-location-btn"
                    class="btn btn-warning btn-sm fw-semibold flex-shrink-0"
                    data-url="{{ route('guide.schedules.update_checkin_location', $tourSchedule->id) }}"
                >
                    <i class="bi bi-floppy me-1"></i>Lưu
                </button>
                <button
                    id="clear-location-btn"
                    class="btn btn-outline-secondary btn-sm flex-shrink-0"
                    title="Xóa địa điểm"
                >
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="location-status" class="text-muted" style="font-size:0.8rem;"></div>
        </div>
        @if($tourSchedule->checkin_location)
        <div class="mt-2 ps-1">
            <span class="badge badge-soft-warning">
                <i class="bi bi-pin-map-fill me-1"></i>
                <span id="current-location-text">{{ $tourSchedule->checkin_location }}</span>
            </span>
        </div>
        @else
        <div class="mt-2 ps-1" id="current-location-display" style="display:none;">
            <span class="badge badge-soft-warning">
                <i class="bi bi-pin-map-fill me-1"></i>
                <span id="current-location-text"></span>
            </span>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <!-- Cột thông tin Tour -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Thông tin Tour</h5>
            </div>
            <div class="card-body">
                @if($tour->primary_image)
                    <img src="{{ Storage::url($tour->primary_image) }}" alt="{{ $tour->name }}" class="img-fluid rounded mb-3 w-100" style="object-fit: cover; height: 180px;">
                @endif
                <h5 class="fw-bold">{{ $tour->name }}</h5>
                <p class="text-muted small mb-3">Mã Tour: {{ $tour->tour_code }}</p>

                <ul class="list-group list-group-flush border-top pt-3">
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Khởi hành:</span>
                        <strong>{{ \Carbon\Carbon::parse($tourSchedule->departure_date)->format('d/m/Y') }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Kết thúc:</span>
                        <strong>{{ \Carbon\Carbon::parse($tourSchedule->return_date)->format('d/m/Y') }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Trạng thái:</span>
                        <span class="badge badge-soft-{{ $statusClass }}">{{ $statusText }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Tổng khách:</span>
                        <strong>{{ $tourSchedule->bookings->sum(fn($b) => $b->adults_count + $b->children_count) }} / {{ $tourSchedule->capacity }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Điểm danh:</span>
                        <span class="fw-bold text-success" id="checkin-counter">{{ $checkedInCount }} / {{ $totalCount }}</span>
                    </li>
                </ul>

                <!-- Progress bar điểm danh -->
                @if($totalCount > 0)
                <div class="mt-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Tiến độ điểm danh</span>
                        <span id="checkin-pct">{{ $totalCount > 0 ? round($checkedInCount / $totalCount * 100) : 0 }}%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" id="checkin-progress"
                             role="progressbar"
                             style="width: {{ $totalCount > 0 ? round($checkedInCount / $totalCount * 100) : 0 }}%">
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cột danh sách hành khách -->

    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0">Danh sách Hành khách</h5>
                <span class="badge bg-primary" id="checkin-badge">{{ $checkedInCount }}/{{ $totalCount }} đã điểm danh</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">STT</th>
                                <th>Họ tên</th>
                                <th>Loại vé</th>
                                <th>Booking</th>
                                <th>Liên hệ</th>
                                <th class="text-center" style="width: 100px;">Điểm danh</th>
                                <th class="text-center" style="width: 90px;">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $stt = 1; @endphp
                            @forelse($tourSchedule->bookings as $booking)
                                @if(in_array($booking->payment_status, ['paid_30', 'paid_100']))
                                    @foreach($booking->booking_passengers as $passenger)
                                        <tr id="row-{{ $passenger->id }}" class="{{ $passenger->checked_in ? 'table-success' : '' }}">
                                            <td>{{ $stt++ }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $passenger->full_name }}</div>
                                                <div class="small text-muted">
                                                    {{ $passenger->gender == 'male' ? 'Nam' : ($passenger->gender == 'female' ? 'Nữ' : 'Khác') }}
                                                    @if($passenger->date_of_birth)
                                                        &ndash; Sinh: {{ \Carbon\Carbon::parse($passenger->date_of_birth)->format('d/m/Y') }}
                                                    @endif
                                                </div>
                                                @if($passenger->identity_number)
                                                    <div class="small text-muted">CCCD: {{ $passenger->identity_number }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($passenger->passenger_type == 'adult')
                                                    <span class="badge badge-soft-primary">Người lớn</span>
                                                @elseif($passenger->passenger_type == 'child')
                                                    <span class="badge badge-soft-warning">Trẻ em</span>
                                                @else
                                                    <span class="badge badge-soft-secondary">Em bé</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-primary fw-bold">#{{ $booking->id }}</span>
                                            </td>
                                            <td>
                                                <div>{{ $booking->user->phone ?? '—' }}</div>
                                                <div class="small text-muted">{{ $booking->user->email ?? '' }}</div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input
                                                        class="form-check-input checkin-checkbox"
                                                        type="checkbox"
                                                        id="checkin-{{ $passenger->id }}"
                                                        data-id="{{ $passenger->id }}"
                                                        data-url="{{ route('guide.passengers.toggle_checkin', $passenger) }}"
                                                        {{ $passenger->checked_in ? 'checked' : '' }}
                                                        style="width: 1.3em; height: 1.3em; cursor: pointer;"
                                                    >
                                                </div>
                                                <div id="checkin-label-{{ $passenger->id }}" class="mt-1" style="font-size:0.68rem; line-height:1.2;">
                                                    @if($passenger->checked_in)
                                                        <span class="text-success fw-semibold">&#10003; Đã điểm danh</span>
                                                    @else
                                                        <span class="text-muted">Chưa</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button
                                                    class="btn btn-sm btn-outline-warning note-btn"
                                                    data-id="{{ $passenger->id }}"
                                                    data-name="{{ $passenger->full_name }}"
                                                    data-note="{{ $passenger->special_note ?? '' }}"
                                                    data-url="{{ route('guide.passengers.update_note', $passenger) }}"
                                                    title="Ghi chú đặc biệt"
                                                >
                                                    @if($passenger->special_note)
                                                        <i class="bi bi-sticky-fill text-warning"></i>
                                                    @else
                                                        <i class="bi bi-sticky"></i>
                                                    @endif
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        Chưa có hành khách nào đặt chỗ cho lịch trình này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $activeBookings = $tourSchedule->bookings->filter(fn($b) =>
        in_array($b->tour_status, ['in_progress', 'checking_in'])
        && in_array($b->payment_status, ['paid_30', 'paid_100'])
    );
@endphp

@if($activeBookings->isNotEmpty())
<div class="card border-0 shadow-sm border-start border-4 border-primary mb-4">
    <div class="admin-card-header d-flex align-items-center gap-2">
        <i class="bi bi-toggles text-primary fs-5"></i>
        <h5 class="admin-card-title mb-0">Điều hành Trạng thái Tour</h5>
        <span class="badge bg-primary ms-auto">{{ $activeBookings->count() }} đơn đang hoạt động</span>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            <i class="bi bi-info-circle me-1"></i>
            Bạn có thể cập nhật trạng thái tour cho từng đơn đặt chỗ bên dưới.
            Chỉ các đơn đang <strong>Đang thực hiện</strong> hoặc <strong>Đang check-in</strong> mới hiển thị.
        </p>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Trạng thái hiện tại</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeBookings as $activeBooking)
                    <tr>
                        <td><span class="fw-bold text-primary">#{{ $activeBooking->id }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ $activeBooking->user->name ?? 'N/A' }}</div>
                            <div class="small text-muted">{{ $activeBooking->adults_count + $activeBooking->children_count }} khách</div>
                        </td>
                        <td>
                            @if($activeBooking->tour_status === 'in_progress')
                                <span class="badge badge-soft-warning"><i class="bi bi-play-fill me-1"></i>Đang thực hiện</span>
                            @else
                                <span class="badge badge-soft-info"><i class="bi bi-geo-alt-fill me-1"></i>Đang check-in</span>
                                @if($activeBooking->current_checkin_step)
                                    <div class="small text-muted mt-1">{{ $activeBooking->current_checkin_step }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                @if($activeBooking->tour_status === 'in_progress')
                                    {{-- Chuyển sang Đang check-in --}}
                                    <button class="btn btn-sm btn-outline-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#checkinModal{{ $activeBooking->id }}">
                                        <i class="bi bi-geo-alt me-1"></i>Check-in
                                    </button>
                                @else
                                    {{-- Quay lại Đang thực hiện --}}
                                    <form action="{{ route('guide.bookings.update_status', $activeBooking->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tour_status" value="in_progress">
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-play me-1"></i>Tiếp tục tour
                                        </button>
                                    </form>
                                @endif
                                {{-- Hoàn thành tour --}}
                                <button class="btn btn-sm btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#completeModal{{ $activeBooking->id }}">
                                    <i class="bi bi-check2-circle me-1"></i>Hoàn thành
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modals cho từng booking --}}
@foreach($activeBookings as $activeBooking)

{{-- Modal Check-in --}}
@if($activeBooking->tour_status === 'in_progress')
<div class="modal fade" id="checkinModal{{ $activeBooking->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt-fill me-2"></i>Chuyển sang Check-in</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guide.bookings.update_status', $activeBooking->id) }}" method="POST">
                @csrf
                <input type="hidden" name="tour_status" value="checking_in">
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">Đơn <strong>#{{ $activeBooking->id }}</strong> – {{ $activeBooking->user->name ?? 'N/A' }}</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa điểm check-in <span class="text-muted fw-normal">(tuỳ chọn)</span></label>
                        <input type="text" name="current_checkin_step" class="form-control"
                            placeholder="VD: Sân bay Nội Bài, Cổng Vịnh Hạ Long..."
                            maxlength="255">
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-info text-white fw-semibold">
                        <i class="bi bi-geo-alt-fill me-1"></i>Xác nhận Check-in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Hoàn thành --}}
<div class="modal fade" id="completeModal{{ $activeBooking->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-check2-circle me-2"></i>Xác nhận Hoàn thành Tour</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guide.bookings.update_status', $activeBooking->id) }}" method="POST">
                @csrf
                <input type="hidden" name="tour_status" value="completed">
                <div class="modal-body p-4">
                    <div class="alert alert-warning d-flex gap-2 align-items-start py-2">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                        <div>Thao tác này <strong>không thể hoàn tác</strong>. Sau khi hoàn thành, không ai có thể thay đổi trạng thái tour này nữa.</div>
                    </div>
                    <p class="mb-0">Xác nhận hoàn thành tour cho đơn <strong>#{{ $activeBooking->id }}</strong> – <strong>{{ $activeBooking->user->name ?? 'N/A' }}</strong>?</p>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-success fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Xác nhận Hoàn thành
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif

<!-- Modal Ghi chú đặc biệt -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="noteModalLabel">
                        <i class="bi bi-sticky-fill text-warning me-2"></i>Ghi chú đặc biệt
                    </h5>
                    <p class="text-muted small mb-0" id="note-passenger-name"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="note-textarea" class="form-label text-muted small">
                        Nhập ghi chú (thông tin sức khỏe, yêu cầu đặc biệt, lưu ý...):
                    </label>
                    <textarea
                        id="note-textarea"
                        class="form-control"
                        rows="5"
                        maxlength="1000"
                        placeholder="Ví dụ: Khách bị dị ứng hải sản, cần chỗ ngồi đầu xe, mang theo thuốc tiểu đường..."
                    ></textarea>
                    <div class="text-end small text-muted mt-1">
                        <span id="note-char-count">0</span>/1000
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning fw-bold px-4" id="save-note-btn">
                    <i class="bi bi-floppy me-1"></i>Lưu ghi chú
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast thông báo -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="toast-msg" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toast-text"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let totalCount = {{ $totalCount }};
    let checkedCount = {{ $checkedInCount }};

    // ─── Toast helper ─────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-msg');
        const toastText = document.getElementById('toast-text');
        toast.className = `toast align-items-center text-white border-0 bg-${type}`;
        toastText.textContent = message;
        new bootstrap.Toast(toast, { delay: 2500 }).show();
    }

    // ─── Update progress bar & counters ───────────────────────────────
    function updateProgress() {
        const pct = totalCount > 0 ? Math.round(checkedCount / totalCount * 100) : 0;
        document.getElementById('checkin-counter').textContent = `${checkedCount} / ${totalCount}`;
        document.getElementById('checkin-badge').textContent  = `${checkedCount}/${totalCount} đã điểm danh`;
        const bar = document.getElementById('checkin-progress');
        const pctEl = document.getElementById('checkin-pct');
        if (bar) bar.style.width = pct + '%';
        if (pctEl) pctEl.textContent = pct + '%';
    }

    // ─── Check-in toggle ──────────────────────────────────────────────
    document.querySelectorAll('.checkin-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const passengerId = this.dataset.id;
            const url = this.dataset.url;
            const row = document.getElementById('row-' + passengerId);
            const label = document.getElementById('checkin-label-' + passengerId);

            this.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                if (data.checked_in) {
                    row.classList.add('table-success');
                    label.innerHTML = '<span class="text-success fw-semibold">&#10003; Đã điểm danh</span>';
                    checkedCount++;
                } else {
                    row.classList.remove('table-success');
                    label.innerHTML = '<span class="text-muted">Chưa</span>';
                    checkedCount--;
                }
                updateProgress();
                showToast(data.message, data.checked_in ? 'success' : 'secondary');
            })
            .catch(() => {
                this.checked = !this.checked;
                showToast('Có lỗi xảy ra, vui lòng thử lại.', 'danger');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });

    // ─── Checkin location ─────────────────────────────────────────────
    const saveLocationBtn = document.getElementById('save-location-btn');
    const clearLocationBtn = document.getElementById('clear-location-btn');
    const locationInput = document.getElementById('checkin-location-input');
    const locationStatus = document.getElementById('location-status');

    function saveLocation(value) {
        const url = saveLocationBtn.dataset.url;
        saveLocationBtn.disabled = true;
        saveLocationBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang lưu...';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ location: value }),
        })
        .then(res => res.json())
        .then(data => {
            // Update the displayed badge
            const display = document.getElementById('current-location-display');
            const text = document.getElementById('current-location-text');
            if (text) text.textContent = data.location || '';
            if (display) display.style.display = data.location ? '' : 'none';

            locationStatus.innerHTML = value
                ? '<span class="text-success"><i class="bi bi-check-circle me-1"></i>' + data.message + '</span>'
                : '<span class="text-muted"><i class="bi bi-x-circle me-1"></i>' + data.message + '</span>';
            setTimeout(() => { locationStatus.innerHTML = ''; }, 3000);

            showToast(data.message, 'success');
        })
        .catch(() => showToast('Có lỗi xảy ra, vui lòng thử lại.', 'danger'))
        .finally(() => {
            saveLocationBtn.disabled = false;
            saveLocationBtn.innerHTML = '<i class="bi bi-floppy me-1"></i>Lưu';
        });
    }

    if (saveLocationBtn) {
        saveLocationBtn.addEventListener('click', () => saveLocation(locationInput.value.trim()));
    }

    if (clearLocationBtn) {
        clearLocationBtn.addEventListener('click', () => {
            locationInput.value = '';
            saveLocation('');
        });
    }

    // Also allow Enter key in location input
    if (locationInput) {
        locationInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); saveLocation(this.value.trim()); }
        });
    }

    // ─── Note modal ───────────────────────────────────────────────────
    let currentNoteBtn = null;

    document.querySelectorAll('.note-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentNoteBtn = this;
            const name = this.dataset.name;
            const note = this.dataset.note;

            document.getElementById('note-passenger-name').textContent = name;
            const textarea = document.getElementById('note-textarea');
            textarea.value = note;
            document.getElementById('note-char-count').textContent = note.length;

            new bootstrap.Modal(document.getElementById('noteModal')).show();
        });
    });

    // Character counter
    document.getElementById('note-textarea').addEventListener('input', function () {
        document.getElementById('note-char-count').textContent = this.value.length;
    });

    // Save note
    document.getElementById('save-note-btn').addEventListener('click', function () {
        if (!currentNoteBtn) return;

        const url = currentNoteBtn.dataset.url;
        const passengerId = currentNoteBtn.dataset.id;
        const note = document.getElementById('note-textarea').value;
        const saveBtn = this;

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang lưu...';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ note: note }),
        })
        .then(res => res.json())
        .then(data => {
            // Update button dataset so re-opening shows updated note
            currentNoteBtn.dataset.note = note;

            // Toggle icon to filled if has note
            const icon = currentNoteBtn.querySelector('i');
            if (note.trim()) {
                icon.className = 'bi bi-sticky-fill text-warning';
            } else {
                icon.className = 'bi bi-sticky';
            }

            bootstrap.Modal.getInstance(document.getElementById('noteModal')).hide();
            showToast(data.message, 'success');
        })
        .catch(() => {
            showToast('Có lỗi xảy ra, vui lòng thử lại.', 'danger');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-floppy me-1"></i>Lưu ghi chú';
        });
    });
});
</script>
@endpush
