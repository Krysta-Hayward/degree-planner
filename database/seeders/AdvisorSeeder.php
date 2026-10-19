<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdvisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Advisors
        User::updateOrCreate(
            ['email' => 'advisor@example.com'],
             ['name' => 'Test Advisor', 'password' => Hash::make('password'), 'role' => 'advisor']
        );
    }
}
