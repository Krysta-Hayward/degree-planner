<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

// Create a test user with the specified role.
function adminUserTestUser(string $role): User
{
    $id = DB::table('users')->insertGetId([
        'name' => ucfirst($role).' Test User',
        'email' => $role.'-'.Str::random(10).'@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::findOrFail($id);
}

// AUTHORIZATION TESTS

test('only admins can access user management', function (string $role) {
    $user = adminUserTestUser('student');

    $this->actingAs(adminUserTestUser($role))
        ->get('/admin/users')
        ->assertForbidden();

    $this->get('/admin/users/create')
        ->assertForbidden();

    $this->post('/admin/users', [
        'name' => 'Unauthorized User',
        'email' => 'unauthorized@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'student',
    ])->assertForbidden();

    $this->put("/admin/users/{$user->id}", [
        'name' => 'Changed Name',
        'email' => $user->email,
        'role' => 'student',
    ])->assertForbidden();

    $this->delete("/admin/users/{$user->id}")
        ->assertForbidden();

    expect(User::find($user->id))->not->toBeNull();
})->with(['student', 'advisor']);

test('guests are redirected to login from user management pages', function () {
    $this->get('/admin/users')
        ->assertRedirect('/login');

    $this->get('/admin/users/create')
        ->assertRedirect('/login');
});

// VIEW TESTS

test('an admin can view the user list', function () {
    $this->actingAs(adminUserTestUser('admin'))
        ->get('/admin/users')
        ->assertOk()
        ->assertSee('Manage Users')
        ->assertSee('Create User');
});

test('an admin can open the create user form', function () {
    $this->actingAs(adminUserTestUser('admin'))
        ->get('/admin/users/create')
        ->assertOk();
});

test('an admin can open the edit user form', function () {
    $user = adminUserTestUser('student');

    $this->actingAs(adminUserTestUser('admin'))
        ->get("/admin/users/{$user->id}/edit")
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($user->email);
});

// CREATE TESTS

test('an admin can create a user', function () {
    $this->actingAs(adminUserTestUser('admin'))
        ->post('/admin/users', [
            'name' => 'New Test Student',
            'email' => 'newstudent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success');

    $user = User::where('email', 'newstudent@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('New Test Student');
    expect($user->role)->toBe('student');
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('a user requires a name, email, password, and valid role', function () {
    $this->actingAs(adminUserTestUser('admin'))
        ->post('/admin/users', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'role' => 'invalid',
        ])
        ->assertSessionHasErrors([
            'name',
            'email',
            'password',
            'role',
        ]);
});

test('a user email address cannot be reused', function () {
    $existingUser = adminUserTestUser('student');

    $this->actingAs(adminUserTestUser('admin'))
        ->post('/admin/users', [
            'name' => 'Duplicate User',
            'email' => $existingUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ])
        ->assertSessionHasErrors('email');
});

// EDIT TESTS

test('an admin can update a user without changing their password', function () {
    $user = adminUserTestUser('student');
    $originalPassword = $user->password;

    $this->actingAs(adminUserTestUser('admin'))
        ->put("/admin/users/{$user->id}", [
            'name' => 'Updated Student',
            'email' => 'updatedstudent@example.com',
            'role' => 'advisor',
        ])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success');

    $updated = User::findOrFail($user->id);

    expect($updated->name)->toBe('Updated Student');
    expect($updated->email)->toBe('updatedstudent@example.com');
    expect($updated->role)->toBe('advisor');
    expect($updated->password)->toBe($originalPassword);
});

test('an admin can change a user password', function () {
    $user = adminUserTestUser('student');

    $this->actingAs(adminUserTestUser('admin'))
        ->put("/admin/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'student',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success');

    $updated = User::findOrFail($user->id);

    expect(Hash::check('newpassword123', $updated->password))->toBeTrue();
});

test('a user email cannot be changed to another users email', function () {
    $user = adminUserTestUser('student');
    $otherUser = adminUserTestUser('advisor');

    $this->actingAs(adminUserTestUser('admin'))
        ->put("/admin/users/{$user->id}", [
            'name' => $user->name,
            'email' => $otherUser->email,
            'role' => 'student',
        ])
        ->assertSessionHasErrors('email');
});

test('an admin cannot remove their own admin role', function () {
    $admin = adminUserTestUser('admin');

    $this->actingAs($admin)
        ->put("/admin/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'student',
        ])
        ->assertRedirect('/')
        ->assertSessionHas('error');

    expect(User::findOrFail($admin->id)->role)->toBe('admin');
});

// DELETE TESTS

test('an admin can delete another user', function () {
    $user = adminUserTestUser('student');

    $this->actingAs(adminUserTestUser('admin'))
        ->delete("/admin/users/{$user->id}")
        ->assertRedirect('/admin/users')
        ->assertSessionHas('success');

    expect(User::find($user->id))->toBeNull();
});

test('an admin cannot delete their own account', function () {
    $admin = adminUserTestUser('admin');

    $this->actingAs($admin)
        ->delete("/admin/users/{$admin->id}")
        ->assertRedirect('/')
        ->assertSessionHas('error');

    expect(User::find($admin->id))->not->toBeNull();
});

test('the last admin cannot be deleted', function () {
    $admin = adminUserTestUser('admin');

    $this->actingAs($admin)
        ->delete("/admin/users/{$admin->id}")
        ->assertRedirect('/')
        ->assertSessionHas('error');

    expect(User::findOrFail($admin->id)->role)->toBe('admin');
});

test('the last admin cannot be changed to another role', function () {
    $admin = adminUserTestUser('admin');

    $this->actingAs($admin)
        ->put("/admin/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'advisor',
        ])
        ->assertRedirect('/')
        ->assertSessionHas('error');

    expect(User::findOrFail($admin->id)->role)->toBe('admin');
});
