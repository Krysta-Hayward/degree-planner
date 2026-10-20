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
        User::factory()->create([
        'name' => 'Test Advisor',
        'email' => 'advisor@example.com',
        'permission' => 'advisor',
        'role' => 'advisor',
        ]);
    }
}
