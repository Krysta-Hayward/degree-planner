<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course_Prerequisite;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['courseNumber', 'name', 'credits'];

    public function prerequisites()
    {
        return $this->hasMany(Course_Prerequisite::class, 'course');
    }
}
