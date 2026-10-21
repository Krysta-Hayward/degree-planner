<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // admin example user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'Password123!',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Advisor User',
            'email' => 'advisor@example.com',
            'password' => 'Password123!',
            'role' => 'advisor',
        ]);

        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => 'Password123!',
            'role' => 'student',
        ]);

        User::factory(9)->create(); // and a total of ten to play with for now.

        Course::factory(25)->create(); // TODO: manually rip class list for Programs!
        // Term::factory(8)->create();
        // TODO: Plans should not be seeded, make one or two with hard options.
        // Course_Prerequisites::factory(8)->create(); //low number to minimize weirdness
        // TODO: Students cannot be seeded; manually assign some students to some advisors.
        // Enrolls::factory(5)->create(); //low number to minimize weirdness
        // TODO: Programs cannot be randomly seeded, make manual program or two for testing.
    }
}
