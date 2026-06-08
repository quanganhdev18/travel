@extends('layouts.admin')

@section('title', 'Quản lý tài khoản')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Quản lý tài khoản</h6>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tạo tài khoản mới
                    </a>
                </div>

                <!-- Statistics -->
                <div class="card-body px-4 pt-3 pb-2">
                    <div class="row mb-4">
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng tài khoản</p>
                                                <h5 class="font-weight-bolder mb-0">{{ $stats['total'] }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                                <i class="ni ni-single-02 text-lg opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Quản trị viên</p>
                                                <h5 class="font-weight-bolder mb-0">{{ $stats['admin'] }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                                <i class="ni ni-badge text-lg opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Nhân viên</p>
                                                <h5 class="font-weight-bolder mb-0">{{ $stats['staff'] }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                                <i class="ni ni-tie-bow text-lg opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Khách hàng</p>
                                                <h5 class="font-weight-bolder mb-0">{{ $stats['customer'] }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                                <i class="ni ni-circle-08 text-lg opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Tìm kiếm theo tên, email, số điện thoại..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="role" class="form-select">
                                    <option value="">Tất cả vai trò</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Nhân viên</option>
                                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Khách hàng</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo"></i> Đặt lại
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tài khoản</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Vai trò</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Đơn hàng</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày tạo</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <div class="avatar avatar-sm me-3 bg-gradient-primary">
                                                    <span class="text-white text-xs">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                                @if($user->phone)
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fas fa-phone text-xs"></i> {{ $user->phone }}
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->role === 'admin')
                                        <span class="badge badge-sm bg-gradient-danger">{{ $user->role_label }}</span>
                                        @elseif($user->role === 'staff')
                                        <span class="badge badge-sm bg-gradient-success">{{ $user->role_label }}</span>
                                        @else
                                        <span class="badge badge-sm bg-gradient-info">{{ $user->role_label }}</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($user->is_active)
                                        <span class="badge badge-sm bg-gradient-success">
                                            <i class="fas fa-check-circle"></i> {{ $user->status_label }}
                                        </span>
                                        @else
                                        <span class="badge badge-sm bg-gradient-danger">
                                            <i class="fas fa-lock"></i> {{ $user->status_label }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $user->bookings->count() }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-link text-info px-2 mb-0" title="Xem chi tiết">
                                            <i class="fas fa-eye text-info me-2"></i>Xem
                                        </a>
                                        
                                        @if($user->role === 'customer')
                                            {{-- Khách hàng: chỉ có khóa/mở khóa --}}
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-link {{ $user->is_active ? 'text-warning' : 'text-success' }} px-2 mb-0"
                                                        title="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}"
                                                        onclick="return confirm('Bạn có chắc chắn muốn {{ $user->is_active ? 'khóa' : 'mở khóa' }} tài khoản này?');">
                                                    <i class="fas {{ $user->is_active ? 'fa-lock' : 'fa-unlock' }} me-2"></i>{{ $user->is_active ? 'Khóa' : 'Mở' }}
                                                </button>
                                            </form>
                                            @endif
                                        @else
                                            {{-- Admin/Staff: có đầy đủ chức năng --}}
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-link text-dark px-2 mb-0" title="Chỉnh sửa">
                                                <i class="fas fa-pencil-alt text-dark me-2"></i>Sửa
                                            </a>
                                            
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-link {{ $user->is_active ? 'text-warning' : 'text-success' }} px-2 mb-0"
                                                        title="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                                    <i class="fas {{ $user->is_active ? 'fa-lock' : 'fa-unlock' }} me-2"></i>{{ $user->is_active ? 'Khóa' : 'Mở' }}
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger px-2 mb-0" title="Xóa">
                                                    <i class="fas fa-trash text-danger me-2"></i>Xóa
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-xs text-secondary mb-0">Không tìm thấy tài khoản nào</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
