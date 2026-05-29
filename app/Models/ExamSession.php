<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = [
        'academic_year_id',
        'type',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function professorConvocations()
    {
        return $this->hasManyThrough(ProfessorConvocation::class, Exam::class);
    }

    public function getNameAttribute()
    {
        return match ($this->type) {
            'normal_autumn' => 'Normale Automne',
            'normal_spring' => 'Normale Printemps',
            'retake_autumn' => 'Rattrapage Automne',
            'retake_spring' => 'Rattrapage Printemps',
            default => 'Inconnu',
        };
    }
}
