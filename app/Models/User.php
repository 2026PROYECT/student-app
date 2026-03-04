<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
   protected $fillable = [
    'name',
    'lastname',  // Must be here
    'surname',
    'email',
    'password',
    'role',      // Must be here
    'picture',
];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relationship: A User has one Student profile.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Helper to check if user is a student
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }
}