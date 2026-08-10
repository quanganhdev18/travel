<?php

it('renders the travel insurance page successfully', function () {
    $response = $this->get(route('frontend.insurance.index'));

    $response->assertStatus(200);
    $response->assertSee('Bảo hiểm du lịch');
    $response->assertSee('An tâm khám phá');
});

it('displays key insurance content sections', function () {
    $response = $this->get(route('frontend.insurance.index'));

    $response->assertStatus(200);

    // Hero section
    $response->assertSee('Bảo hiểm được áp dụng tự động khi đặt tour');

    // CTA banner
    $response->assertSee('ĐẶT TOUR – TỰ ĐỘNG ĐƯỢC BẢO HIỂM');
    $response->assertSee('Không cần mua thêm. Không cần đăng ký riêng.');

    // Benefits
    $response->assertSee('Chi phí y tế');
    $response->assertSee('Tai nạn du lịch');
    $response->assertSee('Hành lý');
    $response->assertSee('Hỗ trợ 24/7');

    // Process steps
    $response->assertSee('Chọn tour');
    $response->assertSee('Đặt tour');
    $response->assertSee('Bảo hiểm tự động');
    $response->assertSee('An tâm du lịch');
});

it('insurance page has no registration form or package selection', function () {
    $response = $this->get(route('frontend.insurance.index'));

    $response->assertStatus(200);
    $response->assertDontSee('form action');
    $response->assertDontSee('package_code');
});
