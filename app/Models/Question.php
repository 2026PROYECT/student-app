<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = ['section_id', 'prompt', 'difficulty_level', 'order'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }
}

