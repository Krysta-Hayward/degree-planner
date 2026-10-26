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
                'role' => 'admin'
            ],
        );

        // advisor example user
        User::updateOrCreate(
            ['email' => 'advisor@example.com'],
            [
                'name' => 'Advisor User',
                'password' => Hash::make('Password123!'),
                'role' => 'advisor'
            ],
        );

        // student example user
        User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('Password123!'),
                'role' => 'student'
            ],
        );

        // A bunch of fake users as test data.
        User::factory(100)->create();
        
        // Link some students to the example advisor
        $this->call(AdvisorSeeder::class);

        // A bunch of fake courses as test data
        Course::factory(25)->create();
        // TODO: manually rip class list for Programs!
        // TODO: Plans should not be seeded, make one or two with hard options.
        // TODO: Seed Random Course Prerequisites
        // TODO: Students cannot be seeded; manually assign some students to some advisors.
        // Enrolls::factory(5)->create(); //low number to minimize weirdness
        // TODO: Programs cannot be randomly seeded, make manual program or two for testing.
    }
}
