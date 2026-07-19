@extends('layouts.admin')

@section('page-title', 'Báo cáo & Quyết toán Tour')

@section('content')
<div class="admin-card border-0 mb-4">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Danh sách Báo Cáo Tour</h5>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Mã Tour</th>
                        <th>Hướng dẫn viên</th>
                        <th>Khách thực tế</th>
                        <th>Hoàn ứng</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $report->tour_schedule->tour->tour_code }}</div>
                            <div class="small text-muted">{{ $report->tour_schedule->departure_date->format('d/m/Y') }}</div>
                        </td>
                        <td>{{ $report->tour_guide->name }}</td>
                        <td>{{ $report->actual_guests }}</td>
                        <td>
                            @if($report->balance > 0)
                                <span class="text-success fw-bold">+{{ number_format($report->balance) }} đ</span>
                            @elseif($report->balance < 0)
                                <span class="text-danger fw-bold">{{ number_format($report->balance) }} đ</span>
                            @else
                                <span class="text-muted">0 đ</span>
                            @endif
                        </td>
                        <td>
                            @if($report->status === 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @else
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Chưa có báo cáo nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-body border-top py-3">
        {{ $reports->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
