<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'course',
        'teacher',
        'seats',
        'seatsOpen',
        'fall',
        'winter',
        'spring',
        'summer',
        'timeStart',
        'timeEnd',
    ];

    public function courseDetails()
    {
        return $this->belongsTo(Course::class, 'course');
    }
}
