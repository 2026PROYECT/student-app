<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
  protected $fillable = [
    'user_id', 'career_id', 'semester', 'saga_code', 'id_number',
];

    /**
     * Relationship: A Student profile belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A Student belongs to a Career.
     */
    public function career()
    {
        return $this->belongsTo(Career::class);
    }
}
