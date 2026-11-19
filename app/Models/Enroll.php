<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enroll extends Model
{
    use HasFactory;

    protected $fillable = ['student', 'course', 'term_id'];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function courseDetails()
    {
        return $this->belongsTo(Course::class, 'course');
    }
}
