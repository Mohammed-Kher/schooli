<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class schedule extends Model
{
    protected $fillable = [
        'department_id',
        'day',
        'time',
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
