<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        // retrieve our six random courses
        $courses = Course::inRandomOrder()->take(6)->get();

        foreach ($courses as $course) {
            for ($i = 2; $i > 0; $i--) {
                DB::table('classrooms')->insert([
                    'created_at' => now(),
                    'updated_at' => now(),
                    'course' => $course->id,
                    'teacher' => fake()->name(),
                    'seats' => rand(20, 30),
                    'seatsOpen' => rand(0, 20),
                    'fall' => rand(0, 1),
                    'winter' => rand(0, 1),
                    'spring' => rand(0, 1),
                    'summer' => rand(0, 1),
                    'timeStart' => rand(9, 12),
                    'timeEnd' => rand(13, 15),
                ]);
            }
        }
    }
}
