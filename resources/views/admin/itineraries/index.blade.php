@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.tours.index') }}" class="text-decoration-none">Quay lại danh sách Tour</a>
    <div class="mt-2 text-dark fs-5">Lịch trình: {{ $tour->title }}</div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="text-dark fs-6 mb-3">Thêm ngày mới</div>
                <form action="{{ route('admin.tours.itineraries.store', $tour->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ngày thứ</label>
                        <input type="number" name="day_number"
                            class="form-control @error('day_number') is-invalid @enderror" required min="1"
                            max="{{ $tour->duration_days }}" value="{{ old('day_number') }}">
                        @error('day_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề ngày</label>
                        <input type="text" name="title" class="form-control" placeholder="VD: Khám phá đảo ngọc"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả tổng quan</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Lưu ngày</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        @forelse($itineraries as $day)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="text-dark fs-6">Ngày {{ $day->day_number }}: {{ $day->title }}</div>
                <form action="{{ route('admin.itineraries.destroy', $day->id) }}" method="POST"
                    onsubmit="return confirm('Xóa ngày này sẽ xóa luôn các hoạt động bên trong?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Xóa ngày</button>
                </form>
            </div>
            <div class="card-body">
                <p class="text-muted small">{{ $day->description }}</p>

                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 60px;">Ảnh</th>
                                <th>Loại</th>
                                <th>Thời gian</th>
                                <th>Hoạt động</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($day->activities as $act)
                            <tr>
                                <td>
                                    @if($act->image_url)
                                    <img src="{{ $act->image_url }}" alt="Ảnh" class="rounded shadow-sm"
                                        style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                    <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 45px; height: 45px; font-size: 11px;">Trống</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $act->activity_type }}</span></td>
                                <td>{{ $act->start_time ? \Carbon\Carbon::parse($act->start_time)->format('H:i') : '--:--' }}
                                </td>
                                <td>{{ $act->title }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.activities.destroy', $act->id) }}" method="POST"
                                        onsubmit="return confirm('Anh có chắc muốn xóa hoạt động này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm text-danger border-0 bg-transparent">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <form action="{{ route('admin.itineraries.activities.store', $day->id) }}" method="POST"
                    enctype="multipart/form-data" class="row g-2 align-items-end border-top pt-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label small">Phân loại</label>
                        <select name="activity_type" class="form-select form-select-sm" required>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Dining">Dining</option>
                            <option value="Attractions">Attractions</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Giờ bắt đầu</label>
                        <input type="time" name="start_time" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Tên hoạt động</label>
                        <input type="text" name="title" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Ảnh tải lên</label>
                        <input type="file" name="image_upload" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-success w-100">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="text-muted text-center py-5 bg-white rounded shadow-sm">Chưa có lịch trình nào. Hãy thêm ở cột bên
            trái.</div>
        @endforelse
    </div>
</div>
@endsection