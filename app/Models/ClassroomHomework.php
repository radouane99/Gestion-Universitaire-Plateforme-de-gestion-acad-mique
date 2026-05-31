<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomHomework extends Model
{
    protected $table = 'classroom_homeworks';

    protected $fillable = [
        'professor_id',
        'group_id',
        'module_id',
        'title',
        'description',
        'attachment_path',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function submissions()
    {
        return $this->hasMany(ClassroomSubmission::class);
    }

    /**
     * Get the student's submission for this homework.
     */
    public function studentSubmission($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }
}
