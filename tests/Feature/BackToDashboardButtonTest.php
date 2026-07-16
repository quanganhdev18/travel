<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('guest does not see the back to dashboard button', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertDontSee('btn-back-to-admin');
    $response->assertDontSee('Quay lại Quản trị');
});

test('regular user does not see the back to dashboard button', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    $response->assertDontSee('btn-back-to-admin');
    $response->assertDontSee('Quay lại Quản trị');
});

test('admin user sees the back to admin dashboard button', function () {
    Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    $response = $this->actingAs($admin)->get('/');

    $response->assertStatus(200);
    $response->assertSee('btn-back-to-admin');
    $response->assertSee('Quay lại Quản trị');
    $response->assertSee(route('admin.dashboard'));
});

test('guide sees the back to guide dashboard button', function () {
    $guide = User::factory()->create(['role' => 'guide']);

    $response = $this->actingAs($guide)->get('/');

    $response->assertStatus(200);
    $response->assertSee('btn-back-to-admin');
    $response->assertSee('Quay lại HDV');
    $response->assertSee(route('guide.dashboard'));
});
