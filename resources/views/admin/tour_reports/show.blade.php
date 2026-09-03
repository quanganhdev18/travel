@extends('layouts.admin')

@section('page-title', 'Chi tiết Báo cáo Tour')

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
                        <p class="mb-1"><strong>Mã Tour:</strong> {{ $report->tour_schedule->tour->code }}</p>
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

                <h6 class="fw-bold border-bottom pb-2 mb-3 mt-4">Danh sách Khách Tách Đoàn</h6>
                @if(isset($freeTimePassengers) && count($freeTimePassengers) > 0)
                    <div class="table-responsive border rounded mb-4">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">#</th>
                                    <th scope="col">Tên khách hàng</th>
                                    <th scope="col">Loại vé</th>
                                    <th scope="col">Địa điểm tách</th>
                                    <th scope="col">Thời gian tách</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($freeTimePassengers as $index => $passenger)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td class="fw-semibold text-dark">{{ $passenger->full_name }}</td>
                                        <td>
                                            @if($passenger->passenger_type == 'adult')
                                                <span class="badge bg-primary bg-opacity-10 text-primary">Người lớn</span>
                                            @elseif($passenger->passenger_type == 'child')
                                                <span class="badge bg-warning bg-opacity-10 text-warning">Trẻ em</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Em bé</span>
                                            @endif
                                        </td>
                                        <td>{{ $passenger->free_time_location ?? 'Không ghi rõ' }}</td>
                                        <td class="text-muted text-sm">
                                            {{ $passenger->free_time_start ? \Carbon\Carbon::parse($passenger->free_time_start)->format('H:i d/m/Y') : '—' }} 
                                            <i class="bi bi-arrow-right mx-1"></i> 
                                            {{ $passenger->free_time_end ? \Carbon\Carbon::parse($passenger->free_time_end)->format('H:i d/m/Y') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center mb-4">
                        <i class="bi bi-info-circle me-1"></i> Không có khách nào tách đoàn trong chuyến đi này.
                    </div>
                @endif

                @if($report->status === 'pending')
                    <div class="d-flex justify-content-end gap-2 mt-4" x-data="{ showRejectForm: false }">
                        <button type="button" @click="showRejectForm = !showRejectForm" class="btn btn-danger px-4">
                            <i class="bi bi-x-circle me-1"></i> Từ chối Báo cáo
                        </button>
                        
                        <form action="{{ route('admin.reports.approve', $report->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success px-4" onclick="return confirm('Xác nhận đã xem báo cáo và duyệt đóng tour này?')">
                                <i class="bi bi-check-circle me-1"></i> Duyệt Báo Cáo & Khóa Tour
                            </button>
                        </form>
                    </div>

                    <div x-show="showRejectForm" class="mt-3 p-3 border rounded bg-light text-start" x-cloak x-transition>
                        <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="reject_reason" class="form-label fw-bold">Lý do từ chối <span class="text-danger">*</span></label>
                                <textarea name="reject_reason" id="reject_reason" rows="3" class="form-control" placeholder="Nhập lý do từ chối..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" @click="showRejectForm = false" class="btn btn-sm btn-secondary me-2">Hủy</button>
                                <button type="submit" class="btn btn-sm btn-danger">Xác nhận Từ chối</button>
                            </div>
                        </form>
                    </div>
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
