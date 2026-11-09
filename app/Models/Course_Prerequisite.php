<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course_Prerequisite extends Model
{
    use HasFactory;

    protected $table = 'course_prerequisites';

    protected $fillable = ['course', 'prereq'];

    public function courseDetails()
    {
        return $this->belongsTo(Course::class, 'course');
    }

    public function prerequisiteDetails()
    {
        return $this->belongsTo(Course::class, 'prereq');
    }
}
