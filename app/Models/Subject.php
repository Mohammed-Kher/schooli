<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $with = [
        'teacher.user',
        'classroom.students.parent.user',
        'lessons.day.schedule',
        'lessons.attendances.student',
        'homeworks',
        'events'
    ];

    protected $fillable = [
        'name',
        'teacher_id',
        'classroom_id',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function homeworks(): HasMany
    {
        return $this->hasMany(Homework::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}