<?php

test('the landing page renders successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('ReplyNat');
    $response->assertSee('Conversation');
    $response->assertSee('Workflow');
});
