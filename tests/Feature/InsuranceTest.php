<?php

it('renders the travel insurance page successfully', function () {
    $response = $this->get(route('frontend.insurance.index'));

    $response->assertStatus(200);
    $response->assertSee('Bảo hiểm du lịch');
    $response->assertSee('Bảo vệ chuyến đi của bạn với các gói bảo hiểm toàn diện');
});

it('displays insurance packages and faqs', function () {
    $response = $this->get(route('frontend.insurance.index'));

    $response->assertStatus(200);

    // Gói bảo hiểm
    $response->assertSee('Cơ bản');
    $response->assertSee('99.000đ');
    $response->assertSee('Tiêu chuẩn');
    $response->assertSee('199.000đ');
    $response->assertSee('Cao cấp');
    $response->assertSee('399.000đ');

    // Quyền lợi
    $response->assertSee('Hỗ trợ y tế khẩn cấp');
    $response->assertSee('Bồi thường mất hành lý');

    // FAQ
    $response->assertSee('Bảo hiểm có hiệu lực khi nào?');
    $response->assertSee('Tôi có thể hủy gói bảo hiểm không?');
    $response->assertSee('Làm thế nào để yêu cầu bồi thường?');
});

it('allows user to register travel insurance successfully', function () {
    $startDate = now()->addDays(1)->format('Y-m-d');
    $endDate = now()->addDays(5)->format('Y-m-d');

    $response = $this->post(route('frontend.insurance.store'), [
        'fullname' => 'Nguyễn Văn Test',
        'phone' => '0987654321',
        'email' => 'test@travelwonder.com',
        'package_code' => 'tieu_chuan',
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success_registration');

    $this->assertDatabaseHas('insurance_registrations', [
        'fullname' => 'Nguyễn Văn Test',
        'email' => 'test@travelwonder.com',
        'package_code' => 'tieu_chuan',
        'package_name' => 'Tiêu chuẩn',
    ]);
});

it('validates required fields for insurance registration', function () {
    $response = $this->post(route('frontend.insurance.store'), []);

    $response->assertSessionHasErrors([
        'fullname',
        'phone',
        'email',
        'package_code',
        'start_date',
        'end_date',
    ]);
});
