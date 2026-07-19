@extends('layouts.guide')

@section('page-title', 'Tổng quan Hướng dẫn viên')

@section('content')

@if(!$tourGuide)
    <div class="alert alert-warning border-start border-warning border-4 shadow-sm mb-4">
        <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Tài khoản chưa liên kết</h5>
        <p class="mb-0">Tài khoản của bạn chưa được liên kết với hồ sơ Hướng dẫn viên. Vui lòng liên hệ Quản trị viên để được cấp quyền dẫn tour.</p>
    </div>
@else
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 56px; height: 56px;">
            <i class="bi bi-person-badge fs-2"></i>
        </div>
        <div>
            <h4 class="mb-1 fw-bold text-dark">Xin chào, {{ $tourGuide->name }}!</h4>
            <p class="text-muted mb-0 mb-0">Chúc bạn một ngày làm việc hiệu quả và tràn đầy năng lượng.</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-600 mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tổng Tour Đã Nhận</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ $stats['total_schedules'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-journal-check fs-4"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-primary position-absolute bottom-0 start-0 w-100" style="height: 3px;"></div>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-600 mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tour Đang Diễn Ra</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ $stats['ongoing_schedules'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-play-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-success position-absolute bottom-0 start-0 w-100" style="height: 3px;"></div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-600 mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tour Sắp Tới</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ $stats['upcoming_schedules'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-warning position-absolute bottom-0 start-0 w-100" style="height: 3px;"></div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-600 mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tour Đã Hoàn Thành</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ $stats['completed_schedules'] ?? 0 }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-check2-all fs-4"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-info position-absolute bottom-0 start-0 w-100" style="height: 3px;"></div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Upcoming Tours List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="bi bi-calendar2-week me-2 text-primary"></i>Lịch trình hiện tại & sắp tới</h5>
                    <a href="{{ route('guide.schedules.index') }}" class="btn btn-sm btn-light border">Xem tất cả</a>
                </div>
                <div class="card-body p-4">
                    @if($recentSchedules->count() > 0)
                        <div class="list-group list-group-flush border-top">
                            @foreach($recentSchedules as $sg)
                                @php
                                    $sch = $sg->tour_schedule;
                                    $isOngoing = \Carbon\Carbon::parse($sch->departure_date)->startOfDay() <= now() && \Carbon\Carbon::parse($sch->return_date)->endOfDay() >= now();
                                @endphp
                                <a href="{{ route('guide.schedules.show', $sch->id) }}" class="list-group-item list-group-item-action px-0 py-3 d-flex align-items-center gap-3" style="border-bottom: 1px dashed var(--bs-border-color-translucent);">
                                    <div class="flex-shrink-0 text-center rounded-3 bg-light border p-2" style="width: 60px;">
                                        <div class="text-danger fw-bold" style="font-size: 0.8rem;">{{ \Carbon\Carbon::parse($sch->departure_date)->format('M') }}</div>
                                        <div class="fw-bold text-dark fs-5">{{ \Carbon\Carbon::parse($sch->departure_date)->format('d') }}</div>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 fw-bold text-truncate text-dark">{{ $sch->tour->title ?? 'Tour không xác định' }}</h6>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($sch->departure_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sch->return_date)->format('d/m/Y') }}</span>
                                            <span><i class="bi bi-people me-1"></i>{{ $sch->capacity }} chỗ</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 d-none d-md-block">
                                        @if($isOngoing)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-2">Đang diễn ra</span>
                                        @else
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-3 py-2">Sắp tới</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Không có lịch trình nào sắp tới</h6>
                            <p class="text-muted small mb-0">Bạn có thể dành thời gian nghỉ ngơi hoặc liên hệ điều hành tour.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="bi bi-person-vcard me-2 text-primary"></i>Hồ sơ Của Bạn</h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="avatar-circle bg-primary text-white mx-auto mb-3 fw-bold shadow-sm d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem; border-radius: 50%;">
                            {{ strtoupper(substr($tourGuide->name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $tourGuide->name }}</h5>
                        @if($tourGuide->status == 'active')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-2 py-1"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem"></i>Đang hoạt động</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-2 py-1">Ngừng hoạt động</span>
                        @endif
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 py-3 d-flex align-items-center border-dashed">
                            <div class="text-muted me-3" style="width: 24px; text-align: center;"><i class="bi bi-telephone fs-5"></i></div>
                            <div>
                                <div class="small text-muted mb-0">Số điện thoại</div>
                                <div class="fw-500 text-dark">{{ $tourGuide->phone }}</div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex align-items-center border-dashed">
                            <div class="text-muted me-3" style="width: 24px; text-align: center;"><i class="bi bi-translate fs-5"></i></div>
                            <div>
                                <div class="small text-muted mb-0">Ngôn ngữ</div>
                                <div class="fw-500 text-dark">
                                    @php
                                        $langs = is_array($tourGuide->languages) ? $tourGuide->languages : explode(',', (string) $tourGuide->languages);
                                    @endphp
                                    @foreach($langs as $lang)
                                        <span class="badge bg-light text-dark border me-1">{{ trim($lang) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex align-items-center border-dashed border-bottom-0">
                            <div class="text-muted me-3" style="width: 24px; text-align: center;"><i class="bi bi-briefcase fs-5"></i></div>
                            <div>
                                <div class="small text-muted mb-0">Kinh nghiệm</div>
                                <div class="fw-500 text-dark">{{ $tourGuide->experience_years }} năm</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
