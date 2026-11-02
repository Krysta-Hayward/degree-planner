<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

// Create a test user with the specified role.
function prereqTestUser(string $role): User
{
    $id = DB::table('users')->insertGetId([
        'name' => ucfirst($role).' Prerequisite Test',
        'email' => $role.'-'.Str::random(10).'@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::findOrFail($id);
}

// Create a test course.
function prereqTestCourse(string $name = 'Prerequisite Test Course'): int
{
    $number = 'PRQ'.strtoupper(Str::random(6));

    return DB::table('courses')->insertGetId([
        'courseNumber' => $number,
        'name' => $name,
        'credits' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Create a prerequisite relationship directly in the database.
function prereqTestCreateRelationship(int $course, int $prereq): int
{
    return DB::table('course_prerequisites')->insertGetId([
        'course' => $course,
        'prereq' => $prereq,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// AUTHORIZATION TESTS

test('only admins can open the prerequisite create form', function (string $role) {
    $this->actingAs(prereqTestUser($role))
        ->get('/admin/prereqs/create')
        ->assertForbidden();
})->with(['student', 'advisor']);

test('only admins can create prerequisites', function (string $role) {
    $course = prereqTestCourse();
    $prereq = prereqTestCourse();

    $this->actingAs(prereqTestUser($role))
        ->post('/admin/prereqs', [
            'course' => $course,
            'prereq' => $prereq,
        ])
        ->assertForbidden();

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $prereq)
        ->exists())->toBeFalse();
})->with(['student', 'advisor']);

test('only admins can edit prerequisite relationships', function (string $role) {
    $course = prereqTestCourse();
    $prereq = prereqTestCourse();
    $newPrereq = prereqTestCourse();

    $relationship = prereqTestCreateRelationship($course, $prereq);

    $this->actingAs(prereqTestUser($role))
        ->put("/admin/prereqs/{$relationship}", [
            'course' => $course,
            'prereq' => $newPrereq,
        ])
        ->assertForbidden();

    expect(DB::table('course_prerequisites')
        ->where('id', $relationship)
        ->value('prereq'))->toBe($prereq);
})->with(['student', 'advisor']);

test('only admins can delete prerequisite relationships', function (string $role) {
    $course = prereqTestCourse();
    $prereq = prereqTestCourse();

    $relationship = prereqTestCreateRelationship($course, $prereq);

    $this->actingAs(prereqTestUser($role))
        ->delete("/admin/prereqs/{$relationship}")
        ->assertForbidden();

    expect(DB::table('course_prerequisites')
        ->where('id', $relationship)
        ->exists())->toBeTrue();
})->with(['student', 'advisor']);

test('guests are sent to login from prerequisite pages', function () {
    $this->get('/admin/prereqs')
        ->assertRedirect('/login');

    $this->get('/admin/prereqs/create')
        ->assertRedirect('/login');

    $this->post('/admin/prereqs', [
        'course' => 1,
        'prereq' => 2,
    ])->assertRedirect('/login');
});

// VIEW TESTS

test('an admin can view the prerequisite list', function () {
    $this->actingAs(prereqTestUser('admin'))
        ->get('/admin/prereqs')
        ->assertOk()
        ->assertSee('Manage Prerequisites')
        ->assertSee('Total prerequisite relationships:')
        ->assertSee('Create Prerequisite');
});

test('an admin can open the prerequisite create form', function () {
    $this->actingAs(prereqTestUser('admin'))
        ->get('/admin/prereqs/create')
        ->assertOk()
        ->assertSee('Select a course')
        ->assertSee('Select a prerequisite');
});

// CREATE TESTS

test('an admin can create a prerequisite relationship', function () {
    $course = prereqTestCourse('Advanced Test Course');
    $prereq = prereqTestCourse('Introductory Test Course');

    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => $course,
            'prereq' => $prereq,
        ])
        ->assertRedirect('/admin/prereqs')
        ->assertSessionHas('success');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $prereq)
        ->exists())->toBeTrue();
});

test('a prerequisite requires both courses', function () {
    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => '',
            'prereq' => '',
        ])
        ->assertSessionHasErrors(['course', 'prereq']);
});

test('a prerequisite must reference existing courses', function () {
    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => 999999,
            'prereq' => 999998,
        ])
        ->assertSessionHasErrors(['course', 'prereq']);
});

test('a course cannot be its own prerequisite', function () {
    $course = prereqTestCourse();

    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => $course,
            'prereq' => $course,
        ])
        ->assertSessionHasErrors('course');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $course)
        ->exists())->toBeFalse();
});

test('a prerequisite relationship cannot be duplicated', function () {
    $course = prereqTestCourse();
    $prereq = prereqTestCourse();

    prereqTestCreateRelationship($course, $prereq);

    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => $course,
            'prereq' => $prereq,
        ])
        ->assertSessionHasErrors('course');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $prereq)
        ->count())->toBe(1);
});

// EDIT TESTS

test('an admin can edit a prerequisite relationship', function () {
    $course = prereqTestCourse('Original Course');
    $prereq = prereqTestCourse('Original Prerequisite');
    $newCourse = prereqTestCourse('Updated Course');
    $newPrereq = prereqTestCourse('Updated Prerequisite');

    $relationship = prereqTestCreateRelationship($course, $prereq);

    $this->actingAs(prereqTestUser('admin'))
        ->get("/admin/prereqs/{$relationship}/edit")
        ->assertOk()
        ->assertSee('Edit Prerequisite');

    $this->put("/admin/prereqs/{$relationship}", [
        'course' => $newCourse,
        'prereq' => $newPrereq,
    ])
        ->assertRedirect('/admin/prereqs')
        ->assertSessionHas('success');

    $updated = DB::table('course_prerequisites')
        ->where('id', $relationship)
        ->first();

    expect($updated->course)->toBe($newCourse);
    expect($updated->prereq)->toBe($newPrereq);
});

// DELETE TESTS

test('an admin can delete a prerequisite relationship', function () {
    $course = prereqTestCourse();
    $prereq = prereqTestCourse();

    $relationship = prereqTestCreateRelationship($course, $prereq);

    $this->actingAs(prereqTestUser('admin'))
        ->delete("/admin/prereqs/{$relationship}")
        ->assertRedirect('/admin/prereqs')
        ->assertSessionHas('success');

    expect(DB::table('course_prerequisites')
        ->where('id', $relationship)
        ->exists())->toBeFalse();
});
