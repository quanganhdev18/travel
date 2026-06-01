<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    // Create destination
    $destination = Destination::create([
        'name' => 'Đà Nẵng',
        'description' => 'Thành phố đáng sống',
    ]);

    // Create tour
    $this->tour = Tour::create([
        'destination_id' => $destination->id,
        'title' => 'Khám phá Đà Nẵng - Hội An',
        'slug' => 'tour-da-nang-3-ngay-2-dem',
        'description' => 'Hành trình khám phá miền Trung',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3500000,
    ]);

    // Create tour schedule
    $this->schedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => Carbon::now()->addDays(10)->toDateTimeString(),
        'return_date' => Carbon::now()->addDays(12)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);
});

test('guest cannot access checkout', function () {
    $response = $this->post(route('frontend.tours.checkout', [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
    ]));

    $response->assertRedirect('/login');
});

test('authenticated user can access checkout with valid parameters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('frontend.tours.checkout', [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
    ]));

    $response->assertOk();
    $response->assertViewIs('frontend.tours.checkout');
    $response->assertViewHas('totalPrice', 7000000);
});

test('booking stores successfully for flight transport', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('frontend.tours.store'), [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
        'customer_name' => 'Nguyễn Văn A',
        'customer_phone' => '0987654321',
        'customer_email' => 'customer@example.com',
        'total_price' => 7000000,
        'transport_type' => 'flight',
        'identity_number' => '036123456789',
        'date_of_birth' => '1996-05-18',
        'gender' => 'male',
        'issue_date' => '2021-05-18',
        'expiry_date' => '2036-05-18',
        'issue_place' => 'Cục Cảnh sát',
    ]);

    $this->assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'tour_schedule_id' => $this->schedule->id,
        'transport_type' => 'flight',
    ]);

    $booking = Booking::where('user_id', $user->id)->first();
    $response->assertRedirect(route('frontend.flights.search', [
        'origin' => 'HAN', // default because destination isn't mapped
        'destination' => 'DAD', // mapped 'Đà Nẵng' => 'DAD'
        'departure_date' => Carbon::parse($this->schedule->departure_date)->format('Y-m-d'),
        'passengers' => 2,
        'cabin_class' => 'economy',
        'tour_booking_id' => $booking->id,
    ]));
});

test('booking stores successfully for bus transport', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('frontend.tours.store'), [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
        'customer_name' => 'Nguyễn Văn A',
        'customer_phone' => '0987654321',
        'customer_email' => 'customer@example.com',
        'total_price' => 7000000,
        'transport_type' => 'bus',
        'identity_number' => '036123456789',
        'date_of_birth' => '1996-05-18',
        'gender' => 'male',
        'issue_date' => '2021-05-18',
        'expiry_date' => '2036-05-18',
        'issue_place' => 'Cục Cảnh sát',
    ]);

    $this->assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'tour_schedule_id' => $this->schedule->id,
        'transport_type' => 'bus',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success', 'Đặt tour thành công. Chúng tôi sẽ liên hệ sớm để xác nhận lịch trình di chuyển bằng xe.');
});

test('booking stores successfully for self transport', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('frontend.tours.store'), [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
        'customer_name' => 'Nguyễn Văn A',
        'customer_phone' => '0987654321',
        'customer_email' => 'customer@example.com',
        'total_price' => 7000000,
        'transport_type' => 'self',
        'identity_number' => '036123456789',
        'date_of_birth' => '1996-05-18',
        'gender' => 'male',
        'issue_date' => '2021-05-18',
        'expiry_date' => '2036-05-18',
        'issue_place' => 'Cục Cảnh sát',
    ]);

    $this->assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'tour_schedule_id' => $this->schedule->id,
        'transport_type' => 'self',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success', 'đặt tour thành công. chúng tôi sẽ liên hệ sớm để xác nhận lịch trình tự túc.');
});
