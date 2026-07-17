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

    $this->province = Province::find(11);
    if (! $this->province) {
        $this->province = new Province;
        $this->province->id = 11;
        $this->province->name = 'Hà Nội';
        $this->province->full_name = 'Thành phố Hà Nội';
        $this->province->save();
    }

    $this->ward = Ward::find(12345);
    if (! $this->ward) {
        $this->ward = new Ward;
        $this->ward->id = 12345;
        $this->ward->province_id = 11;
        $this->ward->name = 'Quốc Oai';
        $this->ward->name_with_type = 'Thị trấn Quốc Oai';
        $this->ward->path = 'Quốc Oai, Hà Nội';
        $this->ward->path_with_type = 'Thị trấn Quốc Oai, Thành phố Hà Nội';
        $this->ward->save();
    }
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
            'departure_province_id' => $this->province->id,
            'departure_ward_id' => $this->ward->id,
            'destination_province_id' => $this->province->id,
            'destination_ward_id' => $this->ward->id,
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
        'destination_province_id' => $this->province->id,
        'destination_ward_id' => $this->ward->id,
        'departure_province_id' => $this->province->id,
        'departure_ward_id' => $this->ward->id,
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
            'departure_province_id' => $this->province->id,
            'departure_ward_id' => $this->ward->id,
            'destination_province_id' => $this->province->id,
            'destination_ward_id' => $this->ward->id,
            'duration_days' => 1,
            'duration_nights' => 0,
            'departure_hour' => 21,
            'departure_minute' => 15,
        ]);

    $response->assertRedirect();

    $tour->refresh();
    expect($tour->departure_time)->toBe('21:15:00');
});
