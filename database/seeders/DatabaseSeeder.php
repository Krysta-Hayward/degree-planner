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
        // TODO: manually rip class list for Programs; make and call ProgramSeeder
        // TODO: $this->call(PlanSeeder::class);
        // TODO: $this->call(PrereqSeeder::class);
        // TODO: $this->call(StudentSeeder::class);
        // TODO: $this->call(EnrollSeeder::class);
    }
}
