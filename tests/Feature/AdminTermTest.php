<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTransactions::class);

function termTestUser(string $role): User
{
    $id = DB::table('users')->insertGetId([
        'name' => ucfirst($role).' Term Test',
        'email' => $role.'-term-test@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::findOrFail($id);
}

function termTestCourse(string $number): int
{
    return DB::table('courses')->insertGetId([
        'courseNumber' => $number,
        'name' => $number.' Course',
        'credits' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function termTestCreateTerm(int $courseId, string $quarterYear = 'Fall 2026'): int
{
    return DB::table('terms')->insertGetId([
        'quarterYear' => $quarterYear,
        'course' => $courseId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

test('only admins can open the term forms', function (string $role) {
    $course = termTestCourse('TERM 101');

    $this->actingAs(termTestUser($role))
        ->get('/admin/terms/create')
        ->assertForbidden();

    $this->post('/admin/terms', [
        'quarterYear' => 'Fall 2026',
        'course' => $course,
    ])->assertForbidden();

    expect(DB::table('terms')->where('quarterYear', 'Fall 2026')->exists())
        ->toBeFalse();
})->with(['student', 'advisor']);

test('guests are sent to login from the term forms', function () {
    $this->get('/admin/terms/create')
        ->assertRedirect('/login');

    $this->get('/admin/terms')
        ->assertRedirect('/login');
});

test('an admin sees every course on the term create form', function () {
    termTestCourse('TERM 101');
    termTestCourse('TERM 102');

    $this->actingAs(termTestUser('admin'))
        ->get('/admin/terms/create')
        ->assertOk()
        ->assertSee('TERM 101')
        ->assertSee('TERM 102');
});

test('an admin can create a term', function () {
    $course = termTestCourse('TERM 101');

    $this->actingAs(termTestUser('admin'))
        ->post('/admin/terms', [
            'quarterYear' => 'Fall 2026',
            'course' => $course,
        ])
        ->assertRedirect('/admin/terms')
        ->assertSessionHas('success');

    expect(DB::table('terms')
        ->where('quarterYear', 'Fall 2026')
        ->where('course', $course)
        ->exists())->toBeTrue();
});

test('a term requires a quarter year and a course', function () {
    $this->actingAs(termTestUser('admin'))
        ->post('/admin/terms', [
            'quarterYear' => '',
            'course' => '',
        ])
        ->assertSessionHasErrors(['quarterYear', 'course']);
});

test('a term cannot use a course that does not exist', function () {
    $this->actingAs(termTestUser('admin'))
        ->post('/admin/terms', [
            'quarterYear' => 'Fall 2026',
            'course' => 999999,
        ])
        ->assertSessionHasErrors('course');
});

test('an admin can edit a term', function () {
    $course = termTestCourse('TERM 101');
    $newCourse = termTestCourse('TERM 102');
    $term = termTestCreateTerm($course, 'Fall 2026');

    $this->actingAs(termTestUser('admin'))
        ->get("/admin/terms/{$term}/edit")
        ->assertOk()
        ->assertSee('Fall 2026')
        ->assertSee('TERM 101');

    $this->put("/admin/terms/{$term}", [
        'quarterYear' => 'Winter 2027',
        'course' => $newCourse,
    ])
        ->assertRedirect('/admin/terms')
        ->assertSessionHas('success');

    expect(DB::table('terms')->where('id', $term)->value('quarterYear'))
        ->toBe('Winter 2027');

    expect(DB::table('terms')->where('id', $term)->value('course'))
        ->toBe($newCourse);
});

test('only admins can edit a term', function (string $role) {
    $course = termTestCourse('TERM 101');
    $term = termTestCreateTerm($course);

    $this->actingAs(termTestUser($role))
        ->put("/admin/terms/{$term}", [
            'quarterYear' => 'Changed Term',
            'course' => $course,
        ])
        ->assertForbidden();

    expect(DB::table('terms')->where('id', $term)->value('quarterYear'))
        ->toBe('Fall 2026');
})->with(['student', 'advisor']);

test('an admin can delete a term', function () {
    $course = termTestCourse('TERM 101');
    $term = termTestCreateTerm($course);

    $this->actingAs(termTestUser('admin'))
        ->delete("/admin/terms/{$term}")
        ->assertRedirect('/admin/terms')
        ->assertSessionHas('success');

    expect(DB::table('terms')->where('id', $term)->exists())
        ->toBeFalse();
});
