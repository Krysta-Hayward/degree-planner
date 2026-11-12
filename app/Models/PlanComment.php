<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['program_map', 'user_id', 'body'])]
class PlanComment extends Model
{
    //
    public function programMap()
    {
        return $this->belongsTo(ProgramMap::class, 'program_map');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
