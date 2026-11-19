<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermSeeder extends Seeder
{
    public function run(): void
    {
        // The advisor created in DatabaseSeeder
        $quarters = ['Fall 2077', 'Winter 2077', 'Spring 2077'];

        // Students assigned to logged in advisor
        $courses = Course::inRandomOrder()->take(9)->get();

        $counter = 0;

        foreach ($quarters as $quarter) {
            for ($i = 0; $i < 3; $i++) {
                DB::table('terms')->insert([
                    'quarterYear' => $quarter,
                    'course' => $courses[$counter + $i]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $counter = $counter + 3;
        }
    }
}
