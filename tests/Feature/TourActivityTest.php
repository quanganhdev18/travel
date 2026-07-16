<?php

use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourItinerary;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('admin can store tour activity without description field in request', function () {
    // 1. Setup admin user
    Role::firstOrCreate(['name' => 'Super Admin']);
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Super Admin');

    // 2. Setup destination and tour
    $destination = Destination::create([
        'name' => 'Hà Nội',
        'description' => 'Thủ đô Hà Nội',
    ]);

    $tour = Tour::create([
        'destination_id' => $destination->id,
        'departure_location_id' => $destination->id,
        'title' => ['vi' => 'Tour khám phá Hà Nội'],
        'slug' => 'tour-kham-pha-ha-noi',
        'description' => ['vi' => 'Chi tiết tour'],
        'duration_days' => 1,
        'duration_nights' => 0,
        'base_price' => 1000000,
    ]);

    // 3. Create itinerary
    $itinerary = TourItinerary::create([
        'tour_id' => $tour->id,
        'day_number' => 1,
        'title' => ['vi' => 'Ngày 1: Khám phá phố cổ'],
        'description' => [
            'vi' => 'Mô tả ngày 1',
            'en' => 'Day 1 Description',
            'zh' => 'Day 1 Description ZH',
        ],
    ]);

    // 4. Post to store activity
    $response = $this->actingAs($admin)
        ->from(route('admin.tours.index'))
        ->post(route('admin.itineraries.activities.store', $itinerary->id), [
            'activity_type' => 'Entertainment',
            'start_time' => '13:01',
            'title' => [
                'vi' => 'Xem rối nước',
                'en' => 'Water puppet show',
                'zh' => 'Water puppet show ZH',
            ],
        ]);

    // 5. Assert successful redirect
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Thêm hoạt động thành công!');

    // 6. Assert database entry exists with empty description
    $this->assertDatabaseHas('tour_activities', [
        'tour_itinerary_id' => $itinerary->id,
        'activity_type' => 'Entertainment',
        'start_time' => '13:01:00',
    ]);

    $activity = $itinerary->activities()->first();
    expect($activity->title)->toBe('Xem rối nước');
    expect($activity->description)->toBe(''); // Translation defaults to empty string
});
