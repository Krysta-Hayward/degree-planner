<?php

namespace Database\Seeders;

use App\Models\ProgramMap;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramMapSeeder extends Seeder
{
    public function run(): void
    {
        // A program map for every student who has planned courses
        $studentsWithPlans = DB::table('plans')->distinct()->pluck('student');

        $statuses = ['submitted', 'submitted', 'draft', 'approved'];

        foreach ($studentsWithPlans as $i => $studentId) {
            ProgramMap::create([
                'student' => $studentId,
                'status' => $statuses[$i % count($statuses)],
            ]);
        }

        // A map for a student NOT assigned to any advisor, for testing the 403
        $assigned = DB::table('students')->pluck('student');

        $otherStudent = User::where('role', 'student')
            ->whereNotIn('id', $assigned)
            ->value('id');

        if ($otherStudent) {
            ProgramMap::create([
                'student' => $otherStudent,
                'status' => 'submitted',
            ]);
        }
    }
}