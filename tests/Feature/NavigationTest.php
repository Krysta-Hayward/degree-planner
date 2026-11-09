<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// tests use the real sqlite file, so every test rolls back
uses(DatabaseTransactions::class);

function makeNavUser(string $role): User
{
    $id = DB::table('users')->insertGetId([
        'name' => ucfirst($role).' Nav',
        'email' => $role.'-nav-test@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::findOrFail($id);
}

function navLink(string $routeName): string
{
    return 'href="'.route($routeName).'"';
}

test('guests see a log in link and no role links', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(navLink('login'), false)
        ->assertDontSee(navLink('student.dashboard'), false)
        ->assertDontSee(navLink('advisor.dashboard'), false)
        ->assertDontSee(navLink('admin.dashboard'), false);
});

test('students see their own page link and a log out button', function () {
    $this->actingAs(makeNavUser('student'))
        ->get('/')
        ->assertOk()
        ->assertSee(navLink('student.dashboard'), false)
        ->assertSee('Log Out')
        ->assertDontSee(navLink('advisor.dashboard'), false)
        ->assertDontSee(navLink('admin.dashboard'), false);
});

test('advisors see advisor links only', function () {
    $this->actingAs(makeNavUser('advisor'))
        ->get('/')
        ->assertOk()
        ->assertSee(navLink('advisor.dashboard'), false)
        ->assertDontSee(navLink('student.dashboard'), false)
        ->assertDontSee(navLink('admin.dashboard'), false);
});

test('admins see admin links only', function () {
    $this->actingAs(makeNavUser('admin'))
        ->get('/')
        ->assertOk()
        ->assertSee(navLink('admin.dashboard'), false)
        ->assertDontSee(navLink('student.dashboard'), false)
        ->assertDontSee(navLink('advisor.dashboard'), false);
});

test('a user can log out and log back in', function () {
    $student = makeNavUser('student');

    $this->actingAs($student)->post('/logout')->assertRedirect('/');
    $this->assertGuest();

    $this->post('/login', ['email' => $student->email, 'password' => 'password'])
        ->assertRedirect(route('student.dashboard'));
});
