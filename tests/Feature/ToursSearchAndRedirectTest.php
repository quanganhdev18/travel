<?php

test('tours search route redirects to tours index page with query parameters', function () {
    $response = $this->get('/tours/search?keyword=hanoi&date=2026-08-01');

    $response->assertRedirect('/tour-tron-goi?keyword=hanoi&date=2026-08-01');
});

test('tours index page is accessible', function () {
    $response = $this->get('/tour-tron-goi');

    $response->assertOk();
    $response->assertViewIs('frontend.tours.index');
});
