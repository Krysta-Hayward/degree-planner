<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollSeeder extends Seeder
{
    public function run(): void
    {
        // The advisor created in DatabaseSeeder
        $students = User::inRandomOrder()->take(3)->get();

        // Students assigned to logged in advisor
        $courses = Course::inRandomOrder()->take(9)->get();

        // for use in for loop
        $counter = 0;

        foreach ($students as $student) {
            for ($i = 0; $i < 3; $i++) {
                DB::table('enrolls')->insert([
                    'student' => $student->id,
                    'course' => $courses[$counter + $i]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $counter = $counter +3;
        }
    }
}
