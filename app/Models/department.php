<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class department extends Model
{
    protected $fillable = [
        'name',
    ];

    public function classrooms(): HasMany
    {
        return $this->hasMany(classroom::class);
    }
}
