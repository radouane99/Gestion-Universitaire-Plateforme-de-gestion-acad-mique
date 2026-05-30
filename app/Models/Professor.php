<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    protected $fillable = ['user_id', 'department', 'status', 'contract_end_date'];

    protected $casts = [
        'contract_end_date' => 'date',
    ];

    public function isContractActive(): bool
    {
        if ($this->status === 'permanent') {
            return true;
        }

        if (!$this->contract_end_date) {
            return false;
        }

        return $this->contract_end_date->isAfter(now()->startOfDay()) || $this->contract_end_date->isToday();
    }

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
