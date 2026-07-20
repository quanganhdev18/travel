<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $destination = Destination::create([
        'name' => 'Đà Nẵng',
        'description' => 'Thành phố đáng sống',
    ]);

    $this->tour = Tour::create([
        'destination_id' => $destination->id,
        'title' => 'Khám phá Đà Nẵng - Hội An',
        'slug' => 'tour-da-nang-3-ngay-2-dem-'.time(),
        'description' => 'Hành trình khám phá miền Trung',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3500000,
    ]);

    $this->schedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => Carbon::now()->addDays(10)->toDateTimeString(),
        'return_date' => Carbon::now()->addDays(12)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);
});

test('auto cancel unpaid bookings older than 30 minutes and record reason', function () {
    $user = User::factory()->create();

    // Create an unpaid booking
    $booking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $this->schedule->id,
        'total_price' => 7000000,
        'adults_count' => 2,
        'children_count' => 0,
        'payment_status' => Booking::PAYMENT_PENDING,
        'booking_status' => 'pending',
        'tour_status' => Booking::TOUR_UPCOMING,
    ]);

    // Force set created_at using direct DB query
    DB::table('bookings')->where('id', $booking->id)->update([
        'created_at' => now()->subMinutes(31),
    ]);

    // Manually decrease available seats to mock the active booking state
    $this->schedule->update(['available_seats' => 18]);

    // Run the console command
    $this->artisan('bookings:cancel-unpaid')
        ->assertSuccessful();

    // Refresh models
    $booking->refresh();
    $this->schedule->refresh();

    // Assert booking is cancelled and reason is recorded
    expect($booking->booking_status)->toBe('cancelled');
    expect($booking->payment_status)->toBe(Booking::PAYMENT_FAILED);
    expect($booking->tour_status)->toBe(Booking::TOUR_CANCELLED_ADMIN);
    expect($booking->cancel_reason)->toBe('Đơn hàng bị hủy do hết hạn thanh toán (quá 30 phút)');

    // Assert seats are freed
    expect($this->schedule->available_seats)->toBe(20);
});

test('do not cancel unpaid bookings younger than 30 minutes', function () {
    $user = User::factory()->create();

    // Create an unpaid booking
    $booking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $this->schedule->id,
        'total_price' => 7000000,
        'adults_count' => 2,
        'children_count' => 0,
        'payment_status' => Booking::PAYMENT_PENDING,
        'booking_status' => 'pending',
        'tour_status' => Booking::TOUR_UPCOMING,
    ]);

    // Force set created_at using direct DB query
    DB::table('bookings')->where('id', $booking->id)->update([
        'created_at' => now()->subMinutes(15),
    ]);

    // Manually decrease available seats
    $this->schedule->update(['available_seats' => 18]);

    // Run the console command
    $this->artisan('bookings:cancel-unpaid')
        ->assertSuccessful();

    // Refresh models
    $booking->refresh();
    $this->schedule->refresh();

    // Assert booking is NOT cancelled
    expect($booking->booking_status)->toBe('pending');
    expect($booking->payment_status)->toBe(Booking::PAYMENT_PENDING);
    expect($booking->tour_status)->toBe(Booking::TOUR_UPCOMING);
    expect($booking->cancel_reason)->toBeNull();

    // Assert seats remain taken
    expect($this->schedule->available_seats)->toBe(18);
});
