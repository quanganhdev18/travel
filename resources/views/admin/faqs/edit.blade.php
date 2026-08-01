@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa FAQ')

@section('content')
<div class="row">
    <div class="col-lg-10">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title mb-0">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa Câu hỏi Thường gặp
                </h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">Câu hỏi <span class="text-danger">*</span></label>
                        
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="width: 100px;">
                                    <i class="bi bi-translate me-2"></i>Tiếng Việt
                                </span>
                                <input type="text" class="form-control border-start-0 @error('question.vi') is-invalid @enderror" 
                                    name="question[vi]" value="{{ old('question.vi', $faq->getTranslation('question', 'vi')) }}" 
                                    placeholder="Nhập câu hỏi..." required>
                            </div>
                            @error('question.vi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="width: 100px;">
                                    <i class="bi bi-translate me-2"></i>English
                                </span>
                                <input type="text" class="form-control border-start-0 @error('question.en') is-invalid @enderror" 
                                    name="question[en]" value="{{ old('question.en', $faq->getTranslation('question', 'en')) }}" 
                                    placeholder="Enter question...">
                            </div>
                            @error('question.en') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="width: 100px;">
                                    <i class="bi bi-translate me-2"></i>中文
                                </span>
                                <input type="text" class="form-control border-start-0 @error('question.zh') is-invalid @enderror" 
                                    name="question[zh]" value="{{ old('question.zh', $faq->getTranslation('question', 'zh')) }}" 
                                    placeholder="输入问题...">
                            </div>
                            @error('question.zh') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Câu trả lời <span class="text-danger">*</span></label>
                        
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">
                                <i class="bi bi-flag me-1"></i>Tiếng Việt
                            </label>
                            <textarea class="form-control @error('answer.vi') is-invalid @enderror" 
                                name="answer[vi]" rows="5" placeholder="Nhập câu trả lời..." required>{{ old('answer.vi', $faq->getTranslation('answer', 'vi')) }}</textarea>
                            @error('answer.vi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">
                                <i class="bi bi-flag me-1"></i>English
                            </label>
                            <textarea class="form-control @error('answer.en') is-invalid @enderror" 
                                name="answer[en]" rows="5" placeholder="Enter answer...">{{ old('answer.en', $faq->getTranslation('answer', 'en')) }}</textarea>
                            @error('answer.en') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">
                                <i class="bi bi-flag me-1"></i>中文
                            </label>
                            <textarea class="form-control @error('answer.zh') is-invalid @enderror" 
                                name="answer[zh]" rows="5" placeholder="输入答案...">{{ old('answer.zh', $faq->getTranslation('answer', 'zh')) }}</textarea>
                            @error('answer.zh') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-folder me-1"></i>Danh mục
                            </label>
                            
                            <div class="mb-2">
                                <input type="text" class="form-control @error('category.vi') is-invalid @enderror" 
                                    name="category[vi]" value="{{ old('category.vi', $faq->category ? $faq->getTranslation('category', 'vi') : '') }}" 
                                    placeholder="VD: Thanh toán">
                                @error('category.vi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-2">
                                <input type="text" class="form-control @error('category.en') is-invalid @enderror" 
                                    name="category[en]" value="{{ old('category.en', $faq->category ? $faq->getTranslation('category', 'en') : '') }}" 
                                    placeholder="E.g: Payment">
                                @error('category.en') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-2">
                                <input type="text" class="form-control @error('category.zh') is-invalid @enderror" 
                                    name="category[zh]" value="{{ old('category.zh', $faq->category ? $faq->getTranslation('category', 'zh') : '') }}" 
                                    placeholder="例如：支付">
                                @error('category.zh') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-sort-numeric-down me-1"></i>Thứ tự hiển thị
                            </label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                name="order" value="{{ old('order', $faq->order) }}" min="0">
                            @error('order') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div class="form-text">Số nhỏ hiển thị trước</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-toggle-on me-1"></i>Trạng thái
                            </label>
                            <div class="form-check form-switch mt-2 pt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                    id="is_active" {{ old('is_active', $faq->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Hiển thị trên trang web
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('admin.faqs.index') }}" class="btn btn-light border">
                            <i class="bi bi-x-lg me-2"></i>Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="bi bi-check-lg me-2"></i>Cập nhật FAQ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
