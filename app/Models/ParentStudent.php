<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentStudent extends Model
{
    protected $with = [
        'user',
        'students.classroom.schedule.days.lessons.subject.teacher.user',
        'students.attendances.lesson.subject'
    ];

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function role(): string
    {
        return 'parent';
    }
}