<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// tests use the real sqlite file, so every test rolls back
uses(DatabaseTransactions::class);

function makeUser(string $role, string $email, ?string $program = null): User
{
    $id = DB::table('users')->insertGetId([
        'name' => ucfirst($role).' Test',
        'email' => $email,
        'password' => Hash::make('password'),
        'role' => $role,
        'program' => $program,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::findOrFail($id);
}

function makeCourse(string $number, string $name = 'Test Course'): int
{
    return DB::table('courses')->insertGetId([
        'courseNumber' => $number,
        'name' => $name,
        'credits' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function makeTerm(int $course, string $quarter = 'Fall 2026'): int
{
    return DB::table('terms')->insertGetId([
        'quarterYear' => $quarter,
        'course' => $course,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function makeProgram(string $name, int $course): void
{
    DB::table('programs')->insert([
        'name' => $name,
        'course' => $course,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

test('guests are sent to login', function () {
    $this->get('/student')->assertRedirect('/login');
});

test('admins cannot open the student page', function () {
    $this->actingAs(makeUser('admin', 'admin-test@example.com'))
        ->get('/student')
        ->assertForbidden();
});

test('a student logging in lands on their degree plan', function () {
    makeUser('student', 'student-test@example.com');

    $this->post('/login', ['email' => 'student-test@example.com', 'password' => 'password'])
        ->assertRedirect(route('student.dashboard'));
});

test('a student with no program is asked to pick one', function () {
    $this->actingAs(makeUser('student', 'student-test@example.com'))
        ->get('/student')
        ->assertOk()
        ->assertSee('You have not picked a program yet.');
});

test('a student can pick a program', function () {
    $course = makeCourse('TEST 101');
    makeProgram('Test Program', $course);
    $student = makeUser('student', 'student-test@example.com');

    $this->actingAs($student)
        ->post('/student/program', ['program' => 'Test Program'])
        ->assertRedirect(route('student.dashboard'));

    expect($student->fresh()->program)->toBe('Test Program');
});

test('an unknown program is rejected', function () {
    $this->actingAs(makeUser('student', 'student-test@example.com'))
        ->post('/student/program', ['program' => 'Nope'])
        ->assertSessionHasErrors('program');
});

test('the dashboard shows required courses and prerequisites', function () {
    $intro = makeCourse('TEST 101', 'Intro Testing');
    $next = makeCourse('TEST 201', 'Advanced Testing');
    makeTerm($next);
    makeProgram('Test Program', $intro);
    makeProgram('Test Program', $next);
    DB::table('course_prerequisites')->insert([
        'course' => $next,
        'prereq' => $intro,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs(makeUser('student', 'student-test@example.com', 'Test Program'))
        ->get('/student')
        ->assertOk()
        ->assertSee('Advanced Testing')
        ->assertSee('TEST 101')
        ->assertSee('Fall 2026');
});

test('a student can add a class to their program map', function () {
    $course = makeCourse('TEST 101');
    $term = makeTerm($course);
    makeProgram('Test Program', $course);
    $student = makeUser('student', 'student-test@example.com', 'Test Program');

    $this->actingAs($student)->post('/student/plan', ['term' => $term])->assertRedirect();
    // adding twice must not duplicate
    $this->actingAs($student)->post('/student/plan', ['term' => $term])->assertRedirect();

    expect(DB::table('plans')->where('student', $student->id)->where('class', $term)->count())->toBe(1);
});

test('a class outside the student program cannot be added', function () {
    $mine = makeCourse('TEST 101');
    $other = makeCourse('OTHR 101');
    makeProgram('Test Program', $mine);
    $term = makeTerm($other);
    $student = makeUser('student', 'student-test@example.com', 'Test Program');

    $this->actingAs($student)->post('/student/plan', ['term' => $term])->assertSessionHasErrors('term');

    expect(DB::table('plans')->where('student', $student->id)->count())->toBe(0);
});

test('a student can remove a class from their program map', function () {
    $course = makeCourse('TEST 101');
    $term = makeTerm($course);
    makeProgram('Test Program', $course);
    $student = makeUser('student', 'student-test@example.com', 'Test Program');
    $plan = DB::table('plans')->insertGetId([
        'student' => $student->id,
        'class' => $term,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($student)->delete("/student/plan/{$plan}")->assertRedirect();

    expect(DB::table('plans')->where('id', $plan)->exists())->toBeFalse();
});

test('a student cannot remove another student plan', function () {
    $course = makeCourse('TEST 101');
    $term = makeTerm($course);
    $owner = makeUser('student', 'owner-test@example.com', 'Test Program');
    $intruder = makeUser('student', 'intruder-test@example.com', 'Test Program');
    $plan = DB::table('plans')->insertGetId([
        'student' => $owner->id,
        'class' => $term,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($intruder)->delete("/student/plan/{$plan}")->assertForbidden();

    expect(DB::table('plans')->where('id', $plan)->exists())->toBeTrue();
});

test('a student with no program still sees the course catalog', function () {
    makeCourse('CATA 101', 'Catalog Course');

    $this->actingAs(makeUser('student', 'student-test@example.com'))
        ->get('/student')
        ->assertOk()
        ->assertSee('Catalog Course');
});

test('a student cannot see another student program map', function () {
    $course = makeCourse('SECR 101', 'Secret Course');
    $term = makeTerm($course, 'Winter 2099');
    $owner = makeUser('student', 'owner-test@example.com', 'Test Program');
    $viewer = makeUser('student', 'viewer-test@example.com', 'Test Program');
    DB::table('plans')->insert([
        'student' => $owner->id,
        'class' => $term,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // the term only appears in the catalog's "Terms Offered", never in a plan row with a Remove button
    $this->actingAs($viewer)
        ->get('/student')
        ->assertOk()
        ->assertSee('Your program map is empty');
});
