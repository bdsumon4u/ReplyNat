<?php

test('privacy policy page renders successfully', function () {
    $response = $this->get('/privacy-policy');

    $response->assertStatus(200);
    $response->assertSee('Privacy Policy');
    $response->assertSee('support@replynat.com');
});

test('terms of service page renders successfully', function () {
    $response = $this->get('/terms-of-service');

    $response->assertStatus(200);
    $response->assertSee('Terms of Service');
    $response->assertSee('support@replynat.com');
});

test('user data deletion instructions page renders successfully', function () {
    $response = $this->get('/user-data-deletion');

    $response->assertStatus(200);
    $response->assertSee('User Data Deletion Instructions');
    $response->assertSee('support@replynat.com');
});
