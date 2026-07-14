@extends('layouts.guide')

@section('page-title', 'Lịch trình Tour')

@section('content')
<style>
    @media (max-width: 767px) {
        /* Chuyển bảng sang dạng các thẻ card */
        .table-responsive {
            border: none;
        }
        .table {
            border: none;
        }
        .table thead {
            display: none;
        }
        .table tbody {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 12px 4px;
        }
        .table tbody tr {
            display: block;
            background: #fff;
            border: 1px solid var(--admin-border) !important;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            padding: 16px;
            margin-bottom: 0;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        .table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #f1f5f9;
            font-size: 0.85rem;
            text-align: right;
        }
        .table td:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .table td:first-child {
            padding-top: 0;
        }
        .table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--admin-text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            margin-right: auto;
            text-align: left;
        }
        .table td > * {
            text-align: right;
            margin: 0 !important;
        }
        .table td.text-end {
            text-align: right;
            justify-content: space-between;
        }
    }
</style>
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
                            
                            $departureDateTime = \Carbon\Carbon::parse($tourSchedule->departure_date->toDateString());
                            if ($tour && $tour->departure_time) {
                                $timeParts = explode(':', $tour->departure_time);
                                $hour = isset($timeParts[0]) ? (int) $timeParts[0] : 0;
                                $minute = isset($timeParts[1]) ? (int) $timeParts[1] : 0;
                                $second = isset($timeParts[2]) ? (int) $timeParts[2] : 0;
                                $departureDateTime->setTime($hour, $minute, $second);
                            }
                            
                            $statusClass = 'secondary';
                            $statusText = 'Chưa xác định';
                            
                            if ($departureDateTime > now()) {
                                $statusClass = 'primary';
                                $statusText = 'Sắp tới';
                            } elseif ($departureDateTime <= now() && \Carbon\Carbon::parse($tourSchedule->return_date) >= now()) {
                                $statusClass = 'success';
                                $statusText = 'Đang diễn ra';
                            } else {
                                $statusClass = 'secondary';
                                $statusText = 'Đã kết thúc';
                            }
                        @endphp
                        <tr>
                            <td data-label="ID">#{{ $tourSchedule->id }}</td>
                            <td data-label="Tên Tour">
                                <strong class="text-md-start d-block">{{ $tour->name }}</strong>
                                <div class="text-muted small mt-1 text-md-start">Mã: {{ $tour->tour_code }}</div>
                            </td>
                            <td data-label="Khởi hành">{{ \Carbon\Carbon::parse($tourSchedule->departure_date)->format('d/m/Y') }}</td>
                            <td data-label="Kết thúc">{{ \Carbon\Carbon::parse($tourSchedule->return_date)->format('d/m/Y') }}</td>
                            <td data-label="Số khách">
                                {{ $tourSchedule->bookings->sum(fn($b) => $b->adults_count + $b->children_count) }} / {{ $tourSchedule->capacity }}
                            </td>
                            <td data-label="Trạng thái">
                                <span class="badge badge-soft-{{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td data-label="Hành động" class="text-end">
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
