<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomSubmission extends Model
{
    protected $fillable = [
        'classroom_homework_id',
        'student_id',
        'file_path',
        'submitted_at',
        'grade',
        'professor_comment',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function homework()
    {
        return $this->belongsTo(ClassroomHomework::class, 'classroom_homework_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
