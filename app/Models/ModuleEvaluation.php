<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleEvaluation extends Model
{
    protected $fillable = [
        'student_hash',
        'module_id',
        'professor_id',
        'academic_year_id',
        'q1_rating',
        'q2_rating',
        'q3_rating',
        'q4_rating',
        'comment'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
