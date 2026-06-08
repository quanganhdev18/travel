@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Tài Khoản')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="admin-card border-0 mb-4">
            <div class="admin-card-header bg-white py-3">
                <h5 class="admin-card-title mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa Tài Khoản: {{ $user->name }}</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu mới (Để trống nếu không đổi)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nhập lại Mật khẩu</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if(auth()->user()->role === 'admin')
                    <div class="mb-4">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                            <option value="guide" {{ old('role', $user->role) == 'guide' ? 'selected' : '' }}>Hướng dẫn viên</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @else
                        <!-- Staff cannot edit role -->
                        <div class="mb-4">
                            <label class="form-label">Vai trò</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                            <div class="form-text">Bạn không có quyền thay đổi vai trò của tài khoản này.</div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
