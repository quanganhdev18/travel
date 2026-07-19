@extends('layouts.admin')

@section('page-title', 'Chi tiết Quyết Toán Tour')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="admin-card border-0 mb-4">
            <div class="admin-card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0">Chi tiết Báo cáo #{{ $report->id }}</h5>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-light border">Quay lại</a>
            </div>
            <div class="admin-card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">Tour:</p>
                        <h6 class="fw-bold">{{ $report->tour_schedule->tour->title }}</h6>
                        <p class="mb-1"><strong>Mã Tour:</strong> {{ $report->tour_schedule->tour->tour_code }}</p>
                        <p class="mb-0"><strong>Thời gian:</strong> {{ $report->tour_schedule->departure_date->format('d/m/Y') }} - {{ $report->tour_schedule->return_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">Hướng dẫn viên:</p>
                        <h6 class="fw-bold">{{ $report->tour_guide->name }}</h6>
                        <p class="mb-1"><strong>SĐT:</strong> {{ $report->tour_guide->phone }}</p>
                        <p class="mb-0"><strong>Khách thực tế:</strong> {{ $report->actual_guests }} / {{ $report->tour_schedule->capacity }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="mb-1 text-muted small">Ghi chú sự cố:</p>
                    <div class="p-3 bg-light rounded border">
                        {!! nl2br(e($report->incident_notes ?? 'Không có ghi chú.')) !!}
                    </div>
                </div>

                <h6 class="fw-bold border-bottom pb-2 mb-3">Quyết Toán Tài Chính</h6>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td class="bg-light w-50">1. Số tiền đã tạm ứng</td>
                            <td class="text-end fw-bold">{{ number_format($report->advance_amount) }} đ</td>
                        </tr>
                        <tr>
                            <td class="bg-light">2. Tổng chi phí thực tế</td>
                            <td class="text-end fw-bold">{{ number_format($report->actual_expense) }} đ</td>
                        </tr>
                        <tr class="table-warning">
                            <td class="fw-bold">3. Hoàn ứng (Tạm ứng - Chi phí)</td>
                            <td class="text-end fw-bold fs-5 {{ $report->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($report->balance) }} đ
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-muted small mb-4">
                    * Nếu số tiền &gt; 0: HDV phải nộp lại cho công ty.<br>
                    * Nếu số tiền &lt; 0: Công ty phải bù thêm cho HDV.
                </div>

                @if($report->status === 'pending')
                    <form action="{{ route('admin.reports.approve', $report->id) }}" method="POST" class="text-end mt-4">
                        @csrf
                        <button type="submit" class="btn btn-success px-4" onclick="return confirm('Xác nhận đã đối chiếu hóa đơn chứng từ và duyệt quyết toán này?')">
                            <i class="bi bi-check-circle me-1"></i> Duyệt Báo Cáo & Khóa Tour
                        </button>
                    </form>
                @else
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle-fill me-2"></i> Báo cáo này đã được duyệt.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
