<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftDevTermSeeder extends Seeder
{
    // Schedules the Software Development courses using the offering patterns in their descriptions
    public function run(): void
    {
        // The quarters students can plan
        $allQuarters = ['fall2026', 'winter2027', 'spring2027', 'fall2027', 'winter2028', 'spring2028'];

        // "Offered in spring odd years and fall and winter in even years"
        $springOddFallWinterEven = ['fall2026', 'spring2027', 'winter2028'];

        // "Offered in spring even years and fall and winter in odd years"
        $springEvenFallWinterOdd = ['winter2027', 'fall2027', 'spring2028'];

        $schedule = [
            'CIS 114' => $springOddFallWinterEven,
            'CIS 230' => $springOddFallWinterEven,
            'CIS 258' => $springOddFallWinterEven,
            'CIS 282' => $springOddFallWinterEven,
            'CIS 225' => $springOddFallWinterEven,
            'CIS 218' => $springOddFallWinterEven,

            'CIS 117' => $springEvenFallWinterOdd,
            'CIS 217' => $springEvenFallWinterOdd,
            'CIS 233' => $springEvenFallWinterOdd,
            'CIS 259' => $springEvenFallWinterOdd,

            // No pattern listed: offered every quarter for now
            'CIS 107'  => $allQuarters,
            'CIS 108'  => $allQuarters,
            'CIS 111'  => $allQuarters,
            'CIS 146'  => $allQuarters,
            'CIS 126'  => $allQuarters,
            'CIS 130'  => $allQuarters,
            'CMST&210' => $allQuarters,
            'ENGL&101' => $allQuarters,
            'CIS 272'  => $allQuarters,
        ];

        foreach ($schedule as $courseNumber => $quarters) {
            $courseId = DB::table('courses')->where('courseNumber', $courseNumber)->value('id');

            if (! $courseId) {
                continue;
            }

            foreach ($quarters as $quarter) {
                DB::table('terms')->insert([
                    'quarterYear' => $quarter,
                    'course' => $courseId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}