<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Student login for local testing and the sprint review. Safe to run more than once.
     * Login: student@example.com / password
     */
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password'),
                'permission' => 'student',
                'role' => 'student',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
