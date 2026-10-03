<?php

test('health endpoint reports app and database up', function () {
    $response = $this->get('/health');

    $response->assertOk();
    $response->assertJson([
        'app' => 'up',
        'database' => 'up',
    ]);
});
