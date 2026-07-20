@extends('layouts.guide')

@section('page-title', 'Chi tiết Lịch trình Tour')

@section('content')
<style>
    @media (max-width: 767px) {
        /* Chuyển bảng sang dạng các thẻ card */
        .table-responsive {
            border: none;
        }
        .table {
            border: none;
        }
        .table thead {
            display: none;
        }
        .table tbody {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 12px 4px;
        }
        .table tbody tr {
            display: block;
            background: #fff;
            border: 1px solid var(--admin-border) !important;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            padding: 16px;
            margin-bottom: 0;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        .table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #f1f5f9;
            font-size: 0.85rem;
            text-align: right;
        }
        .table td:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .table td:first-child {
            padding-top: 0;
        }
        .table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--admin-text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            margin-right: auto;
            text-align: left;
        }
        .table td > div, .table td > span, .table td > button, .table td > input {
            text-align: right;
        }
        .table td.text-center {
            text-align: right;
            justify-content: space-between;
            align-items: center;
        }
        .table td.text-center > div {
            margin: 0 !important;
        }
    }
</style>
@php
    $tourSchedule = $scheduleGuide->tour_schedule;
    $tour = $tourSchedule->tour;

    $firstBooking = $tourSchedule->bookings->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])->first();
    $groupStatus = $firstBooking ? $firstBooking->tour_status : 'upcoming';

    $tourStatusMap = [
        'upcoming' => ['badge-soft-primary', 'Sắp bắt đầu'],
        'in_progress' => ['badge-soft-warning', 'Đang thực hiện'],
        'checking_in' => ['badge-soft-info', 'Đang check-in'],
        'completed' => ['badge-soft-success', 'Hoàn thành'],
        'closed' => ['badge-soft-dark', 'Đã đóng'],
        'cancelled_by_customer' => ['badge-soft-danger', 'Hủy (Khách)'],
        'cancelled_by_admin' => ['badge-soft-danger', 'Hủy (Admin)']
    ];
    $ts = $tourStatusMap[$groupStatus] ?? ['badge-soft-secondary', 'N/A'];

    $scheduleStatusMap = [
        'pending' => ['badge-soft-primary', 'Sắp bắt đầu'],
        'operating' => ['badge-soft-warning', 'Đang vận hành'],
        'completed' => ['badge-soft-success', 'Đã kết thúc'],
        'closed' => ['bg-secondary bg-opacity-10 text-secondary border border-secondary', 'Đã đóng']
    ];
    $ss = $scheduleStatusMap[$tourSchedule->status ?? 'pending'] ?? ['badge-soft-secondary', 'N/A'];

    $allPassengers = $tourSchedule->bookings
        ->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])
        ->whereNotIn('booking_status', ['cancelled'])
        ->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])
        ->flatMap(fn($b) => $b->booking_passengers);
    $checkedInCount = $allPassengers->where('checked_in', true)->count();
    $totalCount = $allPassengers->count();

    $isLocked = ($groupStatus === 'completed' || $tourSchedule->status === 'closed');
@endphp

