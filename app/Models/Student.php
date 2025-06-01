<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'parent_id',
        'classroom_id',
        'gender',
        'name',
        'birth_date',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentStudent::class, 'parent_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
