@extends('layouts.guide')

@section('page-title', 'Lịch trình Tour')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Danh sách Lịch trình được phân công</h5>
    </div>
    
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Tour</th>
                        <th>Khởi hành</th>
                        <th>Kết thúc</th>
                        <th>Số khách / Chỗ trống</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $scheduleGuide)
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
                        <tr>
                            <td>#{{ $tourSchedule->id }}</td>
                            <td>
                                <strong>{{ $tour->name }}</strong>
                                <div class="text-muted small mt-1">Mã: {{ $tour->tour_code }}</div>
                                <div class="mt-1">
                                    @if($scheduleGuide->is_backup)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size: 10px; font-weight: 500;">HDV Dự phòng</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 10px; font-weight: 500;">HDV Chính</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($tourSchedule->departure_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($tourSchedule->return_date)->format('d/m/Y') }}</td>
                            <td>
                                {{ $tourSchedule->bookings->sum(fn($b) => $b->adults_count + $b->children_count) }} / {{ $tourSchedule->capacity }}
                            </td>
                            <td>
                                <span class="badge badge-soft-{{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('guide.schedules.show', $tourSchedule->id) }}" class="btn btn-sm btn-admin-primary">
                                    <i class="bi bi-eye"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Bạn chưa được phân công dẫn tour nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($schedules->hasPages())
    <div class="card-footer bg-white border-top p-3">
        {{ $schedules->links() }}
    </div>
    @endif
</div>
@endsection
