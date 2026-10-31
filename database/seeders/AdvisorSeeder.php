<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvisorSeeder extends Seeder
{
    public function run(): void
    {
        // The advisor created in DatabaseSeeder
        $advisor = User::where('email', 'advisor@example.com')->firstOrFail();

        // Link some students to this advisor (PlanSeeder picks from these)
        $students = User::where('role', 'student')->take(20)->get();

        foreach ($students as $student) {
            DB::table('students')->insert([
                'advisor' => $advisor->id,
                'student' => $student->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}