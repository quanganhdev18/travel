<?php

use App\Models\Destination;
use App\Models\Tour;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Hash;

test('profile page displays authenticated user details', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '123456789',
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/user/profile');

    $response->assertOk();
    $response->assertSee('John Doe');
    $response->assertSee('john@example.com');
    $response->assertSee('123456789');
});

test('profile information can be updated through user controller', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '123456789',
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/user/profile', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '987654321',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $user->refresh();
    $this->assertSame('Jane Doe', $user->name);
    $this->assertSame('jane@example.com', $user->email);
    $this->assertSame('987654321', $user->phone);
});

test('user password can be changed', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/user/password', [
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $user->refresh();
    $this->assertTrue(Hash::check('new-password-123', $user->password));
});

test('user password change fails with wrong current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/user/profile')
        ->post('/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

    $response->assertRedirect('/user/profile');
    $response->assertSessionHasErrors(['current_password']);

    $user->refresh();
    $this->assertTrue(Hash::check('old-password', $user->password));
});

test('user can view their wishlists', function () {
    $user = User::factory()->create();
    $dest = Destination::create([
        'name' => 'Test Destination',
        'description' => 'Test desc',
    ]);
    $tour = Tour::create([
        'destination_id' => $dest->id,
        'title' => 'Test Tour Name',
        'slug' => 'test-tour-name',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 1000000,
    ]);

    Wishlist::create([
        'user_id' => $user->id,
        'tour_id' => $tour->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/my-wishlists');

    $response->assertOk();
    $response->assertSee('Test Tour Name');
});

test('user can toggle a tour in their wishlist', function () {
    $user = User::factory()->create();
    $dest = Destination::create([
        'name' => 'Test Destination',
        'description' => 'Test desc',
    ]);
    $tour = Tour::create([
        'destination_id' => $dest->id,
        'title' => 'Test Tour Name',
        'slug' => 'test-tour-name',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 1000000,
    ]);

    // First toggle: Adds to wishlist
    $response = $this
        ->actingAs($user)
        ->post('/wishlists/toggle', [
            'tour_id' => $tour->id,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('wishlists', [
        'user_id' => $user->id,
        'tour_id' => $tour->id,
    ]);

    // Second toggle: Removes from wishlist
    $response = $this
        ->actingAs($user)
        ->post('/wishlists/toggle', [
            'tour_id' => $tour->id,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseMissing('wishlists', [
        'user_id' => $user->id,
        'tour_id' => $tour->id,
    ]);
});

test('user can remove a tour from wishlist', function () {
    $user = User::factory()->create();
    $dest = Destination::create([
        'name' => 'Test Destination',
        'description' => 'Test desc',
    ]);
    $tour = Tour::create([
        'destination_id' => $dest->id,
        'title' => 'Test Tour Name',
        'slug' => 'test-tour-name',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 1000000,
    ]);

    Wishlist::create([
        'user_id' => $user->id,
        'tour_id' => $tour->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/wishlists/remove', [
            'tour_id' => $tour->id,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseMissing('wishlists', [
        'user_id' => $user->id,
        'tour_id' => $tour->id,
    ]);
});
