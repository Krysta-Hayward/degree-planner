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

        // Students assigned to logged in advisor
        $students = User::where('role', 'student')->take(12)->get();

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
