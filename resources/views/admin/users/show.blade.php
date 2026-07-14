@extends('layouts.admin')

@section('title', 'Chi tiết tài khoản')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">Chi tiết tài khoản</h6>
                        <div class="ms-auto">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-pencil-alt"></i> Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- User Information -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-xxl mx-auto bg-gradient-primary">
                                        <span class="text-white text-xl font-weight-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                    <h5 class="mt-3 mb-0">{{ $user->name }}</h5>
                                    <p class="text-sm text-secondary">{{ $user->email }}</p>
                                    
                                    @if($user->role === 'admin')
                                    <span class="badge badge-lg bg-gradient-danger">{{ $user->role_label ?? 'Admin' }}</span>
                                    @elseif($user->role === 'staff')
                                    <span class="badge badge-lg bg-gradient-success">{{ $user->role_label ?? 'Nhân viên' }}</span>
                                    @elseif($user->role === 'cskh')
                                    <span class="badge badge-lg bg-gradient-primary">{{ $user->role_label ?? 'Nhân viên CSKH' }}</span>
                                    @elseif($user->role === 'guide')
                                    <span class="badge badge-lg bg-gradient-info">{{ $user->role_label ?? 'Hướng dẫn viên' }}</span>
                                    @else
                                    <span class="badge badge-lg bg-gradient-secondary">{{ $user->role_label ?? 'Khách hàng' }}</span>
                                    @endif

                                    <hr class="horizontal dark my-3">

                                    <div class="mb-3">
                                        @if($user->is_active)
                                        <span class="badge badge-lg bg-gradient-success">
                                            <i class="fas fa-check-circle"></i> Đang hoạt động
                                        </span>
                                        @else
                                        <span class="badge badge-lg bg-gradient-danger">
                                            <i class="fas fa-lock"></i> Bị khóa
                                        </span>
                                        @endif
                                    </div>

                                    <hr class="horizontal dark my-3">
                                    
                                    <div class="text-start">
                                        <p class="text-sm mb-2">
                                            <i class="fas fa-phone text-primary me-2"></i>
                                            <strong>Số điện thoại:</strong> {{ $user->phone ?? 'Chưa cập nhật' }}
                                        </p>
                                        <p class="text-sm mb-2">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                                        </p>
                                        <p class="text-sm mb-0">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <strong>Cập nhật:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="numbers">
                                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng đơn hàng</p>
                                                        <h5 class="font-weight-bolder mb-0">{{ $bookingStats['total'] }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                                        <i class="ni ni-cart text-lg opacity-10"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="numbers">
                                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Đã hoàn thành</p>
                                                        <h5 class="font-weight-bolder mb-0">{{ $bookingStats['completed'] }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                                        <i class="ni ni-check-bold text-lg opacity-10"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="numbers">
                                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Đang xử lý</p>
                                                        <h5 class="font-weight-bolder mb-0">{{ $bookingStats['pending'] }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                                        <i class="ni ni-time-alarm text-lg opacity-10"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="numbers">
                                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Đã hủy</p>
                                                        <h5 class="font-weight-bolder mb-0">{{ $bookingStats['cancelled'] }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                                        <i class="ni ni-fat-remove text-lg opacity-10"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Bookings -->
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6>Đơn hàng gần đây</h6>
                                </div>
                                <div class="card-body px-0 pt-0 pb-2">
                                    <div class="table-responsive p-0">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã đơn</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tour</th>
                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày đặt</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($user->bookings->take(5) as $booking)
                                                <tr>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">#{{ $booking->id }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">{{ $booking->tour->title ?? 'N/A' }}</p>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        @if($booking->status === 'completed')
                                                        <span class="badge badge-sm bg-gradient-success">Hoàn thành</span>
                                                        @elseif($booking->status === 'confirmed')
                                                        <span class="badge badge-sm bg-gradient-info">Đã xác nhận</span>
                                                        @elseif($booking->status === 'pending')
                                                        <span class="badge badge-sm bg-gradient-warning">Chờ xử lý</span>
                                                        @else
                                                        <span class="badge badge-sm bg-gradient-secondary">{{ $booking->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-secondary text-xs font-weight-bold">{{ $booking->created_at->format('d/m/Y') }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3">
                                                        <p class="text-xs text-secondary mb-0">Chưa có đơn hàng nào</p>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
