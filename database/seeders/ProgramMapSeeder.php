<?php

namespace Database\Seeders;

use App\Models\ProgramMap;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramMapSeeder extends Seeder
{
    // TODO: Test only. Replaced by the student Submit button.
    public function run(): void
    {
        $advisorId = User::where('email', 'advisor@example.com')->value('id');

        if (! $advisorId) {
            return;
        }

        // A program that has terms, so its courses can be planned
        $programName = DB::table('programs')
            ->join('terms', 'programs.course', '=', 'terms.course')
            ->value('programs.name');

        if (! $programName) {
            return;
        }

        // Terms (course offerings) that belong to that program
        $programTermIds = DB::table('terms')
            ->whereIn('course', DB::table('programs')->where('name', $programName)->pluck('course'))
            ->pluck('id');

        // Students linked to this advisor who don't have a map yet
        $students = DB::table('students')
            ->where('advisor', $advisorId)
            ->pluck('student')
            ->diff(ProgramMap::pluck('student'))
            ->diff(User::where('email', 'student@example.com')->pluck('id'))
            ->take(20);

        foreach ($students as $studentId) {
            // Give them the program
            DB::table('users')->where('id', $studentId)->update(['program' => $programName]);

            // Plan 3 of that program's courses
            foreach ($programTermIds->shuffle()->take(8) as $termId) {
                DB::table('plans')->insert([
                    'student' => $studentId,
                    'class' => $termId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Submit it for review
            ProgramMap::create([
                'student' => $studentId,
                'status' => 'submitted',
            ]);
        }
    }
}
