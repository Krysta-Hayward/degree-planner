<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // The advisor created in DatabaseSeeder
        $students = Student::inRandomOrder()->take(3)->get();

        // Students assigned to logged in advisor
        $terms = Term::inRandomOrder()->take(9)->get();

        // for use in forEach
        $termCounter = 0;

        foreach ($students as $studier) {
            for ($i = 0; $i < 3; $i++) {
                DB::table('plans')->insert([
                    'student' => $studier->student,
                    'class' => $terms[$termCounter + $i]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $termCounter = $termCounter + 3;
        }
    }
}
