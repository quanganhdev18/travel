@extends('layouts.guide')

@section('page-title', 'Báo Cáo Tour')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Báo Cáo Sự Cố Tour</h5>
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
                        <label class="form-label">Số khách thực tế tham gia (Tối đa: {{ $schedule->capacity }}) <span class="text-danger">*</span></label>
                        <input type="number" name="actual_guests" class="form-control @error('actual_guests') is-invalid @enderror" required min="0" max="{{ $schedule->capacity }}" value="{{ old('actual_guests', $schedule->capacity) }}">
                        @error('actual_guests')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Ghi chú sự cố (Nếu có)</label>
                        <textarea name="incident_notes" class="form-control" rows="4" placeholder="Khách bỏ đoàn, tai nạn, trễ giờ, phàn nàn...">{{ old('incident_notes') }}</textarea>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 mt-4">2. Danh sách Khách Tách Đoàn</h6>
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-info-circle me-2"></i> Danh sách dưới đây được hệ thống tự động trích xuất dựa trên thông tin điểm danh của bạn.
                    </div>
                    
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
                        <div class="p-4 text-center border rounded mb-4 bg-light text-muted">
                            <i class="bi bi-check-circle fs-2 text-success d-block mb-2"></i>
                            Tuyệt vời! Không có hành khách nào tách đoàn trong chuyến đi này.
                        </div>
                    @endif

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
