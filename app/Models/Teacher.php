<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $with = [
        'user',
        'subjects.classroom.students.parent.user',
        'subjects.lessons.day.schedule.classroom',
        'subjects.homeworks',
        'subjects.events'
    ];

    protected $table = 'teachers';
    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Fixed: Changed from hasOne to hasMany
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function role(): string
    {
        return 'teacher';
    }
}