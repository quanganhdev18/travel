<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');
});

test('email availability can be checked', function () {
    $user = User::factory()->create(['email' => 'taken@example.com']);

    // Check taken email
    $response = $this->get(route('api.check-email', ['email' => 'taken@example.com']));
    $response->assertOk();
    $response->assertJson(['exists' => true]);

    // Check free email
    $response = $this->get(route('api.check-email', ['email' => 'free@example.com']));
    $response->assertOk();
    $response->assertJson(['exists' => false]);
});
