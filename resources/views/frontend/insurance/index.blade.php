@extends('layouts.master')

@section('title', 'Bảo hiểm du lịch - Travel Wonder')
@section('meta_description', 'Khi đặt tour tại Travel Wonder, bảo hiểm du lịch được tự động áp dụng cho khách hàng mà không cần đăng ký hay thanh toán thêm.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════════════════════════════ --}}
<section class="ins-hero">
    <div class="ins-hero__bg-orb ins-hero__bg-orb--1"></div>
    <div class="ins-hero__bg-orb ins-hero__bg-orb--2"></div>
    <div class="container position-relative" style="z-index:2;">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <span class="ins-badge mb-3">
                    <i class="bi bi-shield-check me-2"></i>Bảo vệ tự động – Không cần đăng ký thêm
                </span>
                <h1 class="ins-hero__title">Bảo hiểm du lịch</h1>
                <p class="ins-hero__subtitle">An tâm khám phá – Bảo vệ trọn hành trình</p>
                <p class="ins-hero__desc">
                    Khi đặt tour trên website, bảo hiểm du lịch được <strong>tự động áp dụng</strong>
                    cho khách hàng mà không cần đăng ký hoặc thanh toán thêm.
                </p>
                <div class="ins-hero__pill mt-4">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span>Bảo hiểm được áp dụng tự động khi đặt tour.</span>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div class="ins-hero__visual">
                    <div class="ins-hero__shield">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="ins-hero__float ins-hero__float--1">
                        <i class="bi bi-check-circle-fill"></i> Tự động
                    </div>
                    <div class="ins-hero__float ins-hero__float--2">
                        <i class="bi bi-heart-pulse-fill"></i> 24/7
                    </div>
                    <div class="ins-hero__float ins-hero__float--3">
                        <i class="bi bi-currency-dollar"></i> Miễn phí
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     CTA BANNER – ĐẶT TOUR → TỰ ĐỘNG ĐƯỢC BẢO HIỂM
═══════════════════════════════════════════════════════════════ --}}
<section class="ins-cta-banner">
    <div class="container">
        <div class="ins-cta-banner__card">
            <div class="ins-cta-banner__glow"></div>
            <div class="row align-items-center gy-4 position-relative" style="z-index:2;">
                <div class="col-lg-8">
                    <div class="ins-cta-banner__eyebrow">
                        <i class="bi bi-lightning-fill me-2"></i>QUYỀN LỢI ĐẶC BIỆT
                    </div>
                    <h2 class="ins-cta-banner__heading">
                        ĐẶT TOUR – TỰ ĐỘNG ĐƯỢC BẢO HIỂM
                    </h2>
                    <p class="ins-cta-banner__body">
                        Không cần mua thêm. Không cần đăng ký riêng.<br>
                        Khi khách hoàn tất đặt tour, quyền lợi bảo hiểm sẽ được áp dụng tự động
                        theo điều kiện của tour.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('frontend.tours.index') }}" class="ins-cta-banner__btn">
                        <i class="bi bi-compass-fill me-2"></i>Xem các tour
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     BENEFITS GRID
═══════════════════════════════════════════════════════════════ --}}
<section class="ins-benefits">
    <div class="container">
        <div class="ins-section-header">
            <span class="ins-section-header__label">QUYỀN LỢI BẢO HIỂM</span>
            <h2 class="ins-section-header__title">Những quyền lợi bạn được bảo vệ</h2>
            <p class="ins-section-header__desc">
                Mọi khách hàng đặt tour đều tự động được hưởng đầy đủ các quyền lợi dưới đây trong suốt chuyến đi.
            </p>
        </div>

        <div class="row g-4">
            @php
            $benefits = [
                [
                    'icon'  => 'bi-hospital-fill',
                    'color' => '#e74c3c',
                    'bg'    => '#fdecea',
                    'title' => 'Chi phí y tế',
                    'desc'  => 'Hỗ trợ chi phí y tế phát sinh khi khách hàng gặp vấn đề sức khỏe hoặc tai nạn trong chuyến đi.',
                ],
                [
                    'icon'  => 'bi-activity',
                    'color' => '#e67e22',
                    'bg'    => '#fef3e8',
                    'title' => 'Tai nạn du lịch',
                    'desc'  => 'Bảo vệ khách hàng trước những rủi ro tai nạn không mong muốn trong thời gian tham gia tour.',
                ],
                [
                    'icon'  => 'bi-airplane-fill',
                    'color' => '#2980b9',
                    'bg'    => '#eaf4fb',
                    'title' => 'Trì hoãn chuyến đi',
                    'desc'  => 'Hỗ trợ theo điều kiện bảo hiểm khi chuyến đi hoặc chuyến bay gặp sự cố ngoài dự kiến.',
                ],
                [
                    'icon'  => 'bi-luggage-fill',
                    'color' => '#8e44ad',
                    'bg'    => '#f5eef8',
                    'title' => 'Hành lý',
                    'desc'  => 'Hỗ trợ khi hành lý của khách hàng bị mất hoặc hư hỏng theo phạm vi bảo hiểm.',
                ],
                [
                    'icon'  => 'bi-globe2',
                    'color' => '#16a085',
                    'bg'    => '#e8f8f5',
                    'title' => 'Hỗ trợ khẩn cấp',
                    'desc'  => 'Hỗ trợ khách hàng trong những tình huống khẩn cấp trong quá trình du lịch.',
                ],
                [
                    'icon'  => 'bi-headset',
                    'color' => '#1abc9c',
                    'bg'    => '#eafaf1',
                    'title' => 'Hỗ trợ 24/7',
                    'desc'  => 'Đội ngũ hỗ trợ luôn sẵn sàng tiếp nhận và hướng dẫn khi khách hàng gặp sự cố.',
                ],
            ];
            @endphp

            @foreach($benefits as $benefit)
                <div class="col-md-6 col-lg-4">
                    <div class="ins-benefit-card">
                        <div class="ins-benefit-card__icon" style="background:{{ $benefit['bg'] }}; color:{{ $benefit['color'] }};">
                            <i class="bi {{ $benefit['icon'] }}"></i>
                        </div>
                        <h3 class="ins-benefit-card__title">{{ $benefit['title'] }}</h3>
                        <p class="ins-benefit-card__desc">{{ $benefit['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     HOW IT WORKS – 4 BƯỚC
═══════════════════════════════════════════════════════════════ --}}
<section class="ins-steps">
    <div class="container">
        <div class="ins-section-header">
            <span class="ins-section-header__label">QUY TRÌNH</span>
            <h2 class="ins-section-header__title">Đơn giản – Tự động – An tâm</h2>
            <p class="ins-section-header__desc">
                Quyền lợi bảo hiểm được tích hợp tự động vào mỗi đơn đặt tour mà không cần bất kỳ thao tác thêm nào.
            </p>
        </div>

        <div class="ins-steps__track">
            @php
            $steps = [
                ['num' => '01', 'icon' => 'bi-map-fill',        'title' => 'Chọn tour',         'desc' => 'Khách hàng lựa chọn tour phù hợp với nhu cầu và sở thích của mình.'],
                ['num' => '02', 'icon' => 'bi-pencil-square',   'title' => 'Đặt tour',          'desc' => 'Điền thông tin hành khách và hoàn tất quy trình đặt tour trên website.'],
                ['num' => '03', 'icon' => 'bi-shield-check',    'title' => 'Bảo hiểm tự động', 'desc' => 'Hệ thống tự động áp dụng bảo hiểm du lịch cho khách hàng ngay khi đặt thành công.'],
                ['num' => '04', 'icon' => 'bi-emoji-smile-fill','title' => 'An tâm du lịch',   'desc' => 'Khách hàng nhận thông tin bảo hiểm cùng thông tin đặt tour qua email xác nhận.'],
            ];
            @endphp

            @foreach($steps as $i => $step)
                <div class="ins-step">
                    <div class="ins-step__icon">
                        <i class="bi {{ $step['icon'] }}"></i>
                    </div>
                    <div class="ins-step__num">{{ $step['num'] }}</div>
                    <h3 class="ins-step__title">{{ $step['title'] }}</h3>
                    <p class="ins-step__desc">{{ $step['desc'] }}</p>
                </div>
                @if($i < count($steps) - 1)
                    <div class="ins-step__arrow"><i class="bi bi-arrow-right"></i></div>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     TRUST STATS
═══════════════════════════════════════════════════════════════ --}}
<section class="ins-stats">
    <div class="container">
        <div class="ins-stats__grid">
            @php
            $stats = [
                ['icon' => 'bi-people-fill',       'value' => '10.000+', 'label' => 'Khách hàng được bảo vệ'],
                ['icon' => 'bi-clock-history',     'value' => '24/7',    'label' => 'Hỗ trợ khẩn cấp'],
                ['icon' => 'bi-shield-fill-check', 'value' => '100%',    'label' => 'Tự động – Không cần đăng ký'],
                ['icon' => 'bi-map-fill',          'value' => '50+',     'label' => 'Điểm đến được bảo vệ'],
            ];
            @endphp
            @foreach($stats as $stat)
                <div class="ins-stats__item">
                    <div class="ins-stats__icon"><i class="bi {{ $stat['icon'] }}"></i></div>
                    <div class="ins-stats__value">{{ $stat['value'] }}</div>
                    <div class="ins-stats__label">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     BOTTOM CTA
