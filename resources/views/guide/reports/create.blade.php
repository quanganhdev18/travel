@extends('layouts.guide')

@section('page-title', 'Báo Cáo & Quyết Toán Tour')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Báo Cáo Kết Thúc Tour</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <strong>Tour:</strong> {{ $schedule->tour->title }} <br>
                    <strong>Khởi hành:</strong> {{ $schedule->departure_date->format('d/m/Y') }} - {{ $schedule->return_date->format('d/m/Y') }}
                </div>

                <form action="{{ route('guide.reports.store', $schedule->id) }}" method="POST">
                    @csrf
                    
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">1. Thông tin Báo cáo</h6>
                    <div class="mb-3">
                        <label class="form-label">Số khách thực tế tham gia <span class="text-danger">*</span></label>
                        <input type="number" name="actual_guests" class="form-control" required min="0" value="{{ old('actual_guests') }}">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Ghi chú sự cố (Nếu có)</label>
                        <textarea name="incident_notes" class="form-control" rows="4" placeholder="Khách bỏ đoàn, tai nạn, trễ giờ, phàn nàn...">{{ old('incident_notes') }}</textarea>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">2. Thông tin Quyết toán</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số tiền đã tạm ứng (VND) <span class="text-danger">*</span></label>
                            <input type="number" name="advance_amount" class="form-control" required min="0" value="{{ old('advance_amount', 0) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tổng chi phí thực tế (VND) <span class="text-danger">*</span></label>
                            <input type="number" name="actual_expense" class="form-control" required min="0" value="{{ old('actual_expense', 0) }}">
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle me-2"></i> Kế toán sẽ đối chiếu và yêu cầu bạn nộp lại hóa đơn, chứng từ giấy sau.
                    </div>

                    <div class="text-end">
                        <a href="{{ route('guide.schedules.show', $schedule->id) }}" class="btn btn-light px-4 me-2">Quay lại</a>
                        <button type="submit" class="btn btn-primary px-4" onclick="return confirm('Bạn có chắc chắn muốn nộp báo cáo? Sau khi nộp sẽ không thể sửa.')">
                            <i class="bi bi-send me-1"></i> Nộp Báo Cáo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
