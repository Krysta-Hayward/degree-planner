# Student account (issue #30)

Students log in at `/login` and land on `/student` ("My Degree Plan").

What a student can do:

- Pick a degree program (saved in `users.program`, matches `programs.name`).
- See the required courses for the program, with prerequisites and the terms each is offered.
- Add a term of a required course to their program map (`plans` table: `student` = user id, `class` = terms.id).
- Remove a course from their program map.
- See total planned credits.

Rules:

- Only users with `role = student` can open `/student` (`StudentMiddleware`); others get 403, guests go to login.
- A student can only change their own plan rows (403 otherwise) and only add classes from their own program.

Files (all new, to keep merge conflicts away from other stories):
`routes/student.php`, `StudentController`, `StudentMiddleware`, `resources/views/student/`, `public/css/student.css`,
migration `add_program_to_users_table`, `StudentSeeder`, `tests/Feature/StudentAccountTest.php`.

Small edits to shared files: one `require` line in `routes/web.php`, the student redirect in `AuthController`, one nav link in `layouts/app.blade.php`.

Test login: `php artisan db:seed --class=StudentSeeder` then log in as `student@example.com` / `password`.

## How it works (for the sprint demo)

Request flow: `/login` -> `AuthController::login` checks the password and sends `role = student` users to `/student`.
`routes/student.php` wraps every student route in `auth` (must be logged in) and `StudentMiddleware` (must be a student, else 403).

`StudentController`:

- `dashboard` reads the catalog (courses, prerequisites, terms offered) with three queries total, no query per row, then reads only the logged-in student's `plans` rows.
- `selectProgram` validates the name with `exists:programs,name` and saves it on the user.
- `addToPlan` validates the term, checks it belongs to the student's own program, and uses `firstOrCreate` so double clicks cannot create duplicates.
- `removeFromPlan` uses route model binding and returns 403 if the plan row belongs to someone else.

Data: `users.program` (new column), `plans.student` -> users.id, `plans.class` -> terms.id, `terms.course` -> courses.id, `programs.name`/`programs.course` list a program's required courses, `course_prerequisites` links a course to its prerequisite.

Security: forms use `@csrf`, output is escaped by Blade `{{ }}`, and ownership is checked on the server, not just hidden in the page.

Order for the sprint review (matches the acceptance criteria): log in as the student, see the all-courses table, pick a program, see its required courses and prerequisites, add a class to the map, reload (still there), remove it, then show another student's plan is not visible and the 403 test.
