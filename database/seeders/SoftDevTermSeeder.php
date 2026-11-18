<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftDevTermSeeder extends Seeder
{
    // Plan from fall of the first year through spring of the last year
    private int $firstYear = 2027;
    private int $lastYear = 2032;

    // Schedules the Software Development courses using the offering rules in their descriptions
    public function run(): void
    {
        // Course number => its offering rule
        $rules = [
            'CIS 114' => 'springOddFallWinterEven',
            'CIS 230' => 'springOddFallWinterEven',
            'CIS 258' => 'springOddFallWinterEven',
            'CIS 282' => 'springOddFallWinterEven',
            'CIS 225' => 'springOddFallWinterEven',
            'CIS 218' => 'springOddFallWinterEven',

            'CIS 117' => 'springEvenFallWinterOdd',
            'CIS 217' => 'springEvenFallWinterOdd',
            'CIS 233' => 'springEvenFallWinterOdd',
            'CIS 259' => 'springEvenFallWinterOdd',

            'CIS 107'  => 'every',
            'CIS 108'  => 'every',
            'CIS 111'  => 'every',
            'CIS 146'  => 'every',
            'CIS 126'  => 'every',
            'CIS 130'  => 'every',
            'CMST&210' => 'every',
            'ENGL&101' => 'every',
            'CIS 272'  => 'every',
        ];

        foreach ($rules as $courseNumber => $rule) {
            $courseId = DB::table('courses')->where('courseNumber', $courseNumber)->value('id');

            if (! $courseId) {
                continue;
            }

            foreach ($this->quarters() as [$season, $year]) {
                if ($this->isOffered($rule, $season, $year)) {
                    DB::table('terms')->insert([
                        // Same format as the admin Terms form, e.g. "Fall 2026"
                        'quarterYear' => ucfirst($season).' '.$year,
                        'course' => $courseId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    // Every quarter from fall of the first year to spring of the last year, in order
    private function quarters(): array
    {
        $quarters = [];

        for ($year = $this->firstYear; $year < $this->lastYear; $year++) {
            $quarters[] = ['fall', $year];
            $quarters[] = ['winter', $year + 1];
            $quarters[] = ['spring', $year + 1];
        }

        return $quarters;
    }

    // Whether a course with this rule is offered in this quarter
    private function isOffered(string $rule, string $season, int $year): bool
    {
        $even = $year % 2 === 0;

        return match ($rule) {
            'springOddFallWinterEven' => $season === 'spring' ? ! $even : $even,
            'springEvenFallWinterOdd' => $season === 'spring' ? $even : ! $even,
            default => true,
        };
    }
}
}
