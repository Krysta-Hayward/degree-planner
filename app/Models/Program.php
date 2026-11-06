<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'course', 'requirement_group'];

    public function courseDetails()
    {
        return $this->belongsTo(Course::class, 'course');
    }
}