═══════════════════════════════════════════════════════════════ --}}
<section class="ins-bottom-cta">
    <div class="container text-center">
        <i class="bi bi-shield-lock-fill ins-bottom-cta__icon"></i>
        <h2 class="ins-bottom-cta__title">Sẵn sàng khám phá thế giới?</h2>
        <p class="ins-bottom-cta__desc">
            Mọi chuyến đi của bạn đều được bảo vệ tự động khi đặt tour tại Travel Wonder.
        </p>
        <a href="{{ route('frontend.tours.index') }}" class="ins-bottom-cta__btn">
            <i class="bi bi-compass-fill me-2"></i>Khám phá các tour ngay
        </a>
    </div>
</section>

<style>
/* ─── CSS Variables ─────────────────────────────────── */
:root {
    --ins-primary: #0ea5e9;
    --ins-primary-dark: #0369a1;
    --ins-secondary: #10b981;
    --ins-dark: #0f172a;
    --ins-text: #334155;
    --ins-muted: #64748b;
    --ins-bg: #f8fafc;
    --ins-white: #ffffff;
    --ins-radius: 20px;
    --ins-shadow: 0 4px 24px rgba(14,165,233,.08);
    --ins-transition: .3s cubic-bezier(.4,0,.2,1);
}

/* ─── Hero ──────────────────────────────────────────── */
.ins-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0369a1 100%);
    padding: 100px 0 120px;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.ins-hero__bg-orb {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}
