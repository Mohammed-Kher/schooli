<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class classroom extends Model
{
    protected $fillable = [
        'name',
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(subject::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(department::class);
    }
}
