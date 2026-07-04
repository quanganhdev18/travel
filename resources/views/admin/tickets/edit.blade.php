@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Vé Tham Quan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Chỉnh sửa: {{ $ticket->title }}</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data" id="ticketForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Thông tin cơ bản -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin cơ bản</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tên vé tham quan <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                               required placeholder="VD: Vé vào cửa Vinpearl Land Nha Trang" 
                                               value="{{ old('title', $ticket->title) }}">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Điểm đến <span class="text-danger">*</span></label>
                                        <select name="destination_id" class="form-select @error('destination_id') is-invalid @enderror" required>
                                            <option value="">-- Chọn điểm đến --</option>
                                            @foreach($destinations as $dest)
                                                <option value="{{ $dest->id }}" 
                                                    {{ old('destination_id', $ticket->destination_id) == $dest->id ? 'selected' : '' }}>
                                                    {{ $dest->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('destination_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nhà cung cấp</label>
                                        <input type="text" name="provider_name" class="form-control" 
                                               placeholder="VD: Vinpearl, Sun World..." 
                                               value="{{ old('provider_name', $ticket->provider_name) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Mô tả chi tiết</label>
                                        <textarea name="description" class="form-control" rows="4" 
                                                  placeholder="Mô tả về vé tham quan...">{{ old('description', $ticket->description) }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Chính sách hủy vé</label>
                                        <textarea name="cancellation_policy" class="form-control" rows="3" 
                                                  placeholder="VD: Hoàn tiền 100% nếu hủy trước 24h...">{{ old('cancellation_policy', $ticket->cancellation_policy) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Hình ảnh -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>Hình ảnh</h6>
                                </div>
                                <div class="card-body">
                                    @if($ticket->ticket_images->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Hình ảnh hiện tại</label>
                                        <div class="row g-2">
                                            @foreach($ticket->ticket_images as $image)
                                            <div class="col-md-3 position-relative image-item" data-id="{{ $image->id }}">
                                                <img src="{{ asset($image->image_url) }}" class="img-fluid rounded" 
                                                     style="width: 100%; height: 150px; object-fit: cover;">
                                                <div class="position-absolute top-0 end-0 p-1">
                                                    <button type="button" class="btn btn-sm btn-danger delete-image" 
                                                            data-id="{{ $image->id }}" data-ticket-id="{{ $ticket->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                                @if($image->is_primary)
                                                <div class="position-absolute top-0 start-0 p-1">
                                                    <span class="badge bg-primary">Ảnh chính</span>
                                                </div>
                                                @else
                                                <div class="position-absolute bottom-0 start-0 p-1 w-100 text-center">
                                                    <button type="button" class="btn btn-sm btn-light btn-sm set-primary" 
                                                            data-id="{{ $image->id }}" data-ticket-id="{{ $ticket->id }}">
                                                        Đặt làm ảnh chính
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Thêm hình ảnh mới</label>
                                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                                        <div class="form-text">Tối đa 2MB/ảnh.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Loại vé / Options -->
                            <div class="card mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Loại vé</h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="addOption">
                                        <i class="bi bi-plus-lg"></i> Thêm loại vé
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="optionsContainer">
                                        @foreach($ticket->ticket_options as $index => $option)
                                        <div class="option-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">Loại vé #{{ $index + 1 }}</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <input type="hidden" name="option_ids[]" value="{{ $option->id }}">
                                            
                                            <div class="mb-2">
                                                <label class="form-label fw-bold small">Tên loại vé <span class="text-danger">*</span></label>
                                                <input type="text" name="option_names[]" class="form-control form-control-sm" 
                                                       required placeholder="VD: Vé người lớn, Vé trẻ em..." 
                                                       value="{{ old('option_names.'.$index, $option->name) }}">
                                            </div>
                                            
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small">Giá bán <span class="text-danger">*</span></label>
                                                    <input type="number" name="option_prices[]" class="form-control form-control-sm" 
                                                           required min="0" placeholder="500000" 
                                                           value="{{ old('option_prices.'.$index, $option->price) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small">Giá gốc (nếu có)</label>
                                                    <input type="number" name="option_original_prices[]" class="form-control form-control-sm" 
                                                           min="0" placeholder="700000" 
                                                           value="{{ old('option_original_prices.'.$index, $option->original_price) }}">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <label class="form-label fw-bold small">Mô tả</label>
                                                <textarea name="option_descriptions[]" class="form-control form-control-sm" rows="2" 
                                                          placeholder="Mô tả loại vé...">{{ old('option_descriptions.'.$index, $option->description) }}</textarea>
                                            </div>
                                            
                                            <div class="mb-0">
                                                <label class="form-label fw-bold small">Điều kiện áp dụng</label>
                                                <textarea name="option_conditions[]" class="form-control form-control-sm" rows="2" 
                                                          placeholder="VD: Áp dụng cho trẻ em từ 1m-1m4...">{{ old('option_conditions.'.$index, $option->conditions) }}</textarea>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Liên kết Tour -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Liên kết Tour</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-text mb-2">Chọn các Tour được phép hiển thị vé này.</div>
                                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                                        @forelse($tours as $tour)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tours[]" 
                                                   value="{{ $tour->id }}" id="tour_{{ $tour->id }}"
                                                   {{ in_array($tour->id, old('tours', $selectedTours)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tour_{{ $tour->id }}">
                                                {{ $tour->title }}
                                            </label>
                                        </div>
                                        @empty
                                        <div class="text-muted small">Chưa có Tour nào trong hệ thống.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-light border">Hủy</a>
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="bi bi-check-lg me-1"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let optionIndex = {{ $ticket->ticket_options->count() }};

const optionTemplate = (index) => `
    <div class="option-item border rounded p-3 mb-3" data-index="${index}">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0">Loại vé #${index + 1}</h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        
        <input type="hidden" name="option_ids[]" value="">
        
        <div class="mb-2">
            <label class="form-label fw-bold small">Tên loại vé <span class="text-danger">*</span></label>
            <input type="text" name="option_names[]" class="form-control form-control-sm" 
                   required placeholder="VD: Vé người lớn, Vé trẻ em...">
        </div>
        
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label fw-bold small">Giá bán <span class="text-danger">*</span></label>
                <input type="number" name="option_prices[]" class="form-control form-control-sm" 
                       required min="0" placeholder="500000">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Giá gốc (nếu có)</label>
                <input type="number" name="option_original_prices[]" class="form-control form-control-sm" 
                       min="0" placeholder="700000">
            </div>
        </div>
        
        <div class="mb-2">
            <label class="form-label fw-bold small">Mô tả</label>
            <textarea name="option_descriptions[]" class="form-control form-control-sm" rows="2" 
                      placeholder="Mô tả loại vé..."></textarea>
        </div>
        
        <div class="mb-0">
            <label class="form-label fw-bold small">Điều kiện áp dụng</label>
            <textarea name="option_conditions[]" class="form-control form-control-sm" rows="2" 
                      placeholder="VD: Áp dụng cho trẻ em từ 1m-1m4..."></textarea>
        </div>
    </div>
`;

document.getElementById('addOption').addEventListener('click', function() {
    const container = document.getElementById('optionsContainer');
    container.insertAdjacentHTML('beforeend', optionTemplate(optionIndex));
    optionIndex++;
});

// Xóa option
document.getElementById('optionsContainer').addEventListener('click', function(e) {
    if (e.target.closest('.remove-option')) {
        const optionItem = e.target.closest('.option-item');
        optionItem.remove();
        
        // Cập nhật lại số thứ tự
        document.querySelectorAll('.option-item').forEach((item, idx) => {
            item.querySelector('h6').textContent = `Loại vé #${idx + 1}`;
        });
    }
});

// Xóa ảnh
document.querySelectorAll('.delete-image').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Bạn có chắc chắn muốn xóa ảnh này?')) return;
        
        const imageId = this.dataset.id;
        const ticketId = this.dataset.ticketId;
        const imageItem = this.closest('.image-item');
        
        fetch(`/admin/tickets/${ticketId}/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                imageItem.remove();
                alert('Đã xóa ảnh thành công.');
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi xóa ảnh.');
        });
    });
});

// Đặt ảnh chính
document.querySelectorAll('.set-primary').forEach(btn => {
    btn.addEventListener('click', function() {
        const imageId = this.dataset.id;
        const ticketId = this.dataset.ticketId;
        
        fetch(`/admin/tickets/${ticketId}/images/${imageId}/set-primary`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra.');
        });
    });
});
</script>
@endpush
@endsection
