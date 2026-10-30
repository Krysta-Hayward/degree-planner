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

        // Three random students with default advisor each make a plan with three random courses
        $this->call(PlanSeeder::class);

        // five random classes gain a random prerequisite.
        $this->call(PrereqSeeder::class);

        // three random terms are created with three random courses each
        $this->call(TermSeeder::class);

        // three random students enroll in three random courses each
        $this->call(EnrollSeeder::class);

        // random program with the terms from TermSeeder
        $this->call(ProgramSeeder::class);

        // AAS-SoftDev data hand-pulled from SCC website
        $this->call(SoftDevSeeder::class);

        // Program maps for students with plans, until the Submit button exists
        $this->call(ProgramMapSeeder::class);
    }
}
