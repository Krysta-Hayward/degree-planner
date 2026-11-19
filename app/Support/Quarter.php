<?php

namespace App\Support;

class Quarter
{
    /**
     * Turn a quarter like "winter2027" into a number that sorts by date (20271).
     */
    public static function order(string $quarter): int
    {
        $seasons = ['winter' => 1, 'spring' => 2, 'summer' => 3, 'fall' => 4];

        if (! preg_match('/^([a-z]+)\s*(\d{4})$/i', $quarter, $parts)) {
            return 0;
        }

        return ((int) $parts[2]) * 10 + ($seasons[strtolower($parts[1])] ?? 0);
    }
}