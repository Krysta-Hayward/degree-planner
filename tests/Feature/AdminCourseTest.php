<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTransactions::class);

function courseTestUser(string $role): User
{
    $id = DB::table('users')->insertGetId([
        'name' => ucfirst($role).' Course Test',
        'email' => $role.'-course-test@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::findOrFail($id);
}

function courseTestCreateCourse(
    string $number,
    string $name = 'Test Course',
    int $credits = 5
): int {
    return DB::table('courses')->insertGetId([
        'courseNumber' => $number,
        'name' => $name,
        'credits' => $credits,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

test('only admins can open the course forms', function (string $role) {
    $this->actingAs(courseTestUser($role))
        ->get('/admin/courses/create')
        ->assertForbidden();

    $this->post('/admin/courses', [
        'courseNumber' => 'TEST 101',
        'name' => 'Test Course',
        'credits' => 5,
    ])->assertForbidden();

    expect(DB::table('courses')->where('courseNumber', 'TEST 101')->exists())
        ->toBeFalse();
})->with(['student', 'advisor']);

test('guests are sent to login from the course forms', function () {
    $this->get('/admin/courses/create')
        ->assertRedirect('/login');

    $this->get('/admin/courses')
        ->assertRedirect('/login');
});

test('an admin can view the course list', function () {
    courseTestCreateCourse('TEST 101', 'Introduction to Testing');

    $this->actingAs(courseTestUser('admin'))
        ->get('/admin/courses')
        ->assertOk()
        ->assertSee('Manage Courses')
        ->assertSee('Total courses:')
        ->assertSee('Create Course');
});

test('an admin can open the course create form', function () {
    $this->actingAs(courseTestUser('admin'))
        ->get('/admin/courses/create')
        ->assertOk();
});

test('an admin can create a course', function () {
    $this->actingAs(courseTestUser('admin'))
        ->post('/admin/courses', [
            'courseNumber' => 'TEST 101',
            'name' => 'Introduction to Testing',
            'credits' => 5,
        ])
        ->assertRedirect('/admin/courses')
        ->assertSessionHas('success');

    $course = DB::table('courses')
        ->where('courseNumber', 'TEST 101')
        ->first();

    expect($course)->not->toBeNull();
    expect($course->name)->toBe('Introduction to Testing');
    expect((int) $course->credits)->toBe(5);
});

test('a course requires a course number, name, and credits', function () {
    $this->actingAs(courseTestUser('admin'))
        ->post('/admin/courses', [
            'courseNumber' => '',
            'name' => '',
            'credits' => '',
        ])
        ->assertSessionHasErrors([
            'courseNumber',
            'name',
            'credits',
        ]);
});

test('a course number cannot be reused', function () {
    courseTestCreateCourse('TEST 101');

    $this->actingAs(courseTestUser('admin'))
        ->post('/admin/courses', [
            'courseNumber' => 'TEST 101',
            'name' => 'Duplicate Course',
            'credits' => 5,
        ])
        ->assertSessionHasErrors('courseNumber');
});

test('course credits cannot be negative', function () {
    $this->actingAs(courseTestUser('admin'))
        ->post('/admin/courses', [
            'courseNumber' => 'TEST 101',
            'name' => 'Invalid Credits Course',
            'credits' => -5,
        ])
        ->assertSessionHasErrors('credits');
});

test('an admin can edit a course', function () {
    $course = courseTestCreateCourse(
        'TEST 101',
        'Old Course Name',
        5
    );

    $this->actingAs(courseTestUser('admin'))
        ->get("/admin/courses/{$course}/edit")
        ->assertOk()
        ->assertSee('Old Course Name');

    $this->put("/admin/courses/{$course}", [
        'courseNumber' => 'TEST 102',
        'name' => 'Updated Course Name',
        'credits' => 10,
    ])
        ->assertRedirect('/admin/courses')
        ->assertSessionHas('success');

    $updated = DB::table('courses')
        ->where('id', $course)
        ->first();

    expect($updated->courseNumber)->toBe('TEST 102');
    expect($updated->name)->toBe('Updated Course Name');
    expect((int) $updated->credits)->toBe(10);
});

test('only admins can edit a course', function (string $role) {
    $course = courseTestCreateCourse('TEST 101');

    $this->actingAs(courseTestUser($role))
        ->put("/admin/courses/{$course}", [
            'courseNumber' => 'HACK 101',
            'name' => 'Changed Course',
            'credits' => 5,
        ])
        ->assertForbidden();

    expect(DB::table('courses')->where('id', $course)->value('courseNumber'))
        ->toBe('TEST 101');
})->with(['student', 'advisor']);

test('an admin can delete a course', function () {
    $course = courseTestCreateCourse('TEST 101');

    $this->actingAs(courseTestUser('admin'))
        ->delete("/admin/courses/{$course}")
        ->assertRedirect('/admin/courses')
        ->assertSessionHas('success');

    expect(DB::table('courses')->where('id', $course)->exists())
        ->toBeFalse();
});
