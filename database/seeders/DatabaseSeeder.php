<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // admin example user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Password123!'),
                'role' => 'admin',
            ],
        );

        // advisor example user
        User::updateOrCreate(
            ['email' => 'advisor@example.com'],
            [
                'name' => 'Advisor User',
                'password' => Hash::make('Password123!'),
                'role' => 'advisor',
            ],
        );

        // student example user
        User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('Password123!'),
                'role' => 'student',
            ],
        );

        // A bunch of fake users as test data.
        User::factory(100)->create();

        // Link some students to the example advisor
        $this->call(AdvisorSeeder::class);

        // A bunch of fake courses as test data
        Course::factory(75)->create();

        // TODO: manually rip class list for Programs, random isn't going to cut it!
        // $this->call(ProgramSeeder::class);

        // Three random students with default advisor each make a plan with three random courses
        $this->call(PlanSeeder::class);

        // five random classes gain a random prerequisite.
        $this->call(PrereqSeeder::class);

        // three random terms are created with three random courses each
        $this->call(TermSeeder::class);

        // three random students enroll in three random courses each
        // TODO: $this->call(EnrollSeeder::class);
    }
}
