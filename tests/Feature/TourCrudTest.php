<?php

use App\Models\Destination;
use App\Models\Province;
use App\Models\Tour;
use App\Models\User;
use App\Models\Ward;
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
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
    ]);

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
            'departure_province_id' => $province->id,
            'departure_ward_id' => $ward->id,
            'destination_province_id' => $province->id,
            'destination_ward_id' => $ward->id,
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
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
    ]);

    $tour = Tour::create([
        'departure_province_id' => $province->id,
        'departure_ward_id' => $ward->id,
        'destination_province_id' => $province->id,
        'destination_ward_id' => $ward->id,
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
            'departure_province_id' => $province->id,
            'departure_ward_id' => $ward->id,
            'destination_province_id' => $province->id,
            'destination_ward_id' => $ward->id,
            'duration_days' => 1,
            'duration_nights' => 0,
            'departure_hour' => 21,
            'departure_minute' => 15,
        ]);

    $response->assertRedirect();

    $tour->refresh();
    expect($tour->departure_time)->toBe('21:15:00');
});

test('admin can store schedule for a one day tour with same departure and return date', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
    ]);

    $tour = Tour::create([
        'departure_province_id' => $province->id,
        'departure_ward_id' => $ward->id,
        'destination_province_id' => $province->id,
        'destination_ward_id' => $ward->id,
        'title' => ['vi' => 'Tour 1 Ngày'],
        'slug' => 'tour-1-ngay',
        'description' => ['vi' => 'Mô tả tour 1 ngày'],
        'duration_days' => 1,
        'duration_nights' => 0,
        'base_price' => 500000,
        'departure_time' => '08:00:00',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.tours.schedules.store', $tour->id), [
            'departure_date' => '2026-08-01',
            'return_date' => '2026-08-01',
            'capacity' => 20,
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('tour_schedules', [
        'tour_id' => $tour->id,
        'departure_date' => '2026-08-01 00:00:00',
        'return_date' => '2026-08-01 00:00:00',
        'capacity' => 20,
    ]);
});
