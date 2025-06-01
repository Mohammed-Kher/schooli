<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Homework extends Model
{
    protected $with = [
        'subject.teacher.user',
        'subject.classroom.students.parent.user',
        'teacher.user'
    ];

    protected $table = 'homeworks';
    protected $fillable = [
        'subject_id',
        'teacher_id',
        'name',
        'description',
        'due_date',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}