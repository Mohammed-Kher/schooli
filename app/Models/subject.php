<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class subject extends Model
{
    protected $fillable = [
        'name',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(classroom::class);
    }
}