.ins-hero__bg-orb--1 {
    width: 600px; height: 600px;
    top: -200px; right: -100px;
    background: radial-gradient(circle, rgba(56,189,248,.15) 0%, transparent 70%);
}
.ins-hero__bg-orb--2 {
    width: 400px; height: 400px;
    bottom: -150px; left: -100px;
    background: radial-gradient(circle, rgba(16,185,129,.1) 0%, transparent 70%);
}
.ins-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255,255,255,.12);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 50px;
    padding: 8px 20px;
    font-size: .875rem;
    font-weight: 600;
    color: #7dd3fc;
}
.ins-hero__title {
    font-size: clamp(2.4rem, 5vw, 3.5rem);
    font-weight: 900;
    letter-spacing: -.03em;
    line-height: 1.15;
    background: linear-gradient(to right, #fff 30%, #bae6fd 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 12px;
}
.ins-hero__subtitle {
    font-size: 1.25rem;
    color: #7dd3fc;
    font-weight: 600;
    margin-bottom: 16px;
}
.ins-hero__desc {
    font-size: 1.1rem;
    color: #cbd5e1;
    line-height: 1.75;
    max-width: 560px;
    margin-bottom: 0;
}
.ins-hero__pill {
    display: inline-flex;
    align-items: center;
    background: rgba(16,185,129,.18);
    border: 1px solid rgba(52,211,153,.35);
    border-radius: 50px;
    padding: 10px 22px;
    font-size: .95rem;
    font-weight: 600;
    color: #6ee7b7;
}

/* Shield visual */
.ins-hero__visual {
    position: relative;
    width: 320px;
    height: 320px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ins-hero__shield {
    width: 220px;
    height: 220px;
    background: rgba(255,255,255,.08);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255,255,255,.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6rem;
    color: #38bdf8;
    animation: ins-pulse 3s ease-in-out infinite;
}
@keyframes ins-pulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(56,189,248,.4); }
    50%      { box-shadow: 0 0 0 24px rgba(56,189,248,0); }
}
.ins-hero__float {
    position: absolute;
    background: rgba(255,255,255,.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 50px;
    padding: 8px 16px;
    font-size: .82rem;
    font-weight: 700;
    color: #fff;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
    animation: ins-float 4s ease-in-out infinite;
}
.ins-hero__float--1 { top: 24px; right: 0;   animation-delay: 0s; }
.ins-hero__float--2 { top: 50%;  left: -10px; transform: translateY(-50%); animation-delay: 1.3s; }
.ins-hero__float--3 { bottom: 24px; right: 0; animation-delay: 2.6s; }
@keyframes ins-float {
    0%,100% { transform: translateY(0); }
    50%     { transform: translateY(-8px); }
}
.ins-hero__float--2 { animation: ins-float2 4s ease-in-out infinite 1.3s; }
@keyframes ins-float2 {
    0%,100% { transform: translateY(-50%); }
    50%     { transform: translateY(calc(-50% - 8px)); }
}

/* ─── CTA Banner ────────────────────────────────────── */
.ins-cta-banner { padding: 60px 0; }
.ins-cta-banner__card {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    border-radius: 28px;
    padding: 56px 60px;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.ins-cta-banner__glow {
    position: absolute;
    top: -80px; right: -80px;
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(56,189,248,.25) 0%, transparent 70%);
    pointer-events: none;
}
.ins-cta-banner__eyebrow {
    display: inline-flex;
    align-items: center;
    background: rgba(56,189,248,.15);
    border: 1px solid rgba(56,189,248,.3);
    border-radius: 50px;
    padding: 6px 18px;
    font-size: .8rem;
    font-weight: 700;
    letter-spacing: .08em;
    color: #38bdf8;
    margin-bottom: 18px;
}
.ins-cta-banner__heading {
    font-size: clamp(1.8rem, 3.5vw, 2.6rem);
    font-weight: 900;
    letter-spacing: -.02em;
    line-height: 1.2;
    margin-bottom: 16px;
}
.ins-cta-banner__body {
    font-size: 1.05rem;
    color: #cbd5e1;
    line-height: 1.75;
    margin: 0;
}
.ins-cta-banner__btn {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, var(--ins-primary) 0%, var(--ins-primary-dark) 100%);
    color: #fff;
    font-weight: 700;
    font-size: 1rem;
    padding: 16px 32px;
    border-radius: 14px;
    text-decoration: none;
    box-shadow: 0 8px 24px rgba(14,165,233,.35);
    transition: var(--ins-transition);
}
.ins-cta-banner__btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 32px rgba(14,165,233,.45);
    color: #fff;
}

