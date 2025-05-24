<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class homework extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
        'description',
        'due_date'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(subject::class);
    }
}
