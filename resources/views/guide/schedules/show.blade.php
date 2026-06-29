@extends('layouts.guide')

@section('page-title', 'Chi tiết Lịch trình Tour')

@section('content')
@php
    $tourSchedule = $scheduleGuide->tour_schedule;
    $tour = $tourSchedule->tour;
    
    $statusClass = 'secondary';
    $statusText = 'Chưa xác định';
    
    if ($tourSchedule->departure_date > now()) {
        $statusClass = 'primary';
        $statusText = 'Sắp tới';
    } elseif ($tourSchedule->departure_date <= now() && $tourSchedule->return_date >= now()) {
        $statusClass = 'success';
        $statusText = 'Đang diễn ra';
    } else {
        $statusClass = 'secondary';
        $statusText = 'Đã kết thúc';
    }
@endphp

<div class="mb-3">
    <a href="{{ route('guide.schedules.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
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
                    <img src="{{ Storage::url($tour->primary_image) }}" alt="{{ $tour->name }}" class="img-fluid rounded mb-3 w-100" style="object-fit: cover; height: 180px;">
                @endif
                <h5 class="fw-bold">{{ $tour->name }}</h5>
                <p class="text-muted small mb-3">Mã Tour: {{ $tour->tour_code }}</p>
                
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
                        <span class="text-muted">Trạng thái:</span>
                        <span class="badge badge-soft-{{ $statusClass }}">{{ $statusText }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Số khách:</span>
                        <strong>{{ $tourSchedule->bookings->sum('passengers_count') }} / {{ $tourSchedule->capacity }}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Cột danh sách khách hàng -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Danh sách Hành khách</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Họ tên</th>
                                <th>Loại vé</th>
                                <th>Mã Booking</th>
                                <th>Số điện thoại LH</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $stt = 1; @endphp
                            @forelse($tourSchedule->bookings as $booking)
                                @if($booking->status == 'paid')
                                    @foreach($booking->passengers as $passenger)
                                        <tr>
                                            <td>{{ $stt++ }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $passenger->full_name }}</div>
                                                <div class="small text-muted">{{ $passenger->gender == 'male' ? 'Nam' : ($passenger->gender == 'female' ? 'Nữ' : 'Khác') }} - Sinh: {{ \Carbon\Carbon::parse($passenger->birth_date)->format('d/m/Y') }}</div>
                                            </td>
                                            <td>
                                                @if($passenger->passenger_type == 'adult')
                                                    Người lớn
                                                @elseif($passenger->passenger_type == 'child')
                                                    Trẻ em
                                                @else
                                                    Em bé
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-primary">{{ $booking->booking_code }}</span>
                                            </td>
                                            <td>
                                                {{ $booking->phone }}
                                                <div class="small text-muted">{{ $booking->email }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
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
@endsection
