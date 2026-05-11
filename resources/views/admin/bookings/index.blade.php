@extends('layouts.admin')

@section('page-title', 'Quản lý Đơn đặt chỗ (Bookings)')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="admin-card border-0 mb-0">
            <div class="admin-card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-cart-check text-primary fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-500 text-uppercase mb-1">Tổng đơn hàng</div>
                    <div class="h5 mb-0 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card border-0 mb-0">
            <div class="admin-card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-clock-history text-warning fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-500 text-uppercase mb-1">Đang chờ xử lý</div>
                    <div class="h5 mb-0 fw-bold text-dark">{{ number_format($stats['pending']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card border-0 mb-0">
            <div class="admin-card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-cash-stack text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-500 text-uppercase mb-1">Doanh thu tạm tính</div>
                    <div class="h5 mb-0 fw-bold text-dark">{{ number_format($stats['revenue'], 0, ',', '.') }} ₫</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card border-0 mb-0">
            <div class="admin-card-body d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-airplane-engines text-danger fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-500 text-uppercase mb-1">Cần xuất vé MB</div>
                    <div class="h5 mb-0 fw-bold text-dark">{{ number_format($stats['flight_ticket_needed']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card border-0 mb-4">
    <div class="admin-card-body">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Tìm mã đơn, PNR, tên khách, số điện thoại..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-admin btn-admin-primary w-100">Lọc dữ liệu</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-admin btn-light border w-100">Làm mới</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-card border-0">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-list-ul me-2"></i>Danh sách Booking</h5>
        <button class="btn btn-admin btn-admin-primary"><i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel</button>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Mã Đơn / Ngày</th>
                        <th>Khách Hàng</th>
                        <th>Sản Phẩm</th>
                        <th>Di Chuyển</th>
                        <th>Thanh Toán</th>
                        <th>Trạng Thái</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="small text-muted">{{ $booking->created_at->format('H:i d/m/Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $booking->user->name ?? 'Khách lẻ' }}</div>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $booking->user->phone ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="fw-500 text-dark text-truncate" style="max-width: 220px;" title="{{ $booking->tour_schedule->tour->title ?? '' }}">
                                {{ $booking->tour_schedule->tour->title ?? 'N/A' }}
                            </div>
                            <div class="small text-primary mt-1"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            @if($booking->pnr_code)
                            <span class="badge-soft badge-soft-danger px-2">
                                <i class="bi bi-airplane me-1"></i>{{ $booking->pnr_code }}
                            </span>
                            @else
                            <span class="badge-soft badge-soft-secondary px-2">
                                <i class="bi bi-car-front me-1"></i>Tự túc
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-danger">{{ number_format($booking->total_price, 0, ',', '.') }} ₫</div>
                            <div class="small text-muted mt-1">{{ $booking->adults_count + $booking->children_count }} khách</div>
                        </td>
                        <td>
                            @php
                            $statusMap = [
                                'pending' => ['badge-soft-warning', 'Chờ xử lý'],
                                'confirmed' => ['badge-soft-success', 'Đã xác nhận'],
                                'paid' => ['badge-soft-primary', 'Đã thanh toán'],
                                'completed' => ['badge-soft-success', 'Hoàn thành'],
                                'cancelled' => ['badge-soft-danger', 'Đã hủy']
                            ];
                            $s = $statusMap[$booking->booking_status] ?? ['badge-soft-secondary', 'N/A'];
                            @endphp
                            <span class="badge-soft {{ $s[0] }}">
                                {{ $s[1] }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-action" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 200px;">
                                    <li>
                                        <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#bookingDetail{{ $booking->id }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header text-uppercase" style="font-size: 0.75rem;">Cập nhật trạng thái</h6></li>
                                    @foreach($statusMap as $key => $val)
                                    <li>
                                        <form action="{{ route('admin.bookings.update_status', $booking->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $key }}">
                                            <button type="submit" class="dropdown-item py-2 {{ $booking->booking_status == $key ? 'bg-light text-primary fw-bold' : '' }}">
                                                @if($booking->booking_status == $key) <i class="bi bi-check-lg me-2"></i> @else <span style="margin-left: 24px;"></span> @endif
                                                {{ $val[1] }}
                                            </button>
                                        </form>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Không tìm thấy đơn hàng nào khớp với yêu cầu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-body border-top py-3">
        {{ $bookings->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

@foreach($bookings as $booking)
<div class="modal fade" id="bookingDetail{{ $booking->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3" style="background-color: var(--admin-sidebar); color: white;">
                <h5 class="modal-title fw-600">Chi Tiết Đơn Đặt Chỗ #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6 border-end pe-md-4">
                        <h6 class="text-primary mb-3 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-person-badge me-2"></i>Thông tin người đặt</h6>
                        <div class="mb-2"><span class="text-muted me-2">Họ tên:</span> <strong class="text-dark">{{ $booking->user->name ?? 'N/A' }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">SĐT:</span> <strong class="text-dark">{{ $booking->user->phone ?? 'N/A' }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Email:</span> <strong class="text-dark">{{ $booking->user->email ?? 'N/A' }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Ngày đặt:</span> <strong class="text-dark">{{ $booking->created_at->format('H:i d/m/Y') }}</strong></div>
                    </div>

                    <div class="col-md-6 ps-md-4">
                        <h6 class="text-primary mb-3 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-briefcase me-2"></i>Sản phẩm (Tour)</h6>
                        <div class="mb-2"><span class="text-muted me-2">Tên tour:</span> <strong class="text-dark">{{ $booking->tour_schedule->tour->title ?? 'N/A' }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Khởi hành:</span> <strong class="text-dark">{{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Hành khách:</span> <strong class="text-dark">{{ $booking->adults_count }} NL, {{ $booking->children_count }} TE</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Vé Máy Bay:</span> 
                            @if($booking->pnr_code)
                                <span class="badge-soft badge-soft-danger">{{ $booking->pnr_code }}</span>
                            @else
                                <span class="text-muted">Tự túc / Chưa xuất vé</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-primary mt-3 mb-3 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-people me-2"></i>Danh Sách Hành Khách</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover border-light mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2">Họ và tên</th>
                                        <th class="py-2">Giới tính</th>
                                        <th class="py-2">Ngày sinh</th>
                                        <th class="py-2">CCCD / Hộ chiếu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking->booking_passengers as $passenger)
                                    <tr>
                                        <td class="fw-500">{{ $passenger->full_name }}</td>
                                        <td>{{ $passenger->gender == 'male' ? 'Nam' : 'Nữ' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($passenger->date_of_birth)->format('d/m/Y') }}</td>
                                        <td class="text-primary">{{ $passenger->identity_number }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Không có dữ liệu hành khách chi tiết.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(isset($booking->user->identity))
                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-primary mt-3 mb-3 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-card-image me-2"></i>Ảnh Định Danh Đại Diện</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small mb-2 text-center">Mặt trước CCCD</div>
                                <img src="{{ asset($booking->user->identity->front_image_url) }}" class="img-fluid rounded border shadow-sm w-100 object-fit-cover" style="height: 150px;" alt="Mặt trước">
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-2 text-center">Mặt sau CCCD</div>
                                <img src="{{ asset($booking->user->identity->back_image_url) }}" class="img-fluid rounded border shadow-sm w-100 object-fit-cover" style="height: 150px;" alt="Mặt sau">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">Tổng cộng cần thanh toán:</span>
                    <span class="fs-4 text-danger mb-0 ms-2 fw-bold">{{ number_format($booking->total_price, 0, ',', '.') }} ₫</span>
                </div>
                <button type="button" class="btn btn-admin btn-light border" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection