<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'career_id',
        'semester',
        'saga_code',
        'id_number',
    ];

    /**
     * Relación: Un registro de estudiante pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un estudiante pertenece a una Carrera.
     */
    public function career()
    {
        return $this->belongsTo(Career::class);
    }
}