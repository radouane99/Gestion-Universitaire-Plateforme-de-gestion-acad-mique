<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'is_current'];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }
}
