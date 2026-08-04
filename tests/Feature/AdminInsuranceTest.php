<?php

use App\Models\InsuranceRegistration;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
});

it('prevents unauthenticated or non-admin user from accessing admin insurance', function () {
    $response = $this->get(route('admin.insurance.index'));

    $response->assertRedirect(route('login'));
});

it('allows admin user to view insurance registrations list and stats', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    InsuranceRegistration::create([
        'registration_code' => 'INS-TEST1234',
        'fullname' => 'Khách Hàng Test',
        'phone' => '0912345678',
        'email' => 'khach@test.com',
        'package_code' => 'tieu_chuan',
        'package_name' => 'Tiêu chuẩn',
        'price_per_day' => 199000,
        'total_days' => 3,
        'total_price' => 597000,
        'start_date' => now()->addDay(),
        'end_date' => now()->addDays(3),
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.insurance.index'));

    $response->assertStatus(200);
    $response->assertSee('INS-TEST1234');
    $response->assertSee('Khách Hàng Test');
    $response->assertSee('Tiêu chuẩn');
});

it('allows admin to update insurance registration status', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $reg = InsuranceRegistration::create([
        'registration_code' => 'INS-STATUS1',
        'fullname' => 'Khách Hàng Status',
        'phone' => '0912345678',
        'email' => 'status@test.com',
        'package_code' => 'co_ban',
        'package_name' => 'Cơ bản',
        'price_per_day' => 99000,
        'total_days' => 2,
        'total_price' => 198000,
        'start_date' => now()->addDay(),
        'end_date' => now()->addDays(2),
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.insurance.update_status', $reg->id), [
        'status' => 'confirmed',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('insurance_registrations', [
        'id' => $reg->id,
        'status' => 'confirmed',
    ]);
});
