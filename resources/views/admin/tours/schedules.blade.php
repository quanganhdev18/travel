@extends('layouts.admin')
@section('page-title', 'Quản lý lịch trình: ' . $tour->title)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Thêm lịch trình mới</div>
            <div class="card-body">
                <form action="{{ route('admin.tours.schedules.store', $tour->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small">Ngày khởi hành</label>
                        <input type="datetime-local" name="departure_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Ngày về</label>
                        <input type="datetime-local" name="return_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Số lượng khách tối đa</label>
                        <input type="number" name="capacity" class="form-control" value="20" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Lưu lịch trình</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Danh sách ngày khởi hành</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Khởi hành</th>
                            <th>Ngày về</th>
                            <th>Chỗ trống</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tour->tour_schedules as $sch)
                        <tr>
                            <td>{{ $sch->departure_date }}</td>
                            <td>{{ $sch->return_date }}</td>
                            <td>{{ $sch->available_seats }}/{{ $sch->capacity }}</td>
                            <td>
                                <span class="badge {{ $sch->status == 'available' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $sch->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection