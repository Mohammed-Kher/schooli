<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    protected $with = [
        'schedule.classroom.students.parent.user',
        'lessons.subject.teacher.user',
        'lessons.attendances.student.parent.user'
    ];

    protected $fillable = [
        'schedule_id',
        'day',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}