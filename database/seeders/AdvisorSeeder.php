<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdvisorSeeder extends Seeder
{
    public function run(): void
    {
        // The advisor created in DatabaseSeeder
        $advisor = User::where('email', 'advisor@example.com')->firstOrFail();

        // Students assigned to that advisor
        $names = ['Aiko Tanaka', 'Jordan Miller', 'Riko Sato'];

        foreach ($names as $i => $name) {
            User::updateOrCreate(
                ['email' => 'student' . ($i + 1) . '@example.com'],
                [
                    'name' => $name,
                    'password' => Hash::make('Password123!'),
                    'role' => 'student',
                    'advisor_id' => $advisor->id,
                ],
            );
        }
    }
}