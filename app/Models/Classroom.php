<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Classroom extends Model
{
    protected $fillable = [
        'name'
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function schedule():HasOne {
        return $this->hasOne(Schedule::class);
    }

    public function events(): HasMany {
        return $this->hasMany(Event::class);
    }

    public function subjects(): HasMany {
        return $this->hasMany(Subject::class);
    }


}
