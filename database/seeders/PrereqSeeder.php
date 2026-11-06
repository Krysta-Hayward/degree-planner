<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrereqSeeder extends Seeder
{
    public function run(): void
    {
        // main course
        $courses = Course::inRandomOrder()->take(5)->get();

        // prerequisites
        $prereqs = Course::inRandomOrder()->take(5)->get();

        // for use in foreach
        $counter = 0;

        foreach ($courses as $course) {
            DB::table('course_prerequisites')->insert([
                'course' => $course->id,
                'prereq' => $prereqs[$counter]->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $counter++;
        }
    }
}
