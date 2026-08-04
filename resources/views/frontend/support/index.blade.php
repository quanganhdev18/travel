@extends('layouts.master')

@section('title', 'Trung tâm Hỗ trợ - Travel Wonder')

@section('content')

<!-- Hero Section -->
<section class="support-hero">
    <div class="support-hero-bg">
        <img src="https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?q=80&w=2072" alt="Support Background">
    </div>
    <div class="support-hero-overlay"></div>
    <div class="container position-relative">
        <div class="support-hero-content text-center text-white">
            <h1 class="display-4 fw-bold mb-3">Trung tâm Hỗ trợ</h1>
            <p class="lead mb-4">Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7</p>
            <div class="search-box mx-auto">
                <i class="bi bi-search"></i>
                <input type="text" 
                       id="faqSearch" 
                       class="form-control" 
                       placeholder="Tìm kiếm câu hỏi hoặc từ khóa...">
            </div>
        </div>
    </div>
</section>

<!-- Quick Contact Section -->
<section class="container py-4">
    <div class="row g-4">
        <div class="col-md-3 col-6">
            <a href="tel:{{ $contactInfo['hotline'] }}" class="quick-contact-card text-decoration-none">
                <div class="icon-wrapper bg-primary">
                    <i class="bi bi-telephone-fill text-white"></i>
                </div>
                <h6 class="mt-3 mb-1">Hotline</h6>
                <p class="text-muted small mb-0">{{ $contactInfo['hotline'] }}</p>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="mailto:{{ $contactInfo['email'] }}" class="quick-contact-card text-decoration-none">
                <div class="icon-wrapper bg-success">
                    <i class="bi bi-envelope-fill text-white"></i>
                </div>
                <h6 class="mt-3 mb-1">Email</h6>
                <p class="text-muted small mb-0">support@travelwonder.vn</p>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ $contactInfo['facebook'] }}" target="_blank" class="quick-contact-card text-decoration-none">
                <div class="icon-wrapper bg-info">
                    <i class="bi bi-facebook text-white"></i>
                </div>
                <h6 class="mt-3 mb-1">Facebook</h6>
                <p class="text-muted small mb-0">Chat trực tuyến</p>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ $contactInfo['zalo'] }}" target="_blank" class="quick-contact-card text-decoration-none">
                <div class="icon-wrapper bg-warning">
                    <i class="bi bi-chat-dots-fill text-white"></i>
                </div>
                <h6 class="mt-3 mb-1">Zalo</h6>
                <p class="text-muted small mb-0">Hỗ trợ nhanh</p>
            </a>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Câu hỏi thường gặp</h2>
        <p class="text-muted">Tìm câu trả lời nhanh chóng cho các thắc mắc phổ biến</p>
    </div>

    <div class="row">
        @foreach($faqs as $index => $category)
        <div class="col-lg-6 mb-4">
            <div class="faq-category-card h-100">
                <div class="faq-category-header">
                    <div class="d-flex align-items-center">
                        <div class="category-icon-wrapper">
                            <i class="bi {{ $category['icon'] }}"></i>
                        </div>
                        <h4 class="mb-0">{{ $category['category'] }}</h4>
                    </div>
                </div>
                <div class="faq-category-body">
                    <div class="accordion" id="accordion{{ $index }}">
                        @foreach($category['questions'] as $qIndex => $item)
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $qIndex === 0 ? '' : 'collapsed' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $index }}_{{ $qIndex }}">
                                    <i class="bi bi-question-circle me-2 text-primary"></i>
                                    {{ $item['question'] }}
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}_{{ $qIndex }}" 
                                 class="accordion-collapse collapse {{ $qIndex === 0 ? 'show' : '' }}" 
                                 data-bs-parent="#accordion{{ $index }}">
                                <div class="accordion-body">
                                    {{ $item['answer'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-4">Không tìm thấy câu trả lời?</h2>
                <p class="text-muted mb-4">Đội ngũ hỗ trợ của chúng tôi luôn sẵn sàng giúp đỡ bạn. Liên hệ ngay để được tư vấn chi tiết!</p>
                
                <div class="contact-info-list">
                    <div class="contact-info-item">
                        <i class="bi bi-geo-alt-fill text-primary"></i>
                        <div>
                            <strong>Địa chỉ văn phòng</strong>
                            <p class="mb-0 text-muted">{{ $contactInfo['address'] }}</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="bi bi-clock-fill text-primary"></i>
                        <div>
                            <strong>Giờ làm việc</strong>
                            <p class="mb-0 text-muted">{{ $contactInfo['working_hours'] }}</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="bi bi-telephone-fill text-danger"></i>
                        <div>
                            <strong>Hotline khẩn cấp (24/7)</strong>
                            <p class="mb-0 text-muted">{{ $contactInfo['emergency'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-form-card">
                    <h4 class="mb-4">Gửi câu hỏi cho chúng tôi</h4>
                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" placeholder="Nhập họ tên của bạn" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="example@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chủ đề</label>
                            <select class="form-select" required>
                                <option value="">Chọn chủ đề</option>
                                <option value="booking">Đặt tour</option>
                                <option value="payment">Thanh toán</option>
                                <option value="service">Dịch vụ tour</option>
                                <option value="account">Tài khoản</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Nội dung</label>
                            <textarea class="form-control" rows="4" placeholder="Mô tả chi tiết câu hỏi của bạn..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3">
                            <i class="bi bi-send me-2"></i>Gửi câu hỏi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Hero Section */
.support-hero {
    position: relative;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.support-hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.support-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.support-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 124, 232, 0.9), rgba(0, 86, 179, 0.8));
    z-index: 1;
}

.support-hero-content {
    position: relative;
    z-index: 2;
}

.search-box {
    max-width: 600px;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 1.2rem;
    z-index: 3;
}

.search-box input {
    padding: 16px 20px 16px 50px;
    border-radius: 50px;
    border: none;
    font-size: 1rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.search-box input:focus {
    outline: none;
    box-shadow: 0 8px 30px rgba(0, 124, 232, 0.3);
}

/* Quick Contact Cards */
.quick-contact-card {
    display: block;
    text-align: center;
    padding: 2rem 1rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.quick-contact-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 24px rgba(0, 124, 232, 0.15);
}

.quick-contact-card .icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 1.5rem;
}

.quick-contact-card h6 {
    color: #1f2937;
    font-weight: 600;
}

.quick-contact-card p {
    font-size: 0.875rem;
}

/* FAQ Category Cards */
.faq-category-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-category-card:hover {
    box-shadow: 0 8px 28px rgba(0, 124, 232, 0.12);
}

.faq-category-header {
    background: linear-gradient(135deg, #007CE8, #0056b3);
    padding: 1.5rem;
    color: white;
}

.category-icon-wrapper {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.5rem;
}

.faq-category-body {
    padding: 1.5rem;
}

.faq-item {
    border: none;
    margin-bottom: 0.75rem;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.faq-item .accordion-button {
    background: white;
    color: #1f2937;
    font-weight: 500;
    padding: 1.25rem;
    border: none;
    box-shadow: none;
}

.faq-item .accordion-button:not(.collapsed) {
    background: #f8f9fa;
    color: #007CE8;
}

.faq-item .accordion-button:focus {
    box-shadow: none;
    border: none;
}

.faq-item .accordion-body {
    padding: 1.25rem;
    background: #f8f9fa;
    color: #4b5563;
    line-height: 1.7;
}

/* Contact Section */
.contact-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

.contact-info-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.contact-info-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.contact-info-item i {
    font-size: 1.5rem;
    margin-top: 0.25rem;
}

.contact-info-item strong {
    display: block;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.contact-form-card {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.contact-form-card h4 {
    color: #1f2937;
    font-weight: 700;
}

.contact-form-card .form-label {
    font-weight: 500;
    color: #4b5563;
    margin-bottom: 0.5rem;
}

.contact-form-card .form-control,
.contact-form-card .form-select {
    padding: 0.75rem 1rem;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.contact-form-card .form-control:focus,
.contact-form-card .form-select:focus {
    border-color: #007CE8;
    box-shadow: 0 0 0 3px rgba(0, 124, 232, 0.1);
}

.contact-form-card .btn-primary {
    background: linear-gradient(135deg, #007CE8, #0056b3);
    border: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.contact-form-card .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 124, 232, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .support-hero {
        height: 300px;
    }
    
    .support-hero-content h1 {
        font-size: 2rem;
    }
    
    .quick-contact-card {
        padding: 1.5rem 0.75rem;
    }
    
    .quick-contact-card .icon-wrapper {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .contact-form-card {
        padding: 1.5rem;
    }
}

/* No Results Message */
.no-results {
    display: none;
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 16px;
    margin-top: 2rem;
}

.no-results.active {
    display: block;
}
</style>

<script>
// FAQ Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faqSearch');
    const faqItems = document.querySelectorAll('.faq-item');
    const categories = document.querySelectorAll('.faq-category-card');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all items
            faqItems.forEach(item => {
                item.style.display = 'block';
            });
            categories.forEach(cat => {
                cat.style.display = 'block';
            });
            return;
        }
        
        let hasResults = false;
        
        categories.forEach(category => {
            const itemsInCategory = category.querySelectorAll('.faq-item');
            let categoryHasMatch = false;
            
            itemsInCategory.forEach(item => {
                const question = item.querySelector('.accordion-button').textContent.toLowerCase();
                const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    categoryHasMatch = true;
                    hasResults = true;
                    
                    // Auto-expand matching items
                    const collapseElement = item.querySelector('.accordion-collapse');
                    if (collapseElement && !collapseElement.classList.contains('show')) {
                        const button = item.querySelector('.accordion-button');
                        button.click();
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            category.style.display = categoryHasMatch ? 'block' : 'none';
        });
        
        // Show "no results" message if needed
        if (!hasResults) {
            // You can add a "no results" element here
            console.log('No results found');
        }
    });
});
</script>

@endsection
