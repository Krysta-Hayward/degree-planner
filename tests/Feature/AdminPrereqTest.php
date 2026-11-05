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
        'name' => ucfirst($role) . ' Prerequisite Test',
        'email' => $role . '-' . Str::random(10) . '@example.com',
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
    $number = 'PRQ' . strtoupper(Str::random(6));

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
            'prereqs' => [$prereq],
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
            'prereqs' => [$newPrereq],
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
        'prereqs' => [2],
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
        ->assertSee('Available Courses')
        ->assertSee('Required Prerequisites');
});

// CREATE TESTS

test('an admin can create a prerequisite relationship', function () {
    $course = prereqTestCourse('Advanced Test Course');
    $prereq = prereqTestCourse('Introductory Test Course');

    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => $course,
            'prereqs' => [$prereq],
        ])
        ->assertRedirect('/admin/prereqs')
        ->assertSessionHas('success');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $prereq)
        ->exists())->toBeTrue();
});

test('an admin can assign multiple prerequisites to a course', function () {
    $course = prereqTestCourse('Advanced Test Course');
    $prereq1 = prereqTestCourse('First Introductory Course');
    $prereq2 = prereqTestCourse('Second Introductory Course');

    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => $course,
            'prereqs' => [$prereq1, $prereq2],
        ])
        ->assertRedirect('/admin/prereqs')
        ->assertSessionHas('success');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->count())->toBe(2);

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $prereq1)
        ->exists())->toBeTrue();

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $prereq2)
        ->exists())->toBeTrue();
});

test('a prerequisite requires a course and at least one prerequisite', function () {
    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => '',
            'prereqs' => [],
        ])
        ->assertSessionHasErrors(['course', 'prereqs']);
});

test('a prerequisite must reference existing courses', function () {
    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => 999999,
            'prereqs' => [999998],
        ])
        ->assertSessionHasErrors(['course', 'prereqs.0']);
});

test('a course cannot be its own prerequisite', function () {
    $course = prereqTestCourse();

    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => $course,
            'prereqs' => [$course],
        ])
        ->assertSessionHasErrors('prereqs.0');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $course)
        ->exists())->toBeFalse();
});

test('a prerequisite cannot be selected more than once', function () {
    $course = prereqTestCourse();
    $prereq = prereqTestCourse();

    $this->actingAs(prereqTestUser('admin'))
        ->post('/admin/prereqs', [
            'course' => $course,
            'prereqs' => [$prereq, $prereq],
        ])
        ->assertSessionHasErrors('prereqs.1');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->count())->toBe(0);
});

// EDIT TESTS

test('an admin can edit a course prerequisite list', function () {
    $course = prereqTestCourse('Advanced Test Course');
    $prereq = prereqTestCourse('Original Prerequisite');
    $newPrereq = prereqTestCourse('Updated Prerequisite');

    $relationship = prereqTestCreateRelationship($course, $prereq);

    $this->actingAs(prereqTestUser('admin'))
        ->get("/admin/prereqs/{$relationship}/edit")
        ->assertOk()
        ->assertSee('Edit Prerequisites for:');

    $this->put("/admin/prereqs/{$relationship}", [
        'prereqs' => [$newPrereq],
    ])
        ->assertRedirect('/admin/prereqs')
        ->assertSessionHas('success');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $prereq)
        ->exists())->toBeFalse();

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $newPrereq)
        ->exists())->toBeTrue();
});

test('an admin can replace multiple prerequisites when editing a course', function () {
    $course = prereqTestCourse();
    $prereq1 = prereqTestCourse('First Original Prerequisite');
    $prereq2 = prereqTestCourse('Second Original Prerequisite');
    $newPrereq = prereqTestCourse('New Prerequisite');

    $relationship = prereqTestCreateRelationship($course, $prereq1);
    prereqTestCreateRelationship($course, $prereq2);

    $this->actingAs(prereqTestUser('admin'))
        ->put("/admin/prereqs/{$relationship}", [
            'prereqs' => [$newPrereq],
        ])
        ->assertRedirect('/admin/prereqs')
        ->assertSessionHas('success');

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->count())->toBe(1);

    expect(DB::table('course_prerequisites')
        ->where('course', $course)
        ->where('prereq', $newPrereq)
        ->exists())->toBeTrue();
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
