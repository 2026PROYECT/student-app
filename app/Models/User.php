<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// This is the line that is likely missing or wrong:
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Relations\HasMany;




class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'lastname', 'surname', 'email', 'password', 
        'role', 'picture', 'saga_code', 'id_number', 
        'career_id', 'semester', 'is_admin', 'is_active',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
    // Inside User.php
public function quizzes()
{
    return $this->belongsToMany(Quiz::class, 'quiz_assignments');
}
public function career()
{
    return $this->belongsTo(Career::class, 'career_id', 'id_career');
}
public function testAssignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class);
    }



}
