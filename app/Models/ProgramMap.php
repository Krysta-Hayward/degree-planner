<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['student', 'status', 'reviewed_by', 'reviewed_at'])]
class ProgramMap extends Model
{
    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    // The student who owns this map
    public function owner()
    {
        return $this->belongsTo(User::class, 'student');
    }

    // The advisor who reviewed it
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // The student's planned courses: every plans row with the same student
    public function courses()
    {
        return $this->hasMany(Plan::class, 'student', 'student');
    }

    // Students can only edit while drafting or after changes are requested
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'changes_requested']);
    }
}