<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentDemoSeeder extends Seeder
{
    /**
     * Demo student for sprint review. Safe to run more than once.
     * Login: student@example.com / password
     */
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'student@example.com'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
