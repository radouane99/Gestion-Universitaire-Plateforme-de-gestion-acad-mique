<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    protected $fillable = [
        'student_id',
        'academic_tutor_id',
        'company_name',
        'company_address',
        'tutor_name',
        'tutor_email',
        'tutor_phone',
        'subject',
        'start_date',
        'end_date',
        'status',
        'grade',
        'tutor_feedback',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicTutor()
    {
        return $this->belongsTo(Professor::class, 'academic_tutor_id');
    }

    public function reports()
    {
        return $this->hasMany(InternshipReport::class);
    }
}
