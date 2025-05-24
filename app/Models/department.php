<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class department extends Model
{
    protected $fillable = [
        'classroom_id',
        'name',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(classroom::class);
    }

}
