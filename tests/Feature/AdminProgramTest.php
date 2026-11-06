<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// tests use the real sqlite file, so every test rolls back
uses(DatabaseTransactions::class);

function programTestUser(string $role, ?string $program = null): User
{
    $id = DB::table('users')->insertGetId([
        'name' => ucfirst($role).' Program Test',
        'email' => $role.'-program-test@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
        'program' => $program,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::findOrFail($id);
}

function programTestCourse(string $number): int
{
    return DB::table('courses')->insertGetId([
        'courseNumber' => $number,
        'name' => $number.' Course',
        'credits' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

test('only admins can open the program forms', function (string $role) {
    $this->actingAs(programTestUser($role))
        ->get('/admin/programs/create')
        ->assertForbidden();

    $this->post('/admin/programs', ['name' => 'Sneaky Program'])
        ->assertForbidden();

    expect(DB::table('program_details')->where('name', 'Sneaky Program')->exists())->toBeFalse();
})->with(['student', 'advisor']);

test('guests are sent to login from the program forms', function () {
    $this->get('/admin/programs/create')->assertRedirect('/login');
});

test('an admin sees every course on the create form', function () {
    programTestCourse('PGM 101');
    programTestCourse('PGM 102');

    $this->actingAs(programTestUser('admin'))
        ->get('/admin/programs/create')
        ->assertOk()
        ->assertSee('PGM 101')
        ->assertSee('PGM 102');
});

test('an admin can create a program with a description and requirement groups', function () {
    $core = programTestCourse('PGM 101');
    $elective = programTestCourse('PGM 201');
    $unused = programTestCourse('PGM 301');

    $this->actingAs(programTestUser('admin'))
        ->post('/admin/programs', [
            'name' => 'Test Program AAS',
            'description' => 'A two year test program.',
            'courses' => [$core, $elective],
            'groups' => [$core => '', $elective => 'Electives', $unused => 'Electives'],
        ])
        ->assertRedirect('/admin/programs/Test%20Program%20AAS')
        ->assertSessionHas('success');

    expect(DB::table('program_details')->where('name', 'Test Program AAS')->value('description'))
        ->toBe('A two year test program.');

    $rows = DB::table('programs')->where('name', 'Test Program AAS')->pluck('requirement_group', 'course');

    expect($rows->all())->toBe([$core => 'Core', $elective => 'Electives']);
});

test('a program needs a name and at least one course', function () {
    $this->actingAs(programTestUser('admin'))
        ->post('/admin/programs', ['name' => '', 'courses' => []])
        ->assertSessionHasErrors(['name', 'courses']);
});

test('a program name cannot be reused', function () {
    $course = programTestCourse('PGM 101');

    // a program that only exists as course rows, like the seeded ones
    DB::table('programs')->insert(['name' => 'Seeded Program', 'course' => $course]);

    $this->actingAs(programTestUser('admin'))
        ->post('/admin/programs', ['name' => 'Seeded Program', 'courses' => [$course]])
        ->assertSessionHasErrors('name');
});

test('the program list and details show the description and groups', function () {
    $core = programTestCourse('PGM 101');
    $elective = programTestCourse('PGM 201');
    $admin = programTestUser('admin');

    $this->actingAs($admin)->post('/admin/programs', [
        'name' => 'Shown Program',
        'description' => 'Shown description.',
        'courses' => [$core, $elective],
        'groups' => [$elective => 'Electives'],
    ]);

    $this->get('/admin/programs')
        ->assertOk()
        ->assertSee('Shown Program')
        ->assertSee('Shown description.');

    $this->get('/admin/programs/Shown Program')
        ->assertOk()
        ->assertSeeInOrder(['Core', 'PGM 101', 'Electives', 'PGM 201']);
});

test('an admin can edit a program and its requirements', function () {
    $keep = programTestCourse('PGM 101');
    $drop = programTestCourse('PGM 102');
    $add = programTestCourse('PGM 103');
    $admin = programTestUser('admin');

    $this->actingAs($admin)->post('/admin/programs', [
        'name' => 'Edit Program',
        'description' => 'Old description.',
        'courses' => [$keep, $drop],
    ]);

    $this->get('/admin/programs/Edit Program/edit')
        ->assertOk()
        ->assertSee('Old description.');

    $this->put('/admin/programs/Edit Program', [
        'name' => 'Edit Program',
        'description' => 'New description.',
        'courses' => [$keep, $add],
        'groups' => [$keep => 'Electives', $add => 'Core'],
    ])->assertRedirect('/admin/programs/Edit%20Program');

    expect(DB::table('program_details')->where('name', 'Edit Program')->value('description'))
        ->toBe('New description.');

    $rows = DB::table('programs')->where('name', 'Edit Program')->orderBy('course')->pluck('requirement_group', 'course');

    expect($rows->all())->toBe([$keep => 'Electives', $add => 'Core']);
});

test('renaming a program keeps students on it', function () {
    $course = programTestCourse('PGM 101');
    $admin = programTestUser('admin');
    $student = programTestUser('student', 'Old Name');

    DB::table('programs')->insert(['name' => 'Old Name', 'course' => $course]);

    // a program from before descriptions existed can still be edited
    $this->actingAs($admin)->put('/admin/programs/Old Name', [
        'name' => 'New Name',
        'description' => 'Renamed.',
        'courses' => [$course],
    ])->assertRedirect('/admin/programs/New%20Name');

    expect($student->fresh()->program)->toBe('New Name');
    expect(DB::table('programs')->where('name', 'Old Name')->exists())->toBeFalse();
    expect(DB::table('program_details')->where('name', 'New Name')->value('description'))->toBe('Renamed.');
});

test('only admins can edit a program', function () {
    $course = programTestCourse('PGM 101');
    DB::table('programs')->insert(['name' => 'Locked Program', 'course' => $course]);

    $this->actingAs(programTestUser('advisor'))
        ->put('/admin/programs/Locked Program', ['name' => 'Hacked', 'courses' => [$course]])
        ->assertForbidden();

    expect(DB::table('programs')->where('name', 'Locked Program')->exists())->toBeTrue();
});

test('a program an admin creates shows up for students', function () {
    $course = programTestCourse('PGM 101');

    $this->actingAs(programTestUser('admin'))->post('/admin/programs', [
        'name' => 'Student Visible Program',
        'courses' => [$course],
    ]);

    $this->actingAs(programTestUser('student'))
        ->get('/student')
        ->assertOk()
        ->assertSee('Student Visible Program');
});

test('the program list counts programs, not course rows', function () {
    $first = programTestCourse('PGM 101');
    $second = programTestCourse('PGM 102');

    DB::table('programs')->insert([
        ['name' => 'Count Program', 'course' => $first],
        ['name' => 'Count Program', 'course' => $second],
    ]);

    $expected = DB::table('programs')->distinct()->count('name');

    $this->actingAs(programTestUser('admin'))
        ->get('/admin/programs')
        ->assertOk()
        ->assertViewHas('programs', fn ($programs) => $programs->total() === $expected);
});
