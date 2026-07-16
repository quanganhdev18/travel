<?php

use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->destination = Destination::create([
        'name' => 'Đà Nẵng',
        'description' => 'Thành phố đáng sống',
    ]);

    $this->tour = Tour::create([
        'destination_id' => $this->destination->id,
        'departure_location_id' => $this->destination->id,
        'title' => ['vi' => 'Tour Đà Nẵng'],
        'slug' => 'tour-da-nang',
        'description' => ['vi' => 'Mô tả tour'],
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3500000,
        'departure_time' => '09:10:00', // Departure time is 09:10 AM
    ]);
});

test('tour schedule departing on July 17 at 9:10am is HIDDEN when checked on July 14 at 12:00pm (less than 72 hours)', function () {
    // 1. Freeze time to July 14, 2026, 12:00:00 (noon)
    Carbon::setTestNow('2026-07-14 12:00:00');

    // 2. Create tour schedule departing on July 17, 2026
    $schedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => '2026-07-17 00:00:00',
        'return_date' => '2026-07-20 00:00:00',
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    // 3. Verify the tour with active schedules is NOT retrieved (realtime hide)
    $activeTours = Tour::whereHas('activeSchedules')->get();
    expect($activeTours->pluck('id'))->not->toContain($this->tour->id);

    // 4. Try booking checkout and assert redirect back with error
    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->from(route('frontend.tours.show', $this->tour->slug))
        ->get(route('frontend.tours.checkout', [
            'schedule_id' => $schedule->id,
            'adults' => 2,
            'children' => 0,
        ]));

    $response->assertRedirect(route('frontend.tours.show', $this->tour->slug));
    $response->assertSessionHas('error');

    // Clean up test clock
    Carbon::setTestNow();
});

test('tour schedule departing on July 17 at 9:10am is VISIBLE when checked on July 14 at 6:00am (more than 72 hours)', function () {
    // 1. Freeze time to July 14, 2026, 06:00:00 (morning)
    Carbon::setTestNow('2026-07-14 06:00:00');

    // 2. Create tour schedule departing on July 17, 2026
    $schedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => '2026-07-17 00:00:00',
        'return_date' => '2026-07-20 00:00:00',
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    // 3. Verify the tour with active schedules IS retrieved
    $activeTours = Tour::whereHas('activeSchedules')->get();
    expect($activeTours->pluck('id'))->toContain($this->tour->id);

    // 4. Try booking checkout and assert success (200 OK)
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('frontend.tours.checkout', [
        'schedule_id' => $schedule->id,
        'adults' => 2,
        'children' => 0,
    ]));

    $response->assertStatus(200);

    // Clean up test clock
    Carbon::setTestNow();
});
