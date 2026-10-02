<?php

test('health endpoint reports app up', function () {
    $response = $this->get('/health');

    $response->assertOk();
    $response->assertJson([
        'app' => 'up',
    ]);
});
