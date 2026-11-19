<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['student', 'class'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The term (a course offered in a quarter) this plan row is for
    public function term()
    {
        return $this->belongsTo(Term::class, 'class');
    }

    // The course this plan row is for, reached through its term (terms id, courses id, match plans.class, match terms.course)
    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            Term::class,
            'id',
            'id',
            'class',  
            'course'   
        );
    }

    // The student's program map, matched through the shared student column
    public function programMap()
    {
        return $this->belongsTo(ProgramMap::class, 'student', 'student');
    }
}
