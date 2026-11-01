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

    public function course()
    {
        return $this->belongsTo(Course::class, 'class');
    }

    // The student's program map, matched through the shared student column
    public function programMap()
    {
        return $this->belongsTo(ProgramMap::class, 'student', 'student');
    }
}