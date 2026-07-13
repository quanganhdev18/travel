@extends('layouts.guide')

@section('page-title', 'Chi tiết Lịch trình Tour')

@section('content')
@php
    $tourSchedule = $scheduleGuide->tour_schedule;
    $tour = $tourSchedule->tour;

    $firstBooking = $tourSchedule->bookings->first();
    $groupStatus = $firstBooking ? $firstBooking->tour_status : 'upcoming';

    $tourStatusMap = [
        'upcoming' => ['badge-soft-primary', 'Sắp bắt đầu'],
        'in_progress' => ['badge-soft-warning', 'Đang thực hiện'],
        'checking_in' => ['badge-soft-info', 'Đang check-in'],
        'completed' => ['badge-soft-success', 'Hoàn thành'],
        'cancelled_by_customer' => ['badge-soft-danger', 'Hủy (Khách)'],
        'cancelled_by_admin' => ['badge-soft-danger', 'Hủy (Admin)']
    ];
    $ts = $tourStatusMap[$groupStatus] ?? ['badge-soft-secondary', 'N/A'];

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

<!-- Card điều hành trạng thái Tour của Nhóm -->
<div class="card border-0 shadow-sm border-start border-4 border-warning mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <div class="rounded-circle bg-warning bg-opacity-15 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-geo-alt-fill text-warning fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:0.85rem;">Trạng thái Tour Đoàn hiện tại</div>
                    <div class="text-muted" style="font-size:0.75rem;">
                        Trạng thái: <span class="badge {{ $ts[0] }}">{{ $ts[1] }}</span>
                        @if($groupStatus === 'checking_in')
                            @php
                                $checkinSteps = $tourSchedule->bookings->pluck('current_checkin_step')->filter()->unique();
                            @endphp
                            @if($checkinSteps->isNotEmpty())
                                <span class="text-secondary ms-1">(Tại: <strong>{{ $checkinSteps->implode(', ') }}</strong>)</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2 align-items-center">
                @if($groupStatus !== 'completed' && !in_array($groupStatus, ['cancelled_by_customer', 'cancelled_by_admin']))
                    <button id="toggle-group-status-btn" class="btn btn-warning btn-sm fw-bold">
                        <i class="bi bi-gear-fill me-1"></i>Thay đổi trạng thái Tour
                    </button>
                @else
                    <button class="btn btn-secondary btn-sm fw-bold" disabled>
                        <i class="bi bi-lock-fill me-1"></i>Đã khóa trạng thái
                    </button>
                @endif
            </div>
        </div>

        <!-- Khối nhập thay đổi trạng thái tour đoàn, mở ra khi click "Thay đổi trạng thái Tour" -->
        <div id="group-status-form-wrapper" class="mt-3 p-3 bg-light rounded" style="display: none; border: 1px dashed #ffc107;">
            <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i>Lưu ý: Thay đổi này sẽ cập nhật trạng thái đồng loạt cho tất cả các đơn đặt chỗ thuộc tour đoàn này.</p>
            <form action="{{ route('guide.schedules.update_group_status', $tourSchedule->id) }}" method="POST">
                @csrf
                @php
                    $validGroupNextStatuses = \App\Models\Booking::getValidNextStatuses($groupStatus);
                @endphp
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Chọn trạng thái Tour</label>
                        <select name="tour_status" class="form-select form-select-sm" id="group-tour-status-select">
                            @if(in_array('in_progress', $validGroupNextStatuses))
                                <option value="in_progress" {{ $groupStatus == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                            @endif
                            @if(in_array('checking_in', $validGroupNextStatuses))
                                <option value="checking_in" {{ $groupStatus == 'checking_in' ? 'selected' : '' }}>Đang ở điểm check-in</option>
                            @endif
                            @if(in_array('completed', $validGroupNextStatuses))
                                <option value="completed" {{ $groupStatus == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-5" id="group-checkin-step-container" style="display: {{ $groupStatus == 'checking_in' ? 'block' : 'none' }};">
                        <label class="form-label fw-bold small">Điểm check-in hiện tại</label>
                        <input type="text" name="current_checkin_step" class="form-control form-control-sm" placeholder="VD: Sân bay, Trạm 1, Khách sạn..." value="{{ $firstBooking ? $firstBooking->current_checkin_step : '' }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark w-100">
                            <i class="bi bi-floppy me-1"></i>Lưu Thay Đổi
                        </button>
                    </div>
                </div>
            </form>
        </div>
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
                        <span class="badge {{ $ts[0] }}">{{ $ts[1] }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Vai trò của bạn:</span>
                        @if($scheduleGuide->is_backup)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-2 py-1">HDV Dự phòng</span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1">HDV Chính</span>
                        @endif
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
            <form action="{{ route('guide.schedules.save_attendance', $tourSchedule->id) }}" method="POST" id="attendance-form">
                @csrf
                <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="admin-card-title mb-0">Danh sách Hành khách</h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-semibold text-dark" id="attendance-selected-counter" style="font-size: 0.9rem;">
                            Đã chọn: <span class="text-success" id="selected-count-val">{{ $checkedInCount }}</span> / <span id="total-count-val">{{ $totalCount }}</span> khách
                        </span>
                        <button type="submit" class="btn btn-success btn-sm fw-bold px-3">
                            <i class="bi bi-floppy me-1"></i>Lưu điểm danh
                        </button>
                    </div>
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
                                                        name="checked_passengers[]"
                                                        value="{{ $passenger->id }}"
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
            </form>
        </div>
    </div>
</div>



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
        const counterEl = document.getElementById('checkin-counter');
        if (counterEl) counterEl.textContent = `${checkedCount} / ${totalCount}`;
        
        const selectedCountVal = document.getElementById('selected-count-val');
        if (selectedCountVal) selectedCountVal.textContent = checkedCount;

        const bar = document.getElementById('checkin-progress');
        const pctEl = document.getElementById('checkin-pct');
        if (bar) bar.style.width = pct + '%';
        if (pctEl) pctEl.textContent = pct + '%';
    }

    // ─── Check-in toggle ──────────────────────────────────────────────
    document.querySelectorAll('.checkin-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const passengerId = this.dataset.id;
            const row = document.getElementById('row-' + passengerId);
            const label = document.getElementById('checkin-label-' + passengerId);

            if (this.checked) {
                row.classList.add('table-success');
                label.innerHTML = '<span class="text-success fw-semibold">&#10003; Đã chọn (Chờ lưu)</span>';
                checkedCount++;
            } else {
                row.classList.remove('table-success');
                label.innerHTML = '<span class="text-muted">Chưa chọn (Chờ lưu)</span>';
                checkedCount--;
            }
            updateProgress();
        });
    });

    // ─── Group Status Toggle ──────────────────────────────────────────
    const toggleGroupBtn = document.getElementById('toggle-group-status-btn');
    const groupWrapper = document.getElementById('group-status-form-wrapper');
    if (toggleGroupBtn && groupWrapper) {
        toggleGroupBtn.addEventListener('click', function() {
            if (groupWrapper.style.display === 'none') {
                groupWrapper.style.display = 'block';
            } else {
                groupWrapper.style.display = 'none';
            }
        });
    }

    const groupStatusSelect = document.getElementById('group-tour-status-select');
    const groupCheckinContainer = document.getElementById('group-checkin-step-container');
    if (groupStatusSelect && groupCheckinContainer) {
        groupStatusSelect.addEventListener('change', function() {
            if (this.value === 'checking_in') {
                groupCheckinContainer.style.display = 'block';
            } else {
                groupCheckinContainer.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('.tour-status-select-guide').forEach(function(select) {
        select.addEventListener('change', function() {
            let bookingId = this.getAttribute('data-booking-id');
            let container = document.getElementById('checkinStepContainerGuide' + bookingId);
            if(this.value === 'checking_in') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    });

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
