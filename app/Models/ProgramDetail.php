<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A degree program's name and description.
 * Its required courses are the rows in programs with the same name.
 */
class ProgramDetail extends Model
{
    protected $fillable = ['name', 'description'];

    public function courses()
    {
        return $this->hasMany(Program::class, 'name', 'name');
    }
}
