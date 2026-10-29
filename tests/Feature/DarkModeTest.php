<?php

test('the home page has the dark mode toggle and theme script', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('theme-toggle', false);
    $response->assertSee('js/theme.js', false);
});

test('the login page has the dark mode toggle and theme script', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('theme-toggle', false);
    $response->assertSee('js/theme.js', false);
});

test('the theme script and stylesheet exist', function () {
    expect(file_exists(public_path('js/theme.js')))->toBeTrue();
    expect(file_get_contents(public_path('css/app.css')))->toContain('[data-theme="dark"]');
});
