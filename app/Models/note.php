<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'teacher_name',
        'title',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}