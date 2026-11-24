<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftDevClassroomSeeder extends Seeder
{
    // TODO: Test data only. Real class times come from the admin Classrooms page.
    public function run(): void
    {
        // Course number => [start hour, end hour], 24-hour clock
        // A few overlap on purpose, to test the schedule conflict warning
        $times = [
            'CIS 107' => [8, 10],
            'CIS 108' => [8, 10],
            'CIS 111' => [10, 12],
            'CIS 146' => [11, 13],
            'CIS 126' => [13, 15],
            'CIS 130' => [15, 17],
            'CMST&210' => [9, 11],
            'ENGL&101' => [13, 15],
            'CIS 114' => [10, 12],
            'CIS 117' => [10, 12],
            'CIS 230' => [13, 15],
            'CIS 258' => [15, 17],
            'CIS 282' => [8, 10],
            'CIS 217' => [13, 15],
            'CIS 233' => [10, 12],
            'CIS 259' => [13, 15],
            'CIS 218' => [10, 12],
            'CIS 225' => [15, 17],
            'CIS 272' => [13, 15],
        ];

        foreach ($times as $courseNumber => [$start, $end]) {
            $courseId = DB::table('courses')->where('courseNumber', $courseNumber)->value('id');

            if (! $courseId) {
                continue;
            }

            DB::table('classrooms')->insert([
                'course' => $courseId,
                'teacher' => 'Staff',
                'seats' => 30,
                'seatsOpen' => 30,
                'fall' => true,
                'winter' => true,
                'spring' => true,
                'summer' => false,
                'timeStart' => $start,
                'timeEnd' => $end,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
