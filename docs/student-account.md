# Student account (issue #30)

Students log in at `/login` and land on `/student` ("My Degree Plan").

## What a student can do

- See every course with its code, title, credits, prerequisites and the terms it is offered.
- Pick a degree program (saved in `users.program`, matches `programs.name`).
- See the required courses for their program, with prerequisites and terms offered.
- Add a term of a required course to their program map (`plans` table: `student` = user id, `class` = terms.id).
- Remove a course from their program map.
- Clear their chosen program (this also clears their program map) and pick again.
- See their total planned credits.

## Rules

- Only users with `role = student` can open `/student` (`StudentMiddleware`). Other roles get a 403, and guests are sent to the login page.
- A student can only change their own plan rows (403 otherwise).
- A student can only add classes that belong to their own program.
- A class can only be added once all of its prerequisites are on the student's program map.
- A class cannot be removed while another planned class needs it as a prerequisite.

## Files

Mostly new files, so this story does not collide with the admin and advisor work:

- `routes/student.php`
- `app/Http/Controllers/StudentController.php`
- `app/Http/Middleware/StudentMiddleware.php`
- `resources/views/student/dashboard.blade.php`
- `public/css/student.css`
- `database/migrations/2026_10_20_170000_add_program_to_users_table.php`
- `tests/Feature/StudentAccountTest.php`

Two shared files get added lines only, nothing existing is changed:

- `routes/web.php`: one `require` line for `routes/student.php`
- `app/Http/Controllers/AuthController.php`: students are redirected to `/student` after login

## Create a student to log in with

Run tinker (`docker compose exec laravel.test php artisan tinker`, or `php artisan tinker` if you run PHP directly):

```php
App\Models\User::forceCreate([
    'name' => 'Student',
    'email' => 'student@example.com',
    'password' => 'Password123!',
    'role' => 'student',
]);

App\Models\User::select('id', 'name', 'email', 'role')->get();
```

The second command lists the users so you can check the student was created. `role` is a required column on `users`, so it has to be set when the row is created, which is why this uses `forceCreate`.

The student also needs courses, programs and terms in the database to have anything to plan with.

## How it works

Request flow: `/login` goes to `AuthController::login`, which checks the password and sends `role = student` users to `/student`. `routes/student.php` wraps every student route in `auth` (must be logged in) and `StudentMiddleware` (must be a student, otherwise 403).

`StudentController`:

- `dashboard` loads the course catalog, prerequisites and terms in three queries (no query per row), then loads only the logged-in student's own `plans` rows.
- `selectProgram` checks the name with `exists:programs,name` and saves it on the user.
- `addToPlan` validates the term, checks it belongs to the student's own program, and uses `firstOrCreate` so a double click cannot create a duplicate row.
- `clearProgram` sets the student's program back to empty and deletes their program map rows. Choosing a different program does the same, since the old plan no longer applies.
- `removeFromPlan` uses route model binding and returns 403 if the plan row belongs to someone else.
- After each action the student is redirected to `/student#student-top`, so the page starts at the top.

Data: `users.program` is the new column. `plans.student` points to `users.id`, `plans.class` points to `terms.id`, and `terms.course` points to `courses.id`. `programs.name` and `programs.course` list a program's required courses, and `course_prerequisites` links a course to its prerequisite.

Security: forms use `@csrf`, Blade `{{ }}` escapes all output, and ownership is checked on the server, not just hidden in the page.

## Tests

`php artisan test` runs `tests/Feature/StudentAccountTest.php`. The tests cover:

- guests and admins being blocked
- login redirect for students
- picking a program (and rejecting an unknown one)
- the catalog and required courses showing correctly
- adding, removing and duplicate-proofing plan rows
- not being able to add a class outside your program
- prerequisites being enforced when adding and removing classes
- not being able to remove, or see, another student's plan

The tests use the real sqlite file with `DatabaseTransactions`, so every test rolls back its data.

## Sprint review order

Matches the acceptance criteria: log in as the student, show the all-courses table, pick a program, show its required courses and prerequisites, add a class to the program map, reload (it is still there), remove it, then show that another student's plan is not visible and run the 403 test.
