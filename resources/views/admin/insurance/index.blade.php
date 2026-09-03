@extends('layouts.admin')

@section('page-title', 'Quản lý Bảo hiểm du lịch')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1: Total Registrations -->
    <div class="col-md-3">
        <div class="admin-card border-0 mb-0">
            <div class="admin-card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-shield-check text-primary fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-500 text-uppercase mb-1">Tổng đơn bảo hiểm</div>
                    <div class="h5 mb-0 fw-bold text-dark">{{ number_format($totalRegistrations) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Confirmed Count -->
    <div class="col-md-3">
        <div class="admin-card border-0 mb-0">
            <div class="admin-card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-patch-check-fill text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-500 text-uppercase mb-1">Đã xác nhận</div>
                    <div class="h5 mb-0 fw-bold text-dark">{{ number_format($confirmedCount) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Pending Count -->
    <div class="col-md-3">
        <div class="admin-card border-0 mb-0">
            <div class="admin-card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-clock-history text-warning fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-500 text-uppercase mb-1">Chờ xử lý</div>
                    <div class="h5 mb-0 fw-bold text-dark">{{ number_format($pendingCount) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Revenue -->
    <div class="col-md-3">
        <div class="admin-card border-0 mb-0">
            <div class="admin-card-body d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-currency-dollar text-info fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-500 text-uppercase mb-1">Tổng doanh thu</div>
                    <div class="h5 mb-0 fw-bold text-dark">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="admin-card border-0 mb-4">
    <div class="admin-card-body">
        <form action="{{ route('admin.insurance.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Tìm mã đơn, tên, sđt, email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="package_code" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả gói bảo hiểm --</option>
                    <option value="co_ban" {{ request('package_code') == 'co_ban' ? 'selected' : '' }}>Gói Cơ bản</option>
                    <option value="tieu_chuan" {{ request('package_code') == 'tieu_chuan' ? 'selected' : '' }}>Gói Tiêu chuẩn</option>
                    <option value="cao_cap" {{ request('package_code') == 'cao_cap' ? 'selected' : '' }}>Gói Cao cấp</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Lọc</button>
                @if(request()->hasAny(['search', 'package_code', 'status']))
                    <a href="{{ route('admin.insurance.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="admin-card border-0">
    <div class="admin-card-header">
        <h5 class="admin-card-title"><i class="bi bi-shield-shaded me-2"></i> Danh Sách Đăng Ký Bảo Hiểm</h5>
    </div>
    <div class="admin-card-body p-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Gói bảo hiểm</th>
                        <th>Thời gian du lịch</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $reg)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-primary">{{ $reg->registration_code }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $reg->fullname }}</div>
                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $reg->phone }}</div>
                                <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $reg->email }}</div>
                            </td>
                            <td>
                                @if($reg->package_code == 'cao_cap')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">👑 {{ $reg->package_name }}</span>
                                @elseif($reg->package_code == 'tieu_chuan')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">⭐ {{ $reg->package_name }}</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">{{ $reg->package_name }}</span>
                                @endif
                                <div class="small text-muted mt-1">{{ number_format($reg->price_per_day, 0, ',', '.') }}đ/ngày</div>
                            </td>
                            <td>
                                <div><i class="bi bi-calendar-range me-1 text-primary"></i>{{ $reg->start_date->format('d/m/Y') }} - {{ $reg->end_date->format('d/m/Y') }}</div>
                                <div class="small text-muted"><span class="badge bg-light text-dark border">{{ $reg->total_days }} ngày</span></div>
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-6">{{ number_format($reg->total_price, 0, ',', '.') }} ₫</span>
                            </td>
                            <td>
                                @if($reg->status == 'confirmed')
                                    <span class="badge badge-soft-success"><i class="bi bi-check-circle me-1"></i>Đã xác nhận</span>
                                @elseif($reg->status == 'pending')
                                    <span class="badge badge-soft-warning"><i class="bi bi-clock me-1"></i>Chờ xử lý</span>
                                @else
                                    <span class="badge badge-soft-danger"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $reg->created_at->format('H:i d/m/Y') }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Trạng thái
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <form action="{{ route('admin.insurance.update_status', $reg->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-lg me-2"></i>Đã xác nhận</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.insurance.update_status', $reg->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="dropdown-item text-warning"><i class="bi bi-clock me-2"></i>Chờ xử lý</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.insurance.update_status', $reg->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-lg me-2"></i>Hủy đơn</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                                <form action="{{ route('admin.insurance.destroy', $reg->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn bảo hiểm này không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-action text-danger ms-1" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-x fs-1 d-block mb-2"></i>
                                Không tìm thấy đơn đăng ký bảo hiểm nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="p-4 border-top">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
