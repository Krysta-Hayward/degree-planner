<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdvisorSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Advisor',
            'email' => 'advisor@example.com',
            'permission' => 'advisor',
            'role' => 'advisor',
        ]);
    }
}
