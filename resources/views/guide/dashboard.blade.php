@extends('layouts.guide')

@section('page-title', 'Tổng quan Hướng dẫn viên')

@section('content')
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm admin-card-header h-100">
            <div class="card-body admin-card-body d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 rounded text-primary p-3 me-3">
                    <i class="bi bi-calendar-check fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Tổng số Tour đã/đang nhận</h6>
                    <h3 class="mb-0 fw-bold">{{ $stats['total_schedules'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm admin-card-header h-100">
            <div class="card-body admin-card-body d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 rounded text-warning p-3 me-3">
                    <i class="bi bi-clock-history fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Tour sắp tới</h6>
                    <h3 class="mb-0 fw-bold">{{ $stats['upcoming_schedules'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Thông tin Hồ sơ</h5>
    </div>
    <div class="admin-card-body">
        @if($tourGuide)
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th width="200" class="bg-light">Họ và tên:</th>
                    <td>{{ $tourGuide->name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Số điện thoại:</th>
                    <td>{{ $tourGuide->phone }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Ngôn ngữ:</th>
                    <td>{{ $tourGuide->languages }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Số năm kinh nghiệm:</th>
                    <td>{{ $tourGuide->experience_years }} năm</td>
                </tr>
                <tr>
                    <th class="bg-light">Trạng thái:</th>
                    <td>
                        @if($tourGuide->status == 'active')
                            <span class="badge badge-soft-success">Hoạt động</span>
                        @else
                            <span class="badge badge-soft-secondary">Ngừng hoạt động</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        @else
        <div class="alert alert-warning">
            Tài khoản của bạn chưa được liên kết với hồ sơ Hướng dẫn viên. Vui lòng liên hệ Quản trị viên.
        </div>
        @endif
    </div>
</div>
@endsection