/* ─── Section Header ────────────────────────────────── */
.ins-section-header { text-align: center; max-width: 680px; margin: 0 auto 52px; }
.ins-section-header__label {
    display: inline-block;
    font-size: .78rem;
    font-weight: 800;
    letter-spacing: .12em;
    color: var(--ins-primary);
    text-transform: uppercase;
    margin-bottom: 12px;
}
.ins-section-header__title {
    font-size: clamp(1.7rem, 3vw, 2.3rem);
    font-weight: 800;
    color: var(--ins-dark);
    letter-spacing: -.02em;
    margin-bottom: 14px;
}
.ins-section-header__desc {
    font-size: 1rem;
    color: var(--ins-muted);
    line-height: 1.7;
    margin: 0;
}

/* ─── Benefits ──────────────────────────────────────── */
.ins-benefits { padding: 80px 0; background: var(--ins-bg); }
.ins-benefit-card {
    background: var(--ins-white);
    border: 1.5px solid #e2e8f0;
    border-radius: var(--ins-radius);
    padding: 36px 28px;
    height: 100%;
    transition: var(--ins-transition);
    box-shadow: var(--ins-shadow);
}
.ins-benefit-card:hover {
    transform: translateY(-6px);
    border-color: #bae6fd;
    box-shadow: 0 16px 40px rgba(14,165,233,.12);
}
.ins-benefit-card__icon {
    width: 62px; height: 62px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 22px;
    transition: var(--ins-transition);
}
.ins-benefit-card:hover .ins-benefit-card__icon {
    transform: scale(1.1);
}
.ins-benefit-card__title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--ins-dark);
    margin-bottom: 10px;
}
.ins-benefit-card__desc {
    font-size: .925rem;
    color: var(--ins-muted);
    line-height: 1.65;
    margin: 0;
}

