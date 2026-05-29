<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    protected $fillable = ['user_id', 'department'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function availabilities()
    {
        return $this->hasMany(ProfessorAvailability::class);
    }

    public function examProctors()
    {
        return $this->belongsToMany(Exam::class, 'exam_proctor');
    }

    public function proctorConvocations()
    {
        return $this->hasMany(ProfessorConvocation::class);
    }
}
