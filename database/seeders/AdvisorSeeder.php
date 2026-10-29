<?php

namespace Database\Seeders;

use App\Models\ProgramMap;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvisorSeeder extends Seeder
{
    public function run(): void
    {
        // The advisor created in DatabaseSeeder
        $advisor = User::where('email', 'advisor@example.com')->firstOrFail();

        // Students assigned to this advisor
        $students = User::where('role', 'student')->take(20)->get();

        foreach ($students as $student) {
            DB::table('students')->insert([
                'advisor' => $advisor->id,
                'student' => $student->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Test program map: the first assigned student, submitted, with two courses
        $myStudent = $students->first();

        ProgramMap::create([
            'student' => $myStudent->id,
            'status' => 'submitted',
        ]);

        $this->addCourses($myStudent->id);

        // A student NOT assigned to this advisor, for testing the 403
        $otherStudent = User::where('role', 'student')
            ->whereNotIn('id', $students->pluck('id'))
            ->first();

        if ($otherStudent) {
            ProgramMap::create([
                'student' => $otherStudent->id,
                'status' => 'submitted',
            ]);

            $this->addCourses($otherStudent->id);
        }
    }

    // Puts two courses in Fall 2026 on a student's program map
    private function addCourses(int $studentId): void
    {
        $courseIds = DB::table('courses')->take(20)->pluck('id');

        foreach ($courseIds as $courseId) {
            $termId = DB::table('terms')->insertGetId([
                'quarterYear' => 'Fall 2026',
                'course' => $courseId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('plans')->insert([
                'student' => $studentId,
                'class' => $termId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}