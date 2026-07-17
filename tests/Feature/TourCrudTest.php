<?php

use App\Models\Destination;
use App\Models\Tour;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Setup roles
    Role::firstOrCreate(['name' => 'Super Admin']);

    $this->adminUser = User::factory()->create(['role' => 'admin']);
    $this->adminUser->assignRole('Super Admin');

    $this->destination = Destination::create([
        'name' => 'Hà Nội',
        'description' => 'Thủ đô Hà Nội',
    ]);
});

test('admin can store tour with departure time', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.tours.store'), [
            'title' => [
                'vi' => 'Tour khám phá Hà Nội mới',
                'en' => 'New Hanoi Tour',
                'zh' => 'New Hanoi Tour ZH',
            ],
            'description' => [
                'vi' => 'Chi tiết tour Hà Nội',
            ],
            'base_price' => 2000000,
            'destination_id' => $this->destination->id,
            'departure_location_id' => $this->destination->id,
            'duration_days' => 2,
            'duration_nights' => 1,
            'departure_hour' => 7,
            'departure_minute' => 30,
        ]);

    $response->assertRedirect();

    $tour = Tour::where('slug', 'like', 'tour-kham-pha-ha-noi-moi%')->first();
    expect($tour)->not->toBeNull();
    expect($tour->departure_time)->toBe('07:30:00');
});

test('admin can update tour with departure time', function () {
    $tour = Tour::create([
        'destination_id' => $this->destination->id,
        'departure_location_id' => $this->destination->id,
        'title' => ['vi' => 'Tour Cũ'],
        'slug' => 'tour-cu',
        'description' => ['vi' => 'Mô tả tour cũ'],
        'duration_days' => 1,
        'duration_nights' => 0,
        'base_price' => 1000000,
        'departure_time' => '08:00:00',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->put(route('admin.tours.update', $tour->id), [
            'title' => [
                'vi' => 'Tour Cũ Cập Nhật',
            ],
            'base_price' => 1200000,
            'destination_id' => $this->destination->id,
            'departure_location_id' => $this->destination->id,
            'duration_days' => 1,
            'duration_nights' => 0,
            'departure_hour' => 21,
            'departure_minute' => 15,
        ]);

    $response->assertRedirect();

    $tour->refresh();
    expect($tour->departure_time)->toBe('21:15:00');
});
