<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistorySeeder extends Seeder
{
    public function run(): void
    {
        // six random students
        $students = User::where('role', 'student')->inRandomOrder()->take(6)->get();

        foreach ($students as $student) {
            // assign a random number of classes...
            $classNum = rand(1, 5);
            $courses = Course::inRandomOrder()->take($classNum)->get();
            // and apply them one by one...
            foreach ($courses as $course) {
                // to the student's history record.
                $grade = rand(2, 4);
                switch ($grade) {
                    case 4:
                        $gradeLetter = 'A';
                        break;
                    case 3:
                        $gradeLetter = 'B';
                        break;
                    case 2:
                        $gradeLetter = 'C';
                        break;
                    case 1:
                        $gradeLetter = 'F';
                        break;
                }
                $qnum = rand(1, 4);
                switch ($qnum) {
                    case 1:
                        $quarter = 'Fall 2020';
                        break;
                    case 2:
                        $quarter = 'Winter 2020';
                        break;
                    case 3:
                        $quarter = 'Spring 2020';
                        break;
                    case 4:
                        $quarter = 'Summer 2020';
                        break;
                }
                DB::table('histories')->insert([
                    'created_at' => now(),
                    'updated_at' => now(),
                    'course' => $course->id,
                    'student' => $student->id,
                    'letterGrade' => $gradeLetter,
                    'gpa' => $grade,
                    'completed' => $quarter,
                ]);
            }

        }
    }
}