<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('guide.schedules.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
    
    @if($tourSchedule->status === 'completed' || $groupStatus === 'completed')
        @php
            $report = \App\Models\TourReport::where('tour_schedule_id', $tourSchedule->id)->first();
        @endphp
        @if(!$report)
            <a href="{{ route('guide.reports.create', $tourSchedule->id) }}" class="btn btn-sm btn-primary fw-bold">
                <i class="bi bi-file-earmark-text"></i> Viết Báo Cáo & Quyết Toán
            </a>
        @else
            <span class="badge bg-success bg-opacity-10 text-success border border-success p-2">
                <i class="bi bi-check-circle-fill me-1"></i>Đã nộp báo cáo
            </span>
        @endif
    @endif
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
                @if($groupStatus !== 'upcoming' && $groupStatus !== 'completed' && !in_array($groupStatus, ['cancelled_by_customer', 'cancelled_by_admin']))
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
                    <img src="{{ Storage::url($tour->primary_image) }}" alt="{{ $tour->title }}" class="img-fluid rounded mb-3 w-100" style="object-fit: cover; height: 180px;">
                @endif
                <h5 class="fw-bold">{{ $tour->title }}</h5>
                <p class="text-muted small mb-3">Mã Tour: #{{ str_pad($tour->id, 4, '0', STR_PAD_LEFT) }}</p>

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
                        <span class="text-muted">Trạng thái Tour:</span>
                        <span class="badge {{ $ss[0] }}">{{ $ss[1] }}</span>
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
                        <strong>{{ $tourSchedule->bookings->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])->whereNotIn('booking_status', ['cancelled'])->sum(fn($b) => $b->adults_count + $b->children_count) }} / {{ $tourSchedule->capacity }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Điểm danh:</span>
                        <span class="fw-bold text-success" id="checkin-counter">{{ $checkedInCount }} / {{ $totalCount }}</span>
                    </li>
                </ul>

                </div>
        </div>
    </div>

    <!-- Cột danh sách hành khách và điểm tham quan -->
    <div class="col-lg-8 mb-4">
        <!-- Tabs Nav -->
        <ul class="nav nav-tabs mb-3 border-bottom-0" id="scheduleTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark border-0 border-bottom border-3 border-warning bg-transparent tab-btn" id="passengers-tab" data-bs-toggle="tab" data-bs-target="#passengers" type="button" role="tab" aria-controls="passengers" aria-selected="true">
                    <i class="bi bi-people-fill me-1 text-warning"></i>Danh sách Hành khách
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-muted border-0 bg-transparent tab-btn" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab" aria-controls="activities" aria-selected="false">
                    <i class="bi bi-geo-alt-fill me-1 text-info"></i>Điểm Tham Quan
                </button>
            </li>
        </ul>

        <div class="tab-content" id="scheduleTabContent">
            <!-- Tab Passengers -->
            <div class="tab-pane fade show active" id="passengers" role="tabpanel" aria-labelledby="passengers-tab">
                <div class="card border-0 shadow-sm">
                    <div>
                        <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="admin-card-title mb-0">Danh sách Hành khách</h5>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill">
                                    Tổng cộng: <span class="total-count-val" id="total-count-val">{{ $totalCount }}</span> khách
                                </span>
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
                                        <th class="text-center" style="width: 100px;">Chi tiết</th>
                                        <th class="text-center" style="width: 90px;">Ghi chú</th>
                                        <th class="text-center" style="width: 100px;">Sửa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $stt = 1; @endphp
                                    @forelse($tourSchedule->bookings as $booking)
                                        @if(in_array($booking->payment_status, ['pending', 'paid_30', 'paid_100']) && !in_array($booking->tour_status, [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER]) && $booking->booking_status !== 'cancelled')
                                            @foreach($booking->booking_passengers as $passenger)
                                                <tr id="row-{{ $passenger->id }}" class="{{ !empty($passenger->special_note) ? 'table-warning' : '' }}">
                                                    <td data-label="STT">{{ $stt++ }}</td>
                                                    <td data-label="Họ tên">
                                                        <div class="fw-bold text-md-start text-end">{{ $passenger->full_name }}</div>
                                                        <div class="small text-muted text-md-start text-end">
                                                            {{ $passenger->gender == 'male' ? 'Nam' : ($passenger->gender == 'female' ? 'Nữ' : 'Khác') }}
                                                        </div>
                                                    </td>
                                                    <td data-label="Loại vé">
                                                        @if($passenger->passenger_type == 'adult')
                                                            <span class="badge badge-soft-primary">Người lớn</span>
                                                        @elseif($passenger->passenger_type == 'child')
                                                            <span class="badge badge-soft-warning">Trẻ em</span>
                                                        @else
                                                            <span class="badge badge-soft-secondary">Em bé</span>
                                                        @endif
                                                    </td>
                                                    <td data-label="Chi tiết" class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 text-end" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#passengerDetailModal-{{ $passenger->id }}" title="Xem chi tiết khách hàng">
                                                            <i class="bi bi-info-circle"></i> Chi tiết
                                                        </button>
                                                        
                                                        <!-- Modal Chi tiết khách hàng -->
                                                        <div class="modal fade text-start" id="passengerDetailModal-{{ $passenger->id }}" tabindex="-1" aria-labelledby="passengerDetailModalLabel-{{ $passenger->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content border-0 shadow">
                                                                    <div class="modal-header border-bottom px-4 py-3 bg-light">
                                                                        <h5 class="modal-title fw-600" id="passengerDetailModalLabel-{{ $passenger->id }}">
                                                                            <i class="bi bi-person-badge text-primary me-2"></i>Chi tiết khách hàng
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body p-4">
                                                                        <div class="text-center mb-4">
                                                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width: 60px; height: 60px;">
                                                                                <i class="bi bi-person-fill fs-2"></i>
                                                                            </div>
                                                                            <h5 class="mb-1 fw-bold text-dark">{{ $passenger->full_name }}</h5>
                                                                            <div>
                                                                                @if($passenger->passenger_type == 'adult')
                                                                                    <span class="badge badge-soft-primary">Người lớn</span>
                                                                                @elseif($passenger->passenger_type == 'child')
                                                                                    <span class="badge badge-soft-warning">Trẻ em</span>
                                                                                @else
                                                                                    <span class="badge badge-soft-secondary">Em bé</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
 
                                                                        <div class="card border-0 bg-light p-3 mb-3">
                                                                            <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Thông tin cá nhân</h6>
                                                                            <div class="row g-2 small">
                                                                                <div class="col-5 text-muted text-start">Giới tính:</div>
                                                                                <div class="col-7 fw-bold text-dark text-start">{{ $passenger->gender == 'male' ? 'Nam' : ($passenger->gender == 'female' ? 'Nữ' : 'Khác') }}</div>
 
                                                                                <div class="col-5 text-muted text-start">Ngày sinh:</div>
                                                                                <div class="col-7 fw-bold text-dark text-start">
                                                                                    {{ $passenger->date_of_birth ? \Carbon\Carbon::parse($passenger->date_of_birth)->format('d/m/Y') : '—' }}
                                                                                </div>
 
                                                                                <div class="col-5 text-muted text-start">Số CCCD/Hộ chiếu:</div>
                                                                                <div class="col-7 fw-bold text-dark text-start">{{ $passenger->identity_number ?? '—' }}</div>
                                                                            </div>
                                                                        </div>
 
                                                                        <div class="card border-0 bg-light p-3 mb-3">
                                                                            <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Thông tin liên hệ & Đơn hàng</h6>
                                                                            <div class="row g-2 small">
                                                                                <div class="col-5 text-muted text-start">Mã Booking:</div>
                                                                                <div class="col-7 fw-bold text-primary text-start">#{{ $booking->id }}</div>
 
                                                                                <div class="col-5 text-muted text-start">Người đặt:</div>
                                                                                <div class="col-7 fw-bold text-dark text-start">{{ $booking->user->name ?? '—' }}</div>
 
                                                                                <div class="col-5 text-muted text-start">Số điện thoại:</div>
                                                                                <div class="col-7 fw-bold text-dark text-start">
                                                                                    @if($booking->user && $booking->user->phone)
                                                                                        <a href="tel:{{ $booking->user->phone }}" class="text-decoration-none"><i class="bi bi-telephone-fill me-1"></i>{{ $booking->user->phone }}</a>
                                                                                    @else
                                                                                        —
                                                                                    @endif
                                                                                </div>
 
                                                                                <div class="col-5 text-muted text-start">Email:</div>
                                                                                <div class="col-7 fw-bold text-dark text-start text-truncate">
                                                                                    @if($booking->user && $booking->user->email)
                                                                                        <a href="mailto:{{ $booking->user->email }}" class="text-decoration-none"><i class="bi bi-envelope-fill me-1"></i>{{ $booking->user->email }}</a>
                                                                                    @else
                                                                                        —
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
 
                                                                        @if($passenger->special_note)
                                                                        <div class="card border-0 border-start border-3 border-warning bg-warning bg-opacity-10 p-3">
                                                                            <h6 class="fw-bold mb-1 text-warning text-start" style="font-size: 0.8rem;"><i class="bi bi-sticky-fill me-1"></i>Ghi chú đặc biệt:</h6>
                                                                            <p class="mb-0 small text-dark text-start">{{ $passenger->special_note }}</p>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="modal-footer bg-light border-top px-4 py-3">
                                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td data-label="Ghi chú" class="text-center">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-warning note-btn"
                                                            data-id="{{ $passenger->id }}"
                                                            data-name="{{ $passenger->full_name }}"
                                                            data-note="{{ $passenger->special_note ?? '' }}"
                                                            data-url="{{ route('guide.passengers.update_note', $passenger) }}"
                                                            title="Ghi chú đặc biệt"
                                                            {{ $isLocked ? 'disabled' : '' }}
                                                        >
                                                            @if($passenger->special_note)
                                                                <i class="bi bi-sticky-fill text-warning"></i>
                                                            @else
                                                                <i class="bi bi-sticky"></i>
                                                            @endif
                                                        </button>
                                                    </td>
                                                    <td data-label="Sửa" class="text-center">
                                                        @if($loop->first)
                                                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#addPassengerModal-{{ $booking->id }}" title="Quản lý danh sách khách" {{ $isLocked ? 'disabled' : '' }}>
                                                                <i class="bi bi-people-fill me-1"></i>Sửa
                                                            </button>
                                                        @else
                                                            <span class="text-muted small">#{{ $booking->id }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
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

            <!-- Tab Activities -->
            <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                <div class="card border-0 shadow-sm">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title mb-0">Lịch trình & Các điểm tham quan</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="accordion accordion-flush" id="itineraryAccordion">
                            @forelse($tour->tour_itineraries as $itinerary)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-day-{{ $itinerary->id }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-day-{{ $itinerary->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-day-{{ $itinerary->id }}">
                                            Ngày {{ $itinerary->day_number }}: {{ $itinerary->title }}
                                        </button>
                                    </h2>
                                    <div id="collapse-day-{{ $itinerary->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-day-{{ $itinerary->id }}" data-bs-parent="#itineraryAccordion">
                                        <div class="accordion-body bg-light">
                                            @if($itinerary->activities->isNotEmpty())
                                                <div class="list-group list-group-flush rounded">
                                                    @foreach($itinerary->activities as $activity)
                                                        @php
                                                            $checkin = $tourSchedule->activity_checkins->firstWhere('tour_activity_id', $activity->id);
                                                            $isChecked = $checkin ? true : false;
                                                        @endphp
                                                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                            <div>
                                                                <h6 class="mb-1 fw-bold text-dark">
                                                                    <i class="bi bi-check-circle-fill text-{{ $isChecked ? 'success' : 'secondary' }} me-2" id="icon-act-{{ $activity->id }}"></i>
                                                                    {{ $activity->title }}
                                                                </h6>
                                                                <p class="mb-1 text-muted small"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }} | <span class="badge bg-secondary">{{ $activity->activity_type_label }}</span></p>
                                                                <p class="mb-0 text-success small fw-semibold mt-1" id="time-act-{{ $activity->id }}" style="display:{{ $isChecked ? 'block' : 'none' }};">
                                                                    Đã check-in lúc {{ $isChecked ? $checkin->checked_in_at->format('H:i d/m/Y') : '' }}
                                                                </p>
                                                            </div>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-sm btn-{{ $isChecked ? 'outline-secondary' : 'success' }} fw-bold px-3 btn-checkin-activity" 
                                                                    data-id="{{ $activity->id }}" 
                                                                    data-url="{{ route('guide.activities.toggle_checkin', [$tourSchedule->id, $activity->id]) }}"
                                                                    id="btn-act-{{ $activity->id }}"
                                                                    {{ $isLocked ? 'disabled' : '' }}>
                                                                    @if($isChecked)
                                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Hủy
                                                                    @else
                                                                        <i class="bi bi-geo-alt-fill me-1"></i>Check-in
                                                                    @endif
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 btn-activity-rollcall"
                                                                    data-activity-id="{{ $activity->id }}"
                                                                    data-activity-title="{{ $activity->title }}"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#activityRollCallModal"
                                                                    {{ $isLocked ? 'disabled' : '' }}>
                                                                    <i class="bi bi-people-fill me-1"></i>Điểm danh
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted small mb-0">Không có hoạt động chi tiết nào.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-map fs-1 d-block mb-2"></i>
                                    Chưa có lịch trình chi tiết.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
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


<!-- Modal Activity Roll Call -->
<div class="modal fade" id="activityRollCallModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-people-fill text-primary me-2"></i>Điểm danh: <span id="activity-rollcall-title" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Họ tên</th>
                                <th>Loại vé</th>
                                <th class="text-center" style="width: 100px;">Trạng thái</th>
                                <th class="text-center" style="width: 120px;">Tách đoàn</th>
                            </tr>
                        </thead>
                        <tbody id="activity-rollcall-body">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-3">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Free Time -->
<div class="modal fade" id="freeTimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" id="freeTimeForm">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Tách đoàn (Tự do tham quan)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">Hành khách được đánh dấu tách đoàn sẽ không bị yêu cầu điểm danh trong khoảng thời gian này.</div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_free_time" id="is_free_time" value="1">
                        <label class="form-check-label fw-bold" for="is_free_time">Cho phép tách đoàn</label>
                    </div>
                    
                    <div id="freeTimeDates" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label small">Từ thời gian</label>
                            <input type="datetime-local" name="free_time_start" id="free_time_start" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Đến thời gian</label>
                            <input type="datetime-local" name="free_time_end" id="free_time_end" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Địa điểm tách đoàn</label>
                            <input type="text" name="free_time_location" id="free_time_location" class="form-control" placeholder="VD: Khách sạn, siêu thị...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Lưu</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Manage Passengers per Booking -->
@foreach($tourSchedule->bookings as $b)
@if(in_array($b->payment_status, ['pending', 'paid_30', 'paid_100']) && !in_array($b->tour_status, [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER]) && $b->booking_status !== 'cancelled')
<div class="modal fade" id="addPassengerModal-{{ $b->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Quản lý hành khách (Booking #{{ $b->id }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="bookingTab-{{ $b->id }}" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" id="manual-tab-{{ $b->id }}" data-bs-toggle="tab" data-bs-target="#manual-pane-{{ $b->id }}" type="button">Nhập thủ công</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="excel-tab-{{ $b->id }}" data-bs-toggle="tab" data-bs-target="#excel-pane-{{ $b->id }}" type="button">Excel</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="manual-pane-{{ $b->id }}">
                        <form action="{{ route('guide.passengers.manual', [$tourSchedule->id, $b->id]) }}" method="POST">
                            @csrf
                            <div class="alert alert-warning small">Sửa danh sách này sẽ xóa các khách (trừ khách số 1) hiện tại của booking và lưu lại.</div>
                            
                            @for($i = 0; $i < $b->adults_count; $i++)
                            @php $p = $b->booking_passengers->where('passenger_type', 'adult')->values()->get($i); @endphp
                            <div class="p-2 border rounded mb-2 bg-light">
                                <div class="fw-bold small mb-1">Người lớn {{ $i+1 }}</div>
                                <input type="hidden" name="passengers[{{ $i }}][passenger_type]" value="adult">
                                <div class="row g-2">
                                    <div class="col-md-3"><input type="text" name="passengers[{{ $i }}][full_name]" class="form-control form-control-sm" placeholder="Họ tên" value="{{ $p ? $p->full_name : '' }}" required></div>
                                    <div class="col-md-3"><input type="text" name="passengers[{{ $i }}][identity_number]" class="form-control form-control-sm" placeholder="CCCD" value="{{ $p ? $p->identity_number : '' }}"></div>
                                    <div class="col-md-3"><input type="date" name="passengers[{{ $i }}][date_of_birth]" class="form-control form-control-sm" value="{{ $p && $p->date_of_birth ? $p->date_of_birth->format('Y-m-d') : '' }}"></div>
                                    <div class="col-md-3">
                                        <select name="passengers[{{ $i }}][gender]" class="form-select form-select-sm">
                                            <option value="male" {{ $p && $p->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                            <option value="female" {{ $p && $p->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                            <option value="other" {{ $p && $p->gender == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endfor

                            @for($i = 0; $i < $b->children_count; $i++)
                            @php 
                                $idx = $b->adults_count + $i; 
                                $p = $b->booking_passengers->where('passenger_type', 'child')->values()->get($i);
                            @endphp
                            <div class="p-2 border rounded mb-2 bg-light">
                                <div class="fw-bold small mb-1">Trẻ em {{ $i+1 }}</div>
                                <input type="hidden" name="passengers[{{ $idx }}][passenger_type]" value="child">
                                <div class="row g-2">
                                    <div class="col-md-4"><input type="text" name="passengers[{{ $idx }}][full_name]" class="form-control form-control-sm" placeholder="Họ tên" value="{{ $p ? $p->full_name : '' }}" required></div>
                                    <div class="col-md-4"><input type="date" name="passengers[{{ $idx }}][date_of_birth]" class="form-control form-control-sm" value="{{ $p && $p->date_of_birth ? $p->date_of_birth->format('Y-m-d') : '' }}"></div>
                                    <div class="col-md-4">
                                        <select name="passengers[{{ $idx }}][gender]" class="form-select form-select-sm">
                                            <option value="male" {{ $p && $p->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                            <option value="female" {{ $p && $p->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endfor
                            <div class="text-end mt-3"><button type="submit" class="btn btn-primary btn-sm">Lưu danh sách</button></div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="excel-pane-{{ $b->id }}">
                        <form action="{{ route('guide.passengers.import', [$tourSchedule->id, $b->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Chọn file Excel hành khách</label>
                                <input type="file" name="excel_file" accept=".xls,.xlsx" class="form-control" required>
                            </div>
                            <div class="text-end"><button type="submit" class="btn btn-success btn-sm">Upload Excel</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Toast thông báo -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="toast-msg" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toast-text"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
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
        
        document.querySelectorAll('.selected-count-val').forEach(function(el) {
            el.textContent = checkedCount;
        });

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

            if (this.checked) {
                row.classList.add('table-success');
                checkedCount++;
            } else {
                row.classList.remove('table-success');
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

            const notePassengerName = document.getElementById('note-passenger-name');
            const noteTextarea = document.getElementById('note-textarea');
            const noteCharCount = document.getElementById('note-char-count');
            const saveNoteBtn = document.getElementById('save-note-btn');
            
            notePassengerName.textContent = name;
            noteTextarea.value = note;
            noteCharCount.textContent = note.length;

            new bootstrap.Modal(document.getElementById('noteModal')).show();
        });
    });

    // ─── Free Time logic ──────────────────────────────────────────────
    const freeTimeModalEl = document.getElementById('freeTimeModal');
    if (freeTimeModalEl) {
        const freeTimeModal = new bootstrap.Modal(freeTimeModalEl);
        const freeTimeForm = document.getElementById('freeTimeForm');
        const isFreeTimeCheck = document.getElementById('is_free_time');
        const freeTimeDates = document.getElementById('freeTimeDates');

        isFreeTimeCheck.addEventListener('change', function() {
            if(this.checked) {
                freeTimeDates.style.display = 'block';
            } else {
                freeTimeDates.style.display = 'none';
            }
        });

        document.querySelectorAll('.free-time-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const url = this.getAttribute('data-url');
                const start = this.getAttribute('data-start');
                const end = this.getAttribute('data-end');
                const location = this.getAttribute('data-location') || '';

                freeTimeForm.action = url;
                
                if(start || end || this.classList.contains('btn-success')) {
                    isFreeTimeCheck.checked = true;
                    freeTimeDates.style.display = 'block';
                    document.getElementById('free_time_start').value = start;
                    document.getElementById('free_time_end').value = end;
                    document.getElementById('free_time_location').value = location;
                } else {
                    isFreeTimeCheck.checked = false;
                    freeTimeDates.style.display = 'none';
                    document.getElementById('free_time_start').value = '';
                    document.getElementById('free_time_end').value = '';
                    document.getElementById('free_time_location').value = '';
                }

                freeTimeModal.show();
            });
        });
    }

    // Character counter
    const noteTextarea = document.getElementById('note-textarea');
    if (noteTextarea) {
        noteTextarea.addEventListener('input', function () {
            document.getElementById('note-char-count').textContent = this.value.length;
        });
    }

    // Save note
    const saveNoteBtn = document.getElementById('save-note-btn');
    if (saveNoteBtn) {
        saveNoteBtn.addEventListener('click', function () {
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
                const row = document.getElementById('row-' + passengerId);
                if (note.trim()) {
                    icon.className = 'bi bi-sticky-fill text-warning';
                    if (row) row.classList.add('table-warning');
                } else {
                    icon.className = 'bi bi-sticky';
                    if (row) row.classList.remove('table-warning');
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
    }
    // Tab switching styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('show.bs.tab', function (e) {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('text-dark', 'border-bottom', 'border-3', 'border-warning');
                b.classList.add('text-muted');
            });
            e.target.classList.remove('text-muted');
            e.target.classList.add('text-dark', 'border-bottom', 'border-3', 'border-warning');
        });
    });

    // Toggle Activity Checkin
    document.querySelectorAll('.btn-checkin-activity').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const actId = this.dataset.id;
            const url = this.dataset.url;
            const selfBtn = this;
            const iconEl = document.getElementById('icon-act-' + actId);
            const timeEl = document.getElementById('time-act-' + actId);

            selfBtn.disabled = true;
            let originalHtml = selfBtn.innerHTML;
            selfBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw err; });
                }
                return res.json();
            })
            .then(data => {
                showToast(data.message, data.checked_in ? 'success' : 'secondary');
                
                if (data.checked_in) {
                    selfBtn.className = 'btn btn-sm btn-outline-secondary fw-bold px-3 btn-checkin-activity';
                    selfBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i>Hủy';
                    iconEl.className = 'bi bi-check-circle-fill text-success me-2';
                    timeEl.style.display = 'block';
                    timeEl.textContent = 'Đã check-in lúc ' + data.time;
                } else {
                    selfBtn.className = 'btn btn-sm btn-success fw-bold px-3 btn-checkin-activity';
                    selfBtn.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i>Check-in';
                    iconEl.className = 'bi bi-check-circle-fill text-secondary me-2';
                    timeEl.style.display = 'none';
                    timeEl.textContent = '';
                }
            })
            .catch((err) => {
                showToast(err.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'danger');
                selfBtn.innerHTML = originalHtml;
            })
            .finally(() => {
                selfBtn.disabled = false;
            });
        });
    });
    // ─── Activity Roll Call Logic ──────────────────────────────────────────────
    @php
        $passengersArray = $tourSchedule->bookings->flatMap(fn($b) => $b->booking_passengers)->map(function($p) {
            return [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'passenger_type' => $p->passenger_type,
                'is_free_time' => (bool)$p->is_free_time,
                'free_time_start' => $p->free_time_start ? \Carbon\Carbon::parse($p->free_time_start)->format('Y-m-d\TH:i') : null,
                'free_time_end' => $p->free_time_end ? \Carbon\Carbon::parse($p->free_time_end)->format('Y-m-d\TH:i') : null,
                'free_time_location' => $p->free_time_location,
                'activity_checkins' => $p->activity_checkins->pluck('tour_activity_id')->toArray()
            ];
        })->values()->all();
    @endphp
    const passengersData = @json($passengersArray);
    const scheduleId = {{ $tourSchedule->id }};
    const isLocked = {{ $isLocked ? 'true' : 'false' }};

    const activityRollCallModalEl = document.getElementById('activityRollCallModal');
    if (activityRollCallModalEl) {
        const activityRollCallModal = new bootstrap.Modal(activityRollCallModalEl);
        let currentActivityId = null;

        document.querySelectorAll('.btn-activity-rollcall').forEach(btn => {
            btn.addEventListener('click', function() {
                currentActivityId = this.getAttribute('data-activity-id');
                const title = this.getAttribute('data-activity-title');
                document.getElementById('activity-rollcall-title').textContent = title;

                const tbody = document.getElementById('activity-rollcall-body');
                tbody.innerHTML = '';

                passengersData.forEach(p => {
                    const isChecked = p.activity_checkins.includes(parseInt(currentActivityId));
                    const typeLabel = p.passenger_type === 'adult' ? '<span class="badge badge-soft-primary">Người lớn</span>' : 
                                      (p.passenger_type === 'child' ? '<span class="badge badge-soft-warning">Trẻ em</span>' : '<span class="badge badge-soft-secondary">Em bé</span>');
                    
                    const checkedHtml = (isChecked && !p.is_free_time) ? 'checked' : '';
                    const disabledHtml = isLocked ? 'disabled' : '';
                    const checkboxDisabled = (isLocked || p.is_free_time) ? 'disabled' : '';

                    const tr = document.createElement('tr');
                    tr.id = `rollcall-row-${p.id}`;
                    if (p.is_free_time) {
                        tr.className = 'table-warning text-muted';
                    } else {
                        tr.className = isChecked ? 'table-success' : '';
                    }

                    const freeTimeBadge = p.is_free_time ? `<span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Tách đoàn (${p.free_time_location || 'Tự do'})</span>` : '';

                    tr.innerHTML = `
                        <td>
                            <div class="fw-bold text-dark">${p.full_name}</div>
                            <div class="small text-muted" id="free-time-info-${p.id}">${freeTimeBadge}</div>
                        </td>
                        <td>${typeLabel}</td>
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input activity-passenger-checkbox" type="checkbox" 
                                    data-passenger-id="${p.id}" ${checkedHtml} ${checkboxDisabled}
                                    style="width: 1.3em; height: 1.3em; cursor: pointer;">
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm ${p.is_free_time ? 'btn-success' : 'btn-outline-secondary'} btn-modal-free-time" 
                                data-passenger-id="${p.id}" ${disabledHtml}>
                                <i class="bi bi-clock-history"></i> ${p.is_free_time ? 'Đang tách' : 'Tách đoàn'}
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);

                    const ftTr = document.createElement('tr');
                    ftTr.id = `free-time-row-${p.id}`;
                    ftTr.className = 'd-none bg-light';
                    ftTr.innerHTML = `
                        <td colspan="4" class="p-3 border-top-0">
                            <div class="row g-2 align-items-end text-start">
                                <div class="col-md-3">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Cho phép tách đoàn</label>
                                    <div class="form-switch pt-1">
                                        <input class="form-check-input free-time-toggle" type="checkbox" id="toggle-ft-${p.id}" ${p.is_free_time ? 'checked' : ''}>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Thời gian bắt đầu</label>
                                    <input type="datetime-local" class="form-control form-control-sm free-time-start" id="start-ft-${p.id}" value="${p.free_time_start || ''}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Thời gian kết thúc</label>
                                    <input type="datetime-local" class="form-control form-control-sm free-time-end" id="end-ft-${p.id}" value="${p.free_time_end || ''}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Địa điểm tách đoàn</label>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control form-control-sm free-time-location" id="loc-ft-${p.id}" placeholder="VD: Khách sạn..." value="${p.free_time_location || ''}">
                                        <button type="button" class="btn btn-sm btn-primary btn-save-free-time-ajax" data-passenger-id="${p.id}"><i class="bi bi-floppy"></i></button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(ftTr);
                });
            });
        });

        // Event delegation inside modal
        const rollcallBody = document.getElementById('activity-rollcall-body');
        
        // 1. Toggle collapse row on click Tách đoàn
        rollcallBody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-modal-free-time');
            if (btn) {
                const passengerId = btn.getAttribute('data-passenger-id');
                const ftRow = document.getElementById(`free-time-row-${passengerId}`);
                if (ftRow) {
                    ftRow.classList.toggle('d-none');
                }
            }
        });

        // 2. Save Tách đoàn via AJAX
        rollcallBody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-save-free-time-ajax');
            if (btn) {
                const passengerId = btn.getAttribute('data-passenger-id');
                const isFreeTime = document.getElementById(`toggle-ft-${passengerId}`).checked ? 1 : 0;
                const start = document.getElementById(`start-ft-${passengerId}`).value;
                const end = document.getElementById(`end-ft-${passengerId}`).value;
                const location = document.getElementById(`loc-ft-${passengerId}`).value;

                btn.disabled = true;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                const url = `/guide/schedules/${scheduleId}/passengers/${passengerId}/free-time`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        is_free_time: isFreeTime,
                        free_time_start: start,
                        free_time_end: end,
                        free_time_location: location
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;

                    // Update local javascript data
                    const p = passengersData.find(p => p.id == passengerId);
                    if (p) {
                        p.is_free_time = data.passenger.is_free_time;
                        p.free_time_start = data.passenger.free_time_start;
                        p.free_time_end = data.passenger.free_time_end;
                        p.free_time_location = data.passenger.free_time_location;
                    }

                    // Update UI elements in row
                    const tr = document.getElementById(`rollcall-row-${passengerId}`);
                    const badgeContainer = document.getElementById(`free-time-info-${passengerId}`);
                    const checkbox = tr.querySelector('.activity-passenger-checkbox');
                    const ftBtn = tr.querySelector('.btn-modal-free-time');
                    const ftRow = document.getElementById(`free-time-row-${passengerId}`);

                    if (data.passenger.is_free_time) {
                        badgeContainer.innerHTML = `<span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Tách đoàn (${data.passenger.free_time_location || 'Tự do'})</span>`;
                        tr.className = 'table-warning text-muted';
                        checkbox.checked = false;
                        checkbox.disabled = true;
                        ftBtn.className = 'btn btn-sm btn-success btn-modal-free-time';
                        ftBtn.innerHTML = '<i class="bi bi-clock-history"></i> Đang tách';

                        // If checked in, toggle to false in local data
                        if (p && p.activity_checkins.includes(parseInt(currentActivityId))) {
                            p.activity_checkins = p.activity_checkins.filter(id => id !== parseInt(currentActivityId));
                        }
                    } else {
                        badgeContainer.innerHTML = '';
                        tr.className = checkbox.checked ? 'table-success' : '';
                        checkbox.disabled = isLocked;
                        ftBtn.className = 'btn btn-sm btn-outline-secondary btn-modal-free-time';
                        ftBtn.innerHTML = '<i class="bi bi-clock-history"></i> Tách đoàn';
                    }

                    ftRow.classList.add('d-none');
                    showToast(data.message, 'success');
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    showToast('Không thể lưu thông tin tách đoàn.', 'danger');
                });
            }
        });

        // 3. Toggle checkin checkbox via AJAX
        rollcallBody.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('activity-passenger-checkbox')) {
                const passengerId = e.target.getAttribute('data-passenger-id');
                const isChecked = e.target.checked;
                const tr = document.getElementById(`rollcall-row-${passengerId}`);
                const checkbox = e.target;

                checkbox.disabled = true;

                const url = `/guide/schedules/${scheduleId}/activities/${currentActivityId}/passengers/${passengerId}/toggle-checkin`;
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    checkbox.disabled = false;
                    checkbox.checked = data.checked_in;
                    
                    if (data.checked_in) {
                        tr.className = 'table-success';
                        const pData = passengersData.find(p => p.id == passengerId);
                        if(pData && !pData.activity_checkins.includes(parseInt(currentActivityId))) {
                            pData.activity_checkins.push(parseInt(currentActivityId));
                        }
                    } else {
                        tr.className = '';
                        const pData = passengersData.find(p => p.id == passengerId);
                        if(pData) {
                            pData.activity_checkins = pData.activity_checkins.filter(id => id !== parseInt(currentActivityId));
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    checkbox.disabled = false;
                    checkbox.checked = !isChecked; // Revert
                    showToast('Có lỗi xảy ra, vui lòng thử lại!', 'danger');
                });
            }
        });
    }
});
</script>

@endsection
