<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $with = [
        'classroom.students.parent.user',
        'days.lessons.subject.teacher.user',
        'days.lessons.attendances.student.parent.user'
    ];

    protected $fillable = [
        'classroom_id'
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function days(): HasMany 
    {
        return $this->hasMany(Day::class);
    }
}