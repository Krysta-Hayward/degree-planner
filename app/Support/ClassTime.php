<?php

namespace App\Support;

class ClassTime
{
    /**
     * Turn an hour like 13 into "1:00 PM".
     */
    public static function format(int $hour): string
    {
        $suffix = $hour >= 12 && $hour < 24 ? 'PM' : 'AM';
        $display = $hour % 12 === 0 ? 12 : $hour % 12;

        return $display.':00 '.$suffix;
    }

    /**
     * Turn a start and end hour into "10:00 AM – 12:00 PM".
     */
    public static function range(int $start, int $end): string
    {
        return self::format($start).' – '.self::format($end);
    }
}
