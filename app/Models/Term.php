<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = ['quarterYear', 'course'];

    public function courseDetails()
    {
        return $this->belongsTo(Course::class, 'course');
    }
}
