<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['courseNumber', 'name', 'credits', 'description'];

    public function prerequisites()
    {
        return $this->hasMany(Course_Prerequisite::class, 'course');
    }

    public function prerequisiteCourses()
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'course',
            'prereq'
        );
    }
}
