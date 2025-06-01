<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $with = [
        'parent.user',
        'classroom.schedule.days.lessons.subject.teacher.user',
        'attendances.lesson.subject.teacher.user'
    ];

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

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
