@extends('layouts.master')

@section('title', __('Trung tâm Hỗ trợ') . ' - Travel Wonder')

@section('content')
<style>
    body {
        background: #f8f9fa;
    }
    
    /* Hero Section - Klook Style */
    .help-hero {
        background: linear-gradient(135deg, #ff6b35 0%, #ff8e53 100%);
        padding: 80px 0 100px;
        position: relative;
        overflow: hidden;
    }
    .help-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path fill="%23ffffff" fill-opacity="0.05" d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,48C672,43,768,53,864,64C960,75,1056,85,1152,85.3L1200,85.3L1200,0L1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>') no-repeat bottom;
        background-size: cover;
        opacity: 0.3;
    }
    .help-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 800px;
        margin: 0 auto;
    }
    .help-hero-title {
        font-size: 48px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 20px;
        line-height: 1.2;
    }
    .help-hero-subtitle {
        font-size: 18px;
        color: rgba(255,255,255,0.95);
        margin-bottom: 40px;
    }
    
    /* Search Box - Klook Style */
    .help-search-box {
        position: relative;
        max-width: 650px;
        margin: 0 auto;
    }
    .help-search-input {
        width: 100%;
        padding: 20px 60px 20px 24px;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
    .help-search-input:focus {
        outline: none;
        box-shadow: 0 12px 40px rgba(0,0,0,0.2);
        transform: translateY(-2px);
    }
    .help-search-icon {
        position: absolute;
        right: 24px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 22px;
        pointer-events: none;
    }

    /* Main Content Container */
    .help-container {
        max-width: 1200px;
        margin: -50px auto 80px;
        position: relative;
        z-index: 10;
        padding: 0 15px;
    }

    /* Popular FAQs Section - Klook Style */
    .popular-section {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
    }
    .popular-faq-item {
        display: flex;
        align-items: flex-start;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .popular-faq-item:hover {
        background: #f8f9fa;
        transform: translateX(4px);
    }
    .popular-faq-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6b35 0%, #ff8e53 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        color: #fff;
        font-size: 18px;
    }
    .popular-faq-text {
        flex: 1;
        font-size: 15px;
        font-weight: 500;
        color: #2d3748;
        line-height: 1.6;
        margin: 0;
    }
    .popular-faq-arrow {
        flex-shrink: 0;
        color: #cbd5e0;
        font-size: 18px;
        transition: all 0.2s ease;
    }
    .popular-faq-item:hover .popular-faq-arrow {
        color: #ff6b35;
        transform: translateX(4px);
    }

    /* Categories Grid - Klook Style */
    .categories-section {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 24px;
    }
    .category-card {
        background: #fff;
        border: 2px solid #f0f0f0;
        border-radius: 16px;
        padding: 24px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .category-card:hover {
        border-color: #ff6b35;
        box-shadow: 0 8px 24px rgba(255, 107, 53, 0.15);
        transform: translateY(-4px);
    }
    .category-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #ff6b35 0%, #ff8e53 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        font-size: 28px;
        color: #fff;
    }
    .category-name {
        font-size: 17px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    .category-count {
        font-size: 14px;
        color: #718096;
        font-weight: 500;
    }

    /* FAQ Modal */
    .faq-modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }
    .faq-modal-header {
        background: linear-gradient(135deg, #ff6b35 0%, #ff8e53 100%);
        color: #fff;
        border: none;
        padding: 24px 30px;
    }
    .faq-modal-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }
    .faq-modal-body {
        padding: 30px;
    }
    .faq-answer-text {
        font-size: 15px;
        line-height: 1.8;
        color: #4a5568;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state-icon {
        font-size: 80px;
        color: #e2e8f0;
        margin-bottom: 20px;
    }
    .empty-state-text {
        font-size: 18px;
        color: #718096;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .help-hero {
            padding: 60px 0 80px;
        }
        .help-hero-title {
            font-size: 32px;
        }
        .help-hero-subtitle {
            font-size: 16px;
        }
        .popular-section, .categories-section {
            padding: 24px;
        }
        .section-title {
            font-size: 20px;
        }
        .category-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Hero Section -->
<section class="help-hero">
    <div class="help-hero-content">
        <h1 class="help-hero-title">{{ __('Chúng tôi có thể giúp gì cho bạn?') }}</h1>
        <p class="help-hero-subtitle">{{ __('Tìm câu trả lời nhanh chóng cho những thắc mắc của bạn') }}</p>
        
        <div class="help-search-box">
            <input type="text" 
                   class="help-search-input" 
                   id="helpSearch" 
                   placeholder="{{ __('Tìm kiếm câu hỏi, chủ đề hoặc từ khóa...') }}">
            <i class="bi bi-search help-search-icon"></i>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="help-container">
    
    @if(!$faqs->isEmpty())
        <!-- Popular FAQs -->
        <div class="popular-section">
            <h2 class="section-title">
                <i class="bi bi-star-fill text-warning me-2"></i>{{ __('Câu hỏi phổ biến') }}
            </h2>
            
            @php
                $popularFaqs = $faqs->flatten()->take(5);
            @endphp
            
            @foreach($popularFaqs as $faq)
                <a href="#" class="popular-faq-item" data-faq-id="{{ $faq->id }}" 
                   data-question="{{ $faq->question }}" 
                   data-answer="{{ $faq->answer }}">
                    <div class="popular-faq-icon">
                        <i class="bi bi-question-lg"></i>
                    </div>
                    <p class="popular-faq-text">{{ $faq->question }}</p>
                    <i class="bi bi-chevron-right popular-faq-arrow"></i>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Categories -->
    <div class="categories-section">
        <h2 class="section-title">
            <i class="bi bi-grid-3x3-gap-fill me-2" style="color: #ff6b35;"></i>{{ __('Duyệt theo chủ đề') }}
        </h2>
        
        @if($faqs->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox empty-state-icon"></i>
                <p class="empty-state-text">{{ __('Chưa có câu hỏi nào') }}</p>
            </div>
        @else
            <div class="category-grid">
                @foreach($faqs as $category => $categoryFaqs)
                    <a href="#category-{{ Str::slug($category ?: 'general') }}" 
                       class="category-card" 
                       data-category="{{ $category ?: 'general' }}">
                        <div class="category-icon">
                            @if($category)
                                @if(Str::contains(strtolower($category), ['đặt', 'booking']))
                                    <i class="bi bi-calendar-check-fill"></i>
                                @elseif(Str::contains(strtolower($category), ['thanh toán', 'payment']))
                                    <i class="bi bi-credit-card-fill"></i>
                                @elseif(Str::contains(strtolower($category), ['bảo hiểm', 'insurance']))
                                    <i class="bi bi-shield-fill-check"></i>
                                @elseif(Str::contains(strtolower($category), ['chuẩn bị', 'preparation']))
                                    <i class="bi bi-backpack-fill"></i>
                                @elseif(Str::contains(strtolower($category), ['giá', 'pricing']))
                                    <i class="bi bi-tags-fill"></i>
                                @else
                                    <i class="bi bi-folder-fill"></i>
                                @endif
                            @else
                                <i class="bi bi-list-ul"></i>
                            @endif
                        </div>
                        <div class="category-name">{{ $category ?: __('Chung') }}</div>
                        <div class="category-count">{{ $categoryFaqs->count() }} {{ __('câu hỏi') }}</div>
                    </a>
                @endforeach
            </div>

            <!-- Category Details (Hidden by default, shown when category clicked) -->
            <div id="categoryDetails" style="display: none; margin-top: 40px;">
                @foreach($faqs as $category => $categoryFaqs)
                    <div class="category-detail" 
                         id="category-{{ Str::slug($category ?: 'general') }}"
                         data-category="{{ $category ?: 'general' }}"
                         style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h3 class="section-title mb-0">{{ $category ?: __('Chung') }}</h3>
                            <button class="btn btn-outline-secondary rounded-pill" onclick="hideCategories()">
                                <i class="bi bi-arrow-left me-2"></i>{{ __('Quay lại') }}
                            </button>
                        </div>
                        
                        @foreach($categoryFaqs as $faq)
                            <a href="#" class="popular-faq-item" data-faq-id="{{ $faq->id }}" 
                               data-question="{{ $faq->question }}" 
                               data-answer="{{ $faq->answer }}">
                                <div class="popular-faq-icon">
                                    <i class="bi bi-question-lg"></i>
                                </div>
                                <p class="popular-faq-text">{{ $faq->question }}</p>
                                <i class="bi bi-chevron-right popular-faq-arrow"></i>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<!-- FAQ Modal -->
<div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content faq-modal-content">
            <div class="modal-header faq-modal-header">
                <h5 class="faq-modal-title" id="faqModalLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body faq-modal-body">
                <div class="faq-answer-text" id="faqModalAnswer"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqModal = new bootstrap.Modal(document.getElementById('faqModal'));
    
    // Click on FAQ item to show details
    document.querySelectorAll('.popular-faq-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const question = this.dataset.question;
            const answer = this.dataset.answer;
            
            document.getElementById('faqModalLabel').textContent = question;
            document.getElementById('faqModalAnswer').innerHTML = answer.replace(/\n/g, '<br>');
            faqModal.show();
        });
    });

    // Click on category card
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('href').substring(1);
            
            // Hide category grid and show details
            document.querySelector('.category-grid').style.display = 'none';
            document.getElementById('categoryDetails').style.display = 'block';
            
            // Hide all category details
            document.querySelectorAll('.category-detail').forEach(detail => {
                detail.style.display = 'none';
            });
            
            // Show selected category
            document.getElementById(categoryId).style.display = 'block';
            
            // Scroll to top of categories section
            document.querySelector('.categories-section').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        });
    });

    // Search functionality
    const searchInput = document.getElementById('helpSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            let hasResults = false;
            
            // Remove existing "no results" message first
            const existingNoResults = document.getElementById('noResults');
            if (existingNoResults) {
                existingNoResults.remove();
            }
            
            // Search in popular FAQs
            document.querySelectorAll('.popular-faq-item').forEach(item => {
                const question = item.dataset.question?.toLowerCase() || '';
                const answer = item.dataset.answer?.toLowerCase() || '';
                const matches = question.includes(searchTerm) || answer.includes(searchTerm);
                
                item.style.display = matches || !searchTerm ? 'flex' : 'none';
                if (matches) hasResults = true;
            });

            // Filter categories
            document.querySelectorAll('.category-card').forEach(card => {
                const categoryName = card.querySelector('.category-name').textContent.toLowerCase();
                const matches = categoryName.includes(searchTerm);
                card.style.display = matches || !searchTerm ? 'flex' : 'none';
                if (matches) hasResults = true;
            });

            // Show "no results" message only once if no matches found
            if (searchTerm && !hasResults) {
                const popularSection = document.querySelector('.popular-section');
                if (popularSection && !document.getElementById('noResults')) {
                    popularSection.insertAdjacentHTML('beforeend', 
                        '<div class="text-center py-4 text-muted" id="noResults"><i class="bi bi-search fs-3 d-block mb-2"></i>Không tìm thấy kết quả</div>');
                }
            }
        });
    }
});

function hideCategories() {
    document.querySelector('.category-grid').style.display = 'grid';
    document.getElementById('categoryDetails').style.display = 'none';
    
    // Scroll to categories section
    document.querySelector('.categories-section').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}
</script>
@endpush

@endsection