/* ─── Steps ─────────────────────────────────────────── */
.ins-steps { padding: 80px 0; background: #fff; }
.ins-steps__track {
    display: flex;
    align-items: flex-start;
    gap: 0;
    justify-content: center;
    flex-wrap: wrap;
}
.ins-step {
    flex: 1;
    min-width: 180px;
    max-width: 220px;
    text-align: center;
    padding: 0 16px;
}
.ins-step__icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ins-primary) 0%, var(--ins-primary-dark) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: #fff;
    margin: 0 auto 14px;
    box-shadow: 0 8px 24px rgba(14,165,233,.3);
    transition: var(--ins-transition);
}
.ins-step:hover .ins-step__icon {
    transform: scale(1.1);
    box-shadow: 0 14px 32px rgba(14,165,233,.4);
}
.ins-step__num {
    font-size: .78rem;
    font-weight: 800;
    color: var(--ins-primary);
    letter-spacing: .08em;
    margin-bottom: 8px;
}
.ins-step__title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--ins-dark);
    margin-bottom: 8px;
}
.ins-step__desc {
    font-size: .875rem;
    color: var(--ins-muted);
    line-height: 1.6;
    margin: 0;
}
.ins-step__arrow {
    display: flex;
    align-items: center;
    padding-top: 36px;
    font-size: 1.4rem;
    color: #cbd5e1;
    flex-shrink: 0;
}

/* ─── Stats ─────────────────────────────────────────── */
.ins-stats {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    padding: 70px 0;
    color: #fff;
}
.ins-stats__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 40px;
    text-align: center;
}
.ins-stats__icon {
    font-size: 2rem;
    color: #38bdf8;
    margin-bottom: 10px;
}
.ins-stats__value {
    font-size: 2.4rem;
    font-weight: 900;
    letter-spacing: -.03em;
    color: #fff;
    margin-bottom: 6px;
}
.ins-stats__label {
    font-size: .875rem;
    color: #94a3b8;
    font-weight: 500;
}

/* ─── Bottom CTA ────────────────────────────────────── */
.ins-bottom-cta { padding: 100px 0; background: var(--ins-bg); }
.ins-bottom-cta__icon {
    font-size: 4rem;
    color: var(--ins-primary);
    display: block;
    margin-bottom: 24px;
}
.ins-bottom-cta__title {
    font-size: clamp(1.7rem, 3.5vw, 2.4rem);
    font-weight: 800;
    color: var(--ins-dark);
    letter-spacing: -.02em;
    margin-bottom: 14px;
}
.ins-bottom-cta__desc {
    font-size: 1.05rem;
    color: var(--ins-muted);
    max-width: 520px;
    margin: 0 auto 36px;
    line-height: 1.7;
}
.ins-bottom-cta__btn {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, var(--ins-primary) 0%, var(--ins-primary-dark) 100%);
    color: #fff;
    font-weight: 700;
    font-size: 1.05rem;
    padding: 18px 40px;
    border-radius: 16px;
    text-decoration: none;
    box-shadow: 0 10px 28px rgba(14,165,233,.35);
    transition: var(--ins-transition);
}
.ins-bottom-cta__btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 36px rgba(14,165,233,.45);
    color: #fff;
}

/* ─── Responsive ────────────────────────────────────── */
@media (max-width: 992px) {
    .ins-hero { padding: 70px 0 90px; }
    .ins-cta-banner__card { padding: 40px 32px; }
    .ins-steps__track { gap: 0; }
    .ins-step { min-width: 140px; }
    .ins-step__arrow { padding-top: 36px; font-size: 1.1rem; }
}
@media (max-width: 768px) {
    .ins-steps__track { flex-direction: column; align-items: center; }
    .ins-step { max-width: 100%; width: 100%; padding: 0 0 24px; }
    .ins-step__arrow { display: none; }
    .ins-cta-banner__card { padding: 36px 24px; }
}
</style>

@endsection
