@extends('layouts.admin')

@section('page-title', 'Thêm Vé Tham Quan')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Có lỗi xảy ra:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Thông tin Vé Tham Quan</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.tickets.store') }}" method="POST" enctype="multipart/form-data" id="ticketForm">
                    @csrf
                    
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
                                               value="{{ old('title') }}">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Điểm đến <span class="text-danger">*</span></label>
                                        <select name="destination_id" class="form-select @error('destination_id') is-invalid @enderror" required>
                                            <option value="">-- Chọn điểm đến --</option>
                                            @foreach($destinations as $dest)
                                                <option value="{{ $dest->id }}" {{ old('destination_id') == $dest->id ? 'selected' : '' }}>
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
                                               placeholder="VD: Vinpearl, Sun World..." value="{{ old('provider_name') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Mô tả chi tiết</label>
                                        <textarea name="description" class="form-control" rows="4" 
                                                  placeholder="Mô tả về vé tham quan...">{{ old('description') }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Chính sách hủy vé</label>
                                        <textarea name="cancellation_policy" class="form-control" rows="3" 
                                                  placeholder="VD: Hoàn tiền 100% nếu hủy trước 24h...">{{ old('cancellation_policy') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Hình ảnh -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>Hình ảnh</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Chọn hình ảnh</label>
                                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                                        <div class="form-text">Ảnh đầu tiên sẽ được đặt làm ảnh chính. Tối đa 2MB/ảnh.</div>
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
                                        <!-- Option template sẽ được thêm vào đây -->
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
                                                   {{ in_array($tour->id, old('tours', [])) ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-admin-primary" id="submitBtn">
                            <i class="bi bi-check-lg me-1"></i> Lưu lại
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    // Namespace để tránh init nhiều lần
    if (window.ticketOptionsInitialized) {
        return;
    }
    
    let optionIndex = 0;

    const optionTemplate = (index) => `
        <div class="option-item border rounded p-3 mb-3" data-index="${index}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">Loại vé #${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
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

    function initTicketOptions() {
        const addOptionBtn = document.getElementById('addOption');
        const optionsContainer = document.getElementById('optionsContainer');
        const ticketForm = document.getElementById('ticketForm');

        if (!addOptionBtn || !optionsContainer || !ticketForm) {
            console.error('Required elements not found');
            return;
        }

        // Đánh dấu đã init
        window.ticketOptionsInitialized = true;

        // Thêm option mới
        addOptionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            optionsContainer.insertAdjacentHTML('beforeend', optionTemplate(optionIndex));
            optionIndex++;
        });

        // Xóa option
        optionsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                const optionItems = document.querySelectorAll('.option-item');
                
                // Không cho xóa nếu chỉ còn 1 option
                if (optionItems.length <= 1) {
                    alert('Phải có ít nhất 1 loại vé!');
                    return;
                }
                
                const optionItem = e.target.closest('.option-item');
                optionItem.remove();
                
                // Cập nhật lại số thứ tự
                document.querySelectorAll('.option-item').forEach((item, idx) => {
                    item.querySelector('h6').textContent = `Loại vé #${idx + 1}`;
                });
            }
        });

        // Validate form trước khi submit
        ticketForm.addEventListener('submit', function(e) {
            const optionItems = document.querySelectorAll('.option-item');
            
            if (optionItems.length === 0) {
                e.preventDefault();
                alert('Vui lòng thêm ít nhất 1 loại vé!');
                return false;
            }
        });

        // Thêm 1 option mặc định nếu chưa có
        if (optionsContainer.children.length === 0) {
            addOptionBtn.click();
        }
    }

    // Khởi tạo ngay lập tức (vì script được load sau DOM)
    initTicketOptions();
})();
</script>
@endpush
