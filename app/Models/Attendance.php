<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    // Auto-load these relations whenever the model is retrieved
    protected $with = [
        'lesson.subject.teacher.user',
        'lesson.subject.classroom.students.parent.user',
        'lesson.day.schedule.classroom',
        'student.parent.user',
        'student.classroom'
    ];

    protected $fillable = [
        'lesson_id',
        'student_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}