<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'program'])]
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

    // Role helpers
    public function isAdvisor(): bool
    {
        return $this->role === 'advisor';
    }

    // Get advisor assigned to student
    public function advisor(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'students',
            'student',
            'advisor'
        );
    }

    // Get students assigned to advisor
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'students',
            'advisor',
            'student'
        );
    }

    public function plans()
    {
        return $this->hasMany(Plan::class, 'student');
    }
}
