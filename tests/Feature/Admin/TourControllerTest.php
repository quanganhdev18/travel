<?php

use App\Models\Category;
use App\Models\Destination;
use App\Models\District;
use App\Models\Province;
use App\Models\Tour;
use App\Models\User;
use App\Models\Ward;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Setup roles
    Role::firstOrCreate(['name' => 'Super Admin']);

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->admin->assignRole('Super Admin');
    $this->actingAs($this->admin);

    $this->destination = Destination::create([
        'name' => 'Đà Nẵng',
        'description' => 'Thành phố Đà Nẵng',
    ]);
});

test('admin can create a tour with unique name', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
        'district_id' => District::first()?->id ?? 1,
    ]);
    $category = Category::first() ?? Category::create([
        'name' => ['vi' => 'Test Category', 'en' => 'Test Category', 'zh' => 'Test Category'],
        'slug' => 'test-category',
    ]);

    $tourData = [
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Da Nang Tour',
            'zh' => '岘港之旅',
        ],
        'description' => [
            'vi' => 'Mô tả tour',
            'en' => 'Tour description',
            'zh' => '旅游描述',
        ],
        'base_price' => 1000000,
        'child_price' => 500000,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'departure_hour' => 8,
        'departure_minute' => 30,
        'categories' => [$category->id],
    ];

    $response = $this->post(route('admin.tours.store'), $tourData);

    $response->assertRedirect();
    $this->assertDatabaseHas('tours', [
        'title->vi' => 'Tour Du Lịch Đà Nẵng',
    ]);
});

test('admin cannot create tour with duplicate Vietnamese name', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
        'district_id' => District::first()?->id ?? 1,
    ]);

    // Create first tour
    Tour::create([
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Da Nang Tour',
            'zh' => '岘港之旅',
        ],
        'slug' => 'tour-du-lich-da-nang-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 1000000,
        'duration_days' => 3,
        'duration_nights' => 2,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
    ]);

    // Try to create second tour with same Vietnamese name
    $tourData = [
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Different Name',
            'zh' => '不同的名字',
        ],
        'description' => [
            'vi' => 'Mô tả tour',
            'en' => 'Tour description',
            'zh' => '旅游描述',
        ],
        'base_price' => 1000000,
        'child_price' => 500000,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'departure_hour' => 8,
        'departure_minute' => 30,
    ];

    $response = $this->post(route('admin.tours.store'), $tourData);

    $response->assertSessionHasErrors('title.vi');
    expect(session('errors')->get('title.vi')[0])
        ->toBe('Tên tour (Tiếng Việt) đã tồn tại. Vui lòng chọn tên khác.');
});

test('admin can update tour while keeping same name', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
        'district_id' => District::first()?->id ?? 1,
    ]);

    $tour = Tour::create([
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Da Nang Tour',
            'zh' => '岘港之旅',
        ],
        'slug' => 'tour-du-lich-da-nang-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 1000000,
        'duration_days' => 3,
        'duration_nights' => 2,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
    ]);

    $updateData = [
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng', // Same name
            'en' => 'Updated Da Nang Tour',
            'zh' => '更新岘港之旅',
        ],
        'description' => [
            'vi' => 'Mô tả tour cập nhật',
            'en' => 'Updated tour description',
            'zh' => '更新旅游描述',
        ],
        'base_price' => 1500000,
        'child_price' => 750000,
        'meeting_point' => 'Cổng công viên Thống Nhất Cập Nhật',
        'destination_id' => $this->destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'departure_hour' => 8,
        'departure_minute' => 30,
    ];

    $response = $this->put(route('admin.tours.update', $tour->id), $updateData);

    $response->assertRedirect(route('admin.tours.index'));
    $this->assertDatabaseHas('tours', [
        'id' => $tour->id,
        'title->vi' => 'Tour Du Lịch Đà Nẵng',
        'title->en' => 'Updated Da Nang Tour',
    ]);
});

test('admin cannot update tour to duplicate name', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
        'district_id' => District::first()?->id ?? 1,
    ]);

    // Create first tour
    Tour::create([
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Da Nang Tour',
            'zh' => '岘港之旅',
        ],
        'slug' => 'tour-du-lich-da-nang-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 1000000,
        'duration_days' => 3,
        'duration_nights' => 2,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
    ]);

    // Create second tour
    $tour2 = Tour::create([
        'title' => [
            'vi' => 'Tour Du Lịch Hà Nội',
            'en' => 'Ha Noi Tour',
            'zh' => '河内之旅',
        ],
        'slug' => 'tour-du-lich-ha-noi-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 1000000,
        'duration_days' => 3,
        'duration_nights' => 2,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
    ]);

    // Try to update second tour to have same name as first tour
    $updateData = [
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng', // Duplicate name
            'en' => 'Updated Tour',
            'zh' => '更新之旅',
        ],
        'description' => [
            'vi' => 'Mô tả tour',
            'en' => 'Tour description',
            'zh' => '旅游描述',
        ],
        'base_price' => 1000000,
        'child_price' => 500000,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'departure_hour' => 8,
        'departure_minute' => 30,
    ];

    $response = $this->put(route('admin.tours.update', $tour2->id), $updateData);

    $response->assertSessionHasErrors('title.vi');
    expect(session('errors')->get('title.vi')[0])
        ->toBe('Tên tour (Tiếng Việt) đã tồn tại. Vui lòng chọn tên khác.');
});
