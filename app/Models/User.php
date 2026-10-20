<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'advisor_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function plans(){
        return $this->hasMany(Plan::class);
    }

    public function advisor(){
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function advisees(){
        return $this->hasMany(User::class, 'advisor_id');
    }

    public function planComments(){
        return $this->hasMany(PlanComment::class);
    }

    // Role helpers
    public function isStudent():bool
    {
        return $this->role === 'student';
    }

    public function isAdvisor():bool
    {
        return $this->role === 'advisor';
    }

    public function isAdmin():bool
    {
        return $this->role === 'admin';
    }

    // Query Scopes
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopesAdvisors($query)
    {
        return $query->where('role', 'advisor');
    }
}
