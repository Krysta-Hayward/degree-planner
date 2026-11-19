<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftDevPrereqSeeder extends Seeder
{
    // Software Development prerequisites, from the course descriptions
    public function run(): void
    {
        $prerequisites = [
            'CIS 114' => ['CIS 111', 'CIS 146'],
            'CIS 130' => ['CIS 111'],
            'CIS 117' => ['CIS 114'],
            'CIS 230' => ['CIS 114', 'CIS 126'],
            'CIS 258' => ['CIS 114'],
            'CIS 282' => ['CIS 146'],
            'CIS 217' => ['CIS 282'],
            'CIS 233' => ['CIS 230'],
            'CIS 259' => ['CIS 117', 'CIS 258'],
            'CIS 218' => ['CIS 217'],
            'CIS 225' => ['CIS 230'],
            'CIS 272' => ['CIS 259', 'CIS 233'],
        ];

        foreach ($prerequisites as $courseNumber => $prereqNumbers) {
            $courseId = DB::table('courses')->where('courseNumber', $courseNumber)->value('id');

            if (! $courseId) {
                continue;
            }

            foreach ($prereqNumbers as $prereqNumber) {
                $prereqId = DB::table('courses')->where('courseNumber', $prereqNumber)->value('id');

                if (! $prereqId) {
                    continue;
                }

                DB::table('course_prerequisites')->updateOrInsert(
                    ['course' => $courseId, 'prereq' => $prereqId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}